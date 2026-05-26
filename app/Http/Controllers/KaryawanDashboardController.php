<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanDashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
            // 1. Hitung Pesanan Pending & Ambil 5 Teratas
            $pendingCount = DB::table('orders')->where('status', 'Pending')->count();
            $pendingOrders = DB::table('orders')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'asc') // Yang paling lama menunggu diutamakan
                ->limit(5)
                ->get();

            foreach ($pendingOrders as $order) {
                $items = DB::table('order_items')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('order_items.order_id', $order->order_id)
                    ->pluck('menus.menuName')
                    ->implode(', ');
                $order->items_text = $items ?: 'Item tidak diketahui';
            }

            // 2. Hitung Stok Menipis (Misal batas kritis adalah <= 10)
            $lowStockCount = DB::table('inventories')->where('quantity', '<=', 10)->count();

            // 3. Reservasi Hari Ini (Aman dari error jika tabel belum eksis)
            $reservationsCount = 0;
            $todayReservations = [];
            try {
                $reservationsCount = DB::table('reservations')
                    ->whereDate('reservation_date', now()->toDateString())
                    ->count();
                
                $todayReservations = DB::table('reservations')
                    ->whereDate('reservation_date', now()->toDateString())
                    ->where('status', 'Pending')
                    ->orderBy('reservation_time', 'asc')
                    ->limit(3)
                    ->get();
            } catch (\Exception $e) {
                // Abaikan jika tabel reservations belum ada di database
            }

            return response()->json([
                'user' => [
                    'name' => 'Budi Santoso', // Nanti bisa diganti Auth::user()->name
                    'role' => 'KITCHEN MANAGER',
                    'initial' => 'BS'
                ],
                'metrics' => [
                    'pending_orders' => $pendingCount,
                    'low_stock' => $lowStockCount,
                    'today_reservations' => $reservationsCount
                ],
                'online_orders' => $pendingOrders,
                'reservations' => $todayReservations
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
