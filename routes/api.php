<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\MenuController;

// --- PUBLIC ROUTES (Tidak perlu login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/menus', [MenuController::class, 'index']);

// --- PROTECTED ROUTES (Wajib bawa Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    // --- Rute khusus Owner ---
    Route::middleware('role:Owner')->group(function () {
        // Route dashboard owner yang dikembalikan seperti semula
        Route::get('/owner/dashboard', function () {
            return response()->json(['message' => 'Berhasil masuk! Ini data rahasia Owner.']);
        });
        
        // Endpoint Management Akun Pegawai
        Route::get('/owner/accounts', [AccountManagementController::class, 'index']); // Lihat daftar
        Route::post('/owner/accounts', [AccountManagementController::class, 'store']); // Tambah akun
        Route::delete('/owner/accounts/{id}', [AccountManagementController::class, 'destroy']); // Hapus akun
    });

    // 2. Group Route untuk OWNER dan ADMIN
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/stok', function () {
            return response()->json(['message' => 'Halaman kelola stok khusus Admin.']);
        });
    });

    // --- Rute khusus Staff ---
    Route::middleware('role:Staff')->group(function () {
        Route::get('/staff/pesanan', function () {
            return response()->json(['message' => 'Halaman kelola pesanan khusus Staff.']);
        });
    });

    // 4. Group Route khusus CUSTOMER
    Route::middleware('role:Customer')->group(function () {
        Route::get('/customer/profil', function () {
            return response()->json(['message' => 'Berhasil! Ini halaman profil Customer.']);
        });
    });
});
