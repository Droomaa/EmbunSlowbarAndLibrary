<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuVariant;

class MenuVariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            ['menu_id' => 1, 'name' => 'Hot', 'price' => 8000],
            ['menu_id' => 1, 'name' => 'Ice', 'price' => 10000],
            ['menu_id' => 2, 'name' => 'Single Shot', 'price' => 10000],
            ['menu_id' => 2, 'name' => 'Double Shot', 'price' => 15000],
            ['menu_id' => 3, 'name' => 'Hot', 'price' => 13000],
            ['menu_id' => 3, 'name' => 'Ice', 'price' => 15000],
            ['menu_id' => 4, 'name' => 'Hot', 'price' => 10000],
            ['menu_id' => 4, 'name' => 'Ice', 'price' => 12000],
            ['menu_id' => 5, 'name' => 'Hot', 'price' => 10000],
            ['menu_id' => 5, 'name' => 'Ice', 'price' => 12000],
            ['menu_id' => 6, 'name' => 'Hot', 'price' => 16000],
            ['menu_id' => 6, 'name' => 'Ice', 'price' => 18000],
            ['menu_id' => 7, 'name' => 'Hot', 'price' => 8000],
            ['menu_id' => 7, 'name' => 'Ice', 'price' => 10000],
        ];

        foreach ($variants as $variant) {
            MenuVariant::create($variant);
        }
    }
}
