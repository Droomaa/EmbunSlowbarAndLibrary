<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

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
}
