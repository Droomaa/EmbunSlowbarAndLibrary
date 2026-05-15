<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class KaryawanReservasiController extends Controller
{
    public function index(): JsonResponse
    {
        $reservations = Reservation::orderBy('reservation_date', 'asc')->get();

        $pending = $reservations->filter(fn($res) => strtolower($res->status) === 'pending')->count();
        $confirmed = $reservations->filter(fn($res) => strtolower($res->status) === 'confirmed')->count();
        
        $availableTables = 8;
        $waitlist = 3;

        return response()->json([
            'message' => 'Berhasil mengambil data reservasi',
            'data' => [
                'pending' => $pending,
                'confirmed' => $confirmed,
                'available_tables' => $availableTables,
                'waitlist' => $waitlist,
                'reservations' => $reservations
            ]
        ], 200);
    }
}
