<?php

namespace App\Http\Controllers;

use App\Models\StaffShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class StaffShiftController extends Controller
{
    public function current(Request $request)
    {
        $shift = StaffShift::where('user_id', $request->user()->id)
            ->where('status', 'Active')
            ->first();

        if ($shift) {
            $shift->current_orders_count = $shift->orders()->count();
            $shift->current_sales_total = $shift->orders()->where('status', 'Completed')->sum('total_price');

            return response()->json([
                'success' => true,
                'data' => $shift
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Belum ada shift aktif.'
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'check_in_note' => 'nullable|string|max:255',
            'opening_cash' => 'nullable|numeric|min:0'
        ]);

        try {
            $shift = DB::transaction(function () use ($request) {
                // Gunakan lockForUpdate untuk mencegah double check-in
                $activeShift = StaffShift::where('user_id', $request->user()->id)
                    ->where('status', 'Active')
                    ->lockForUpdate()
                    ->first();

                if ($activeShift) {
                    throw ValidationException::withMessages([
                        'shift' => 'Anda masih memiliki shift aktif.'
                    ]);
                }

                return StaffShift::create([
                    'user_id' => $request->user()->id,
                    'shift_date' => Carbon::today(),
                    'started_at' => Carbon::now(),
                    'status' => 'Active',
                    'check_in_note' => $request->check_in_note,
                    'opening_cash' => $request->opening_cash,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil dimulai.',
                'data' => $shift
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'check_out_note' => 'nullable|string|max:255',
            'closing_cash' => 'nullable|numeric|min:0'
        ]);

        try {
            $shift = DB::transaction(function () use ($request) {
                $activeShift = StaffShift::where('user_id', $request->user()->id)
                    ->where('status', 'Active')
                    ->lockForUpdate()
                    ->first();

                if (!$activeShift) {
                    throw ValidationException::withMessages([
                        'shift' => 'Tidak ada shift aktif untuk diakhiri.'
                    ]);
                }

                $now = Carbon::now();
                $startedAt = Carbon::parse($activeShift->started_at);
                $durationMinutes = $startedAt->diffInMinutes($now);

                // Hitung dari order yang terhubung ke shift ini
                $totalOrders = $activeShift->orders()->count();
                $totalSales = $activeShift->orders()->where('status', 'Completed')->sum('total_price');

                $activeShift->update([
                    'ended_at' => $now,
                    'status' => 'Completed',
                    'check_out_note' => $request->check_out_note,
                    'closing_cash' => $request->closing_cash,
                    'duration_minutes' => $durationMinutes,
                    'total_orders' => $totalOrders,
                    'total_sales' => $totalSales,
                ]);

                return $activeShift;
            });

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil diakhiri.',
                'data' => $shift
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function history(Request $request)
    {
        $query = StaffShift::where('user_id', $request->user()->id)
            ->orderBy('started_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('shift_date', [$request->start_date, $request->end_date]);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }
}
