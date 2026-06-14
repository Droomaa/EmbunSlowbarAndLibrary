<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StaffShift;
use Illuminate\Support\Facades\DB;

class KaryawanKasirController extends Controller
{
    // API: Ambil Semua Data Menu
    public function getMenus()
    {
        $menus = Menu::all();
        return response()->json(['data' => $menus], 200);
    }

    // API: Proses Checkout (Kasir Offline & QR Menu)
    public function checkout(Request $request)
    {
        $user = $request->user();
        $staffShiftId = null;

        if ($user && $user->role === 'Staff') {
            $activeShift = StaffShift::where('user_id', $user->id)
                ->where('status', 'Active')
                ->first();

            if (!$activeShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus memulai shift sebelum membuat transaksi.'
                ], 422);
            }

            $staffShiftId = $activeShift->id;
        }

        try {
            DB::beginTransaction();

            $calculatedSubtotal = 0;
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $calculatedSubtotal += $item['subtotal'];
                }
            }
            $tax = $calculatedSubtotal * 0.10;
            $finalTotalPrice = $calculatedSubtotal + $tax;
            
            // Bikin record Order (Struk)
            $order = new Order();
            $order->customer_name = $request->input('customer_name', 'Walk-in Customer');
            $order->staff_shift_id = $staffShiftId;
            
            // --- DATA MEJA & PEMBAYARAN ---
            $order->table_number = $request->input('table_number');
            $order->order_type = $request->input('order_type', 'Dine In');
            $order->payment_method = $request->input('payment_method', 'Tunai');
            $order->status = 'Pending';
            // ------------------------------------------------------
            
            $order->total_price = $finalTotalPrice;
            $order->save();

            // Looping untuk nyimpan detail item ke tabel order_items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->order_id ?? $order->id;
                    $orderItem->menu_id = $item['menu_id'];
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->subtotal = $item['subtotal'];
                    $orderItem->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Yeay! Transaksi berhasil disimpan.',
                'order_id' => $order->order_id ?? $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal memproses kasir: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
        }
    }
    
    // API: Ambil Pesanan Offline (Dine In & Takeaway)
    public function getOfflineOrders()
    {
        try {
            $orders = \App\Models\Order::with('items.menu')
                        ->whereIn('order_type', ['Dine In', 'Takeaway'])
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function ($order) {
                            return [
                                'order_id' => $order->order_id,
                                'customer_name' => $order->customer_name,
                                'table_number' => $order->table_number,
                                'total_price' => $order->total_price,
                                'status' => $order->status,
                                'payment_method' => $order->payment_method,
                                'order_type' => $order->order_type,
                                'created_at' => $order->created_at,
                                'items' => $order->items->map(function ($item) {
                                    return [
                                        'menuName' => $item->menu ? $item->menu->menuName : 'Menu Terhapus',
                                        'quantity' => $item->quantity,
                                        'subtotal' => $item->subtotal
                                    ];
                                })
                            ];
                        });
                        
            return response()->json(['data' => $orders], 200);

        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data offline order: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
        }
    }
}
