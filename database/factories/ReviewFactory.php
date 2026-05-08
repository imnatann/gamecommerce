<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'product_id' => null,
            'order_item_id' => null,
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->optional(0.7)->paragraph(),
            'images' => fake()->optional(0.2)->randomElement([
                ['reviews/review1.jpg'],
                ['reviews/review2.jpg', 'reviews/review3.jpg'],
            ]),
            'is_anonymous' => fake()->boolean(10),
        ];
    }

    public function fiveStar(): static
    {
        return $this->state(['rating' => 5]);
    }

    public function oneStar(): static
    {
        return $this->state(['rating' => 1]);
    }
}