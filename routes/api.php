<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/menus', [MenuController::class, 'index']);
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
