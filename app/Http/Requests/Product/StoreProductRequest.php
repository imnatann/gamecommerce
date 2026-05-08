<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', 'exists:games,id'],
            'game_product_id' => ['nullable', 'exists:game_products,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'string', Rule::in(collect(ProductType::cases())->pluck('value'))],
            'price' => ['required', 'numeric', 'min:100', 'max:999999999'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'server' => ['nullable', 'string', 'max:100'],
            'delivery_type' => ['required', 'string', 'in:instant,manual,login'],
            'delivery_time' => ['nullable', 'string', 'max:255'],
            'delivery_data' => ['nullable', 'array'],
            'delivery_data.keys' => ['nullable', 'array'],
            'delivery_data.keys.*' => ['string', 'max:1000'],
            'delivery_data.code' => ['nullable', 'string', 'max:255'],
            'delivery_data.instructions' => ['nullable', 'string', 'max:2000'],
            'delivery_data.redeem_url' => ['nullable', 'url', 'max:500'],
            'delivery_data.player_id' => ['nullable', 'string', 'max:255'],
            'delivery_data.topup_data' => ['nullable', 'array'],
            'delivery_data.username' => ['nullable', 'string', 'max:255'],
            'delivery_data.password' => ['nullable', 'string', 'max:255'],
            'delivery_data.email' => ['nullable', 'email', 'max:255'],
            'delivery_data.email_password' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.required' => 'Please select a game',
            'game_id.exists' => 'Selected game does not exist',
            'name.required' => 'Product name is required',
            'name.max' => 'Product name must not exceed 255 characters',
            'description.required' => 'Product description is required',
            'type.required' => 'Product type is required',
            'type.in' => 'Invalid product type selected',
            'price.required' => 'Price is required',
            'price.min' => 'Minimum price is Rp 100',
            'price.max' => 'Price exceeds maximum allowed',
            'delivery_type.required' => 'Delivery type is required',
            'delivery_type.in' => 'Invalid delivery type',
            'images.max' => 'Maximum 5 images allowed',
            'images.*.max' => 'Each image must not exceed 2MB',
        ];
    }
}