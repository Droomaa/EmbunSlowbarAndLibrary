<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Tambahkan ini untuk mengambil semua data pesanan
    public function index()
    {
        return response()->json(Order::all());
    }
    // 1. PUBLIC: Pelanggan membuat pesanan
    // 1. PUBLIC: Pelanggan membuat pesanan
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'nullable|string',
            'items' => 'required|array',
            // PERBAIKAN 1: Cari referensi ke kolom 'id', bukan 'menu_id'
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Buat Order Induk (Total harga 0 dulu)
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'total_price' => 0,
        ]);

        $grandTotal = 0;

        // Looping item yang dipesan
        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            $subtotal = $menu->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->order_id,
                // PERBAIKAN 2: Ambil ID menggunakan $menu->id, bukan $menu->menu_id
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ]);

            $grandTotal += $subtotal;
        }

        // Update total harga di tabel Order
        $order->update(['total_price' => $grandTotal]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->order_id,
            'total_price' => $grandTotal
        ], 201);
    }

    // 2. PROTECTED: Karyawan / Admin mengubah status pesanan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Processing,Completed,Canceled',
        ]);

        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);

        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Status pesanan diupdate menjadi ' . $order->status], 200);
    }
}
