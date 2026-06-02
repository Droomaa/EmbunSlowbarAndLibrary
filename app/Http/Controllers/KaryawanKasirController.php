<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
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
            return response()->json(['message' => 'Waduh, gagal menyimpan transaksi: ' . $e->getMessage()], 500);
        }
    }
    
    // API: Ambil Pesanan Offline (Dine In & Takeaway)
    public function getOfflineOrders()
    {
        try {
            // 1. Ambil data pesanan utama
            $orders = DB::table('orders')
                        ->whereIn('order_type', ['Dine In', 'Takeaway'])
                        ->orderBy('created_at', 'desc')
                        ->get();
            
            // 2. Ambil detail item untuk masing-masing pesanan
            foreach ($orders as $order) {
                // Pastikan order_id yang dipakai akurat
                $orderId = $order->order_id ?? $order->id; 

                $items = DB::table('order_items')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('order_items.order_id', $orderId)
                    // HANYA MENGAMBIL menuName agar tidak terjadi error SQL "Column not found"
                    ->select('menus.menuName', 'order_items.quantity', 'order_items.subtotal')
                    ->get();
                
                $order->items = $items;
            }
                        
            return response()->json(['data' => $orders], 200);

        } catch (\Exception $e) {
            // Jika masih error, kita biarkan pesannya terkirim agar ketahuan salahnya di mana
            return response()->json(['error' => 'Gagal mengambil data: ' . $e->getMessage()], 500);
        }
    }
}
