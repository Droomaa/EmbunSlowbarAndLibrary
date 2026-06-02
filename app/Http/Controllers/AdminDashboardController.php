<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str; // <-- Kunci kecerdasan filter kita!

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Hitung Total Penjualan & Transaksi
        $totalPenjualan = Order::sum('total_price');
        $totalTransaksi = Order::count();

        // 2. Cek Stok Kritis (Misal: batas kritis adalah di bawah 10 unit/gram/liter)
        $stokKritis = Inventory::where('stock_quantity', '<=', 10)->count();

        // 3. Ambil 5 Transaksi Terbaru
        $transaksiTerbaru = Order::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'message' => 'Berhasil mengambil data dashboard admin',
            'data' => [
                'total_penjualan' => $totalPenjualan,
                'total_transaksi' => $totalTransaksi,
                'stok_kritis' => $stokKritis,
                'recent_transactions' => $transaksiTerbaru
            ]
        ], 200);
    }
    
    public function getAdminDashboardData()
    {
        try {
            $totalPenjualan = DB::table('orders')->where('status', 'Completed')->sum('total_price');
            $totalTransaksi = DB::table('orders')->count();

            $stokKritisList = DB::table('inventories')
                ->where('quantity', '<=', 500)
                ->orderBy('quantity', 'asc')
                ->get();
            $stokKritisCount = $stokKritisList->count();

            // Limit dinaikkan jadi 15 agar filter status di tabel terlihat hasilnya
            $transaksiTerbaru = DB::table('orders')
                ->orderBy('created_at', 'desc')
                ->limit(15)
                ->get();

            // Data Mingguan (7 Hari Terakhir)
            $grafikMingguan = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::today()->subDays($i);
                $dailySales = DB::table('orders')
                    ->where('status', 'Completed')
                    ->whereDate('created_at', $date)
                    ->sum('total_price');
                
                $grafikMingguan[] = [
                    'label' => $date->translatedFormat('D'), // Sen, Sel, Rab...
                    'total' => $dailySales
                ];
            }

            // Data Bulanan (6 Bulan Terakhir)
            $grafikBulanan = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = \Carbon\Carbon::today()->startOfMonth()->subMonths($i);
                $monthlySales = DB::table('orders')
                    ->where('status', 'Completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_price');
                
                $grafikBulanan[] = [
                    'label' => $month->translatedFormat('M'), // Jan, Feb, Mar...
                    'total' => $monthlySales
                ];
            }

            return response()->json([
                'total_penjualan' => $totalPenjualan,
                'total_transaksi' => $totalTransaksi,
                'stok_kritis_count' => $stokKritisCount,
                'stok_kritis_list' => $stokKritisList,
                'transaksi_terbaru' => $transaksiTerbaru,
                'grafik_mingguan' => $grafikMingguan,
                'grafik_bulanan' => $grafikBulanan // Kirim data bulanan ke frontend
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // 1. Tarik Data Transaksi (Dengan Filter & Metrik All-Time)
    public function getDataTransaksi(Request $request)
    {
        try {
            $dateFilter = $request->input('date'); 
            $statusFilter = $request->input('status'); 
            $limit = $request->input('limit', 10);
            $search = $request->input('search');

            // METRIK KESELURUHAN (ALL TIME)
            $totalPenjualan = DB::table('orders')->where('status', 'Completed')->sum('total_price');
            $jumlahTransaksi = DB::table('orders')->where('status', 'Completed')->count();
            $rataKeranjang = $jumlahTransaksi > 0 ? $totalPenjualan / $jumlahTransaksi : 0;

            // Produk Terlaris All Time
            $bestSeller = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('orders.status', 'Completed')
                ->select('menus.menuName', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('menus.id', 'menus.menuName')
                ->orderBy('total_sold', 'desc')
                ->first();

            // DATA TABEL (Sesuai Filter)
            $query = DB::table('orders');

            if ($dateFilter && $dateFilter !== 'all') {
                $query->whereDate('created_at', $dateFilter);
            }
            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            // Logika pencarian cerdas (Mencari ID atau Nama)
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'LIKE', "%{$search}%")
                      ->orWhere('customer_name', 'LIKE', "%{$search}%");
                });
            }
            $orders = $query->orderBy('created_at', 'desc')->limit($limit)->get();

            $tableData = [];
            foreach ($orders as $order) {
                // Ambil item untuk digabung jadi "2x Latte, 1x Americano"
                $items = DB::table('order_items')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('order_items.order_id', $order->order_id)
                    ->select('menus.menuName', 'order_items.quantity')
                    ->get();

                $itemsText = $items->map(function($item) {
                    return $item->quantity . 'x ' . $item->menuName;
                })->implode(', ');

                $tableData[] = [
                    'order_id' => $order->order_id,
                    'created_at' => $order->created_at,
                    'payment_method' => 'QRIS/Tunai', // Bisa disesuaikan kalau ada kolomnya
                    'items_text' => $itemsText ?: 'Item tidak diketahui',
                    'total_price' => $order->total_price,
                    'status' => $order->status
                ];
            }

            return response()->json([
                'metrics' => [
                    'total_penjualan' => $totalPenjualan,
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'rata_keranjang' => $rataKeranjang,
                    'produk_terlaris' => $bestSeller ? $bestSeller->menuName : '-'
                ],
                'table_data' => $tableData
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 2. Update Status Transaksi
    public function updateStatusTransaksi(Request $request, $id)
    {
        try {
            DB::table('orders')->where('order_id', $id)->update([
                'status' => $request->input('status'),
                'updated_at' => now()
            ]);
            return response()->json(['message' => 'Status berhasil diubah!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Hapus Transaksi & Itemnya
    public function deleteTransaksi($id)
    {
        DB::beginTransaction();
        try {
            // Hapus isi keranjangnya dulu (Foreign Key)
            DB::table('order_items')->where('order_id', $id)->delete();
            // Baru hapus pesanan utamanya
            DB::table('orders')->where('order_id', $id)->delete();
            
            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil dihapus permanen!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 4. API LAPORAN STOK (VERSI WILDCARD JSON & TABLE_DATA FIX)
    // =========================================================================
    public function getLaporanStokData(Request $request)
    {
        try {
            $kategori = trim(strtolower($request->query('kategori', 'all')));
            $status = trim(strtolower($request->query('status', 'all')));
            $search = trim(strtolower($request->query('search', ''))); 

            $items = DB::table('inventories')->get();

            // Ubah menjadi Collection
            $filteredItems = collect($items);

            // 1. FILTER PENCARIAN (Search Bar Atas)
            if ($search !== '') {
                $filteredItems = $filteredItems->filter(function($item) use ($search) {
                    // Cerdas: Jadikan seluruh data baris MySQL sebagai string, lalu cari katanya
                    return Str::contains(strtolower(json_encode($item)), $search);
                });
            }

            // 2. FILTER KATEGORI (Mendeteksi dari seluruh properti baris MySQL)
            if ($kategori !== 'all' && $kategori !== 'semua' && $kategori !== '') {
                $filteredItems = $filteredItems->filter(function($item) use ($kategori) {
                    $itemString = strtolower(json_encode($item));
                    
                    if (Str::contains($kategori, 'kopi')) {
                        return Str::contains($itemString, ['kopi', 'espresso', 'bean', 'robusta', 'arabica']);
                    }
                    if (Str::contains($kategori, ['susu', 'krim'])) {
                        return Str::contains($itemString, ['susu', 'milk', 'krim', 'cream', 'oat', 'dairy']);
                    }

                    return Str::contains($itemString, $kategori);
                });
            }

            // 3. FILTER STATUS (Kalkulasi Kritis)
            if ($status !== 'all' && $status !== 'semua status' && $status !== '') {
                $filteredItems = $filteredItems->filter(function($item) use ($status) {
                    // Mencari secara dinamis nama kolom jumlah dan batas minimum
                    $qty = (float) ($item->quantity ?? $item->stock_quantity ?? $item->stok ?? 0);
                    $min = (float) ($item->minimum_stock ?? $item->batas_minimum ?? $item->min_stock ?? 10);
                    
                    $itemStatus = 'aman';
                    if ($qty <= 0) $itemStatus = 'habis';
                    else if ($qty <= $min) $itemStatus = 'menipis';

                    if (Str::contains($status, ['habis', 'critical'])) return $itemStatus === 'habis';
                    if (Str::contains($status, ['menipis', 'low'])) return $itemStatus === 'menipis';
                    if (Str::contains($status, ['aman', 'safe'])) return $itemStatus === 'aman';
                    
                    return true; 
                });
            }

            // Hitung Metrik dari data Asli
            $habis = 0; $menipis = 0; $aman = 0;
            foreach ($items as $inv) {
                $q = (float) ($inv->quantity ?? $inv->stock_quantity ?? $inv->stok ?? 0);
                $m = (float) ($inv->minimum_stock ?? $inv->batas_minimum ?? $inv->min_stock ?? 10);
                if ($q <= 0) $habis++;
                else if ($q <= $m) $menipis++;
                else $aman++;
            }

            return response()->json([
                // 🌟 FIX UTAMA: MENGGUNAKAN KEY 'table_data' AGAR TERBACA OLEH FRONTEND
                'table_data' => $filteredItems->values()->all(),
                'metrics' => [
                    'habis' => $habis, 
                    'hampir_habis' => $menipis, 
                    'aman' => $aman, 
                    'total' => $items->count()
                ]
            ], 200);

        } catch (\Throwable $e) { 
            return response()->json(['error' => 'Gagal memproses filter: ' . $e->getMessage()], 500);
        }
    }
}
