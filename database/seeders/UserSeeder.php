<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $users = [
            [
                'name' => 'Sandro',
                'username' => 'owner_sandro',
                'email' => 'sandro@embuncafe.com',
                'password' => $password,
                'role' => 'Owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Embun',
                'username' => 'admin_embun',
                'email' => 'admin@embuncafe.com',
                'password' => $password,
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barista',
                'username' => 'staff_barista',
                'email' => 'barista@embuncafe.com',
                'password' => $password,
                'role' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gavra',
                'username' => 'gavra',
                'email' => 'gavra@embuncafe.com',
                'password' => $password,
                'role' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer 01',
                'username' => 'customer_01',
                'email' => 'cust01@embuncafe.com',
                'password' => $password,
                'role' => 'Customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
