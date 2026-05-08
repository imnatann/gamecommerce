<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => null,
            'method' => fake()->randomElement(['qris', 'bank_transfer', 'gopay', 'ovo', 'dana', 'shopeepay', 'credit_card', 'alfamart', 'indomaret']),
            'gateway' => fake()->randomElement(['midtrans', 'xendit']),
            'amount' => fake()->numberBetween(10000, 1000000),
            'status' => PaymentStatus::PENDING,
            'gateway_response' => null,
            'paid_at' => null,
        ];
    }

    public function successful(): static
    {
        return $this->state([
            'status' => PaymentStatus::SUCCESS,
            'paid_at' => now(),
            'gateway_response' => ['transaction_id' => fake()->uuid()],
        ]);
    }
}