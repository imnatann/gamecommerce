<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Actions\CreateOrderAction;
use App\Actions\ProcessPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Services\Payment\PaymentManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiBaseController
{
    public function __construct(
        private CreateOrderAction $createOrderAction,
        private ProcessPaymentAction $processPaymentAction,
        private PaymentManager $paymentManager,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 15);

        $orders = Order::where('buyer_id', $request->user()->id)
            ->with(['items.product.gameProduct.game', 'items.product.seller', 'payment'])
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($orders);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user'] = $request->user();
        $validated['ip_address'] = $request->ip();
        $validated['payment_gateway'] ??= $this->paymentManager->defaultGateway();

        try {
            $order = $this->createOrderAction->execute($validated);
            $payment = $this->processPaymentAction->execute($order, $validated['payment_method'], $validated['payment_gateway']);

            return $this->successResponse([
                'order' => $order->fresh()->load(['items.product.gameProduct.game', 'items.product.seller', 'payment']),
                'payment' => $payment,
            ], $payment['success'] ? 'Pesanan berhasil dibuat.' : 'Pesanan dibuat, tetapi pembayaran belum dapat dimulai.', 201);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal membuat pesanan. Silakan coba lagi.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with([
            'items.product.gameProduct.game', 'items.product.seller',
            'payment', 'dispute',
        ])
            ->where('buyer_id', $request->user()->id)
            ->findOrFail($id);

        return $this->successResponse($order);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $order = DB::transaction(function () use ($request, $id) {
                $order = Order::where('buyer_id', $request->user()->id)
                    ->with(['items.product', 'payment'])
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($order->status !== OrderStatus::PENDING) {
                    throw new \RuntimeException('Pesanan tidak dapat dibatalkan.');
                }

                $order->update(['status' => OrderStatus::CANCELLED->value]);

                if ($order->payment && $order->payment->status === PaymentStatus::PENDING) {
                    $order->payment->update(['status' => PaymentStatus::EXPIRED->value]);
                }

                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock !== null) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                return $order->fresh()->load(['items.product.gameProduct.game', 'items.product.seller', 'payment']);
            });
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw $e;
            }

            return $this->errorResponse('Gagal membatalkan pesanan. Silakan coba lagi.', 500);
        }

        return $this->successResponse($order, 'Pesanan berhasil dibatalkan.');
    }
}
