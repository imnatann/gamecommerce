<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\Search\MeilisearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends ApiBaseController
{
    public function __construct(
        private MeilisearchService $searchService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:all,game,product',
            'category_id' => 'nullable|integer|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|in:relevant,cheapest,popular,newest,rating',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = $validated['q'];
        $type = $validated['type'] ?? 'all';
        $filters = array_filter([
            'category_id' => $validated['category_id'] ?? null,
            'min_price' => $validated['min_price'] ?? null,
            'max_price' => $validated['max_price'] ?? null,
        ]);
        $sort = $validated['sort'] ?? 'relevant';
        $perPage = $validated['per_page'] ?? 20;

        $results = match ($type) {
            'game' => $this->searchGames($query),
            'product' => $this->searchProducts($query, $filters, $sort, $perPage),
            default => $this->searchAll($query, $filters, $sort, $perPage),
        };

        return $this->successResponse($results);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1|max:50',
        ]);

        $suggestions = $this->searchService->suggest($validated['q']);

        return $this->successResponse($suggestions);
    }

    public function popular(): JsonResponse
    {
        $trending = \Illuminate\Support\Facades\Cache::remember('api.search.popular', 3600, fn () => [
            'keywords' => ['Mobile Legends', 'Free Fire', 'PUBG Mobile', 'Genshin Impact', 'Valorant', 'ML Diamond'],
            'games' => \App\Models\Game::with('media')
                ->withCount('products')
                ->where('is_active', true)
                ->orderByDesc('products_count')
                ->limit(8)
                ->get(),
        ]);

        return $this->successResponse($trending);
    }

    private function searchGames(string $query): array
    {
        $games = \App\Models\Game::with(['category', 'media'])
            ->withCount('products')
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->orderByDesc('products_count')
            ->limit(10)
            ->get();

        return ['games' => $games];
    }

    private function searchProducts(string $query, array $filters, string $sort, int $perPage): array
    {
        $products = $this->searchService->search($query, $filters, $sort, $perPage);

        return ['products' => $products];
    }

    private function searchAll(string $query, array $filters, string $sort, int $perPage): array
    {
        $games = \App\Models\Game::with(['category', 'media'])
            ->withCount('products')
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();

        $products = $this->searchService->search($query, $filters, $sort, $perPage);

        return [
            'games' => $games,
            'products' => $products,
        ];
    }
}