<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Flash Sale Gaming Week!',
                'subtitle' => 'Diskon hingga 50% untuk top-up game favorit kamu',
                'image' => 'banners/flash-sale-gaming.jpg',
                'link' => '/search?promo=flash_sale',
                'position' => 'home_hero',
                'sort_order' => 1,
                'is_active' => true,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(7),
            ],
            [
                'title' => 'Mobile Legends Top Up',
                'subtitle' => 'Diamond termurah, proses instan 5 detik',
                'image' => 'banners/ml-topup.jpg',
                'link' => '/g/mobile-legends/topup',
                'position' => 'home_hero',
                'sort_order' => 2,
                'is_active' => true,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(30),
            ],
            [
                'title' => 'New: Honkai Star Rail',
                'subtitle' => 'Oneiric Shard & Stellar Jade tersedia sekarang',
                'image' => 'banners/hsr-new.jpg',
                'link' => '/g/honkai-star-rail/topup',
                'position' => 'home_hero',
                'sort_order' => 3,
                'is_active' => true,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(30),
            ],
            [
                'title' => 'Joki Rank Murah',
                'subtitle' => 'Tinggal pesan, rank naik sendiri —Garansi!',
                'image' => 'banners/joki-promo.jpg',
                'link' => '/c/joki',
                'position' => 'home_mid',
                'sort_order' => 1,
                'is_active' => true,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(14),
            ],
            [
                'title' => 'Steam Wallet Code',
                'subtitle' => 'Beli game Steam lebih hemat dengan voucher kami',
                'image' => 'banners/steam-wallet.jpg',
                'link' => '/g/steam-wallet/voucher',
                'position' => 'home_bottom',
                'sort_order' => 1,
                'is_active' => true,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(30),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}