<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerDashboardController;


Route::get('/', function () { return view('landing'); });
Route::get('/menu', function () { return view('customer.menu'); });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/reservasi', function () { return view('customer.reservasi'); });
Route::post('/reservasi', [\App\Http\Controllers\KaryawanReservasiController::class, 'storeCustomerReservation']);

Route::get('/owner/reports/export', [OwnerDashboardController::class, 'exportSalesReports']);
Route::get('/owner/transactions/export', [OwnerDashboardController::class, 'exportTransactions']);

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', function () { return view('owner.dashboard'); });
    Route::get('/accounts', function () { return view('owner.accounts'); });
    Route::get('/menu', function () { return view('owner.menu'); });
    Route::get('/transactions', function () { return view('owner.transactions'); });
    Route::get('/reports', function () { return view('owner.reports'); });
    Route::get('/stock', function () { return view('owner.stock'); });
    
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); });
    Route::get('/penjualan', function () { return view('admin.penjualan'); });
    Route::get('/stok', function () { return view('admin.stok'); });
    Route::get('/transaksi', function () { return view('admin.transaksi'); });
});

Route::middleware(['auth', 'role:staff'])->prefix('karyawan')->group(function () {
    Route::get('/dashboard', function () { return view('karyawan.dashboard'); });
    Route::get('/stok', function () { return view('karyawan.stok'); });
    Route::get('/reservasi', function () { return view('karyawan.reservasi'); });
    Route::get('/online', function () { return view('karyawan.online'); });
    Route::get('/kasir', function () { return view('karyawan.kasir'); });
    Route::get('/offline', function () { return view('karyawan.offline'); });
});
