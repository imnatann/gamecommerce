<?php

namespace App\Actions;

use App\Enums\DisputeStatus;
use App\Enums\OrderStatus;
use App\Events\DisputeCreated;
use App\Models\Dispute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDisputeAction
{
    public function execute(int $userId, int $orderId, array $validatedData): Dispute
    {
        return DB::transaction(function () use ($userId, $orderId, $validatedData) {
            $order = \App\Models\Order::where('id', $orderId)
                ->where('buyer_id', $userId)
                ->first();

            if (!$order) {
                throw new \RuntimeException('Order not found or does not belong to you');
            }

            if (!in_array($order->status->value, [
                OrderStatus::PAID->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::DELIVERED->value,
                OrderStatus::COMPLETED->value,
            ])) {
                throw new \RuntimeException('You can only dispute orders that have been paid, delivered, or completed');
            }

            $existingDispute = Dispute::where('order_id', $orderId)
                ->where('buyer_id', $userId)
                ->whereIn('status', [DisputeStatus::OPEN->value, DisputeStatus::UNDER_REVIEW->value])
                ->first();

            if ($existingDispute) {
                throw new \RuntimeException('You already have an active dispute for this order');
            }

            $orderItem = $order->items()->where('id', $validatedData['order_item_id'] ?? null)->first();

            if (!$orderItem) {
                throw new \RuntimeException('Order item not found in this order');
            }

            if (!$order->status->canTransitionTo(OrderStatus::DISPUTED)) {
                throw new \RuntimeException('Cannot dispute order in current status');
            }

            $dispute = Dispute::create([
                'order_id' => $orderId,
                'order_item_id' => $validatedData['order_item_id'],
                'buyer_id' => $userId,
                'seller_id' => $orderItem->product->seller_id,
                'reason' => $validatedData['reason'],
                'description' => $validatedData['description'],
                'evidence' => $validatedData['evidence'] ?? [],
                'status' => DisputeStatus::OPEN->value,
                'resolution_deadline' => now()->addDays(config('gamecommerce.dispute.resolution_days', 7)),
            ]);

            if ($validatedData['message'] ?? null) {
                $dispute->messages()->create([
                    'user_id' => $userId,
                    'message' => $validatedData['message'],
                    'is_admin' => false,
                ]);
            }

            $order->update(['status' => OrderStatus::DISPUTED->value]);

            event(new DisputeCreated($dispute));

            Log::info('Dispute created', [
                'dispute_id' => $dispute->id,
                'order_id' => $orderId,
                'buyer_id' => $userId,
                'seller_id' => $orderItem->product->seller_id,
                'reason' => $validatedData['reason'],
            ]);

            return $dispute->load(['order', 'messages']);
        });
    }
}