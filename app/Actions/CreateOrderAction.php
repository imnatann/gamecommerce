<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateOrderAction
{
    public function execute(array $validatedData): Order
    {
        return DB::transaction(function () use ($validatedData) {
            $user = $validatedData['user'];

            $cart = $this->resolveCart($user, $validatedData);
            $items = $this->resolveItems($cart, $validatedData);

            if (empty($items)) {
                throw new \RuntimeException('Order must contain at least one item');
            }

            $products = $this->lockProducts($items);

            $this->validateStock($items, $products);

            $subtotal = $this->calculateSubtotal($items, $products);
            [$voucher, $discount] = $this->resolveVoucher($validatedData['voucher_code'] ?? null, $subtotal, $user);

            $order = $this->createOrder($user, $items, $products, $subtotal, $discount, $voucher, $validatedData);

            $this->deductStock($items, $products);
            $this->recordVoucherUsage($voucher, $order, $user);

            $this->clearCart($cart);

            event(new OrderCreated($order));

            Log::info('Order created', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total_amount' => $order->total_amount,
                'discount_amount' => $order->discount_amount,
            ]);

            return $order->load(['items.product.gameProduct.game', 'items.product.seller', 'payment']);
        });
    }

    private function resolveCart($user, array $data): ?Cart
    {
        if (! empty($data['items'])) {
            return null;
        }

        $cartQuery = Cart::where('user_id', $user->id);

        if (! empty($data['cart_id'])) {
            $cartQuery->whereKey($data['cart_id']);
        }

        $cart = $cartQuery->first();

        if (! $cart || $cart->items()->count() === 0) {
            throw new \RuntimeException('Cart is empty');
        }

        return $cart;
    }

    private function resolveItems(?Cart $cart, array $data): array
    {
        if (! empty($data['items'])) {
            return collect($data['items'])->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'server_info' => $item['server_info'] ?? null,
                'server' => $item['server'] ?? null,
                'region' => $item['region'] ?? null,
            ])->toArray();
        }

        return $cart->items()->with('product')->get()->map(fn ($cartItem) => [
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'server_info' => $cartItem->server_info ?? null,
            'server' => $cartItem->server ?? null,
            'region' => $cartItem->region ?? null,
        ])->toArray();
    }

    private function lockProducts(array $items): Collection
    {
        $productIds = collect($items)->pluck('product_id')->unique()->values();

        $products = Product::whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw new \RuntimeException('One or more products are no longer available');
        }

        return $products;
    }

    private function validateStock(array $items, Collection $products): void
    {
        $quantities = collect($items)
            ->groupBy('product_id')
            ->map(fn ($rows) => $rows->sum('quantity'));

        foreach ($quantities as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product->is_active) {
                throw new \RuntimeException("Product \"{$product->name}\" is no longer available");
            }

            if ($product->stock !== null && $product->stock < $quantity) {
                throw new \RuntimeException("Insufficient stock for \"{$product->name}\". Available: {$product->stock}, Requested: {$quantity}");
            }
        }
    }

    private function calculateSubtotal(array $items, Collection $products): int
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $products->get($item['product_id'])->price * $item['quantity'];
        }

        return $subtotal;
    }

    private function createOrder($user, array $items, Collection $products, int $subtotal, int $discount, ?Voucher $voucher, array $data): Order
    {
        $finalAmount = max(0, $subtotal - $discount);

        $order = Order::create([
            'buyer_id' => $user->id,
            'subtotal' => $subtotal,
            'subtotal_amount' => $subtotal,
            'discount' => $discount,
            'total' => $finalAmount,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'voucher_id' => $voucher?->id,
            'status' => OrderStatus::PENDING->value,
            'notes' => $data['notes'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
        ]);

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);

            $order->items()->create([
                'product_id' => $product->id,
                'seller_id' => $product->seller_id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'total' => $product->price * $item['quantity'],
                'server' => $item['server'] ?? $product->server,
                'region' => $item['region'] ?? $product->region,
                'server_info' => $item['server_info'] ?? $item['server'] ?? $product->server,
                'delivery_type' => $product->delivery_type instanceof \BackedEnum
                    ? $product->delivery_type->value
                    : $product->delivery_type,
                'delivery_status' => 'pending',
                'delivery_data' => $this->deliveryData($item, $product),
                'status' => OrderStatus::PENDING->value,
            ]);
        }

        return $order;
    }

    private function resolveVoucher(?string $voucherCode, int $subtotal, $user): array
    {
        if (! $voucherCode) {
            return [null, 0];
        }

        $voucher = Voucher::where('code', $voucherCode)
            ->lockForUpdate()
            ->first();

        if (! $voucher || ! $voucher->is_active) {
            throw new \RuntimeException('Invalid or expired voucher code');
        }

        if ($voucher->starts_at && $voucher->starts_at->isFuture()) {
            throw new \RuntimeException('Invalid or expired voucher code');
        }

        if ($voucher->ends_at && $voucher->ends_at->isPast()) {
            throw new \RuntimeException('Invalid or expired voucher code');
        }

        if ($voucher->max_uses && $voucher->used_count >= $voucher->max_uses) {
            throw new \RuntimeException('Voucher has reached maximum usage limit');
        }

        if ($voucher->max_uses_per_user) {
            $userUsages = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('user_id', $user->id)
                ->count();

            if ($userUsages >= $voucher->max_uses_per_user) {
                throw new \RuntimeException('You have already used this voucher');
            }
        }

        if ($voucher->min_purchase && $subtotal < $voucher->min_purchase) {
            throw new \RuntimeException('Minimum purchase of Rp '.number_format($voucher->min_purchase, 0, ',', '.').' required');
        }

        return [$voucher, $voucher->calculateDiscount($subtotal)];
    }

    private function deductStock(array $items, Collection $products): void
    {
        $quantities = collect($items)
            ->groupBy('product_id')
            ->map(fn ($rows) => $rows->sum('quantity'));

        foreach ($quantities as $productId => $quantity) {
            $product = $products->get($productId);

            if ($product->stock !== null) {
                $updated = Product::whereKey($product->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($updated !== 1) {
                    throw new \RuntimeException("Insufficient stock for \"{$product->name}\"");
                }
            }
        }
    }

    private function recordVoucherUsage(?Voucher $voucher, Order $order, $user): void
    {
        if (! $voucher) {
            return;
        }

        VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
        ]);

        $voucher->increment('used_count');
    }

    private function clearCart(?Cart $cart): void
    {
        if ($cart) {
            $cart->items()->delete();
        }
    }

    private function deliveryData(array $item, Product $product): ?array
    {
        $data = [
            'server_info' => $item['server_info'] ?? null,
            'server' => $item['server'] ?? $product->server,
            'region' => $item['region'] ?? $product->region,
        ];

        $data = array_filter($data, fn ($value) => $value !== null && $value !== '');

        return $data === [] ? null : $data;
    }
}
