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
