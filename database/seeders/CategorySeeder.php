<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Top Up', 'slug' => 'top-up', 'type' => 'topup', 'sort_order' => 1, 'icon' => 'icons/topup.svg', 'is_active' => true],
            ['name' => 'Game Key', 'slug' => 'game-key', 'type' => 'game_key', 'sort_order' => 2, 'icon' => 'icons/game-key.svg', 'is_active' => true],
            ['name' => 'Akun', 'slug' => 'akun', 'type' => 'account', 'sort_order' => 3, 'icon' => 'icons/account.svg', 'is_active' => true],
            ['name' => 'Voucher', 'slug' => 'voucher', 'type' => 'voucher', 'sort_order' => 4, 'icon' => 'icons/voucher.svg', 'is_active' => true],
            ['name' => 'Item', 'slug' => 'item', 'type' => 'item', 'sort_order' => 5, 'icon' => 'icons/item.svg', 'is_active' => true],
            ['name' => 'Joki', 'slug' => 'joki', 'type' => 'joki', 'sort_order' => 6, 'icon' => 'icons/joki.svg', 'is_active' => true],
            ['name' => 'Koin Game', 'slug' => 'koin-game', 'type' => 'coin', 'sort_order' => 7, 'icon' => 'icons/coin.svg', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}