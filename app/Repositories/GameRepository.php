<?php

namespace App\Repositories;

use App\Models\Game;
use App\Repositories\Contracts\GameRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GameRepository implements GameRepositoryInterface
{
    public function all(): Collection
    {
        return Cache::remember('games.list', 1800, fn () => Game::with(['category', 'products'])->orderBy('sort_order')->get());
    }

    public function findBySlug(string $slug): ?Game
    {
        return Cache::remember("games.{$slug}", 900, fn () => Game::where('slug', $slug)->with(['category', 'products'])->first());
    }

    public function popular(int $limit = 10): Collection
    {
        return Cache::remember('games.popular', 3600, fn () => Game::with('products')
            ->withCount(['products as sold_count' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('sold_count')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get());
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Game::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with('category')
            ->orderBy('sort_order')
            ->paginate($perPage);
    }

    public function withProducts(int $gameId): ?Game
    {
        return Cache::remember("games.products.{$gameId}", 900, fn () => Game::where('id', $gameId)
            ->with(['products' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->first());
    }
}