<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Models\Game;
use App\Models\GameProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameProductFactory extends Factory
{
    protected $model = GameProduct::class;

    public function definition(): array
    {
        $type = fake()->randomElement(ProductType::cases());

        return [
            'game_id' => Game::factory(),
            'type' => $type,
            'name' => $type->label(),
            'slug' => Str::slug($type->label() . '-' . fake()->unique()->word()),
            'required_info' => $this->getRequiredInfo($type),
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }

    private function getRequiredInfo(ProductType $type): ?array
    {
        return match ($type) {
            ProductType::TOPUP => [
                ['field' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
                ['field' => 'server_id', 'label' => 'Server ID', 'type' => 'select', 'required' => true],
            ],
            ProductType::GAME_KEY => [
                ['field' => 'platform', 'label' => 'Platform', 'type' => 'select', 'options' => ['Steam', 'Epic Games', 'Xbox', 'PlayStation'], 'required' => true],
            ],
            ProductType::ACCOUNT => [
                ['field' => 'login_method', 'label' => 'Login Method', 'type' => 'select', 'options' => ['Email', 'Facebook', 'Google'], 'required' => true],
            ],
            ProductType::JOKI => [
                ['field' => 'rank_current', 'label' => 'Current Rank', 'type' => 'text', 'required' => true],
                ['field' => 'rank_target', 'label' => 'Target Rank', 'type' => 'text', 'required' => true],
            ],
            ProductType::ITEM => [
                ['field' => 'server', 'label' => 'Server', 'type' => 'text', 'required' => false],
            ],
            ProductType::VOUCHER => [],
            ProductType::COIN => [
                ['field' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
            ],
        };
    }
}