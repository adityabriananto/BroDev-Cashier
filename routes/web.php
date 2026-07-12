<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

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

    // INVENTORY
    Route::post('/api/inventory/adjust', [InventoryController::class, 'adjust'])->name('api.inventory.adjust');
    Route::get('/api/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('api.inventory.low-stock');

    Route::get('/admin/transactions', [AdminController::class, 'transactionIndex'])->name('admin.transactions');
    Route::get('/api/transactions/{id}', [AdminController::class, 'transactionDetail'])->name('api.transactions.detail');
});
