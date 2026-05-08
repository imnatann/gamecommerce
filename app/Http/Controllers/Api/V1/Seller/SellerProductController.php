<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerProductController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $status = $request->query('status');

        $products = Product::where('seller_id', $request->user()->id)
            ->with(['game', 'category', 'media'])
            ->when($status, fn ($q, $s) => $q->where('is_active', $s === 'active'))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'category_id' => 'nullable|exists:categories,id',
            'game_product_id' => 'nullable|exists:game_products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'product_type' => 'required|in:topup,key,item,akun,voucher,joki,koin',
            'delivery_type' => 'required|in:auto,manual,instant',
            'server' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
            'required_info' => 'nullable|array',
            'required_info.*' => 'string',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create([
                'seller_id' => $request->user()->id,
                'game_id' => $validated['game_id'],
                'category_id' => $validated['category_id'] ?? null,
                'game_product_id' => $validated['game_product_id'] ?? null,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'] ?? null,
                'product_type' => $validated['product_type'],
                'delivery_type' => $validated['delivery_type'],
                'server' => $validated['server'] ?? null,
                'region' => $validated['region'] ?? null,
                'required_info' => $validated['required_info'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'sold_count' => 0,
                'avg_rating' => 0,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $product->addMedia($image)
                        ->withCustomProperties(['order' => $index])
                        ->toMediaCollection('product_images');
                }
            }

            DB::commit();

            return $this->successResponse($product->load(['game', 'category', 'media']), 'Produk berhasil ditambahkan.', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Gagal menambahkan produk.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $product = Product::where('seller_id', $request->user()->id)
            ->with(['game', 'category', 'media'])
            ->findOrFail($id);

        return $this->successResponse($product);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::where('seller_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'delivery_type' => 'sometimes|in:auto,manual,instant',
            'server' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'required_info' => 'nullable|array',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        }

        $product->update($validated);

        if ($request->hasFile('new_images')) {
            $request->validate([
                'new_images' => 'array|max:5',
                'new_images.*' => 'image|max:2048',
            ]);

            foreach ($request->file('new_images') as $image) {
                $product->addMedia($image)->toMediaCollection('product_images');
            }
        }

        return $this->successResponse($product->fresh()->load(['game', 'category', 'media']), 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $product = Product::where('seller_id', $request->user()->id)
            ->findOrFail($id);

        if ($product->sold_count > 0) {
            $product->update(['is_active' => false]);

            return $this->successResponse(null, 'Produk dinonaktifkan. Produk dengan riwayat penjualan tidak dapat dihapus permanen.');
        }

        $product->delete();

        return $this->successResponse(null, 'Produk berhasil dihapus.');
    }
}