<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    public function sendOrderConfirmation(User $buyer, array $orderData): bool
    {
        try {
            Mail::to($buyer->email)->queue(new \App\Mail\OrderConfirmationMail($orderData));

            Log::info('Order confirmation email sent', [
                'user_id' => $buyer->id,
                'order_id' => $orderData['order_id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'user_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendPaymentReceived(User $buyer, array $paymentData): bool
    {
        try {
            Mail::to($buyer->email)->queue(new \App\Mail\PaymentReceivedMail($paymentData));

            Log::info('Payment received email sent', [
                'user_id' => $buyer->id,
                'order_id' => $paymentData['order_id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send payment received email', [
                'user_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendDeliveryNotification(User $buyer, array $deliveryData): bool
    {
        try {
            Mail::to($buyer->email)->queue(new \App\Mail\DeliveryNotificationMail($deliveryData));

            Log::info('Delivery notification email sent', [
                'user_id' => $buyer->id,
                'order_id' => $deliveryData['order_id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send delivery notification email', [
                'user_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendNewOrderNotification(User $seller, array $orderData): bool
    {
        try {
            Mail::to($seller->email)->queue(new \App\Mail\NewOrderSellerMail($orderData));

            Log::info('New order seller notification email sent', [
                'seller_id' => $seller->id,
                'order_id' => $orderData['order_id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send new order seller notification email', [
                'seller_id' => $seller->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendDisputeNotification(User $user, array $disputeData): bool
    {
        try {
            Mail::to($user->email)->queue(new \App\Mail\DisputeNotificationMail($disputeData));

            Log::info('Dispute notification email sent', ['user_id' => $user->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send dispute notification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendRefundNotification(User $user, array $refundData): bool
    {
        try {
            Mail::to($user->email)->queue(new \App\Mail\RefundNotificationMail($refundData));

            Log::info('Refund notification email sent', ['user_id' => $user->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send refund notification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}