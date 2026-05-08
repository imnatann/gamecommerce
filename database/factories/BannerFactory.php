<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Flash Sale Gaming!',
                'Top Up Termurah',
                'Game Key Promo',
                'New Game Available',
                'Cashback 15%',
            ]),
            'subtitle' => fake()->optional()->sentence(),
            'image' => 'banners/banner-' . fake()->numberBetween(1, 10) . '.jpg',
            'link' => fake()->optional(0.7)->url(),
            'position' => fake()->randomElement(['home_hero', 'home_mid', 'home_bottom', 'category_top']),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'starts_at' => now()->subDays(1),
            'ends_at' => now()->addDays(30),
        ];
    }
}