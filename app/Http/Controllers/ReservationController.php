<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Reservation::all(), 200);
    }

    public function store(Request $request): JsonResponse
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
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibuat! Silakan tunggu konfirmasi dari staff kami.',
            'data' => $reservation
        ], 201);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed',
        ]);

        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
        }

        $reservation->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status reservasi berhasil diupdate menjadi ' . $request->status,
            'data' => $reservation
        ], 200);
    }
}
