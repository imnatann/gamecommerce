<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:5000'],
            'type' => ['sometimes', 'string', Rule::in(collect(ProductType::cases())->pluck('value'))],
            'price' => ['sometimes', 'numeric', 'min:100', 'max:999999999'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'server' => ['nullable', 'string', 'max:100'],
            'delivery_type' => ['sometimes', 'string', 'in:instant,manual,login'],
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
            'is_active' => ['sometimes', 'boolean'],
            'new_images' => ['nullable', 'array', 'max:5'],
            'new_images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
            'removed_images' => ['nullable', 'array'],
            'removed_images.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Product name must not exceed 255 characters',
            'type.in' => 'Invalid product type selected',
            'price.min' => 'Minimum price is Rp 100',
            'price.max' => 'Price exceeds maximum allowed',
            'delivery_type.in' => 'Invalid delivery type',
            'new_images.max' => 'Maximum 5 images allowed',
            'new_images.*.max' => 'Each image must not exceed 2MB',
        ];
    }
}