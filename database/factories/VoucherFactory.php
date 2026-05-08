<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percent', 'fixed']);
        $discountValue = $type === 'percent'
            ? fake()->randomElement([5, 10, 15, 20, 25, 50])
            : fake()->randomElement([5000, 10000, 25000, 50000, 100000]);

        return [
            'code' => strtoupper(fake()->bothify('GC??##??')),
            'type' => $type,
            'discount_value' => $discountValue,
            'min_purchase' => fake()->randomElement([0, 50000, 100000, 250000]),
            'max_uses' => fake()->randomElement([null, 100, 500, 1000]),
            'used_count' => 0,
            'max_uses_per_user' => fake()->randomElement([1, 2, 3]),
            'starts_at' => now()->subDays(1),
            'ends_at' => now()->addDays(fake()->numberBetween(7, 60)),
            'is_active' => true,
        ];
    }
}