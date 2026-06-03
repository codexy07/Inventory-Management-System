<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard')->middleware('can:admin');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Product Management
    Route::resource('products', ProductController::class);

    // Supplier Management
    Route::resource('suppliers', SupplierController::class);

    // Order Management
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Stock Management
    Route::prefix('stock')->name('stock.')->group(function () {
        // Stock In
        Route::get('/in', [StockInController::class, 'index'])->name('in.index');
        Route::get('/in/create', [StockInController::class, 'create'])->name('in.create');
        Route::post('/in', [StockInController::class, 'store'])->name('in.store');
        Route::get('/in/{stockIn}/edit', [StockInController::class, 'edit'])->name('in.edit');
        Route::put('/in/{stockIn}', [StockInController::class, 'update'])->name('in.update');
        Route::delete('/in/{stockIn}', [StockInController::class, 'destroy'])->name('in.destroy');

        // Stock Out
        Route::get('/out', [StockOutController::class, 'index'])->name('out.index');
        Route::get('/out/create', [StockOutController::class, 'create'])->name('out.create');
        Route::post('/out', [StockOutController::class, 'store'])->name('out.store');
        Route::get('/out/{stockOut}/edit', [StockOutController::class, 'edit'])->name('out.edit');
        Route::put('/out/{stockOut}', [StockOutController::class, 'update'])->name('out.update');
        Route::delete('/out/{stockOut}', [StockOutController::class, 'destroy'])->name('out.destroy');
    });

    // Reports
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/history', [ReportController::class, 'history'])->name('reports.history');
    Route::get('/reports/lowstock', [ReportController::class, 'lowStock'])->name('reports.lowstock');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::post('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');

    // Categories (Admin Only)
    Route::resource('categories', CategoryController::class)->middleware('can:admin');

    // User Management (Admin Only)
    Route::resource('users', UserController::class)->middleware('can:admin');

    // Activity Logs (Admin Only)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('can:admin');
});

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});
