<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventories = [
            ['item_name' => 'Biji Kopi Arabica', 'quantity' => 10000, 'unit' => 'gram'],
            ['item_name' => 'Biji Kopi Robusta', 'quantity' => 8000, 'unit' => 'gram'],
            ['item_name' => 'Susu Fresh Milk', 'quantity' => 24000, 'unit' => 'ml'],
            ['item_name' => 'Gula Pasir', 'quantity' => 5000, 'unit' => 'gram'],
            ['item_name' => 'Gula Aren', 'quantity' => 3000, 'unit' => 'gram'],
            ['item_name' => 'Daun Teh Hitam', 'quantity' => 2000, 'unit' => 'gram'],
            ['item_name' => 'Bayam Segar', 'quantity' => 1000, 'unit' => 'gram'],
            ['item_name' => 'Daging Ayam', 'quantity' => 5000, 'unit' => 'gram'],
        ];

        foreach ($inventories as $inv) {
            Inventory::create($inv);
        }
    }
}
