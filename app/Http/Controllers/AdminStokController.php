<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class AdminStokController extends Controller
{
    public function index(): JsonResponse
    {
        $inventories = Inventory::all();

        $habis = $inventories->where('quantity', '<=', 0)->count();
        $hampirHabis = $inventories->where('quantity', '>', 0)->where('quantity', '<=', 10)->count();
        $aman = $inventories->where('quantity', '>', 10)->count();
        $total = $inventories->count();

        return response()->json([
            'message' => 'Berhasil mengambil data laporan stok',
            'data' => [
                'habis' => $habis,
                'hampir_habis' => $hampirHabis,
                'aman' => $aman,
                'total' => $total,
                'items' => $inventories
            ]
        ], 200);
    }
}
