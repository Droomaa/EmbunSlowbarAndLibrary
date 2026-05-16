<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class KaryawanKasirController extends Controller
{
    public function getMenus()
    {
        $menus = Menu::all();
        return response()->json(['data' => $menus], 200);
    }

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
            
            // --- BUKA COMMENT UNTUK MENYIMPAN MEJA & PEMBAYARAN ---
            $order->table_number = $request->input('table_number');
            $order->order_type = $request->input('order_type', 'Dine In');
            // $order->payment_method = $request->input('payment_method', 'Tunai'); // Default Tunai, tapi dari QR akan ngirim 'QRIS'
            $order->status = 'Pending'; // Kalau dari QR, statusnya 'Pending' nunggu dikonfirmasi kasir/dapur
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
        $orders = DB::table('orders')
                    ->whereIn('order_type', ['Dine In', 'Takeaway'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
        return response()->json(['data' => $orders], 200);
    }

    // API: Update Status Pesanan (Bisa dari semua page Karyawan)
    public function updateOrderStatus(Request $request, $id)
    {
        DB::table('orders')->where('order_id', $id)->update([
            'status' => $request->input('status')
        ]);
        return response()->json(['message' => 'Status berhasil diubah!']);
    }
}
