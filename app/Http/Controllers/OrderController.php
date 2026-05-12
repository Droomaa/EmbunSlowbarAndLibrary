<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Order::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'nullable|string',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'table_number' => $request->table_number,
                'total_price' => 0,
            ]);

            $grandTotal = 0;

            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $subtotal = $menu->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);

                $grandTotal += $subtotal;
            }

            $order->update(['total_price' => $grandTotal]);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->order_id,
                'total_price' => $grandTotal
            ], 201);
        });
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:Pending,Processing,Completed,Canceled',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status pesanan diupdate menjadi ' . $order->status
        ], 200);
    }
}
