<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuIngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Espresso (menu_id 2) -> Biji Kopi Arabica (inv_id 1)
            ['menu_id' => 2, 'inventory_id' => 1, 'quantity_needed' => 15],
            // Americano (menu_id 3) -> Biji Kopi Arabica (inv_id 1)
            ['menu_id' => 3, 'inventory_id' => 1, 'quantity_needed' => 15],
            // Tubruk Susu (menu_id 4) -> Biji Kopi Robusta (inv_id 2), Susu Fresh Milk (inv_id 3)
            ['menu_id' => 4, 'inventory_id' => 2, 'quantity_needed' => 12],
            ['menu_id' => 4, 'inventory_id' => 3, 'quantity_needed' => 100],
            // Vietnam Susu (menu_id 5) -> Biji Kopi Robusta (inv_id 2), Susu Fresh Milk (inv_id 3)
            ['menu_id' => 5, 'inventory_id' => 2, 'quantity_needed' => 15],
            ['menu_id' => 5, 'inventory_id' => 3, 'quantity_needed' => 150],
            // Cappuccino (menu_id 6) -> Biji Kopi Arabica (inv_id 1), Susu Fresh Milk (inv_id 3)
            ['menu_id' => 6, 'inventory_id' => 1, 'quantity_needed' => 18],
            ['menu_id' => 6, 'inventory_id' => 3, 'quantity_needed' => 120],
            // Matcha Latte (menu_id 8) -> Susu Fresh Milk (inv_id 3)
            ['menu_id' => 8, 'inventory_id' => 3, 'quantity_needed' => 150],
        ];

        foreach ($ingredients as $ing) {
            DB::table('menu_ingredients')->insert([
                'menu_id' => $ing['menu_id'],
                'inventory_id' => $ing['inventory_id'],
                'quantity_needed' => $ing['quantity_needed'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
