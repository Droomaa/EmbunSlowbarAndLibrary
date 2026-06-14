<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            InventorySeeder::class,
            MenuSeeder::class,
            AddOnSeeder::class,
            MenuVariantSeeder::class,
            MenuIngredientSeeder::class,
            ReservationSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            OrderItemAddonSeeder::class,
        ]);
    }
}
