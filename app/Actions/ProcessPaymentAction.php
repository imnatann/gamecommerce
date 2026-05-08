<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\PaymentReceived;
use App\Models\Order;
use App\Services\Payment\PaymentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAction
{
    public function __construct(
        private PaymentManager $paymentManager,
    ) {}

    public function execute(Order $order, string $paymentMethod, string $gateway = 'midtrans'): array
    {
        try {
            if (! $order->status->canTransitionTo(OrderStatus::PAID)) {
                throw new \RuntimeException("Order cannot be paid in current status: {$order->status->value}");
            }

            if ($order->payment && $order->payment->status === PaymentStatus::SUCCESS) {
                throw new \RuntimeException('Order has already been paid');
            }

            $paymentService = $this->paymentManager->gateway($gateway);

            $result = $paymentService->createTransaction([
                'order' => $order->load('items.product', 'buyer'),
                'buyer' => $order->buyer,
                'payment_method' => $paymentMethod,
            ]);

            if (! $result['success']) {
                Log::error('Payment transaction creation failed', [
                    'order_id' => $order->id,
                    'gateway' => $gateway,
                    'error' => $result['message'] ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Payment initialization failed',
                ];
            }

            Log::info('Payment transaction created', [
                'order_id' => $order->id,
                'gateway' => $gateway,
            ]);

            return [
                'success' => true,
                'gateway' => $gateway,
                'payment_id' => $result['payment_id'] ?? null,
                'snap_token' => $result['snap_token'] ?? null,
                'invoice_url' => $result['invoice_url'] ?? null,
                'client_key' => $result['client_key'] ?? null,
                'external_id' => $result['external_id'] ?? null,
                'order_id' => $order->id,
                'total' => $order->net_amount,
            ];
        } catch (\Exception $e) {
            Log::error('ProcessPaymentAction failed', [
                'order_id' => $order->id,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handlePaymentNotification(array $payload, string $gateway = 'midtrans'): array
    {
        try {
            $paymentService = $this->paymentManager->gateway($gateway);

            $result = $paymentService->handleNotification($payload);

            if ($result['success'] && ($result['payment_status'] ?? null) === PaymentStatus::SUCCESS->value) {
                $order = Order::with('payment')->find($result['order_id'] ?? null);

                if ($order && $order->payment) {
                    event(new PaymentReceived($order, $order->payment));
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment notification handling failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to handle payment notification: '.$e->getMessage(),
            ];
        }
    }

    public function cancelPayment(Order $order): array
    {
        try {
            if (! $order->payment) {
                throw new \RuntimeException('No payment record found for this order');
            }

            if ($order->payment->status !== PaymentStatus::PENDING) {
                throw new \RuntimeException('Payment cannot be cancelled in current status');
            }

            $gateway = $order->payment->gateway;
            $gatewayResponse = $order->payment->gateway_response ?? [];
            $transactionId = $gatewayResponse['transaction_id']
                ?? $gatewayResponse['invoice_id']
                ?? $gatewayResponse['external_id']
                ?? (string) $order->id;

            $paymentService = $this->paymentManager->gateway($gateway);

            $result = $paymentService->cancelTransaction($transactionId);

            if ($result['success']) {
                DB::transaction(function () use ($order) {
                    $order->payment->update(['status' => PaymentStatus::EXPIRED->value]);
                    $order->update(['status' => OrderStatus::CANCELLED->value]);

                    $this->restoreStock($order);
                });
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment cancellation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock !== null) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}
