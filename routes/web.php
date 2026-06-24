<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// Authentication
// -------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',       [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// -------------------------------------------------------
// Authenticated Routes
// -------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard — owner only
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:owner');

    // -------------------------------------------------------
    // POS — kasir + owner
    // -------------------------------------------------------
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/',               [PosController::class, 'index'])->name('index');
        Route::get('/products',       [PosController::class, 'getProducts'])->name('products');
        Route::post('/payment',       [PosController::class, 'processPayment'])->name('payment');
        Route::post('/qris/{transaction}/confirm', [PosController::class, 'confirmQris'])->name('qris.confirm');
        Route::get('/receipt/{transaction}',       [PosController::class, 'receipt'])->name('receipt');
    });

    // -------------------------------------------------------
    // Transactions
    // -------------------------------------------------------
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/',                        [TransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}',           [TransactionController::class, 'show'])->name('show');
        Route::post('/{transaction}/cancel',   [TransactionController::class, 'cancel'])->name('cancel')->middleware('role:owner');
    });

    // -------------------------------------------------------
    // Owner-only Routes
    // -------------------------------------------------------
    Route::middleware('role:owner')->group(function () {

        // Products
        Route::resource('products', ProductController::class)->except(['show']);
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/daily',        [ReportController::class, 'daily'])->name('daily');
            Route::get('/monthly',      [ReportController::class, 'monthly'])->name('monthly');
            Route::get('/export/pdf',   [ReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        });

        // Users
        Route::resource('users', UserController::class)->except(['show']);

        // Settings
        Route::get('/settings',        [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',       [SettingController::class, 'update'])->name('settings.update');

        // Backup & Restore
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/',                   [BackupController::class, 'index'])->name('index');
            Route::post('/create',            [BackupController::class, 'backup'])->name('create');
            Route::get('/download/{file}',    [BackupController::class, 'download'])->name('download')->where('file', '[a-zA-Z0-9\-\_\.]+');
            Route::post('/restore',           [BackupController::class, 'restore'])->name('restore');
            Route::delete('/delete/{file}',   [BackupController::class, 'delete'])->name('delete')->where('file', '[a-zA-Z0-9\-\_\.]+');
        });
    });
});
