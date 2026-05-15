<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Hitung Total Penjualan & Transaksi
        $totalPenjualan = Order::sum('total_price');
        $totalTransaksi = Order::count();

        // 2. Cek Stok Kritis (Misal: batas kritis adalah di bawah 10 unit/gram/liter)
        $stokKritis = Inventory::where('stock_quantity', '<=', 10)->count();

        // 3. Ambil 5 Transaksi Terbaru
        $transaksiTerbaru = Order::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'message' => 'Berhasil mengambil data dashboard admin',
            'data' => [
                'total_penjualan' => $totalPenjualan,
                'total_transaksi' => $totalTransaksi,
                'stok_kritis' => $stokKritis,
                'recent_transactions' => $transaksiTerbaru
            ]
        ], 200);
    }
}
