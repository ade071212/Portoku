<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\RequireAuth;
use App\Http\Middleware\RequireAdmin;

// Wrap ALL API routes with 'web' middleware so sessions are shared
Route::middleware('web')->group(function () {

// =============================================
//  Auth Routes (public: login, logout, check)
// =============================================
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/logout',   [AuthController::class, 'logout']);
    Route::get('/check',     [AuthController::class, 'check']);
    // Register hanya untuk admin
    Route::post('/register', [AuthController::class, 'register'])->middleware(RequireAdmin::class);
});

// =============================================
//  Portfolio Routes
// =============================================
Route::prefix('portfolio')->group(function () {
    Route::get('/',        [PortfolioController::class, 'index']);
    Route::get('/all',     [PortfolioController::class, 'all'])->middleware(RequireAuth::class);
    Route::post('/',       [PortfolioController::class, 'store'])->middleware(RequireAuth::class);
    Route::put('/{id}',    [PortfolioController::class, 'update'])->middleware(RequireAuth::class);
    Route::patch('/{id}/toggle', [PortfolioController::class, 'toggle'])->middleware(RequireAuth::class);
    Route::delete('/{id}', [PortfolioController::class, 'destroy'])->middleware(RequireAuth::class);
});

// =============================================
//  Services Routes
// =============================================
Route::prefix('services')->group(function () {
    Route::get('/',        [ServiceController::class, 'index']);
    Route::get('/all',     [ServiceController::class, 'all'])->middleware(RequireAuth::class);
    Route::post('/',       [ServiceController::class, 'store'])->middleware(RequireAuth::class);
    Route::put('/{id}',    [ServiceController::class, 'update'])->middleware(RequireAuth::class);
    Route::patch('/{id}/toggle', [ServiceController::class, 'toggle'])->middleware(RequireAuth::class);
    Route::delete('/{id}', [ServiceController::class, 'destroy'])->middleware(RequireAuth::class);
});

// =============================================
//  Contact Routes
// =============================================
Route::prefix('contact')->group(function () {
    Route::get('/',  [ContactController::class, 'show']);
    Route::post('/', [ContactController::class, 'upsert'])->middleware(RequireAuth::class);
});

// =============================================
//  Profile Routes
// =============================================
Route::prefix('profile')->group(function () {
    Route::get('/',  [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'update'])->middleware(RequireAuth::class);
});

// =============================================
//  Upload Route
// =============================================
Route::post('/upload', [UploadController::class, 'upload'])->middleware(RequireAuth::class);

// =============================================
//  User Management (Admin Only)
// =============================================
Route::prefix('users')->middleware(RequireAdmin::class)->group(function () {
    Route::get('/',             [UserController::class, 'index']);
    Route::post('/delete',      [UserController::class, 'delete']);
    Route::post('/make-admin',  [UserController::class, 'makeAdmin']);
    Route::post('/remove-admin',[UserController::class, 'removeAdmin']);
});

}); // end web middleware group
