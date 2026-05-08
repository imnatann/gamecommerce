<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;

interface PaymentServiceInterface
{
    public function createTransaction(array $params): array;

    public function getTransactionStatus(string $transactionId): array;

    public function handleNotification(array $payload): array;

    public function cancelTransaction(string $transactionId): array;

    public function refundTransaction(string $transactionId, ?float $amount = null): array;
}

abstract class PaymentService implements PaymentServiceInterface
{
    protected string $gatewayName;

    protected bool $isProduction;

    public function __construct()
    {
        $this->isProduction = (bool) config("gamecommerce.payment.{$this->gatewayName}.is_production", false);
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    protected function successResponse(array $data = []): array
    {
        return array_merge([
            'success' => true,
            'gateway' => $this->gatewayName,
        ], $data);
    }

    protected function errorResponse(string $message, int $code = 400): array
    {
        return [
            'success' => false,
            'gateway' => $this->gatewayName,
            'message' => $message,
            'code' => $code,
        ];
    }

    protected function payableAmount(Order $order): int
    {
        return max(0, (int) $order->total_amount - (int) $order->discount_amount);
    }

    protected function gatewayReference(Order $order): string
    {
        return 'GC-'.$order->id;
    }

    protected function orderIdFromGatewayReference(?string $reference): ?int
    {
        if (! $reference || ! preg_match('/^GC-(\d+)/', $reference, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    protected function redirectUrl(Order $order): string
    {
        return route('order.status', ['orderId' => $order->id]);
    }

    protected function upsertPendingPayment(Order $order, string $method, array $gatewayResponse = []): Payment
    {
        $payment = $order->payment()->first();
        $existingResponse = $payment?->gateway_response ?? [];

        return $order->payment()->updateOrCreate(
            [],
            [
                'method' => $method,
                'payment_method' => $method,
                'gateway' => $this->gatewayName,
                'gateway_transaction_id' => $gatewayResponse['transaction_id']
                    ?? $gatewayResponse['invoice_id']
                    ?? $gatewayResponse['external_id']
                    ?? null,
                'snap_token' => $gatewayResponse['snap_token'] ?? null,
                'currency' => 'IDR',
                'amount' => $this->payableAmount($order),
                'status' => PaymentStatus::PENDING->value,
                'gateway_response' => array_replace_recursive($existingResponse, $gatewayResponse),
                'payload' => $gatewayResponse['payload'] ?? null,
                'paid_at' => null,
            ],
        );
    }

    protected function updatePaymentFromNotification(Order $order, string $status, ?string $method, array $gatewayResponse): ?Payment
    {
        $payment = $order->payment()->first();

        if (! $payment) {
            return null;
        }

        $existingResponse = $payment->gateway_response ?? [];

        $payment->update([
            'method' => $method ?: $payment->method,
            'payment_method' => $method ?: $payment->method,
            'gateway_transaction_id' => $gatewayResponse['transaction_id']
                ?? $gatewayResponse['invoice_id']
                ?? $payment->gateway_transaction_id,
            'status' => $status,
            'gateway_response' => array_replace_recursive($existingResponse, $gatewayResponse),
            'raw_notification' => $gatewayResponse['notification'] ?? null,
            'paid_at' => $status === PaymentStatus::SUCCESS->value ? now() : $payment->paid_at,
        ]);

        return $payment->fresh();
    }
}
