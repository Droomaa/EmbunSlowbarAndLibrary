<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanOnlineController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\KaryawanReservasiController;
use App\Http\Controllers\KaryawanKasirController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminStokController;
use App\Http\Controllers\AdminPenjualanController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

Route::get('/owner/dashboard/data', [OwnerDashboardController::class, 'getOverview']);
Route::get('/owner/reports/data', [OwnerDashboardController::class, 'getSalesReports']);
Route::get('/owner/stock/data', [OwnerDashboardController::class, 'getStockReports']);
Route::get('/owner/accounts/data', [OwnerDashboardController::class, 'getAccounts']);
Route::post('/owner/accounts/add', [OwnerDashboardController::class, 'storeAccount']);
Route::get('/owner/menus/data', [OwnerDashboardController::class, 'getMenus']);
Route::post('/owner/menus/add', [OwnerDashboardController::class, 'storeMenu']);
Route::put('/owner/menus/{id}', [OwnerDashboardController::class, 'updateMenu']);
Route::delete('/owner/menus/{id}', [OwnerDashboardController::class, 'deleteMenu']);
Route::get('/owner/transactions/data', [OwnerDashboardController::class, 'getTransactions']);
Route::put('/owner/transactions/{id}/status', [OwnerDashboardController::class, 'updateTransactionStatus']);

Route::get('/karyawan/orders/offline', [KaryawanKasirController::class, 'getOfflineOrders']);
Route::get('/karyawan/orders/online', [KaryawanKasirController::class, 'getOnlineOrders']);
Route::post('/karyawan/orders/{id}/status', [KaryawanKasirController::class, 'updateOrderStatus']);
Route::get('/karyawan/pos/menus', [KaryawanKasirController::class, 'getMenus']);
Route::post('/karyawan/pos/checkout', [KaryawanKasirController::class, 'checkout']);
Route::get('/karyawan/reservations/data', [KaryawanReservasiController::class, 'index']);
Route::post('/karyawan/reservations/{id}/status', [KaryawanReservasiController::class, 'updateStatus']);
Route::get('/karyawan/online-orders', [KaryawanOnlineController::class, 'index']);

Route::get('/admin/stock-report', [AdminStokController::class, 'index']);
Route::get('/admin/sales-report', [AdminPenjualanController::class, 'index']);
Route::get('/admin/transactions', [AdminTransactionController::class, 'index']);
Route::get('/admin/dashboard-stats', [AdminDashboardController::class, 'index']);
Route::get('/admin/dashboard/data', [AdminDashboardController::class, 'getAdminDashboardData']);
Route::get('/admin/laporan-penjualan/data', [AdminPenjualanController::class, 'getLaporanPenjualan']);
Route::get('/admin/data-transaksi/data', [AdminDashboardController::class, 'getDataTransaksi']);
Route::put('/admin/data-transaksi/{id}', [AdminDashboardController::class, 'updateStatusTransaksi']);
Route::delete('/admin/data-transaksi/{id}', [AdminDashboardController::class, 'deleteTransaksi']);
Route::get('/admin/laporan-stok/data', [AdminDashboardController::class, 'getLaporanStokData']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/addons', fn() => response()->json(\App\Models\AddOn::all()));
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/reservations', [ReservationController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn(Request $request) => response()->json($request->user()));

    Route::middleware('role:Owner,Admin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);
        Route::post('/menus/{id}', [MenuController::class, 'update']);
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    });

    Route::middleware('role:Owner')->group(function () {
        Route::get('/owner/dashboard', fn() => response()->json(['message' => 'Berhasil masuk! Ini data rahasia Owner.']));
        Route::get('/owner/accounts', [AccountManagementController::class, 'index']);
        Route::post('/owner/accounts', [AccountManagementController::class, 'store']);
        Route::delete('/owner/accounts/{id}', [AccountManagementController::class, 'destroy']);
    });

    Route::middleware('role:Admin,Staff')->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        Route::get('/inventory', [InventoryController::class, 'index']);
        Route::post('/inventory', [InventoryController::class, 'store']);
        Route::patch('/inventory/{id}', [InventoryController::class, 'update']);
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/stok', fn() => response()->json(['message' => 'Halaman kelola stok khusus Admin.']));
    });

    Route::middleware('role:Staff')->group(function () {
        Route::get('/staff/pesanan', fn() => response()->json(['message' => 'Halaman kelola pesanan khusus Staff.']));
    });
});
