<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

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
            // 👈 2. Logika pencarian cerdas (Mencari ID atau Nama)
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

    public function getLaporanStokData(Request $request)
    {
        try {
            $kategori = $request->input('kategori', 'Semua');
            $status = $request->input('status', 'all');

            // 1. Query Dasar ke Tabel Inventori
            $query = DB::table('inventories');

            // Jika Kategori bukan "Semua", filter berdasarkan kategori
            if ($kategori !== 'Semua') {
                $query->where('category', $kategori); 
            }

            $inventories = $query->orderBy('item_name', 'asc')->get();

            // 2. Siapkan Wadah untuk Metrik (Kartu di Atas)
            $habis = 0;
            $hampirHabis = 0;
            $aman = 0;
            $totalItem = $inventories->count();

            $tableData = [];

            // 3. Looping untuk menentukan status dan menyusun data tabel
            foreach ($inventories as $item) {
                $qty = (float) $item->quantity;
                // Asumsikan batas minimum 10 jika kolom minimum_stock tidak ada
                $minStock = isset($item->minimum_stock) ? (float) $item->minimum_stock : 10;

                // Tentukan Status Barang
                if ($qty <= 0) {
                    $itemStatus = 'Habis';
                    $habis++;
                } elseif ($qty <= ($minStock * 5)) { // Contoh: Jika min 10, maka < 50 itu hampir habis
                    $itemStatus = 'Hampir Habis';
                    $hampirHabis++;
                } else {
                    $itemStatus = 'Aman';
                    $aman++;
                }

                // Jika filter status aktif, buang barang yang tidak cocok dari tabel
                if ($status !== 'all' && $status !== $itemStatus) {
                    continue; 
                }

                $tableData[] = [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'category' => isset($item->category) ? $item->category : 'Bahan/Material',
                    'quantity' => $qty,
                    'unit' => isset($item->unit) ? $item->unit : 'gram',
                    'minimum_stock' => $minStock,
                    'status' => $itemStatus,
                    'updated_at' => \Carbon\Carbon::parse($item->updated_at ?? now())->translatedFormat('d M, H:i')
                ];
            }

            return response()->json([
                'metrics' => [
                    'habis' => $habis,
                    'hampir_habis' => $hampirHabis,
                    'aman' => $aman,
                    'total' => $totalItem
                ],
                'table_data' => $tableData
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
