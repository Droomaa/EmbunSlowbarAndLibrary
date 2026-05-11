<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- PUBLIC ROUTES (Tidak perlu login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- PROTECTED ROUTES (Wajib bawa Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    // 1. Group Route khusus OWNER
    Route::middleware('role:Owner')->group(function () {
        // Nanti route laporan keuangan, hapus user, dll taruh di sini
        Route::get('/owner/dashboard', function () {
            return response()->json(['message' => 'Selamat datang, Owner!']);
        });
    });

    // 2. Group Route untuk OWNER dan ADMIN
    Route::middleware('role:Owner,Admin')->group(function () {
        // Nanti route kelola stok bahan, kelola menu, dll taruh di sini
        Route::get('/admin/stok', function () {
            return response()->json(['message' => 'Ini data stok bahan']);
        });
    });

    // 3. Group Route untuk STAFF / KARYAWAN
    Route::middleware('role:Staff')->group(function () {
        // Nanti route kelola pesanan masuk taruh di sini
    });

    // 4. Group Route khusus CUSTOMER
    Route::middleware('role:Customer')->group(function () {
        // Nanti route bikin reservasi, lihat riwayat pesanan taruh di sini
    });
});