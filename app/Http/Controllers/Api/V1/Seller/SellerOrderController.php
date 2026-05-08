<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\ApiBaseController;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerOrderController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 20);

        $orders = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $request->user()->id))
            ->with(['buyer', 'items.product.game', 'payment'])
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $request->user()->id))
            ->with(['buyer', 'items.product.game', 'items.product.media', 'payment', 'dispute'])
            ->findOrFail($id);

        return $this->successResponse($order);
    }

    public function deliver(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'delivery_data' => 'required|array',
            'delivery_data.*' => 'string',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $request->user()->id))
            ->where('status', OrderStatus::PAID->value)
            ->findOrFail($id);

        foreach ($order->items as $item) {
            if ($item->product->seller_id === $request->user()->id) {
                $item->update([
                    'delivery_data' => $validated['delivery_data'],
                    'status' => OrderStatus::DELIVERED->value,
                    'delivered_at' => now(),
                ]);
            }
        }

        $allDelivered = $order->items()->where('status', '!=', OrderStatus::DELIVERED->value)->doesntExist();

        if ($allDelivered) {
            $order->update(['status' => OrderStatus::DELIVERED->value]);
        }

        return $this->successResponse($order->fresh()->load(['buyer', 'items.product', 'payment']), 'Pesanan berhasil dikirim.');
    }

    public function process(Request $request, int $id): JsonResponse
    {
        $order = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $request->user()->id))
            ->where('status', OrderStatus::PAID->value)
            ->findOrFail($id);

        $order->update(['status' => OrderStatus::PROCESSING->value]);

        return $this->successResponse($order->fresh()->load(['buyer', 'items.product', 'payment']), 'Pesanan sedang diproses.');
    }
}