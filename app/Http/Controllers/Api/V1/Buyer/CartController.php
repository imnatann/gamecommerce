<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $cart = $request->user()->cart()->with(['items.product.gameProduct.game', 'items.product.seller'])->first();

        if (! $cart) {
            return $this->successResponse(['items' => [], 'total' => 0]);
        }

        $items = $cart->items;
        $total = $items->sum(fn ($item) => ($item->price ?: ($item->product?->price ?? 0)) * $item->quantity);

        return $this->successResponse([
            'items' => $items,
            'total' => $total,
            'item_count' => $items->count(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'server' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $quantity = $validated['quantity'] ?? 1;

        if ($product->stock !== null && $product->stock < $quantity) {
            return $this->errorResponse('Stok tidak mencukupi.', 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('server', $validated['server'] ?? null)
            ->where('region', $validated['region'] ?? null)
            ->first();

        $newQuantity = ($existingItem?->quantity ?? 0) + $quantity;

        if ($product->stock !== null && $product->stock < $newQuantity) {
            return $this->errorResponse('Stok tidak mencukupi.', 400);
        }

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price,
                'server_info' => $validated['server'] ?? null,
            ]);
            $existingItem->load(['product.gameProduct.game', 'product.seller']);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'server' => $validated['server'] ?? null,
                'region' => $validated['region'] ?? null,
                'server_info' => $validated['server'] ?? null,
            ]);
            $cartItem->load(['product.gameProduct.game', 'product.seller']);
        }

        return $this->successResponse($cart->load(['items.product.gameProduct.game', 'items.product.seller']), 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $request->user()->id))
            ->findOrFail($itemId);

        $product = Product::findOrFail($cartItem->product_id);

        if ($product->stock !== null && $product->stock < $validated['quantity']) {
            return $this->errorResponse('Stok tidak mencukupi.', 400);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'price' => $product->price,
        ]);
        $cartItem->load(['product.gameProduct.game', 'product.seller']);

        return $this->successResponse($cartItem, 'Jumlah produk diperbarui.');
    }

    public function remove(Request $request, int $itemId): JsonResponse
    {
        $cartItem = CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $request->user()->id))
            ->findOrFail($itemId);

        $cartItem->delete();

        return $this->successResponse(null, 'Produk dihapus dari keranjang.');
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $request->user()->cart;

        if ($cart) {
            $cart->items()->delete();
        }

        return $this->successResponse(null, 'Keranjang dikosongkan.');
    }
}
