<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'total_amount' => fake()->numberBetween(10000, 1000000),
            'discount_amount' => 0,
            'voucher_id' => null,
            'status' => OrderStatus::PENDING,
            'notes' => fake()->optional()->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function paid(): static
    {
        return $this->state(['status' => OrderStatus::PAID]);
    }

    public function completed(): static
    {
        return $this->state(['status' => OrderStatus::COMPLETED]);
    }
}