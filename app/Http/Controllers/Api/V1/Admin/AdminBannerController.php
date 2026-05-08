<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminBannerController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $position = $request->query('position');

        $banners = Banner::when($position, fn ($q, $p) => $q->where('position', $p))
            ->orderBy('sort_order')
            ->paginate($perPage);

        return $this->paginateResponse($banners);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'link' => 'nullable|url|max:500',
            'position' => 'required|in:hero,sidebar,bottom',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'image' => 'required|image|max:5120',
        ]);

        $banner = Banner::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'link' => $validated['link'] ?? null,
            'position' => $validated['position'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        Cache::forget('banners.active');

        return $this->successResponse($banner->fresh()->load('media'), 'Banner berhasil ditambahkan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $banner = Banner::with('media')->findOrFail($id);

        return $this->successResponse($banner);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'link' => 'nullable|url|max:500',
            'position' => 'sometimes|in:hero,sidebar,bottom',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'image' => 'nullable|image|max:5120',
        ]);

        $banner->update($validated);

        if ($request->hasFile('image')) {
            $banner->clearMediaCollection('banner_image');
            $banner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        Cache::forget('banners.active');

        return $this->successResponse($banner->fresh()->load('media'), 'Banner berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        Cache::forget('banners.active');

        return $this->successResponse(null, 'Banner berhasil dihapus.');
    }

    public function toggleActive(int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        Cache::forget('banners.active');

        return $this->successResponse($banner->fresh(), $banner->is_active ? 'Banner diaktifkan.' : 'Banner dinonaktifkan.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['banners'] as $item) {
            Banner::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        Cache::forget('banners.active');

        return $this->successResponse(null, 'Urutan banner berhasil diperbarui.');
    }
}