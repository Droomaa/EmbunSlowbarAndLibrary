<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- DATA USER (Buat Login) ---
        // Owner cafe-nya si Andi
        \App\Models\User::create([
            'name' => 'Andi',
            'username' => 'owner_andi',
            'email' => 'andi@embunslowbar.com',
            'password' => bcrypt('password123'),
            'role' => 'Owner',
        ]);

        // Akun buat Admin
        \App\Models\User::create([
            'name' => 'Siti Aminah',
            'username' => 'admin_siti',
            'email' => 'siti@embunslowbar.com',
            'password' => bcrypt('password123'),
            'role' => 'Admin',
        ]);

        // Akun buat Karyawan/Barista
        $employee = \App\Models\User::create([
            'name' => 'Bambang Pamungkas',
            'username' => 'employee_bambang',
            'email' => 'bambang@embunslowbar.com',
            'password' => bcrypt('password123'),
            'role' => 'Employee',
        ]);

        // --- DATA PELANGGAN ---
        // Buat ngetes reservasi atau order
        $customer1 = \App\Models\Customer::create(['name' => 'Joko Susilo', 'noHP' => '081234567890']);
        \App\Models\Customer::create(['name' => 'Rina Kartika', 'noHP' => '082234567891']);
        \App\Models\Customer::create(['name' => 'Agus Heryanto', 'noHP' => '083234567892']);

        // --- DAFTAR MENU ---
        // Data ini diambil langsung dari gambar menu Embun Slowbar
        $menus = [
            // Kategori: Black Coffee
            ['menuName' => 'Tubruk/Vietnam', 'price' => 8000, 'description' => 'Black Coffee'],
            ['menuName' => 'Espresso', 'price' => 10000, 'description' => 'Black Coffee'],
            ['menuName' => 'Americano', 'price' => 13000, 'description' => 'Black Coffee'],
            ['menuName' => 'Honey Americano', 'price' => 17000, 'description' => 'Black Coffee'],
            
            // Kategori: White Coffee
            ['menuName' => 'Tubruk Susu', 'price' => 10000, 'description' => 'White Coffee'],
            ['menuName' => 'Vietnam Susu', 'price' => 10000, 'description' => 'White Coffee'],
            ['menuName' => 'Cappuccino/Cafe Latte', 'price' => 16000, 'description' => 'White Coffee'],
            ['menuName' => 'Mocchaccino', 'price' => 18000, 'description' => 'White Coffee'],
            ['menuName' => 'Embun Ori Latte', 'price' => 16000, 'description' => 'White Coffee'],
            ['menuName' => 'Gula Aren Latte', 'price' => 16000, 'description' => 'White Coffee'],
            
            // Kategori: Tea
            ['menuName' => 'Java Tea', 'price' => 8000, 'description' => 'Tea'],
            ['menuName' => 'Lemon Tea', 'price' => 10000, 'description' => 'Tea'],
            ['menuName' => 'Thai Milk Tea', 'price' => 12000, 'description' => 'Tea'],
            
            // Kategori: Matcha
            ['menuName' => 'Matcha Latte', 'price' => 16000, 'description' => 'Matcha'],
            
            // Kategori: Santapan (Makanan Berat)
            ['menuName' => 'Nasi Bayam Polos', 'price' => 12000, 'description' => 'Santapan'],
            ['menuName' => 'Nasi Bayam Ayam', 'price' => 16000, 'description' => 'Santapan'],
            ['menuName' => 'Nasi Ayam Telur Asin', 'price' => 19000, 'description' => 'Santapan'],
            ['menuName' => 'Nasi Koro Sapi', 'price' => 35000, 'description' => 'Santapan'],
            ['menuName' => 'Nasi Goreng Bayam', 'price' => 16000, 'description' => 'Santapan'],
            ['menuName' => 'Indomie Goreng', 'price' => 7000, 'description' => 'Santapan'],
            
            // Kategori: Kudapan (Snacks)
            ['menuName' => 'French Fries', 'price' => 10000, 'description' => 'Kudapan'],
            ['menuName' => 'Spinach Fries', 'price' => 14000, 'description' => 'Kudapan'],
            ['menuName' => 'Dimsum Mentai', 'price' => 15000, 'description' => 'Kudapan'],
            
            // Kategori: Dessert
            ['menuName' => 'Ice Cream Vanilla', 'price' => 8000, 'description' => 'Dessert'],
            ['menuName' => 'Cafe Affogato', 'price' => 13000, 'description' => 'Dessert'],
        ];

        foreach ($menus as $menu) {
            \App\Models\Menu::create($menu);
        }

        // --- STOK INVENTARIS ---
        // Ini estimasi stok awal bahan baku
        \App\Models\Inventaris::create(['itemName' => 'Biji Kopi Arabica', 'stock' => 10, 'satuan' => 'kg']);
        \App\Models\Inventaris::create(['itemName' => 'Susu Fresh Milk', 'stock' => 24, 'satuan' => 'liter']);
        \App\Models\Inventaris::create(['itemName' => 'Bayam Segar', 'stock' => 5, 'satuan' => 'kg']);
        \App\Models\Inventaris::create(['itemName' => 'Daging Ayam', 'stock' => 15, 'satuan' => 'kg']);
        \App\Models\Inventaris::create(['itemName' => 'Daging Sapi (Koro)', 'stock' => 10, 'satuan' => 'kg']);

        // --- CONTOH RESERVASI ---
        // Buat ngetes kalau fitur reservasi jalan
        \App\Models\Reservation::create([
            'customer_id' => $customer1->id,
            'date' => now()->addDays(1)->toDateString(),
            'startTime' => '14:00:00',
            'duration' => 2,
            'jumlahOrang' => 2,
            'status' => 'Pending'
        ]);
    }
}
