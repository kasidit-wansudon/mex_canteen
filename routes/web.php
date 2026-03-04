<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'app');
Route::view('/reservation', 'app');
Route::view('/history', 'app');
Route::view('/admin', 'app');
Route::view('/admin/menu', 'app');
Route::view('/admin/scan', 'app');
Route::view('/admin/dashboard', 'app');
Route::view('/admin/users', 'app');
Route::view('/admin/reports', 'app');

Route::fallback(function () {
    return view('app');
});
