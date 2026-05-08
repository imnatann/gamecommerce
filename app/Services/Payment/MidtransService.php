<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService extends PaymentService
{
    protected string $gatewayName = 'midtrans';

    private ?string $serverKey;

    private ?string $clientKey;

    public function __construct()
    {
        parent::__construct();
        $this->serverKey = config('gamecommerce.payment.midtrans.server_key');
        $this->clientKey = config('gamecommerce.payment.midtrans.client_key');
    }

    public function createTransaction(array $params): array
    {
        try {
            $order = $params['order'];
            $buyer = $params['buyer'];
            $paymentMethod = $params['payment_method'] ?? 'qris';
            $externalId = $this->gatewayReference($order);
            $amount = $this->payableAmount($order);

            Config::$serverKey = $this->serverKey;
            Config::$isProduction = $this->isProduction;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $transactionDetails = [
                'order_id' => $externalId,
                'gross_amount' => $amount,
            ];

            $itemDetails = $order->items->map(fn ($item) => [
                'id' => (string) $item->product_id,
                'price' => (int) round($item->price),
                'quantity' => $item->quantity,
                'name' => $item->product?->name ?? "Product #{$item->product_id}",
            ])->toArray();

            if ($order->discount_amount > 0) {
                $itemDetails[] = [
                    'id' => 'voucher-discount',
                    'price' => -1 * (int) $order->discount_amount,
                    'quantity' => 1,
                    'name' => 'Voucher Discount',
                ];
            }

            $customerDetails = [
                'first_name' => $buyer->name,
                'email' => $buyer->email,
                'phone' => $buyer->phone ?? '',
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'callbacks' => [
                    'finish' => $this->redirectUrl($order),
                    'unfinish' => $this->redirectUrl($order),
                    'error' => $this->redirectUrl($order),
                ],
            ];

            $snapToken = Snap::getSnapToken($payload);

            Log::info('Midtrans snap token created', [
                'order_id' => $order->id,
                'external_id' => $externalId,
            ]);

            $payment = DB::transaction(function () use ($order, $paymentMethod, $externalId, $snapToken, $payload) {
                $payment = $this->upsertPendingPayment($order, $paymentMethod, [
                    'external_id' => $externalId,
                    'snap_token' => $snapToken,
                    'payload' => $payload,
                ]);

                $order->update(['status' => OrderStatus::PENDING->value]);

                return $payment;
            });

            return $this->successResponse([
                'payment_id' => $payment->id,
                'snap_token' => $snapToken,
                'client_key' => $this->clientKey,
                'is_production' => $this->isProduction,
                'order_id' => $order->id,
                'external_id' => $externalId,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans createTransaction failed', [
                'order_id' => $params['order']->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse('Failed to create payment transaction: '.$e->getMessage(), 500);
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            Config::$serverKey = $this->serverKey;
            Config::$isProduction = $this->isProduction;

            $status = Transaction::status($transactionId);

            Log::info('Midtrans transaction status retrieved', [
                'transaction_id' => $transactionId,
            ]);

            return $this->successResponse([
                'transaction_status' => $status->transaction_status ?? null,
                'payment_type' => $status->payment_type ?? null,
                'fraud_status' => $status->fraud_status ?? null,
                'raw_response' => (array) $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getTransactionStatus failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to get transaction status: '.$e->getMessage(), 500);
        }
    }

    public function handleNotification(array $payload): array
    {
        try {
            Config::$serverKey = $this->serverKey;
            Config::$isProduction = $this->isProduction;

            $notification = (object) $payload;

            if (empty($notification->order_id)) {
                $notification = new Notification;
            }

            $externalId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type ?? null;
            $transactionId = $notification->transaction_id ?? null;
            $orderId = $this->orderIdFromGatewayReference($externalId);

            $order = $orderId ? Order::find($orderId) : null;

            if (! $order) {
                Log::warning('Midtrans notification: order not found', ['external_id' => $externalId]);

                return $this->errorResponse('Order not found', 404);
            }

            $paymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);
            $orderStatus = $this->mapOrderStatus($paymentStatus);

            $payment = DB::transaction(function () use ($order, $paymentStatus, $orderStatus, $transactionId, $paymentType, $payload, $externalId) {
                $payment = $this->updatePaymentFromNotification($order, $paymentStatus, $paymentType, [
                    'external_id' => $externalId,
                    'transaction_id' => $transactionId,
                    'notification' => $payload,
                ]);

                if ($orderStatus && $order->status->canTransitionTo($orderStatus)) {
                    $order->update(['status' => $orderStatus->value]);
                }

                return $payment;
            });

            Log::info('Midtrans notification handled', [
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
            Log::error('Midtrans handleNotification failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse('Failed to handle notification: '.$e->getMessage(), 500);
        }
    }

    public function cancelTransaction(string $transactionId): array
    {
        try {
            Config::$serverKey = $this->serverKey;
            Config::$isProduction = $this->isProduction;

            $result = Transaction::cancel($transactionId);

            Log::info('Midtrans transaction cancelled', ['transaction_id' => $transactionId]);

            return $this->successResponse(['transaction_id' => $transactionId]);
        } catch (\Exception $e) {
            Log::error('Midtrans cancelTransaction failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to cancel transaction: '.$e->getMessage(), 500);
        }
    }

    public function refundTransaction(string $transactionId, ?float $amount = null): array
    {
        try {
            Config::$serverKey = $this->serverKey;
            Config::$isProduction = $this->isProduction;

            $refundParams = ['refund_key' => 'refund-'.$transactionId.'-'.time()];

            if ($amount) {
                $refundParams['amount'] = (int) round($amount);
            }

            $result = Transaction::refund($transactionId, $refundParams);

            Log::info('Midtrans refund processed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'refund_amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans refundTransaction failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to refund transaction: '.$e->getMessage(), 500);
        }
    }

    private function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => $fraudStatus === 'challenge'
                ? PaymentStatus::PENDING->value
                : PaymentStatus::SUCCESS->value,
            'pending' => PaymentStatus::PENDING->value,
            'deny', 'cancel', 'expire', 'failure' => PaymentStatus::FAILED->value,
            'refund' => PaymentStatus::REFUNDED->value,
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
