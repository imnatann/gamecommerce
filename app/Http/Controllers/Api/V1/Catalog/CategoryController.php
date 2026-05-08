<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('api.categories.all', 1800, fn () => Category::withCount('games')
            ->with(['media'])
            ->orderBy('sort_order')
            ->get());

        return $this->successResponse($categories);
    }

    public function show(string $slug): JsonResponse
    {
        $category = Cache::remember("api.categories.{$slug}", 1800, fn () => Category::with(['games' => fn ($q) => $q->where('is_active', true)])
            ->withCount('games')
            ->where('slug', $slug)
            ->firstOrFail());

        return $this->successResponse($category);
    }

    public function games(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $games = $category->games()
            ->with(['media'])
            ->withCount('products')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate(20);

        return $this->paginateResponse($games);
    }
}