<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    private string $apiKey;

    private string $apiUrl;

    private string $senderPhone;

    public function __construct()
    {
        $this->apiKey = config('gamecommerce.notifications.whatsapp.api_key', '');
        $this->apiUrl = config('gamecommerce.notifications.whatsapp.api_url', '');
        $this->senderPhone = config('gamecommerce.notifications.whatsapp.sender_phone', '');
    }

    public function sendOrderConfirmation(User $buyer, array $orderData): array
    {
        $phone = $buyer->phone;

        if (!$phone) {
            Log::info('WhatsApp: no phone number for buyer', ['user_id' => $buyer->id]);

            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = $this->buildOrderConfirmationMessage($orderData);

        return $this->sendMessage($phone, $message);
    }

    public function sendPaymentConfirmed(User $buyer, array $paymentData): array
    {
        $phone = $buyer->phone;

        if (!$phone) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = $this->buildPaymentConfirmedMessage($paymentData);

        return $this->sendMessage($phone, $message);
    }

    public function sendDeliveryNotification(User $buyer, array $deliveryData): array
    {
        $phone = $buyer->phone;

        if (!$phone) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = $this->buildDeliveryNotificationMessage($deliveryData);

        return $this->sendMessage($phone, $message);
    }

    public function sendNewOrderSeller(User $seller, array $orderData): array
    {
        $phone = $seller->phone;

        if (!$phone) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = $this->buildNewOrderSellerMessage($orderData);

        return $this->sendMessage($phone, $message);
    }

    public function sendDisputeNotification(User $user, array $disputeData): array
    {
        $phone = $user->phone;

        if (!$phone) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = $this->buildDisputeNotificationMessage($disputeData);

        return $this->sendMessage($phone, $message);
    }

    public function sendMessage(string $phone, string $message, array $media = []): array
    {
        try {
            if (empty($this->apiKey) || empty($this->apiUrl)) {
                Log::info('WhatsApp: API not configured, skipping notification', [
                    'phone' => $phone,
                ]);

                return ['success' => true, 'message' => 'WhatsApp API not configured - notification skipped'];
            }

            // Placeholder for actual WhatsApp API call
            // In production, this would use Fonnte, Wablas, or similar Indonesian WhatsApp API providers
            // $response = Http::withHeaders(['Authorization' => $this->apiKey])
            //     ->post($this->apiUrl, [
            //         'phone' => $phone,
            //         'message' => $message,
            //         'media' => $media,
            //     ]);

            Log::info('WhatsApp notification sent (placeholder)', [
                'phone' => $phone,
                'message_length' => strlen($message),
            ]);

            return [
                'success' => true,
                'message' => 'WhatsApp notification queued',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send WhatsApp notification: ' . $e->getMessage(),
            ];
        }
    }

    private function buildOrderConfirmationMessage(array $data): string
    {
        return "🎮 *GameCommerce - Order Confirmed*\n\n"
            . "Order #{$data['order_number']}\n"
            . "Total: Rp " . number_format($data['total'] ?? 0, 0, ',', '.') . "\n"
            . "Status: Pending Payment\n\n"
            . "Please complete your payment to proceed.\n"
            . config('app.url') . "/orders/{$data['order_number']}";
    }

    private function buildPaymentConfirmedMessage(array $data): string
    {
        return "✅ *GameCommerce - Payment Confirmed*\n\n"
            . "Order #{$data['order_number']}\n"
            . "Amount: Rp " . number_format($data['amount'] ?? 0, 0, ',', '.') . "\n"
            . "Payment Method: {$data['payment_method']}\n\n"
            . "We're processing your order now.";
    }

    private function buildDeliveryNotificationMessage(array $data): string
    {
        return "📦 *GameCommerce - Order Delivered*\n\n"
            . "Order #{$data['order_number']}\n"
            . "Your items have been delivered!\n\n"
            . "Please check and confirm receipt.";
    }

    private function buildNewOrderSellerMessage(array $data): string
    {
        return "🛒 *GameCommerce - New Order*\n\n"
            . "You have a new order #{$data['order_number']}\n"
            . "Buyer: {$data['buyer_name']}\n"
            . "Total: Rp " . number_format($data['total'] ?? 0, 0, ',', '.') . "\n\n"
            . "Please process it promptly.";
    }

    private function buildDisputeNotificationMessage(array $data): string
    {
        return "⚠️ *GameCommerce - Dispute Opened*\n\n"
            . "Order #{$data['order_number']}\n"
            . "Reason: {$data['reason']}\n\n"
            . "Please check the dispute details.";
    }
}