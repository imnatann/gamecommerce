<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => null,
            'product_id' => Product::factory(),
            'seller_id' => User::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'price' => fake()->numberBetween(5000, 500000),
            'delivery_data' => null,
            'status' => OrderStatus::PENDING,
        ];
    }
}