<?php

namespace App\Repositories;

use App\Enums\ProductType;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function search(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = Product::query()->where('is_active', true);

        if ($query) {
            $builder->where(fn ($q) => $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%"));
        }

        if (!empty($filters['game_id'])) {
            $builder->where('game_id', $filters['game_id']);
        }

        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }

        if (!empty($filters['min_price'])) {
            $builder->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $builder->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['server'])) {
            $builder->where('server', $filters['server']);
        }

        $sortField = $filters['sort'] ?? 'popular';
        $builder->with('game', 'seller');

        return match ($sortField) {
            'price_asc' => $builder->orderBy('price')->paginate($perPage),
            'price_desc' => $builder->orderByDesc('price')->paginate($perPage),
            'newest' => $builder->orderByDesc('created_at')->paginate($perPage),
            'rating' => $builder->orderByDesc('rating')->paginate($perPage),
            default => $builder->orderByDesc('sold_count')->orderByDesc('rating')->paginate($perPage),
        };
    }

    public function findByGame(int $gameId, string $productType = null, int $perPage = 15): LengthAwarePaginator
    {
        $builder = Product::where('game_id', $gameId)
            ->where('is_active', true)
            ->with('seller');

        if ($productType) {
            $builder->where('type', $productType);
        }

        return $builder->orderBy('price')->paginate($perPage);
    }

    public function findCheapest(int $gameProductId): ?Product
    {
        return Cache::remember("products.cheapest.{$gameProductId}", 600, fn () => Product::where('game_product_id', $gameProductId)
            ->where('is_active', true)
            ->orderBy('price')
            ->first());
    }

    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember("products.popular.{$limit}", 1800, fn () => Product::where('is_active', true)
            ->with('game', 'seller')
            ->orderByDesc('sold_count')
            ->orderByDesc('rating')
            ->limit($limit)
            ->get());
    }

    public function findBySeller(int $sellerId, int $perPage = 15): LengthAwarePaginator
    {
        return Cache::remember("seller.products.{$sellerId}.page", 300, fn () => Product::where('seller_id', $sellerId)
            ->with('game')
            ->orderByDesc('created_at')
            ->paginate($perPage));
    }

    public function filterAndSort(array $filters, string $sort = 'popular', int $perPage = 15): LengthAwarePaginator
    {
        $builder = Product::query()->where('is_active', true);

        if (!empty($filters['game_id'])) {
            $builder->where('game_id', $filters['game_id']);
        }

        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }

        if (!empty($filters['min_price'])) {
            $builder->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $builder->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['seller_id'])) {
            $builder->where('seller_id', $filters['seller_id']);
        }

        if (!empty($filters['server'])) {
            $builder->where('server', $filters['server']);
        }

        if (!empty($filters['rating_min'])) {
            $builder->where('rating', '>=', $filters['rating_min']);
        }

        $builder->with('game', 'seller');

        return match ($sort) {
            'price_asc' => $builder->orderBy('price')->paginate($perPage),
            'price_desc' => $builder->orderByDesc('price')->paginate($perPage),
            'newest' => $builder->orderByDesc('created_at')->paginate($perPage),
            'rating' => $builder->orderByDesc('rating')->paginate($perPage),
            default => $builder->orderByDesc('sold_count')->orderByDesc('rating')->paginate($perPage),
        };
    }
}