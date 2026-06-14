<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = [
            ['customer_name' => 'Budi Santoso', 'phone_number' => '081234567890', 'reservation_date' => Carbon::tomorrow()->setTime(15, 0), 'pax' => 4, 'status' => 'Pending', 'notes' => 'Dekat jendela'],
            ['customer_name' => 'Siti Aminah', 'phone_number' => '082345678901', 'reservation_date' => Carbon::tomorrow()->setTime(19, 0), 'pax' => 2, 'status' => 'Confirmed', 'notes' => ''],
            ['customer_name' => 'Joko Widodo', 'phone_number' => '083456789012', 'reservation_date' => Carbon::now()->addDays(2)->setTime(18, 30), 'pax' => 6, 'status' => 'Pending', 'notes' => 'Tolong gabungkan meja'],
            ['customer_name' => 'Anya Geraldine', 'phone_number' => '084567890123', 'reservation_date' => Carbon::now()->addDays(3)->setTime(20, 0), 'pax' => 2, 'status' => 'Confirmed', 'notes' => 'Smoking area'],
            ['customer_name' => 'Deddy Corbuzier', 'phone_number' => '085678901234', 'reservation_date' => Carbon::yesterday()->setTime(14, 0), 'pax' => 3, 'status' => 'Rejected', 'notes' => 'Meeting'],
        ];

        foreach ($reservations as $res) {
            Reservation::create($res);
        }
    }
}
