<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'category' => 'MOBA', 'region' => 'SEA', 'sort_order' => 1, 'icon' => 'games/mobile-legends.png', 'banner' => 'games/mobile-legends-banner.jpg', 'meta' => ['developer' => 'Moonton', 'platform' => 'Mobile', 'release_year' => 2016]],
            ['name' => 'Free Fire', 'slug' => 'free-fire', 'category' => 'Battle Royale', 'region' => 'Global', 'sort_order' => 2, 'icon' => 'games/free-fire.png', 'banner' => 'games/free-fire-banner.jpg', 'meta' => ['developer' => 'Garena', 'platform' => 'Mobile', 'release_year' => 2017]],
            ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'category' => 'RPG', 'region' => 'Global', 'sort_order' => 3, 'icon' => 'games/genshin-impact.png', 'banner' => 'games/genshin-impact-banner.jpg', 'meta' => ['developer' => 'HoYoverse', 'platform' => 'Cross-platform', 'release_year' => 2020]],
            ['name' => 'Valorant', 'slug' => 'valorant', 'category' => 'FPS', 'region' => 'Global', 'sort_order' => 4, 'icon' => 'games/valorant.png', 'banner' => 'games/valorant-banner.jpg', 'meta' => ['developer' => 'Riot Games', 'platform' => 'PC', 'release_year' => 2020]],
            ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'category' => 'Battle Royale', 'region' => 'Global', 'sort_order' => 5, 'icon' => 'games/pubg-mobile.png', 'banner' => 'games/pubg-mobile-banner.jpg', 'meta' => ['developer' => 'Krafton', 'platform' => 'Mobile', 'release_year' => 2018]],
            ['name' => 'Roblox', 'slug' => 'roblox', 'category' => 'Casual', 'region' => 'Global', 'sort_order' => 6, 'icon' => 'games/roblox.png', 'banner' => 'games/roblox-banner.jpg', 'meta' => ['developer' => 'Roblox Corporation', 'platform' => 'Cross-platform', 'release_year' => 2006]],
            ['name' => 'League of Legends', 'slug' => 'league-of-legends', 'category' => 'MOBA', 'region' => 'Global', 'sort_order' => 7, 'icon' => 'games/league-of-legends.png', 'banner' => 'games/league-of-legends-banner.jpg', 'meta' => ['developer' => 'Riot Games', 'platform' => 'PC', 'release_year' => 2009]],
            ['name' => 'Steam Wallet', 'slug' => 'steam-wallet', 'category' => 'Platform', 'region' => 'Global', 'sort_order' => 8, 'icon' => 'games/steam.png', 'banner' => 'games/steam-banner.jpg', 'meta' => ['developer' => 'Valve', 'platform' => 'PC', 'release_year' => 2003]],
            ['name' => 'Honkai: Star Rail', 'slug' => 'honkai-star-rail', 'category' => 'RPG', 'region' => 'Global', 'sort_order' => 9, 'icon' => 'games/honkai-star-rail.png', 'banner' => 'games/honkai-star-rail-banner.jpg', 'meta' => ['developer' => 'HoYoverse', 'platform' => 'Cross-platform', 'release_year' => 2023]],
            ['name' => 'Clash of Clans', 'slug' => 'clash-of-clans', 'category' => 'Strategy', 'region' => 'Global', 'sort_order' => 10, 'icon' => 'games/clash-of-clans.png', 'banner' => 'games/clash-of-clans-banner.jpg', 'meta' => ['developer' => 'Supercell', 'platform' => 'Mobile', 'release_year' => 2012]],
            ['name' => 'Call of Duty Mobile', 'slug' => 'cod-mobile', 'category' => 'FPS', 'region' => 'Global', 'sort_order' => 11, 'icon' => 'games/cod-mobile.png', 'banner' => 'games/cod-mobile-banner.jpg', 'meta' => ['developer' => 'Activision', 'platform' => 'Mobile', 'release_year' => 2019]],
            ['name' => 'Arena of Valor', 'slug' => 'arena-of-valor', 'category' => 'MOBA', 'region' => 'SEA', 'sort_order' => 12, 'icon' => 'games/arena-of-valor.png', 'banner' => 'games/arena-of-valor-banner.jpg', 'meta' => ['developer' => 'Tencent', 'platform' => 'Mobile', 'release_year' => 2016]],
            ['name' => 'Apex Legends', 'slug' => 'apex-legends', 'category' => 'Battle Royale', 'region' => 'Global', 'sort_order' => 13, 'icon' => 'games/apex-legends.png', 'banner' => 'games/apex-legends-banner.jpg', 'meta' => ['developer' => 'Respawn', 'platform' => 'Cross-platform', 'release_year' => 2020]],
            ['name' => 'Point Blank', 'slug' => 'point-blank', 'category' => 'FPS', 'region' => 'ID', 'sort_order' => 14, 'icon' => 'games/point-blank.png', 'banner' => 'games/point-blank-banner.jpg', 'meta' => ['developer' => 'Zepetto', 'platform' => 'PC', 'release_year' => 2008]],
            ['name' => 'Ragnarok Origin', 'slug' => 'ragnarok-origin', 'category' => 'MMO', 'region' => 'SEA', 'sort_order' => 15, 'icon' => 'games/ragnarok-origin.png', 'banner' => 'games/ragnarok-origin-banner.jpg', 'meta' => ['developer' => 'Gravity', 'platform' => 'Mobile', 'release_year' => 2019]],
            ['name' => 'Fortnite', 'slug' => 'fortnite', 'category' => 'Battle Royale', 'region' => 'Global', 'sort_order' => 16, 'icon' => 'games/fortnite.png', 'banner' => 'games/fortnite-banner.jpg', 'meta' => ['developer' => 'Epic Games', 'platform' => 'Cross-platform', 'release_year' => 2017]],
            ['name' => 'Clash Royale', 'slug' => 'clash-royale', 'category' => 'Strategy', 'region' => 'Global', 'sort_order' => 17, 'icon' => 'games/clash-royale.png', 'banner' => 'games/clash-royale-banner.jpg', 'meta' => ['developer' => 'Supercell', 'platform' => 'Mobile', 'release_year' => 2016]],
            ['name' => 'FIFA Mobile', 'slug' => 'fifa-mobile', 'category' => 'Sports', 'region' => 'Global', 'sort_order' => 18, 'icon' => 'games/fifa-mobile.png', 'banner' => 'games/fifa-mobile-banner.jpg', 'meta' => ['developer' => 'EA Sports', 'platform' => 'Mobile', 'release_year' => 2016]],
            ['name' => 'Tower of Fantasy', 'slug' => 'tower-of-fantasy', 'category' => 'RPG', 'region' => 'Global', 'sort_order' => 19, 'icon' => 'games/tower-of-fantasy.png', 'banner' => 'games/tower-of-fantasy-banner.jpg', 'meta' => ['developer' => 'Hotta Studio', 'platform' => 'Cross-platform', 'release_year' => 2021]],
            ['name' => 'Lost Ark', 'slug' => 'lost-ark', 'category' => 'MMO', 'region' => 'Global', 'sort_order' => 20, 'icon' => 'games/lost-ark.png', 'banner' => 'games/lost-ark-banner.jpg', 'meta' => ['developer' => 'Smilegate', 'platform' => 'PC', 'release_year' => 2022]],
            ['name' => 'Dragon Raja', 'slug' => 'dragon-raja', 'category' => 'MMO', 'region' => 'SEA', 'sort_order' => 21, 'icon' => 'games/dragon-raja.png', 'banner' => 'games/dragon-raja-banner.jpg', 'meta' => ['developer' => 'Zulong Games', 'platform' => 'Mobile', 'release_year' => 2020]],
            ['name' => 'Google Play', 'slug' => 'google-play', 'category' => 'Platform', 'region' => 'ID', 'sort_order' => 22, 'icon' => 'games/google-play.png', 'banner' => 'games/google-play-banner.jpg', 'meta' => ['developer' => 'Google', 'platform' => 'Mobile', 'release_year' => 2012]],
        ];

        foreach ($games as $game) {
            Game::create($game);
        }
    }
}