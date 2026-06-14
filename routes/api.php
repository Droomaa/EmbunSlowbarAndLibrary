<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanOnlineController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\KaryawanReservasiController;
use App\Http\Controllers\KaryawanKasirController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\StaffShiftController;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminStokController;
use App\Http\Controllers\AdminPenjualanController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

// ==========================================
// RUTE UMUM & AUTENTIKASI (PUBLIC)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/addons', fn() => response()->json(\App\Models\AddOn::all()));

// Public form submissions with throttle
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/reservations', [ReservationController::class, 'store']);
});

// ==========================================
// MIDDLEWARE AUTENTIKASI & ROLE (SECURED)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Rute Global yang membutuhkan Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn(Request $request) => response()->json($request->user()));

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::put('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::put('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);

    // Inventory Manajemen (Staff, Admin, Owner)
    Route::middleware('role:Staff,Admin,Owner')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index']);
        Route::post('/inventory', [InventoryController::class, 'store']);
        Route::patch('/inventory/{id}', [InventoryController::class, 'update']);
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);
    });

    // ==========================================
    // RUTE OWNER
    // ==========================================
    Route::middleware('role:Owner')->prefix('owner')->group(function () {
        Route::get('/dashboard', fn() => response()->json(['message' => 'Berhasil masuk! Ini data rahasia Owner.']));
        Route::get('/dashboard/data', [OwnerDashboardController::class, 'getOverview']);
        Route::get('/reports/data', [OwnerDashboardController::class, 'getSalesReports']);
        Route::get('/stock/data', [OwnerDashboardController::class, 'getStockReports']);
        Route::get('/accounts/data', [OwnerDashboardController::class, 'getAccounts']);
        Route::post('/accounts/add', [OwnerDashboardController::class, 'storeAccount']);
        Route::get('/menus/data', [OwnerDashboardController::class, 'getMenus']);
        Route::post('/menus/add', [OwnerDashboardController::class, 'storeMenu']);
        Route::put('/menus/{id}', [OwnerDashboardController::class, 'updateMenu']);
        Route::delete('/menus/{id}', [OwnerDashboardController::class, 'deleteMenu']);
        Route::get('/transactions/data', [OwnerDashboardController::class, 'getTransactions']);
        Route::put('/transactions/{id}/status', [OwnerDashboardController::class, 'updateTransactionStatus']);
        
        // Owner accounts route via AccountManagementController (existing)
        Route::get('/accounts', [AccountManagementController::class, 'index']);
        Route::post('/accounts', [AccountManagementController::class, 'store']);
        Route::delete('/accounts/{id}', [AccountManagementController::class, 'destroy']);
    });

    // ==========================================
    // RUTE ADMIN
    // ==========================================
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        Route::get('/stok', fn() => response()->json(['message' => 'Halaman kelola stok khusus Admin.']));
        Route::get('/stock-report', [AdminStokController::class, 'index']);
        Route::get('/sales-report', [AdminPenjualanController::class, 'index']);
        Route::get('/transactions', [AdminTransactionController::class, 'index']);
        Route::get('/dashboard-stats', [AdminDashboardController::class, 'index']);
        Route::get('/dashboard/data', [AdminDashboardController::class, 'getAdminDashboardData']);
        Route::get('/laporan-penjualan/data', [AdminPenjualanController::class, 'getLaporanPenjualan']);
        Route::get('/data-transaksi/data', [AdminDashboardController::class, 'getDataTransaksi']);
        Route::put('/data-transaksi/{id}', [AdminDashboardController::class, 'updateStatusTransaksi']);
        Route::delete('/data-transaksi/{id}', [AdminDashboardController::class, 'deleteTransaksi']);
        Route::get('/laporan-stok/data', [AdminDashboardController::class, 'getLaporanStokData']);
    });

    // ==========================================
    // RUTE KARYAWAN / STAFF
    // ==========================================
    
    // Shift Attendance (HANYA UNTUK STAFF)
    Route::middleware('role:Staff')->prefix('karyawan/shift')->group(function () {
        Route::get('/current', [StaffShiftController::class, 'current']);
        Route::post('/check-in', [StaffShiftController::class, 'checkIn']);
        Route::post('/check-out', [StaffShiftController::class, 'checkOut']);
        Route::get('/history', [StaffShiftController::class, 'history']);
    });

    // Operasional harian
    Route::middleware('role:Staff,Admin,Owner')->prefix('karyawan')->group(function () {
        Route::get('/pesanan', fn() => response()->json(['message' => 'Halaman kelola pesanan khusus Staff.']));
        Route::get('/dashboard/data', [KaryawanDashboardController::class, 'getDashboardData']);
        Route::get('/online-orders', [KaryawanOnlineController::class, 'index']);
        Route::get('/orders/online', [KaryawanOnlineController::class, 'getOnlineOrders']);
        Route::post('/orders/{id}/status', [KaryawanOnlineController::class, 'updateOrderStatus']);
        Route::get('/reservations/data', [KaryawanReservasiController::class, 'index']);
        Route::post('/reservations/{id}/status', [KaryawanReservasiController::class, 'updateStatus']);
    });

    // KASIR POS
    Route::middleware('role:Staff,Admin,Owner')->prefix('karyawan')->group(function () {
        Route::get('/orders/offline', [KaryawanKasirController::class, 'getOfflineOrders']);
        Route::get('/pos/menus', [KaryawanKasirController::class, 'getMenus']);
        Route::post('/pos/checkout', [KaryawanKasirController::class, 'checkout']);
    });

    // ==========================================
    // RUTE SHARED / LEGACY
    // ==========================================
    Route::middleware('role:Owner,Admin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);
        Route::post('/menus/{id}', [MenuController::class, 'update']);
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    });

    Route::middleware('role:Admin,Staff')->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
