<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductPageController extends Controller
{
    public function show(string $slug, int $id): View
    {
        $product = $this->productDetailQuery()
            ->where('products.id', $id)
            ->where('products.slug', $slug)
            ->first();

        abort_if(!$product, 404);

        $reviews = DB::table('reviews')
            ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->where('reviews.product_id', $product->id)
            ->orderByDesc('reviews.created_at')
            ->select([
                'reviews.rating',
                'reviews.comment',
                'reviews.is_anonymous',
                'reviews.created_at',
                'users.name as user_name',
            ])
            ->paginate(10);

        $reviewItems = $reviews
            ->getCollection()
            ->map(fn (object $review) => $this->reviewData($review))
            ->values();

        $relatedProducts = $this->productCardQuery()
            ->where('products.id', '!=', $product->id)
            ->when($product->catalog_game_id, fn ($query, int $gameId) => $query->where('game_products.game_id', $gameId))
            ->orderByDesc('products.sold_count')
            ->limit(6)
            ->get()
            ->map(fn (object $relatedProduct) => $this->productCardData($relatedProduct));

        $cheapestAlternatives = $this->productCardQuery()
            ->where('products.game_product_id', $product->game_product_id)
            ->where('products.id', '!=', $product->id)
            ->orderBy('products.price')
            ->limit(5)
            ->get()
            ->map(fn (object $alternative) => $this->productCardData($alternative));

        $productData = $this->productDetailData($product, $reviewItems);

        return view('pages.product', [
            'product' => $productData,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
            'cheapestAlternatives' => $cheapestAlternatives,
        ]);
    }

    private function productDetailQuery()
    {
        return DB::table('products')
            ->join('game_products', 'game_products.id', '=', 'products.game_product_id')
            ->leftJoin('games', 'games.id', '=', 'game_products.game_id')
            ->leftJoin('users', 'users.id', '=', 'products.seller_id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->select([
                'products.id',
                'products.seller_id',
                'products.game_product_id',
                'products.slug',
                'products.name',
                'products.description',
                'products.price',
                'products.original_price',
                'products.stock',
                'products.server',
                'products.region',
                'products.delivery_type',
                'products.sold_count',
                'products.avg_rating',
                'products.rating_count',
                'games.id as catalog_game_id',
                'games.name as game_name',
                'games.slug as game_slug',
                'games.icon as game_icon',
                'games.banner as game_banner',
                'users.name as seller_name',
                DB::raw('NULL as seller_avatar'),
                'users.email_verified_at as seller_email_verified_at',
            ]);
    }

    private function productCardQuery()
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

    private function productDetailData(object $product, Collection $reviews): array
    {
        $image = $this->assetUrl($product->game_banner ?: $product->game_icon);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'game_name' => $product->game_name,
            'game_url' => $product->game_slug ? route('game.show', ['slug' => $product->game_slug]) : '#',
            'image' => $image,
            'images' => array_values(array_filter([$image, $this->assetUrl($product->game_icon)])),
            'server' => $product->server,
            'region' => $product->region,
            'currency' => 'Rp',
            'price' => $this->formatNumber($product->price),
            'original_price' => $product->original_price ? $this->formatNumber($product->original_price) : null,
            'discount' => $this->discountLabel($product->price, $product->original_price),
            'rating' => (float) $product->avg_rating,
            'rating_count' => $product->rating_count,
            'sold_count' => $product->sold_count,
            'stock' => $product->stock,
            'delivery_type' => $product->delivery_type,
            'seller_name' => $product->seller_name,
            'seller_avatar' => $this->assetUrl($product->seller_avatar),
            'seller_verified' => (bool) $product->seller_email_verified_at,
            'seller_rating' => DB::table('products')
                ->where('seller_id', $product->seller_id)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->avg('avg_rating') ?: 0,
            'seller_total_sold' => DB::table('products')
                ->where('seller_id', $product->seller_id)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->sum('sold_count'),
            'seller_url' => '#',
            'reviews' => $reviews->all(),
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

    private function reviewData(object $review): array
    {
        return [
            'user_name' => $review->is_anonymous ? 'Anonim' : ($review->user_name ?? 'Anonim'),
            'rating' => $review->rating,
            'comment' => $review->comment,
            'date' => $review->created_at ? \Carbon\Carbon::parse($review->created_at)->diffForHumans() : '',
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
        return 'Rp ' . $this->formatNumber($amount);
    }

    private function formatNumber(?int $amount): string
    {
        return number_format($amount ?? 0, 0, ',', '.');
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
