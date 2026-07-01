<?php

namespace Database\Seeders;

use App\Models\CoinPack;
use App\Models\Gift;
use Illuminate\Database\Seeder;

class CoinAndGiftSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            ['name' => 'Découverte',  'coins' => 50,   'bonus_coins' => 0,   'price' => 500,   'is_popular' => false, 'sort_order' => 1],
            ['name' => 'Starter',     'coins' => 120,  'bonus_coins' => 10,  'price' => 1000,  'is_popular' => false, 'sort_order' => 2],
            ['name' => 'Populaire',   'coins' => 300,  'bonus_coins' => 50,  'price' => 2000,  'is_popular' => true,  'sort_order' => 3],
            ['name' => 'Premium',     'coins' => 800,  'bonus_coins' => 200, 'price' => 5000,  'is_popular' => false, 'sort_order' => 4],
            ['name' => 'VIP',         'coins' => 2000, 'bonus_coins' => 800, 'price' => 10000, 'is_popular' => false, 'sort_order' => 5],
        ];

        foreach ($packs as $p) {
            CoinPack::updateOrCreate(['name' => $p['name']], $p + ['is_active' => true]);
        }

        foreach (Gift::CATALOG as $i => $g) {
            Gift::updateOrCreate(['name' => $g['name']], $g + ['sort_order' => $i + 1, 'is_active' => true]);
        }
    }
}
