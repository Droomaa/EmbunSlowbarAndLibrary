<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Order 1: 26000 -> 1 Espresso Double Shot (15k) + 1 Tubruk Susu Ice (11k -> let's say 11k wait, variant ice is 12k. 15k+12k = 27k. Let's adjust.)
            // Let's use menu_id 2 (Espresso Single 10k), menu_id 6 (Cappuccino Hot 16k) -> 26k
            ['order_id' => 1, 'menu_id' => 2, 'menu_variant_id' => 3, 'quantity' => 1, 'subtotal' => 10000],
            ['order_id' => 1, 'menu_id' => 6, 'menu_variant_id' => 11, 'quantity' => 1, 'subtotal' => 16000],

            // Order 2: 38000 -> 2 Matcha Latte Hot (2 * 18k = 36k? Wait variant not specified, let's say base price 18k * 2 = 36k + add on 2k... wait just make it simple)
            ['order_id' => 2, 'menu_id' => 8, 'menu_variant_id' => null, 'quantity' => 2, 'subtotal' => 36000],
            // + Addon? Let's leave order item addon for next seeder.

            // Order 3: 24000 -> 2 Nasi Bayam Polos (12k * 2)
            ['order_id' => 3, 'menu_id' => 9, 'menu_variant_id' => null, 'quantity' => 2, 'subtotal' => 24000],

            // Order 4: 45000 -> 3 Americano Ice (15k * 3)
            ['order_id' => 4, 'menu_id' => 3, 'menu_variant_id' => 6, 'quantity' => 3, 'subtotal' => 45000],

            // Order 5: 16000 -> 2 Ice Cream Vanilla (8k * 2)
            ['order_id' => 5, 'menu_id' => 11, 'menu_variant_id' => null, 'quantity' => 2, 'subtotal' => 16000],
        ];

        foreach ($items as $item) {
            OrderItem::create($item);
        }
    }
}
