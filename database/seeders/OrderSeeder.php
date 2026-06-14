<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            ['customer_name' => 'Rio', 'table_number' => '1', 'total_price' => 26000, 'status' => 'Completed', 'payment_method' => 'Tunai', 'order_type' => 'Dine In', 'created_at' => Carbon::today()->subHours(5)],
            ['customer_name' => 'Diana', 'table_number' => '2', 'total_price' => 38000, 'status' => 'Completed', 'payment_method' => 'QRIS', 'order_type' => 'Dine In', 'created_at' => Carbon::today()->subHours(4)],
            ['customer_name' => 'Bagas', 'table_number' => null, 'total_price' => 24000, 'status' => 'Completed', 'payment_method' => 'Tunai', 'order_type' => 'Takeaway', 'created_at' => Carbon::today()->subHours(3)],
            ['customer_name' => 'Ayu', 'table_number' => '3', 'total_price' => 45000, 'status' => 'Processing', 'payment_method' => 'QRIS', 'order_type' => 'Dine In', 'created_at' => Carbon::now()->subMinutes(30)],
            ['customer_name' => 'Rangga', 'table_number' => '4', 'total_price' => 16000, 'status' => 'Pending', 'payment_method' => 'Tunai', 'order_type' => 'Dine In', 'created_at' => Carbon::now()->subMinutes(5)],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
