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
        'customer_name' => 'required|string',
        'items' => 'required|array',
        'items.*.menu_id' => 'required|exists:menus,id',
        'items.*.menu_variant_id' => 'nullable|exists:menu_variants,id',
        'items.*.add_ons' => 'nullable|array',
        'items.*.add_ons.*' => 'exists:add_ons,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    return DB::transaction(function () use ($request) {
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'total_price' => 0,
        ]);

        $grandTotal = 0;

        foreach ($request->items as $itemData) {
            $menu = Menu::findOrFail($itemData['menu_id']);
            
            // 1. Tentukan harga dasar (Pakai harga varian jika ada, jika tidak pakai harga menu)
            $basePrice = $menu->price;
            if (!empty($itemData['menu_variant_id'])) {
                $variant = MenuVariant::findOrFail($itemData['menu_variant_id']);
                $basePrice = $variant->price;
            }

            // 2. Buat item pesanan
            $orderItem = OrderItem::create([
                'order_id' => $order->order_id,
                'menu_id' => $menu->id,
                'menu_variant_id' => $itemData['menu_variant_id'] ?? null,
                'quantity' => $itemData['quantity'],
                'subtotal' => 0, // Akan diupdate setelah hitung add-ons
            ]);

            $itemSubtotal = $basePrice;

            // 3. Proses Add-ons jika ada
            if (!empty($itemData['add_ons'])) {
                foreach ($itemData['add_ons'] as $addOnId) {
                    $addOn = AddOn::findOrFail($addOnId);
                    
                    OrderItemAddon::create([
                        'order_item_id' => $orderItem->item_id,
                        'add_on_id' => $addOn->id,
                        'price' => $addOn->price,
                    ]);
                    
                    $itemSubtotal += $addOn->price;
                }
            }

            // Update subtotal per item (Harga dasar + Addons) * Qty
            $totalPerItem = $itemSubtotal * $itemData['quantity'];
            $orderItem->update(['subtotal' => $totalPerItem]);
            
            $grandTotal += $totalPerItem;
        }

        $order->update(['total_price' => $grandTotal]);

        return response()->json([
            'message' => 'Pesanan (dengan Add-ons) berhasil dibuat!',
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
