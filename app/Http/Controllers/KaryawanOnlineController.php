<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KaryawanOnlineController extends Controller
{
    public function index(): JsonResponse
    {
        $today = now()->toDateString();

        $orders = Order::with('items.menu')
            // ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data pesanan online',
            'data' => [
                'total_pesanan' => $orders->count(),
                'pesanan' => $orders
            ]
        ], 200);
    }

    // Fungsi untuk mendapatkan semua pesanan yang belum selesai
    public function getOnlineOrders()
    {
        try {
            // Ambil semua pesanan dengan status 'Pending'
            $pendingOrders = DB::table('orders')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'asc') // Antrian dari yang terlama
                ->get();

            // Hitung total semua pesanan HARI INI (baik selesai maupun pending)
            $totalToday = DB::table('orders')
                ->whereDate('created_at', now()->toDateString())
                ->count();

            // Gabungkan detail item untuk masing-masing order
            foreach ($pendingOrders as $order) {
                $items = DB::table('order_items')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('order_items.order_id', $order->order_id)
                    ->select('menus.menuName', 'order_items.quantity', 'order_items.subtotal')
                    ->get();
                
                $order->items = $items;
            }

            return response()->json([
                'data' => $pendingOrders,
                'total_today' => $totalToday
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error get online orders: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
        }
    }

    // =========================================================================
    // API UNTUK UPDATE STATUS PESANAN DAN POTONG STOK OTOMATIS
    // =========================================================================
    public function updateOrderStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 1. Cari data pesanan (Hanya gunakan kolom order_id agar tidak error!)
            $order = DB::table('orders')->where('order_id', $id)->first();
            
            if (!$order) {
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            $newStatus = $request->input('status');
            $oldStatus = $order->status;

            // 2. 🌟 LOGIKA PEMOTONGAN STOK GUDANG 📉📦
            // Jika pesanan diubah menjadi 'Completed' DAN sebelumnya belum 'Completed'
            if ($newStatus === 'Completed' && $oldStatus !== 'Completed') {
                
                // Ambil daftar minuman/makanan yang dibeli
                $orderItems = DB::table('order_items')->where('order_id', $id)->get();

                foreach ($orderItems as $item) {
                    // Ambil resep untuk menu ini
                    $ingredients = DB::table('menu_ingredients')->where('menu_id', $item->menu_id)->get();

                    foreach ($ingredients as $ing) {
                        // Rumus: Takaran Resep x Jumlah Pesanan
                        $totalDeduction = ($ing->quantity_needed) * ($item->quantity);

                        // Kurangi stok di tabel inventories
                        DB::table('inventories')
                            ->where('id', $ing->inventory_id)
                            ->decrement('quantity', $totalDeduction);
                    }
                }
            }

            // 3. Update status pesanan menjadi Selesai (Hanya gunakan order_id)
            DB::table('orders')->where('order_id', $id)->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json(['message' => 'Status pesanan berhasil diubah dan stok bahan otomatis dikurangi!']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal mengubah status: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
        }
    }
}
