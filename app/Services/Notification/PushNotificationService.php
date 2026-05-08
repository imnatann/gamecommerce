<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    public function send(User $user, string $title, string $body, array $data = []): bool
    {
        try {
            $notification = $user->notifications()->create([
                'type' => $data['type'] ?? 'general',
                'data' => array_merge($data, [
                    'title' => $title,
                    'body' => $body,
                ]),
            ]);

            $pushTokens = $user->pushTokens()->where('is_active', true)->pluck('token')->toArray();

            if (empty($pushTokens)) {
                Log::info('No active push tokens for user', ['user_id' => $user->id]);

                return true;
            }

            $this->sendToFirebase($pushTokens, $title, $body, $data);

            Log::info('Push notification sent', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendOrderCreated(User $buyer, array $orderData): bool
    {
        return $this->send(
            $buyer,
            'Order Created',
            "Your order #{$orderData['order_number']} has been created successfully.",
            array_merge($orderData, ['type' => 'order_created', 'order_id' => $orderData['order_id'] ?? null])
        );
    }

    public function sendPaymentConfirmed(User $buyer, array $paymentData): bool
    {
        return $this->send(
            $buyer,
            'Payment Confirmed',
            "Payment for order #{$paymentData['order_number']} has been confirmed.",
            array_merge($paymentData, ['type' => 'payment_confirmed'])
        );
    }

    public function sendOrderDelivered(User $buyer, array $deliveryData): bool
    {
        return $this->send(
            $buyer,
            'Order Delivered',
            "Your order #{$deliveryData['order_number']} has been delivered!",
            array_merge($deliveryData, ['type' => 'order_delivered'])
        );
    }

    public function sendNewOrder(User $seller, array $orderData): bool
    {
        return $this->send(
            $seller,
            'New Order',
            "You have a new order #{$orderData['order_number']}! Please process it.",
            array_merge($orderData, ['type' => 'new_order_seller'])
        );
    }

    public function sendDisputeCreated(User $user, array $disputeData): bool
    {
        return $this->send(
            $user,
            'Dispute Created',
            "A dispute has been created for order #{$disputeData['order_number']}.",
            array_merge($disputeData, ['type' => 'dispute_created'])
        );
    }

    private function sendToFirebase(array $tokens, string $title, string $body, array $data = []): void
    {
        // This would use the Firebase Cloud Messaging API
        // For now, we store it in the notifications table and dispatch a job
        // The actual FCM integration would be in a queued job

        Log::info('Firebase push notification dispatched', [
            'token_count' => count($tokens),
            'title' => $title,
        ]);

        // \App\Jobs\SendFirebaseNotificationJob::dispatch($tokens, $title, $body, $data);
    }
}