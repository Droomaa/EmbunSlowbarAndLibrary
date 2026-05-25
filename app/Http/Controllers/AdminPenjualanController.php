<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AdminPenjualanController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('items.menu')->orderBy('created_at', 'desc')->get();

        $totalPenjualan = $orders->sum('total_price');
        $totalTransaksi = $orders->count();
        $totalPelanggan = $orders->pluck('customer_name')->unique()->count();
        
        $dibatalkan = $orders->where('total_price', 0)->count();

        return response()->json([
            'message' => 'Berhasil mengambil data laporan penjualan',
            'data' => [
                'total_penjualan' => $totalPenjualan,
                'total_transaksi' => $totalTransaksi,
                'total_pelanggan' => $totalPelanggan,
                'dibatalkan' => $dibatalkan,
                'laporan' => $orders
            ]
        ], 200);
    }
    public function getLaporanPenjualan(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // 1. Query Dasar Orders
            $query = DB::table('orders');

            // Jika ada filter tanggal, terapkan ke query
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // 2. Hitung Metrik Atas
            $totalPenjualan = $orders->where('status', 'Completed')->sum('total_price');
            $totalTransaksi = $orders->where('status', 'Completed')->count();
            
            // Total pelanggan unik (menghitung nama yang berbeda)
            $totalPelanggan = $orders->where('status', 'Completed')->pluck('customer_name')->unique()->count();
            
            $dibatalkan = $orders->where('status', 'Reject')->count();

            // 3. Cari Item Paling Laris (Best Seller)
            $bestSellerQuery = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('orders.status', 'Completed');
            
            if ($startDate && $endDate) {
                $bestSellerQuery->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $bestSeller = $bestSellerQuery->select('menus.menuName', 'menus.category', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('menus.id', 'menus.menuName', 'menus.category')
                ->orderBy('total_sold', 'desc')
                ->first();

            // 4. Siapkan Data Tabel (Ambil nama-nama menu yang dipesan)
            $tableData = [];
            foreach($orders as $order) {
                // Ambil daftar menu untuk pesanan ini
                $items = DB::table('order_items')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('order_items.order_id', $order->order_id)
                    ->pluck('menus.menuName')
                    ->implode(', '); // Gabungkan nama menu dengan koma
                
                $tableData[] = [
                    'order_id' => $order->order_id,
                    'created_at' => $order->created_at,
                    'customer_name' => $order->customer_name,
                    'items_text' => $items ?: '-', // Teks "Embun Ori Latte, Americano" dst
                    'total_price' => $order->total_price,
                    'status' => $order->status
                ];
            }

            return response()->json([
                'metrics' => [
                    'total_penjualan' => $totalPenjualan,
                    'total_transaksi' => $totalTransaksi,
                    'total_pelanggan' => $totalPelanggan,
                    'dibatalkan' => $dibatalkan
                ],
                'best_seller' => $bestSeller,
                'table_data' => $tableData
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
