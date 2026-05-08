<?php

namespace App\Http\Requests\Order;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required_without:cart_id', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:99'],
            'items.*.server_info' => ['nullable', 'string', 'max:255'],
            'items.*.server' => ['nullable', 'string', 'max:100'],
            'items.*.region' => ['nullable', 'string', 'max:100'],
            'cart_id' => ['nullable', 'integer', 'exists:carts,id'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,credit_card,debit_card,e_wallet,qris,shopeepay,indomaret,alfamart'],
            'payment_gateway' => ['nullable', 'string', 'in:midtrans,xendit'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            if (empty($items) && ! $this->input('cart_id')) {
                $validator->errors()->add('items', 'Please provide items or a cart ID');
            }

            if (! empty($items)) {
                $productIds = collect($items)->pluck('product_id')->unique();
                $products = Product::with('gameProduct')
                    ->whereIn('id', $productIds)
                    ->get()
                    ->keyBy('id');

                foreach ($items as $index => $item) {
                    $productId = $item['product_id'] ?? null;

                    if (! $productId) {
                        continue;
                    }

                    $product = $products->get($productId);

                    if (! $product) {
                        $validator->errors()->add("items.{$index}.product_id", 'Product not found');

                        continue;
                    }

                    if (! $product->is_active) {
                        $validator->errors()->add("items.{$index}.product_id", "Product \"{$product->name}\" is no longer available");

                        continue;
                    }

                    $quantity = $item['quantity'] ?? 0;

                    if ($product->stock !== null && $quantity > $product->stock) {
                        $validator->errors()->add("items.{$index}.quantity", "Only {$product->stock} available for \"{$product->name}\"");
                    }

                    $productType = $product->gameProduct?->type;
                    $hasServerInfo = ! empty($item['server_info']) || ! empty($item['server']);

                    if ($productType && method_exists($productType, 'requiresServerInfo') && $productType->requiresServerInfo() && ! $hasServerInfo) {
                        $validator->errors()->add("items.{$index}.server_info", 'Server info is required for this product type');
                    }
                }

                $sellerIds = $products->pluck('seller_id')->unique();
                if ($sellerIds->count() > 10) {
                    $validator->errors()->add('items', 'Orders from more than 10 different sellers are not supported');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required_without' => 'Please add items to your order',
            'items.min' => 'Order must contain at least one item',
            'items.*.product_id.required_with' => 'Product ID is required',
            'items.*.product_id.exists' => 'Selected product does not exist',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.quantity.max' => 'Maximum quantity per item is 99',
            'voucher_code.max' => 'Voucher code is too long',
            'payment_method.required' => 'Please select a payment method',
            'payment_method.in' => 'Invalid payment method selected',
            'payment_gateway.in' => 'Invalid payment gateway',
        ];
    }
}
