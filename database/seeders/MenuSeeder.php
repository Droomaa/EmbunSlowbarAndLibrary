<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['menuName' => 'Tubruk/Vietnam', 'price' => 8000, 'description' => 'Kopi hitam kuat', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Espresso', 'price' => 10000, 'description' => 'Ekstrak kopi murni', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Americano', 'price' => 13000, 'description' => 'Espresso dengan air', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Tubruk Susu', 'price' => 10000, 'description' => 'Kopi susu klasik', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Vietnam Susu', 'price' => 10000, 'description' => 'Kopi susu ala Vietnam', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Cappuccino/Cafe Latte', 'price' => 16000, 'description' => 'Kopi dengan buih susu', 'category' => 'Coffee', 'status' => 'Available'],
            ['menuName' => 'Java Tea', 'price' => 8000, 'description' => 'Teh hitam klasik', 'category' => 'Tea', 'status' => 'Available'],
            ['menuName' => 'Matcha Latte', 'price' => 18000, 'description' => 'Teh hijau dengan susu', 'category' => 'Tea', 'status' => 'Available'],
            ['menuName' => 'Nasi Bayam Polos', 'price' => 12000, 'description' => 'Nasi sehat', 'category' => 'Food', 'status' => 'Available'],
            ['menuName' => 'French Fries', 'price' => 10000, 'description' => 'Kentang goreng', 'category' => 'Snack', 'status' => 'Available'],
            ['menuName' => 'Ice Cream Vanilla', 'price' => 8000, 'description' => 'Es krim manis', 'category' => 'Dessert', 'status' => 'Available'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
