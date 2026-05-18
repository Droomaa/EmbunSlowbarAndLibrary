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

    // API PUBLIK: Menyimpan data reservasi dari Form Customer
    public function storeCustomerReservation(Request $request)
    {
        // Karena di database kamu kolom reservation_date bertipe DATETIME,
        // kita gabungkan input tanggal dan jam dari customer jadi satu format yang pas.
        $datetime = $request->input('tanggal') . ' ' . $request->input('jam') . ':00';

        \App\Models\Reservation::insert([
            'customer_name' => $request->input('nama'),
            'phone_number' => $request->input('telepon'),
            'reservation_date' => $datetime,
            'pax' => $request->input('pax'),
            'notes' => $request->input('notes', 'Tidak ada catatan'),
            'status' => 'Pending', // Default status selalu Pending
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kembalikan ke halaman form dengan pesan sukses
        return back()->with('success', '🎉 Hore! Permintaan reservasi kamu berhasil dikirim. Silakan tunggu konfirmasi dari tim kami ya!');
    }
}
