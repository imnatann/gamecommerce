<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditService extends PaymentService
{
    protected string $gatewayName = 'xendit';

    private ?string $apiKey;

    private ?string $webhookSecret;

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = config('gamecommerce.payment.xendit.secret_key');
        $this->webhookSecret = config('gamecommerce.payment.xendit.webhook_secret');
    }

    public function createTransaction(array $params): array
    {
        try {
            $order = $params['order'];
            $buyer = $params['buyer'];
            $paymentMethod = $params['payment_method'] ?? 'all';
            $externalId = $this->gatewayReference($order);
            $amount = $this->payableAmount($order);

            $payload = [
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => "Payment for order #{$order->id}",
                'customer' => [
                    'given_names' => $buyer->name,
                    'email' => $buyer->email,
                    'mobile_number' => $buyer->phone ?? '',
                ],
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->product?->name ?? "Product #{$item->product_id}",
                    'quantity' => $item->quantity,
                    'price' => (int) round($item->price),
                    'category' => 'Digital Goods',
                ])->toArray(),
                'success_redirect_url' => $this->redirectUrl($order),
                'failure_redirect_url' => $this->redirectUrl($order),
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ];

            $response = $this->callXenditApi('POST', '/v1/invoices', $payload);

            $payment = DB::transaction(function () use ($order, $paymentMethod, $externalId, $response, $payload) {
                $payment = $this->upsertPendingPayment($order, $paymentMethod, [
                    'external_id' => $externalId,
                    'invoice_id' => $response['id'] ?? null,
                    'invoice_url' => $response['invoice_url'] ?? null,
                    'payload' => $payload,
                ]);

                $order->update(['status' => OrderStatus::PENDING->value]);

                return $payment;
            });

            Log::info('Xendit invoice created', [
                'order_id' => $order->id,
                'external_id' => $externalId,
            ]);

            return $this->successResponse([
                'payment_id' => $payment->id,
                'invoice_url' => $response['invoice_url'] ?? null,
                'expiry_date' => $response['expiry_date'] ?? null,
                'order_id' => $order->id,
                'external_id' => $externalId,
            ]);
        } catch (\Exception $e) {
            Log::error('Xendit createTransaction failed', [
                'order_id' => $params['order']->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse('Failed to create Xendit invoice: '.$e->getMessage(), 500);
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = $this->callXenditApi('GET', "/v1/invoices/{$transactionId}");

            return $this->successResponse([
                'status' => $response['status'] ?? null,
                'payment_method' => $response['payment_method'] ?? null,
                'paid_at' => $response['paid_at'] ?? null,
                'raw_response' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Xendit getTransactionStatus failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to get Xendit transaction status: '.$e->getMessage(), 500);
        }
    }

    public function handleNotification(array $payload): array
    {
        try {
            $externalId = $payload['external_id'] ?? null;
            $status = $payload['status'] ?? null;

            if (! $externalId || ! $status) {
                return $this->errorResponse('Invalid webhook payload', 400);
            }

            $orderId = $this->orderIdFromGatewayReference($externalId);
            $order = $orderId ? Order::find($orderId) : null;

            if (! $order) {
                Log::warning('Xendit notification: order not found', ['external_id' => $externalId]);

                return $this->errorResponse('Order not found', 404);
            }

            $paymentStatus = $this->mapPaymentStatus($status);
            $orderStatus = $this->mapOrderStatus($paymentStatus);

            $payment = DB::transaction(function () use ($order, $paymentStatus, $orderStatus, $payload, $externalId) {
                $payment = $this->updatePaymentFromNotification($order, $paymentStatus, $payload['payment_method'] ?? null, [
                    'external_id' => $externalId,
                    'invoice_id' => $payload['id'] ?? null,
                    'notification' => $payload,
                ]);

                if ($orderStatus && $order->status->canTransitionTo($orderStatus)) {
                    $order->update(['status' => $orderStatus->value]);
                }

                return $payment;
            });

            Log::info('Xendit notification handled', [
                'order_id' => $order->id,
                'payment_status' => $paymentStatus,
            ]);

            return $this->successResponse([
                'order_id' => $order->id,
                'payment_id' => $payment?->id,
                'external_id' => $externalId,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus?->value,
            ]);
        } catch (\Exception $e) {
            Log::error('Xendit handleNotification failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to handle Xendit notification: '.$e->getMessage(), 500);
        }
    }

    public function cancelTransaction(string $transactionId): array
    {
        try {
            $response = $this->callXenditApi('DELETE', "/v1/invoices/{$transactionId}");

            Log::info('Xendit invoice cancelled', ['transaction_id' => $transactionId]);

            return $this->successResponse(['transaction_id' => $transactionId]);
        } catch (\Exception $e) {
            Log::error('Xendit cancelTransaction failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to cancel Xendit invoice: '.$e->getMessage(), 500);
        }
    }

    public function refundTransaction(string $transactionId, ?float $amount = null): array
    {
        try {
            $payload = [];
            if ($amount) {
                $payload['amount'] = $amount;
            }

            $response = $this->callXenditApi('POST', '/v1/refunds', array_merge($payload, [
                'invoice_id' => $transactionId,
            ]));

            Log::info('Xendit refund processed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'refund_amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Xendit refundTransaction failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to refund Xendit transaction: '.$e->getMessage(), 500);
        }
    }

    private function callXenditApi(string $method, string $endpoint, array $data = []): array
    {
        // Placeholder — real implementation uses Guzzle HTTP client
        // In production, use \Xendit\Xendit::setApiKey($this->apiKey) and respective SDK methods
        throw new \RuntimeException('Xendit API integration requires SDK configuration. Endpoint: '.$endpoint);
    }

    private function mapPaymentStatus(string $status): string
    {
        return match ($status) {
            'PAID', 'SETTLED' => PaymentStatus::SUCCESS->value,
            'PENDING' => PaymentStatus::PENDING->value,
            'EXPIRED' => PaymentStatus::EXPIRED->value,
            'VOIDED', 'CANCELLED' => PaymentStatus::FAILED->value,
            'REFUNDED' => PaymentStatus::REFUNDED->value,
            default => PaymentStatus::PENDING->value,
        };
    }

    private function mapOrderStatus(string $paymentStatus): ?OrderStatus
    {
        return match ($paymentStatus) {
            PaymentStatus::SUCCESS->value => OrderStatus::PAID,
            PaymentStatus::FAILED->value => OrderStatus::CANCELLED,
            PaymentStatus::EXPIRED->value => OrderStatus::CANCELLED,
            PaymentStatus::REFUNDED->value => OrderStatus::REFUNDED,
            default => null,
        };
    }
}
