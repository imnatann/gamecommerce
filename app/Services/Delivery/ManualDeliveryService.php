<?php

namespace App\Services\Delivery;

use App\Enums\OrderStatus;
use App\Events\ProductDelivered;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManualDeliveryService
{
    public function prepareDelivery(Order $order): array
    {
        try {
            $itemsRequiringDelivery = $order->items->filter(fn ($item) => $item->product->delivery_type->value === 'manual');

            if ($itemsRequiringDelivery->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'No manual delivery items found',
                    'items' => [],
                ];
            }

            $order->update(['status' => OrderStatus::PROCESSING->value]);

            Log::info('Manual delivery prepared', [
                'order_id' => $order->id,
                'items_count' => $itemsRequiringDelivery->count(),
            ]);

            return [
                'success' => true,
                'message' => 'Order marked for manual delivery',
                'items' => $itemsRequiringDelivery->map(fn ($item) => [
                    'order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'delivery_type' => $item->product->delivery_type->value,
                    'estimated_delivery' => $item->product->delivery_time ?? '5-30 minutes',
                ])->values()->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Manual delivery preparation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to prepare manual delivery: ' . $e->getMessage(),
                'items' => [],
            ];
        }
    }

    public function confirmDelivery(OrderItem $item, array $deliveryData): array
    {
        try {
            return DB::transaction(function () use ($item, $deliveryData) {
                $item->update([
                    'delivery_status' => 'delivered',
                    'delivered_at' => now(),
                    'delivery_data' => $deliveryData,
                ]);

                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sold_count', $item->quantity);

                $order = $item->order;
                $allDelivered = $order->items()->whereNull('delivered_at')->doesntExist();

                if ($allDelivered) {
                    $order->update(['status' => OrderStatus::DELIVERED->value]);
                    event(new ProductDelivered($order));
                }

                Log::info('Manual delivery confirmed', [
                    'order_item_id' => $item->id,
                    'order_id' => $item->order_id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Item delivered successfully',
                    'order_status' => $order->fresh()->status->value,
                    'all_delivered' => $allDelivered,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Manual delivery confirmation failed', [
                'order_item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to confirm delivery: ' . $e->getMessage(),
            ];
        }
    }

    public function bulkConfirmDelivery(Order $order, array $itemsData): array
    {
        try {
            return DB::transaction(function () use ($order, $itemsData) {
                $confirmedItems = [];
                $failedItems = [];

                foreach ($itemsData as $itemData) {
                    $item = $order->items()->where('id', $itemData['order_item_id'])->first();

                    if (!$item) {
                        $failedItems[] = [
                            'order_item_id' => $itemData['order_item_id'],
                            'error' => 'Item not found in order',
                        ];
                        continue;
                    }

                    if ($item->delivery_status === 'delivered') {
                        $failedItems[] = [
                            'order_item_id' => $item->id,
                            'error' => 'Item already delivered',
                        ];
                        continue;
                    }

                    $result = $this->confirmDelivery($item, $itemData['delivery_data'] ?? []);

                    if ($result['success']) {
                        $confirmedItems[] = $result;
                    } else {
                        $failedItems[] = [
                            'order_item_id' => $item->id,
                            'error' => $result['message'],
                        ];
                    }
                }

                Log::info('Bulk manual delivery confirmed', [
                    'order_id' => $order->id,
                    'confirmed' => count($confirmedItems),
                    'failed' => count($failedItems),
                ]);

                return [
                    'success' => count($failedItems) === 0,
                    'confirmed_items' => $confirmedItems,
                    'failed_items' => $failedItems,
                    'order_status' => $order->fresh()->status->value,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Bulk manual delivery failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'confirmed_items' => [],
                'failed_items' => [],
                'error' => 'Bulk delivery failed: ' . $e->getMessage(),
            ];
        }
    }

    public function rejectDelivery(OrderItem $item, string $reason): array
    {
        try {
            return DB::transaction(function () use ($item, $reason) {
                $item->update([
                    'delivery_status' => 'rejected',
                    'delivery_data' => ['rejection_reason' => $reason],
                ]);

                $order = $item->order;
                $anyPending = $order->items()->where('delivery_status', '!=', 'delivered')->where('delivery_status', '!=', 'rejected')->exists();

                if (!$anyPending) {
                    $order->update(['status' => OrderStatus::CANCELLED->value]);
                }

                Log::info('Delivery rejected', [
                    'order_item_id' => $item->id,
                    'reason' => $reason,
                ]);

                return [
                    'success' => true,
                    'message' => 'Delivery rejected',
                    'order_status' => $order->fresh()->status->value,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Delivery rejection failed', [
                'order_item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reject delivery: ' . $e->getMessage(),
            ];
        }
    }
}