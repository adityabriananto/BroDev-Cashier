<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Cashier Workspace

Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
Route::get('/api/products', [CashierController::class, 'getProducts'])->name('api.products');
Route::post('/api/checkout', [CashierController::class, 'checkout'])->name('api.checkout');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Inventory Management
Route::middleware(['auth'])->group(function () {
    // MAIN ANALYTICS DASHBOARD
    Route::get('/admin/dashboard', [AdminController::class, 'dashboardIndex'])->name('admin.dashboard');

    // MANAGEMENT OUTLETS
    Route::get('/admin/products', [AdminController::class, 'index'])->name('admin.products');
    Route::post('/api/products', [AdminController::class, 'store'])->name('api.products.store');
    Route::put('/api/products/{id}', [AdminController::class, 'update'])->name('api.products.update');
    Route::delete('/api/products/{id}', [AdminController::class, 'destroy'])->name('api.products.destroy');
    Route::post('/api/products/restore/{id}', [AdminController::class, 'restore'])->name('api.products.restore');

    Route::get('/admin/transactions', [AdminController::class, 'transactionIndex'])->name('admin.transactions');
    Route::get('/api/transactions/{id}', [AdminController::class, 'transactionDetail'])->name('api.transactions.detail');
});
