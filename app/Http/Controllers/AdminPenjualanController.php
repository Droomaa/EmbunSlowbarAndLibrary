<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
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
}
