<?php

namespace App\Http\Controllers\Web;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'popular');
        $categoryId = $request->query('category');
        $productType = $this->productTypeFromRequest($request);
        $genre = $request->query('genre');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $perPage = min(max((int) $request->query('per_page', 24), 1), 60);

        $productsQuery = DB::table('products')
            ->join('game_products', 'game_products.id', '=', 'products.game_product_id')
            ->leftJoin('games', 'games.id', '=', 'game_products.game_id')
            ->leftJoin('users', 'users.id', '=', 'products.seller_id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->when($query !== '', fn ($builder) => $builder->where(function ($searchQuery) use ($query) {
                $searchQuery->where('products.name', 'like', "%{$query}%")
                    ->orWhere('products.description', 'like', "%{$query}%")
                    ->orWhere('products.server', 'like', "%{$query}%")
                    ->orWhere('products.region', 'like', "%{$query}%")
                    ->orWhere('games.name', 'like', "%{$query}%")
                    ->orWhere('games.category', 'like', "%{$query}%");
            }))
            ->when($productType, fn ($builder, string $type) => $builder
                ->where('game_products.type', $type))
            ->when($genre, fn ($builder, string $gameCategory) => $builder
                ->where('games.category', 'like', "%{$gameCategory}%"))
            ->when($minPrice !== null && $minPrice !== '', fn ($builder) => $builder->where('products.price', '>=', (int) $minPrice))
            ->when($maxPrice !== null && $maxPrice !== '', fn ($builder) => $builder->where('products.price', '<=', (int) $maxPrice))
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

        $categories = Category::active()
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'url' => route('search', ['type' => $category->type]),
                'active' => $productType === $category->type || (string) $categoryId === (string) $category->id,
            ])
            ->values()
            ->all();

        $popularSearches = [
            'Mobile Legends',
            'Free Fire',
            'PUBG Mobile',
            'Genshin Impact',
            'Valorant',
            'ML Diamond',
            'FF Diamond',
            'Steam Wallet',
        ];

        $games = Game::where('is_active', true)
            ->orderBy('name')
            ->limit(20)
            ->get();

        $totalResults = $products->total();

        return view('pages.search', compact(
            'products',
            'query',
            'sort',
            'categories',
            'popularSearches',
            'games',
            'categoryId',
            'productType',
            'minPrice',
            'maxPrice',
            'totalResults'
        ));
    }

    private function productTypeFromRequest(Request $request): ?string
    {
        $type = $this->normalizeProductType($request->query('type'));

        if ($type) {
            return $type;
        }

        $categoryId = $request->query('category');

        if (!$categoryId) {
            return null;
        }

        return $this->normalizeProductType(Category::whereKey($categoryId)->value('type'));
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
