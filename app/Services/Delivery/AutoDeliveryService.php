<?php

namespace App\Services\Delivery;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Events\ProductDelivered;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoDeliveryService
{
    public function deliver(Order $order): array
    {
        try {
            return DB::transaction(function () use ($order) {
                $deliveredItems = [];
                $failedItems = [];

                foreach ($order->items as $item) {
                    $product = $item->product;

                    $deliveryResult = $this->deliverItem($item);

                    if ($deliveryResult['success']) {
                        $deliveredItems[] = [
                            'order_item_id' => $item->id,
                            'product_id' => $item->product_id,
                            'delivery_data' => $deliveryResult['delivery_data'],
                        ];

                        $item->update([
                            'delivery_status' => 'delivered',
                            'delivered_at' => now(),
                            'delivery_data' => $deliveryResult['delivery_data'],
                        ]);

                        $product->decrement('stock', $item->quantity);
                        $product->increment('sold_count', $item->quantity);
                    } else {
                        $failedItems[] = [
                            'order_item_id' => $item->id,
                            'error' => $deliveryResult['error'],
                        ];
                    }
                }

                $allDelivered = count($failedItems) === 0;

                if ($allDelivered) {
                    $order->update(['status' => OrderStatus::DELIVERED->value]);
                    event(new ProductDelivered($order));
                } elseif (count($deliveredItems) > 0) {
                    $order->update(['status' => OrderStatus::PROCESSING->value]);
                    Log::warning('Partial auto-delivery', [
                        'order_id' => $order->id,
                        'delivered' => count($deliveredItems),
                        'failed' => count($failedItems),
                    ]);
                }

                Log::info('Auto delivery processed', [
                    'order_id' => $order->id,
                    'all_delivered' => $allDelivered,
                ]);

                return [
                    'success' => $allDelivered,
                    'delivered_items' => $deliveredItems,
                    'failed_items' => $failedItems,
                    'order_status' => $order->fresh()->status->value,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Auto delivery failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'delivered_items' => [],
                'failed_items' => [],
                'error' => 'Auto delivery failed: ' . $e->getMessage(),
            ];
        }
    }

    private function deliverItem(OrderItem $item): array
    {
        $product = $item->product;

        return match ($product->delivery_type) {
            DeliveryType::INSTANT->value => $this->deliverInstantItem($item),
            DeliveryType::LOGIN->value => $this->deliverLoginItem($item),
            default => [
                'success' => false,
                'error' => "Unsupported delivery type for auto-delivery: {$product->delivery_type}",
            ],
        };
    }

    private function deliverInstantItem(OrderItem $item): array
    {
        $product = $item->product;

        if (empty($product->delivery_data)) {
            return [
                'success' => false,
                'error' => 'No delivery data configured for this product',
            ];
        }

        if ($product->stock < $item->quantity) {
            return [
                'success' => false,
                'error' => 'Insufficient stock',
            ];
        }

        $deliveryData = is_string($product->delivery_data)
            ? json_decode($product->delivery_data, true)
            : $product->delivery_data;

        $keyIndex = 0;
        $deliveredKeys = [];

        if (isset($deliveryData['keys']) && is_array($deliveryData['keys'])) {
            for ($i = 0; $i < $item->quantity; $i++) {
                if ($keyIndex < count($deliveryData['keys'])) {
                    $deliveredKeys[] = $deliveryData['keys'][$keyIndex];
                    $keyIndex++;
                }
            }

            if (count($deliveredKeys) < $item->quantity) {
                return [
                    'success' => false,
                    'error' => 'Not enough keys available',
                ];
            }

            $remainingKeys = array_slice($deliveryData['keys'], $keyIndex);
            $product->update([
                'delivery_data' => json_encode(array_merge($deliveryData, ['keys' => $remainingKeys])),
            ]);

            return [
                'success' => true,
                'delivery_data' => [
                    'type' => 'game_key',
                    'keys' => $deliveredKeys,
                    'instructions' => $deliveryData['instructions'] ?? null,
                ],
            ];
        }

        if (isset($deliveryData['code'])) {
            return [
                'success' => true,
                'delivery_data' => [
                    'type' => 'voucher',
                    'code' => $deliveryData['code'],
                    'instructions' => $deliveryData['instructions'] ?? null,
                    'redeem_url' => $deliveryData['redeem_url'] ?? null,
                ],
            ];
        }

        if (isset($deliveryData['topup_data'])) {
            return [
                'success' => true,
                'delivery_data' => [
                    'type' => 'topup',
                    'player_id' => $item->server_info ?? $deliveryData['player_id'] ?? null,
                    'topup_details' => $deliveryData['topup_data'],
                    'estimated_time' => $deliveryData['estimated_time'] ?? '1-5 minutes',
                ],
            ];
        }

        return [
            'success' => false,
            'error' => 'Unknown delivery data format',
        ];
    }

    private function deliverLoginItem(OrderItem $item): array
    {
        $product = $item->product;

        $deliveryData = is_string($product->delivery_data)
            ? json_decode($product->delivery_data, true)
            : $product->delivery_data;

        return [
            'success' => true,
            'delivery_data' => [
                'type' => 'account',
                'username' => $deliveryData['username'] ?? null,
                'password' => $deliveryData['password'] ?? null,
                'email' => $deliveryData['email'] ?? null,
                'email_password' => $deliveryData['email_password'] ?? null,
                'instructions' => $deliveryData['instructions'] ?? 'Please change the password immediately after login.',
                'warning' => 'For security, please change all credentials after first login.',
            ],
        ];
    }
}