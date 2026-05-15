<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class AdminTransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $today = now()->toDateString();

        $ordersToday = Order::whereDate('created_at', $today)->get();
        
        $totalPenjualanHariIni = $ordersToday->sum('total_price');
        $jumlahTransaksi = $ordersToday->count();
        $rataKeranjang = $jumlahTransaksi > 0 ? $totalPenjualanHariIni / $jumlahTransaksi : 0;

        $allTransactions = Order::with('items.menu')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Berhasil mengambil data riwayat transaksi',
            'data' => [
                'total_penjualan_hari_ini' => $totalPenjualanHariIni,
                'jumlah_transaksi' => $jumlahTransaksi,
                'rata_keranjang' => $rataKeranjang,
                'transactions' => $allTransactions
            ]
        ], 200);
    }
}
