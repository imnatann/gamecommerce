<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.game', 'product.seller', 'product.media'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($wishlists);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return $this->successResponse(['is_wishlisted' => false], 'Produk dihapus dari favorit.');
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return $this->successResponse(['is_wishlisted' => true], 'Produk ditambahkan ke favorit.');
    }

    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $wishlisted = Wishlist::where('user_id', $request->user()->id)
            ->whereIn('product_id', $validated['product_ids'])
            ->pluck('product_id')
            ->toArray();

        return $this->successResponse(['wishlisted_product_ids' => $wishlisted]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        return $this->successResponse(null, 'Produk dihapus dari favorit.');
    }
}