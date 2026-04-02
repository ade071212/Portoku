<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'index']);
Route::get('/login', [PageController::class, 'login']);
// Register halaman dinonaktifkan — hanya admin yang bisa tambah user
Route::get('/register', fn() => redirect('/login'));
Route::get('/admin', [PageController::class, 'admin']);

// Catch-all: redirect unknown routes to home
Route::fallback(fn() => redirect('/'));
