<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Reservation;

class KaryawanDashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $pendingCount = 0;
        $pendingOrders = [];
        $lowStockCount = 0;
        $reservationsCount = 0;
        $todayReservations = [];

        // A. BLOK PESANAN (ORDERS)
        try {
            $pendingCount = DB::table('orders')->where('status', 'Pending')->count();
            $pendingOrders = DB::table('orders')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'asc')
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
        } catch (\Exception $e) { }

        // B. BLOK INVENTARIS (STOK)
        try {
            $lowStockCount = DB::table('inventories')->where('quantity', '<=', 10)->count();
        } catch (\Exception $e) { }

        // C. BLOK RESERVASI
        try {
            $today = Carbon::now('Asia/Jakarta')->toDateString();
            
            $reservationsCount = Reservation::whereDate('reservation_date', $today)->count();
            
            $todayReservations = Reservation::whereDate('reservation_date', $today)
                ->orderBy('reservation_date', 'asc')
                ->limit(3)
                ->get();
        } catch (\Exception $e) { }

        // D. BLOK DATA USER LOGIN (POIN KE-6)
        // Mengambil data user berdasarkan Token Sanctum yang dikirim dari Frontend
        $user = auth('sanctum')->user(); 
        
        $userName = $user ? $user->name : 'Karyawan Embun';
        $userRole = $user ? strtoupper($user->role) : 'KITCHEN TEAM';
        
        // Logika cerdas pembuat Inisial (contoh: "Budi Santoso" -> "BS")
        $words = explode(' ', $userName);
        $initial = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initial .= strtoupper($w[0]);
        }

        return response()->json([
            'user' => [
                'name' => $userName, 
                'role' => $userRole,
                'initial' => $initial
            ],
            'metrics' => [
                'pending_orders' => $pendingCount,
                'low_stock' => $lowStockCount,
                'today_reservations' => $reservationsCount
            ],
            'online_orders' => $pendingOrders,
            'reservations' => $todayReservations
        ], 200);
    }
}