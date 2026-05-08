<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Mobile Legends', 'Free Fire', 'Genshin Impact', 'Valorant',
            'PUBG Mobile', 'Roblox', 'League of Legends', 'Fortnite',
            'Call of Duty Mobile', 'Apex Legends', 'Steam Wallet',
            'Honkai: Star Rail', 'Clash of Clans', 'Clash Royale',
            'FIFA Mobile', 'Arena of Valor', 'Ragnarok Origin',
            'Lost Ark', 'Point Blank', 'Tower of Fantasy',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'games/' . Str::slug($name) . '.png',
            'banner' => 'games/' . Str::slug($name) . '-banner.jpg',
            'is_active' => true,
            'category' => fake()->randomElement(['MOBA', 'Battle Royale', 'RPG', 'FPS', 'MMO', 'Strategy', 'Casual']),
            'region' => fake()->randomElement(['ID', 'SEA', 'Global', 'NA', 'EU']),
            'sort_order' => fake()->numberBetween(0, 100),
            'meta' => [
                'developer' => fake()->company(),
                'release_year' => fake()->numberBetween(2015, 2024),
                'platform' => fake()->randomElement(['Mobile', 'PC', 'Cross-platform']),
            ],
        ];
    }
}