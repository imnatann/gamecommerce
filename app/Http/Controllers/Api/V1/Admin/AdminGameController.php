<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Game;
use App\Models\GameProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminGameController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $search = $request->query('search');

        $games = Game::with(['category', 'media'])
            ->withCount('products')
            ->when($search, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('sort_order')
            ->paginate($perPage);

        return $this->paginateResponse($games);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'region' => 'nullable|string|max:10',
            'meta' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'icon' => 'nullable|image|max:1024',
            'banner' => 'nullable|image|max:2048',
        ]);

        $game = Game::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category_id' => $validated['category_id'],
            'region' => $validated['region'] ?? 'ID',
            'meta' => $validated['meta'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        if ($request->hasFile('icon')) {
            $game->addMediaFromRequest('icon')->toMediaCollection('icon');
        }

        if ($request->hasFile('banner')) {
            $game->addMediaFromRequest('banner')->toMediaCollection('banner');
        }

        Cache::forget('games.*');
        Cache::forget('games.popular');

        return $this->successResponse($game->fresh()->load(['category', 'media']), 'Game berhasil ditambahkan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $game = Game::with(['category', 'media', 'gameProducts'])
            ->withCount('products')
            ->findOrFail($id);

        return $this->successResponse($game);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'region' => 'nullable|string|max:10',
            'meta' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'icon' => 'nullable|image|max:1024',
            'banner' => 'nullable|image|max:2048',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $game->update($validated);

        if ($request->hasFile('icon')) {
            $game->clearMediaCollection('icon');
            $game->addMediaFromRequest('icon')->toMediaCollection('icon');
        }

        if ($request->hasFile('banner')) {
            $game->clearMediaCollection('banner');
            $game->addMediaFromRequest('banner')->toMediaCollection('banner');
        }

        Cache::forget('games.*');
        Cache::forget("games.detail.{$game->slug}");

        return $this->successResponse($game->fresh()->load(['category', 'media']), 'Game berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $game = Game::findOrFail($id);

        if ($game->products()->exists()) {
            $game->update(['is_active' => false]);
            Cache::forget('games.*');

            return $this->successResponse(null, 'Game dinonaktifkan. Game dengan produk tidak dapat dihapus permanen.');
        }

        $game->delete();
        Cache::forget('games.*');
        Cache::forget("games.detail.{$game->slug}");

        return $this->successResponse(null, 'Game berhasil dihapus.');
    }
}