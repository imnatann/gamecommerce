<?php

namespace Database\Factories;

use App\Enums\DeliveryType;
use App\Models\GameProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $deliveryType = fake()->randomElement(DeliveryType::cases());
        $gameProduct = GameProduct::factory()->create();
        $price = fake()->numberBetween(5000, 500000);
        $hasDiscount = fake()->boolean(30);

        return [
            'seller_id' => User::factory(),
            'game_product_id' => $gameProduct->id,
            'name' => $gameProduct->name . ' - ' . fake()->randomElement(['Diamond', 'BP', 'UC', 'V-Bucks', 'Primogem', 'Riot Points', 'Steam Credit']) . ' ' . fake()->randomElement(['100', '500', '1000', '5000']),
            'slug' => Str::slug(fake()->words(3, true) . '-' . fake()->numberBetween(100, 9999)),
            'description' => fake()->paragraphs(2, true),
            'price' => $price,
            'original_price' => $hasDiscount ? (int) ($price * 1.15) : null,
            'stock' => fake()->numberBetween(0, 500),
            'server' => fake()->randomElement(['Indonesia', 'Singapore', 'Global', 'SEA', null]),
            'region' => fake()->randomElement(['ID', 'SEA', 'Global', null]),
            'delivery_type' => $deliveryType,
            'delivery_data' => $deliveryType === DeliveryType::INSTANT ? ['auto_delivery' => true] : null,
            'is_active' => true,
            'sold_count' => fake()->numberBetween(0, 10000),
            'avg_rating' => fake()->randomFloat(2, 3.5, 5.0),
            'rating_count' => fake()->numberBetween(0, 5000),
        ];
    }

    public function active(): static
    {
        return $this->state(['is_active' => true, 'stock' => fake()->numberBetween(10, 500)]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    public function discounted(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? 50000;
            return [
                'original_price' => (int) ($price * 1.25),
            ];
        });
    }
}