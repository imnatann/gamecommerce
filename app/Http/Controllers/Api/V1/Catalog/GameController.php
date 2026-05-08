<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Game;
use App\Models\GameProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GameController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 20);

        $games = Game::with(['category', 'media'])
            ->withCount('products')
            ->where('is_active', true)
            ->when($categoryId, fn ($q, $id) => $q->where('category_id', $id))
            ->when($search, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('sort_order')
            ->orderByDesc('products_count')
            ->paginate($perPage);

        return $this->paginateResponse($games);
    }

    public function show(string $slug): JsonResponse
    {
        $game = Cache::remember("api.games.{$slug}", 1800, fn () => Game::with(['category', 'media'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail());

        $gameProducts = GameProduct::where('game_id', $game->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $topProducts = \App\Models\Product::with(['seller', 'media'])
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->orderByDesc('sold_count')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'game' => $game,
            'game_products' => $gameProducts,
            'top_products' => $topProducts,
        ]);
    }

    public function popular(): JsonResponse
    {
        $games = Cache::remember('api.games.popular', 3600, fn () => Game::with(['category', 'media'])
            ->withCount('products')
            ->where('is_active', true)
            ->orderByDesc('products_count')
            ->limit(12)
            ->get());

        return $this->successResponse($games);
    }

    public function gameProducts(string $slug, Request $request): JsonResponse
    {
        $game = Game::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $productType = $request->query('type');

        $gameProducts = GameProduct::where('game_id', $game->id)
            ->where('is_active', true)
            ->when($productType, fn ($q, $type) => $q->where('type', $type))
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse($gameProducts);
    }
}