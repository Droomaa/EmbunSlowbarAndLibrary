<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class KaryawanReservasiController extends Controller
{
    public function index(): JsonResponse
    {
        // PERBAIKAN 1: Hapus order by reservation_time karena tanggal & jam sudah gabung di reservation_date
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

    public function updateStatus(Request $request, $id): JsonResponse
    {
        // PERBAIKAN 2: Pakai 'where' mencocokkan dengan nama kolom 'reservation_id' di databasemu
        $reservation = Reservation::where('reservation_id', $id)->first();

        if (!$reservation) {
            return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
        }

        $reservation->status = $request->input('status');
        $reservation->save();
        
        return response()->json(['message' => 'Status reservasi berhasil diperbarui!'], 200);
    }
}
