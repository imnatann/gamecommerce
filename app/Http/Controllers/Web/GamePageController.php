<?php

namespace App\Http\Controllers\Web;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GamePageController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $game = Game::with('gameProducts')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $productType = $this->normalizeProductType($request->query('type'));
        $server = $request->query('server');
        $sort = $request->query('sort', 'popular');
        $perPage = min(max((int) $request->query('per_page', 24), 1), 60);

        $baseProducts = DB::table('products')
            ->join('game_products', 'game_products.id', '=', 'products.game_product_id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->where('game_products.game_id', $game->id);

        $servers = (clone $baseProducts)
            ->whereNotNull('products.server')
            ->distinct()
            ->orderBy('products.server')
            ->pluck('products.server')
            ->map(fn (string $serverName) => ['id' => $serverName, 'name' => $serverName])
            ->values()
            ->all();

        $regions = (clone $baseProducts)
            ->whereNotNull('products.region')
            ->distinct()
            ->orderBy('products.region')
            ->pluck('products.region')
            ->map(fn (string $region) => ['id' => $region, 'name' => $region])
            ->values()
            ->all();

        $productsQuery = (clone $baseProducts)
            ->leftJoin('games', 'games.id', '=', 'game_products.game_id')
            ->leftJoin('users', 'users.id', '=', 'products.seller_id')
            ->when($productType, fn ($query, string $type) => $query
                ->where('game_products.type', $type))
            ->when($server, fn ($query, string $serverName) => $query->where('products.server', $serverName))
            ->select([
                'products.id',
                'products.slug',
                'products.name',
                'products.price',
                'products.original_price',
                'products.sold_count',
                'products.avg_rating',
                'products.rating_count',
                'games.name as game_name',
                'games.icon as game_icon',
                'games.banner as game_banner',
                'users.name as seller_name',
                'users.email_verified_at as seller_email_verified_at',
            ]);

        $this->applyProductSort($productsQuery, $sort);

        $products = $productsQuery
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (object $product) => $this->productCardData($product));

        $gameProducts = $game->gameProducts
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values();

        $productTypes = $gameProducts
            ->map(fn (GameProduct $gameProduct) => [
                'slug' => $this->productTypeValue($gameProduct->type),
                'name' => $gameProduct->name ?: $this->productTypeLabel($this->productTypeValue($gameProduct->type)),
            ])
            ->unique('slug')
            ->values()
            ->all();

        if ($productTypes === []) {
            $productTypes = collect(ProductType::cases())
                ->map(fn (ProductType $type) => ['slug' => $type->value, 'name' => $type->label()])
                ->all();
        }

        $relatedGames = Game::query()
            ->where('is_active', true)
            ->where('id', '!=', $game->id)
            ->when($game->category, fn ($query, string $category) => $query->where('category', $category))
            ->orderBy('sort_order')
            ->limit(6)
            ->get()
            ->map(fn (Game $relatedGame) => [
                'name' => $relatedGame->name,
                'slug' => $relatedGame->slug,
                'icon' => $this->assetUrl($relatedGame->icon),
                'url' => route('game.show', ['slug' => $relatedGame->slug]),
            ])
            ->values()
            ->all();

        $gameData = [
            'name' => $game->name,
            'slug' => $game->slug,
            'icon' => $this->assetUrl($game->icon),
            'banner' => $this->assetUrl($game->banner),
            'region' => $game->region,
            'category' => $game->category,
        ];

        $activeTab = $productType ?: ($productTypes[0]['slug'] ?? 'topup');
        $totalProducts = $products->total();

        return view('pages.game', compact(
            'gameData',
            'products',
            'relatedGames',
            'gameProducts',
            'productTypes',
            'servers',
            'regions',
            'activeTab',
            'sort',
            'totalProducts'
        ))->with('game', $gameData);
    }

    private function applyProductSort($query, string $sort): void
    {
        match ($sort) {
            'cheapest' => $query->orderBy('products.price'),
            'newest' => $query->orderByDesc('products.created_at'),
            'rating' => $query->orderByDesc('products.avg_rating')->orderByDesc('products.rating_count'),
            default => $query->orderByDesc('products.sold_count')->orderByDesc('products.created_at'),
        };
    }

    private function productCardData(object $product): array
    {
        return [
            'id' => $product->id,
            'url' => route('product.show', ['slug' => $product->slug, 'id' => $product->id]),
            'image' => $this->assetUrl($product->game_icon ?: $product->game_banner),
            'name' => $product->name,
            'game_name' => $product->game_name,
            'price' => $this->formatRupiah($product->price),
            'original_price' => $product->original_price ? $this->formatRupiah($product->original_price) : null,
            'discount' => $this->discountLabel($product->price, $product->original_price),
            'rating' => (float) $product->avg_rating,
            'rating_count' => $product->rating_count,
            'sold_count' => number_format($product->sold_count),
            'seller_name' => $product->seller_name,
            'seller_verified' => (bool) $product->seller_email_verified_at,
        ];
    }

    private function discountLabel(?int $price, ?int $originalPrice): ?string
    {
        if (!$originalPrice || !$price || $originalPrice <= $price) {
            return null;
        }

        return round((1 - $price / $originalPrice) * 100, 1) . '% OFF';
    }

    private function normalizeProductType(?string $type): ?string
    {
        if (!$type) {
            return null;
        }

        $type = $type === 'key' ? 'game_key' : $type;

        return ProductType::tryFrom($type)?->value;
    }

    private function productTypeValue(ProductType|string|null $type): string
    {
        return $type instanceof ProductType ? $type->value : (string) $type;
    }

    private function productTypeLabel(string $type): string
    {
        return ProductType::tryFrom($type)?->label() ?? str($type)->replace('_', ' ')->title()->toString();
    }

    private function formatRupiah(?int $amount): string
    {
        return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
    }

    private function assetUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
