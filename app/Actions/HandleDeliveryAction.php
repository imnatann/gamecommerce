<?php

namespace App\Actions;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Delivery\AutoDeliveryService;
use App\Services\Delivery\ManualDeliveryService;
use Illuminate\Support\Facades\Log;

class HandleDeliveryAction
{
    private AutoDeliveryService $autoDeliveryService;

    private ManualDeliveryService $manualDeliveryService;

    public function __construct(AutoDeliveryService $autoDeliveryService, ManualDeliveryService $manualDeliveryService)
    {
        $this->autoDeliveryService = $autoDeliveryService;
        $this->manualDeliveryService = $manualDeliveryService;
    }

    public function execute(Order $order): array
    {
        try {
            if (!$order->status->canTransitionTo(OrderStatus::PROCESSING) && $order->status->value !== OrderStatus::PAID->value) {
                throw new \RuntimeException("Order cannot be processed for delivery in status: {$order->status->value}");
            }

            $autoDeliveryItems = $order->items->filter(fn ($item) => in_array(
                $item->product->delivery_type,
                [DeliveryType::INSTANT->value, DeliveryType::LOGIN->value]
            ));

            $manualDeliveryItems = $order->items->filter(fn ($item) =>
                $item->product->delivery_type === DeliveryType::MANUAL->value
            );

            $results = [
                'auto_delivery' => null,
                'manual_delivery' => null,
            ];

            if ($autoDeliveryItems->isNotEmpty()) {
                $results['auto_delivery'] = $this->autoDeliveryService->deliver($order);
            }

            if ($manualDeliveryItems->isNotEmpty()) {
                $results['manual_delivery'] = $this->manualDeliveryService->prepareDelivery($order);
            }

            if ($autoDeliveryItems->isNotEmpty() && $manualDeliveryItems->isEmpty()) {
                $autoResult = $results['auto_delivery'];
                if (($autoResult['success'] ?? false) && $order->fresh()->status->value === OrderStatus::DELIVERED->value) {
                    Log::info('Order fully auto-delivered', ['order_id' => $order->id]);
                }
            } elseif ($manualDeliveryItems->isNotEmpty()) {
                Log::info('Order requires manual delivery', ['order_id' => $order->id]);
            }

            return [
                'success' => true,
                'order_status' => $order->fresh()->status->value,
                'results' => $results,
            ];
        } catch (\Exception $e) {
            Log::error('HandleDeliveryAction failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'results' => null,
            ];
        }
    }

    public function confirmManualDelivery(int $orderItemId, array $deliveryData): array
    {
        try {
            $orderItem = \App\Models\OrderItem::findOrFail($orderItemId);

            return $this->manualDeliveryService->confirmDelivery($orderItem, $deliveryData);
        } catch (\Exception $e) {
            Log::error('Manual delivery confirmation failed', [
                'order_item_id' => $orderItemId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function rejectManualDelivery(int $orderItemId, string $reason): array
    {
        try {
            $orderItem = \App\Models\OrderItem::findOrFail($orderItemId);

            return $this->manualDeliveryService->rejectDelivery($orderItem, $reason);
        } catch (\Exception $e) {
            Log::error('Manual delivery rejection failed', [
                'order_item_id' => $orderItemId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function bulkConfirmDelivery(Order $order, array $itemsData): array
    {
        try {
            return $this->manualDeliveryService->bulkConfirmDelivery($order, $itemsData);
        } catch (\Exception $e) {
            Log::error('Bulk delivery confirmation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}