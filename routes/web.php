<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-ui', function () {
    return view('test');
});

Route::prefix('owner')->group(function () {
    Route::get('/dashboard', function () { return view('owner.dashboard'); });
    Route::get('/accounts', function () { return view('owner.accounts'); });
    Route::get('/menu', function () { return view('owner.menu'); });
    Route::get('/transactions', function () { return view('owner.transactions'); });
    Route::get('/reports', function () { return view('owner.reports'); });
    Route::get('/stock', function () { return view('owner.stock'); });
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); });
    Route::get('/penjualan', function () { return view('admin.penjualan'); });
    Route::get('/stok', function () { return view('admin.stok'); });
    Route::get('/transaksi', function () { return view('admin.transaksi'); });
});
