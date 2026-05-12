<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Inventory::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $item = Inventory::create($request->all());

        return response()->json([
            'message' => 'Bahan berhasil ditambahkan ke inventaris!',
            'data' => $item
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $item = Inventory::find($id);

        if (!$item) {
            return response()->json(['message' => 'Bahan tidak ditemukan'], 404);
        }

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json(['message' => 'Stok bahan berhasil diupdate!'], 200);
    }

    public function destroy($id): JsonResponse
    {
        $item = Inventory::find($id);

        if (!$item) {
            return response()->json(['message' => 'Bahan tidak ditemukan'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Bahan berhasil dihapus!'], 200);
    }
}
