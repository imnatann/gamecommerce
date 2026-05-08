<?php

namespace App\Http\Controllers\Web;

use App\Actions\CreateOrderAction;
use App\Actions\ProcessPaymentAction;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CreateOrderAction $createOrderAction,
        private ProcessPaymentAction $processPaymentAction,
        private PaymentManager $paymentManager,
    ) {}

    public function cart(Request $request): View
    {
        return $this->checkoutView($request);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'server' => 'nullable|string',
            'region' => 'nullable|string',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $quantity = $validated['quantity'] ?? 1;
        $cart = $this->getOrCreateCart($request);
        $existingQuantity = (int) $cart->items()
            ->where('product_id', $product->id)
            ->where('server', $validated['server'] ?? null)
            ->where('region', $validated['region'] ?? null)
            ->value('quantity');
        $newQuantity = $existingQuantity + $quantity;

        if ($product->stock !== null && $product->stock < $newQuantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart->items()->updateOrCreate(
            [
                'product_id' => $product->id,
                'server' => $validated['server'] ?? null,
                'region' => $validated['region'] ?? null,
            ],
            [
                'quantity' => $newQuantity,
                'price' => $product->price,
                'server_info' => $validated['server'] ?? null,
            ],
        );

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function buyNow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $quantity = $validated['quantity'] ?? 1;

        if ($product->stock !== null && $product->stock < $quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = $this->getOrCreateCart($request);
        $cart->items()->updateOrCreate(
            [
                'product_id' => $product->id,
                'server' => null,
                'region' => null,
            ],
            [
                'quantity' => $quantity,
                'price' => $product->price,
            ],
        );

        return redirect()->route('checkout')->with('success', 'Produk siap dibayar.');
    }

    public function removeFromCart(Request $request, int $itemId): RedirectResponse
    {
        $cart = $this->getCart($request);

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $cartItems = $this->cartItems($request);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong.');
        }

        return $this->checkoutView($request);
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_gateway' => 'nullable|string|in:midtrans,xendit',
            'voucher_code' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->getCart($request);

        if (! $cart || $cart->items()->doesntExist()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong.');
        }

        $validated['user'] = $request->user();
        $validated['cart_id'] = $cart->id;
        $validated['ip_address'] = $request->ip();
        $validated['payment_gateway'] ??= $this->paymentManager->defaultGateway();

        try {
            $order = $this->createOrderAction->execute($validated);
            $payment = $this->processPaymentAction->execute($order, $validated['payment_method'], $validated['payment_gateway']);

            return redirect()->route('order.status', $order->id)
                ->with($payment['success'] ? 'success' : 'error', $payment['success']
                    ? 'Pesanan berhasil dibuat!'
                    : 'Pesanan dibuat, tetapi pembayaran belum dapat dimulai.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal memproses pesanan. Silakan coba lagi.');
        }
    }

    public function orderStatus(Request $request, int $orderId): View
    {
        $order = Order::with(['items.product.gameProduct.game', 'items.product.seller', 'payment'])
            ->where('buyer_id', $request->user()->id)
            ->findOrFail($orderId);

        return view('pages.order-status', [
            'order' => $this->formatOrder($order),
        ]);
    }

    private function getCart(Request $request): ?Cart
    {
        if (! $request->user()) {
            return null;
        }

        return Cart::with('items')->where('user_id', $request->user()->id)->first();
    }

    private function getOrCreateCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function cartItems(Request $request): Collection
    {
        $cart = $this->getCart($request);

        return $cart
            ? $cart->items()->with(['product.gameProduct.game', 'product.seller'])->get()
            : collect();
    }

    private function checkoutView(Request $request): View
    {
        $cartItems = $this->cartItems($request);
        $subtotal = $cartItems->sum(fn ($item) => ($item->price ?: ($item->product?->price ?? 0)) * $item->quantity);
        $paymentMethods = $this->paymentManager->getAvailableMethods();

        return view('pages.checkout', [
            'cartItems' => $cartItems,
            'items' => $this->formatCartItems($cartItems),
            'totalItems' => $cartItems->sum('quantity'),
            'subtotal' => $this->formatMoney($subtotal),
            'subtotalAmount' => $subtotal,
            'discount' => null,
            'serviceFee' => $this->formatMoney(0),
            'total' => $this->formatMoney($subtotal),
            'totalAmount' => $subtotal,
            'paymentMethods' => $paymentMethods,
            'ewallets' => $paymentMethods['ewallets'],
            'banks' => $paymentMethods['banks'],
            'convenience' => $paymentMethods['convenience'],
        ]);
    }

    private function formatCartItems(Collection $cartItems): array
    {
        return $cartItems->map(function ($item) {
            $product = $item->product;
            $game = $product?->gameProduct?->game;

            return [
                'id' => $item->id,
                'name' => $product?->name ?? '',
                'game_name' => $game?->name,
                'variant' => trim(collect([$product?->server, $product?->region])->filter()->implode(' / ')),
                'quantity' => $item->quantity,
                'price' => $this->formatMoney(($item->price ?: ($product?->price ?? 0)) * $item->quantity),
                'unit_price' => $item->price ?: ($product?->price ?? 0),
                'image' => '',
            ];
        })->all();
    }

    private function formatOrder(Order $order): array
    {
        $status = $order->status instanceof OrderStatus ? $order->status->value : $order->status;

        return [
            'id' => $order->id,
            'status' => $status,
            'created_at' => $order->created_at?->format('d M Y H:i'),
            'items' => $order->items->map(function ($item) {
                $product = $item->product;
                $game = $product?->gameProduct?->game;

                return [
                    'id' => $item->id,
                    'name' => $product?->name ?? '',
                    'game_name' => $game?->name,
                    'variant' => trim(collect([$item->delivery_data['server'] ?? null, $item->delivery_data['region'] ?? null])->filter()->implode(' / ')),
                    'quantity' => $item->quantity,
                    'price' => $this->formatMoney($item->price * $item->quantity),
                    'image' => '',
                ];
            })->all(),
            'delivery_type' => $order->items->contains(fn ($item) => ($item->product?->delivery_type?->value ?? $item->product?->delivery_type) !== 'instant') ? 'manual' : 'instant',
            'subtotal' => $this->formatMoney($order->total_amount),
            'discount' => $order->discount_amount > 0 ? $this->formatMoney($order->discount_amount) : null,
            'service_fee' => $this->formatMoney(0),
            'total' => $this->formatMoney($order->net_amount),
            'payment_method' => $order->payment?->method,
        ];
    }

    private function formatMoney(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
