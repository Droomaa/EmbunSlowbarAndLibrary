<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function getOverview()
    {
        // Memastikan zona waktu sesuai dengan Malang (WIB)
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // 1 & 2. Penjualan & Total Order Hari Ini
        $ordersToday = DB::table('orders')->whereDate('created_at', $today)->get();
        $todaySales = $ordersToday->sum('total_price');
        $totalOrders = $ordersToday->count();

        // 3 & 5. FIX: Ganti 'stock' menjadi 'quantity' sesuai kolom di database
        $inventories = DB::table('inventories')->get();
        
        // Batas running low diubah ke 500 (gram/ml) agar lebih realistis untuk kafe
        $outOfStock = $inventories->where('quantity', '<=', 0)->count();
        $runningLow = $inventories->where('quantity', '>', 0)->where('quantity', '<=', 500)->count();
        $available = $inventories->where('quantity', '>', 500)->count();
        
        $lowStockAlerts = $outOfStock + $runningLow;

        // 4. Reservasi Aktif Hari Ini
        $activeReservations = DB::table('reservations')
            ->whereDate('reservation_date', $today)
            ->whereIn('status', ['Pending', 'Confirmed', 'PENDING', 'CONFIRMED'])
            ->count();

        return response()->json([
            'today_sales' => $todaySales,
            'total_orders' => $totalOrders,
            'low_stock_alerts' => $lowStockAlerts,
            'active_reservations' => $activeReservations,
            'stock_status' => [
                'available' => $available,
                'running_low' => $runningLow,
                'out_of_stock' => $outOfStock
            ]
        ], 200);
    }
    // API: Ambil data untuk halaman Sales Reports
    public function getSalesReports()
    {
        // Ambil semua order, urutkan dari yang paling baru
        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $avgOrderValue,
            'transactions' => $orders
        ], 200);
    }

    // WEB: Fungsi Export Report ke CSV (Excel)
    public function exportSalesReports()
    {
        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        $filename = "Embun_Sales_Report_" . date('Y-m-d') . ".csv";

        // Buka file sementara di memori server
        $handle = fopen('php://output', 'w');

        // Beri tahu browser bahwa ini adalah file CSV untuk di-download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Tulis baris Header (Judul Kolom)
        fputcsv($handle, ['Tanggal', 'ID Transaksi', 'Nama Pelanggan', 'Tipe', 'Metode Pembayaran', 'Total Penjualan', 'Status']);

        // Tulis baris Data
        foreach ($orders as $order) {
            fputcsv($handle, [
                Carbon::parse($order->created_at)->format('Y-m-d H:i'),
                '#EB-' . $order->order_id,
                $order->customer_name,
                $order->order_type ?? 'Walk-in',
                $order->payment_method ?? 'Tunai',
                $order->total_price,
                $order->status
            ]);
        }

        fclose($handle);
        exit;
    }

    // API: Ambil data untuk halaman Stock Reports
    public function getStockReports()
    {
        // Ambil semua bahan baku, urutkan berdasarkan abjad
        $inventories = DB::table('inventories')->orderBy('item_name', 'asc')->get();
        
        return response()->json([
            'data' => $inventories
        ], 200);
    }

    // API: Ambil semua data akun
    public function getAccounts()
    {
        // Ambil semua data user
        $users = DB::table('users')->get();
        
        $totalUsers = $users->count();
        
        // Menghitung jumlah Admin/Owner. Sesuaikan nama kolom 'role' jika di databasemu berbeda.
        $adminRoles = $users->whereIn('role', ['admin', 'owner', 'Manager', 'Admin', 'Owner'])->count();
        
        return response()->json([
            'total_users' => $totalUsers,
            'active_now' => $totalUsers, // Anggap semua aktif (bisa disesuaikan jika ada sistem session)
            'admin_roles' => $adminRoles,
            'users' => $users
        ], 200);
    }

    // API: Simpan akun baru
    public function storeAccount(Request $request)
    {
        try {
            // Tangkap input dari frontend
            $inputUserEmail = $request->input('username'); 
            
            // Logika pintar: Kalau ada '@', ambil teks sebelum '@' sebagai username
            // Contoh: gavra@gmail.com -> username-nya 'gavra'
            $username = str_contains($inputUserEmail, '@') ? explode('@', $inputUserEmail)[0] : $inputUserEmail;

            DB::table('users')->insert([
                'name' => $request->input('name'),
                'username' => $username,           // Masuk ke kolom username
                'email' => $inputUserEmail,        // Masuk ke kolom email
                'password' => bcrypt($request->input('password')),
                'role' => $request->input('role'), // Mengirim 'Staff', 'Admin', atau 'Owner'
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Akun berhasil ditambahkan!'], 201);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // ==========================================
    // AREA MENU MANAGEMENT
    // ==========================================

    // 1. Ambil Data Menu & Metrik
    public function getMenus()
    {
        // Tarik semua menu
        $menus = DB::table('menus')->orderBy('category', 'asc')->orderBy('menuName', 'asc')->get();

        // Tarik Metrik Total Catalog
        $totalCatalog = $menus->count();

        // Tarik Metrik Stock Alerts (Bahan baku yang <= 500 gram/ml)
        $stockAlerts = DB::table('inventories')->where('quantity', '<=', 500)->count();

        // Tarik Metrik Most Popular (Menu paling banyak dibeli di tabel order_items)
        $popular = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.menuName', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('menus.id', 'menus.menuName')
            ->orderByDesc('total_sold')
            ->first();

        return response()->json([
            'menus' => $menus,
            'metrics' => [
                'total_catalog' => $totalCatalog,
                'stock_alerts' => $stockAlerts,
                'popular_name' => $popular ? $popular->menuName : 'Belum ada pesanan',
                'popular_sold' => $popular ? $popular->total_sold : 0
            ]
        ], 200);
    }

    // 2. Tambah Menu Baru
    public function storeMenu(Request $request)
    {
        try {
            DB::table('menus')->insert([
                'menuName' => $request->input('menuName'),
                'category' => $request->input('category'),
                'price' => $request->input('price'),
                'status' => $request->input('status', 'Available'),
                'description' => $request->input('description', ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Menu berhasil ditambahkan!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Edit Menu
    public function updateMenu(Request $request, $id)
    {
        try {
            DB::table('menus')->where('id', $id)->update([
                'menuName' => $request->input('menuName'),
                'category' => $request->input('category'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
                'description' => $request->input('description', ''),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Menu berhasil diperbarui!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 4. Hapus Menu
    public function deleteMenu($id)
    {
        try {
            DB::table('menus')->where('id', $id)->delete();
            return response()->json(['message' => 'Menu dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // ==========================================
    // AREA TRANSACTION MANAGEMENT
    // ==========================================

    // 1. Ambil Data Transaksi
    public function getTransactions()
    {
        $transactions = DB::table('orders')->orderBy('created_at', 'desc')->get();
        return response()->json(['transactions' => $transactions], 200);
    }

    // 2. Update Status Transaksi
    public function updateTransactionStatus(Request $request, $id)
    {
        try {
            DB::table('orders')->where('order_id', $id)->update([
                'status' => $request->input('status'),
                'updated_at' => now()
            ]);
            return response()->json(['message' => 'Status berhasil diubah!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Export CSV Transaksi
    public function exportTransactions()
    {
        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        $filename = "Embun_Transactions_" . date('Y-m-d') . ".csv";

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header Kolom CSV
        fputcsv($handle, ['ID Transaksi', 'Tanggal', 'Nama Pelanggan', 'Tipe Order', 'Total Penjualan', 'Metode Pembayaran', 'Status']);

        // Isi Data
        foreach ($orders as $order) {
            fputcsv($handle, [
                '#EMB-' . $order->order_id,
                Carbon::parse($order->created_at)->format('Y-m-d H:i'),
                $order->customer_name,
                $order->order_type ?? '-',
                $order->total_price,
                $order->payment_method ?? '-',
                $order->status
            ]);
        }

        fclose($handle);
        exit;
    }
}

//ALL
// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class OwnerDashboardController extends Controller
// {
//     public function getOverview()
//     {
//         // 1 & 2. Penjualan & Total Order
//         // Kita hitung SEMUA order yang ada di database sementara waktu. 
//         // Ini untuk menghindari bug zona waktu (UTC vs WIB) saat testing tengah malam.
//         $orders = DB::table('orders')->get();
//         $todaySales = $orders->sum('total_price'); 
//         $totalOrders = $orders->count();

//         // 3 & 5. Status Stok (Sudah sukses pakai 'quantity')
//         $inventories = DB::table('inventories')->get();
//         $outOfStock = $inventories->where('quantity', '<=', 0)->count();
//         $runningLow = $inventories->where('quantity', '>', 0)->where('quantity', '<=', 500)->count();
//         $available = $inventories->where('quantity', '>', 500)->count();
        
//         $lowStockAlerts = $outOfStock + $runningLow;

//         // 4. Reservasi Aktif
//         // Kita HAPUS filter tanggalnya. 
//         // Jadi SEMUA reservasi yang statusnya Pending/Confirmed akan terhitung sebagai "Active".
//         $activeReservations = DB::table('reservations')
//             ->whereIn('status', ['Pending', 'Confirmed', 'PENDING', 'CONFIRMED'])
//             ->count();

//         return response()->json([
//             'today_sales' => $todaySales,
//             'total_orders' => $totalOrders,
//             'low_stock_alerts' => $lowStockAlerts,
//             'active_reservations' => $activeReservations,
//             'stock_status' => [
//                 'available' => $available,
//                 'running_low' => $runningLow,
//                 'out_of_stock' => $outOfStock
//             ]
//         ], 200);
//     }
// }
