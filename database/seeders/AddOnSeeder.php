<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddOn;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['name' => 'Extra Shot Espresso', 'price' => 5000, 'category' => 'Drink'],
            ['name' => 'Oat Milk', 'price' => 8000, 'category' => 'Drink'],
            ['name' => 'Almond Milk', 'price' => 8000, 'category' => 'Drink'],
            ['name' => 'Vanilla Syrup', 'price' => 4000, 'category' => 'Drink'],
            ['name' => 'Caramel Syrup', 'price' => 4000, 'category' => 'Drink'],
            ['name' => 'Hazelnut Syrup', 'price' => 4000, 'category' => 'Drink'],
            ['name' => 'Telur Mata Sapi', 'price' => 4000, 'category' => 'Food'],
            ['name' => 'Telur Dadar', 'price' => 4000, 'category' => 'Food'],
            ['name' => 'Sosis Goreng', 'price' => 6000, 'category' => 'Food'],
            ['name' => 'Keju Slice', 'price' => 3000, 'category' => 'Food'],
        ];

        foreach ($addons as $addon) {
            AddOn::create($addon);
        }
    }
}
