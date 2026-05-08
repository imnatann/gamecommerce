<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banners = Cache::remember('home.hero_banners', 1800, fn () => Banner::active()
            ->byPosition('home_hero')
            ->get()
            ->map(fn (Banner $banner) => $this->bannerSlide($banner))
            ->values()
            ->all());

        $topUpGames = Cache::remember('home.games.topup', 1800, fn () => $this->gamesForType('topup'));
        $voucherGames = Cache::remember('home.games.voucher', 1800, fn () => $this->gamesForType('voucher'));

        $gameKeyProducts = Cache::remember('home.products.game_key', 900, fn () => $this->productsForType('game_key'));
        $robloxProducts = Cache::remember('home.products.roblox', 900, fn () => $this->productsForSearch('roblox'));
        $accountProducts = Cache::remember('home.products.account', 900, fn () => $this->productsForType('account'));

        $paymentMethods = [
            ['name' => 'QRIS'],
            ['name' => 'BCA'],
            ['name' => 'BNI'],
            ['name' => 'BRI'],
            ['name' => 'GoPay'],
            ['name' => 'OVO'],
            ['name' => 'DANA'],
            ['name' => 'ShopeePay'],
        ];

        return view('pages.home', compact(
            'banners',
            'topUpGames',
            'voucherGames',
            'gameKeyProducts',
            'robloxProducts',
            'accountProducts',
            'paymentMethods'
        ));
    }

    private function gamesForType(string $type): array
    {
        $games = Game::query()
            ->where('is_active', true)
            ->whereHas('gameProducts', fn ($query) => $query
                ->where('is_active', true)
                ->where('type', $type))
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        if ($games->isEmpty()) {
            $games = Game::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get();
        }

        return $games
            ->map(fn (Game $game) => $this->gameCardData($game, ['type' => $type]))
            ->values()
            ->all();
    }

    private function productsForType(string $type): array
    {
        return $this->productBaseQuery()
            ->where('game_products.type', $type)
            ->orderByDesc('products.sold_count')
            ->limit(8)
            ->get()
            ->map(fn (object $product) => $this->productCardData($product))
            ->values()
            ->all();
    }

    private function productsForSearch(string $term): array
    {
        return $this->productBaseQuery()
            ->where(function ($query) use ($term) {
                $query->where('products.name', 'like', "%{$term}%")
                    ->orWhere('games.name', 'like', "%{$term}%");
            })
            ->orderByDesc('products.sold_count')
            ->limit(8)
            ->get()
            ->map(fn (object $product) => $this->productCardData($product))
            ->values()
            ->all();
    }

    private function productBaseQuery()
    {
        return DB::table('products')
            ->join('game_products', 'game_products.id', '=', 'products.game_product_id')
            ->leftJoin('games', 'games.id', '=', 'game_products.game_id')
            ->leftJoin('users', 'users.id', '=', 'products.seller_id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
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
    }

    private function bannerSlide(Banner $banner): array
    {
        return [
            'title' => $banner->title,
            'subtitle' => $banner->subtitle,
            'image' => $this->assetUrl($banner->image),
            'cta_text' => $banner->link ? 'Lihat Promo' : null,
            'cta_url' => $banner->link ?: '#',
        ];
    }

    private function gameCardData(Game $game, array $query = []): array
    {
        return [
            'name' => $game->name,
            'slug' => $game->slug,
            'icon' => $this->assetUrl($game->icon),
            'url' => route('game.show', array_merge(['slug' => $game->slug], $query)),
        ];
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
