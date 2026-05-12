<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Public: Membuat reservasi baru (Guest)
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'reservation_date' => 'required|date',
            'pax' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::create([
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'reservation_date' => $request->reservation_date,
            'pax' => $request->pax,
            // Status otomatis 'Pending' dari database
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibuat! Silakan tunggu konfirmasi dari staff kami.',
            'data' => $reservation
        ], 201);
    }

    // Protected: Verifikasi / Update Status Reservasi (Khusus Admin & Staff)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed', // Status yang diperbolehkan
        ]);

        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
        }

        $reservation->status = $request->status;
        $reservation->save();

        return response()->json([
            'message' => 'Status reservasi berhasil diupdate menjadi ' . $request->status,
            'data' => $reservation
        ], 200);
    }
}
