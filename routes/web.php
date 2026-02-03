<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|-----------------------------------------------------------------------
| DASHBOARD
|-----------------------------------------------------------------------
*/

// Dashboard KASIR
Route::get('/dashboard', [App\Http\Controllers\KasirController::class, 'dashboard'])
    ->middleware(['auth', 'isKasir'])->name('dashboard');

// Dashboard ADMIN
Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])
    ->middleware(['auth', 'isAdmin'])->name('admin.dashboard');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/barang-list', function() {
        return \App\Models\Barang::with('kategori')->get();
    });
});

/*
|-----------------------------------------------------------------------
| ROUTES YANG BUTUH LOGIN
|-----------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

// Transaksi (khusus kasir)
    Route::get('/transaksi', [App\Http\Controllers\KasirController::class, 'transaksi'])
        ->middleware('isKasir')->name('transaksi');

    Route::post('/transaksi/process', [App\Http\Controllers\TransaksiController::class, 'process'])
        ->middleware('isKasir')->name('transaksi.process');

    // Group Routes Kasir
    Route::middleware('isKasir')->prefix('kasir')->name('kasir.')->group(function() {
        Route::get('/stock', [App\Http\Controllers\KasirController::class, 'stock'])->name('stock');
        Route::get('/laporan/export', [App\Http\Controllers\KasirController::class, 'exportLaporan'])->name('laporan.export');
        Route::get('/laporan', [App\Http\Controllers\KasirController::class, 'laporan'])->name('laporan');
    });

    /*
    |---------------------------------------------------------------
    | ADMIN ONLY ROUTES
    |---------------------------------------------------------------
    */
    Route::middleware('isAdmin')->group(function () {

        // === SETTINGS ===
        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('admin.settings.update');

        // === USERS / PENGGUNA ===
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('users/{user}/approve-reset', [App\Http\Controllers\Auth\ManualPasswordResetController::class, 'approve'])->name('admin.users.approve-reset');
        Route::post('users/{user}/reject-reset', [App\Http\Controllers\Auth\ManualPasswordResetController::class, 'reject'])->name('admin.users.reject-reset');

        // === LAPORAN ===
        Route::get('/laporan/export', [App\Http\Controllers\AdminController::class, 'exportLaporan'])->name('admin.laporan.export');
        Route::get('/laporan', [App\Http\Controllers\AdminController::class, 'laporan'])->name('admin.laporan');
        
        // API Stats
        Route::get('/api/dashboard/stats', [App\Http\Controllers\AdminController::class, 'dashboardStats'])->name('admin.dashboard.stats');
        Route::get('/api/laporan/stats', [App\Http\Controllers\AdminController::class, 'laporanStats'])->name('admin.laporan.stats');

        // === KATEGORI ===
        Route::post('/kategori', [KategoriController::class, 'store'])
            ->name('kategori.store');
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])
            ->name('kategori.destroy');

        // === SUPPLIER ===
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->only(['store', 'update', 'index']);

        // === STOCK ===
        Route::prefix('admin/stock')->name('admin.stock.')->group(function () {
             
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::post('/', [StockController::class, 'store'])->name('store');
            Route::get('/export', [StockController::class, 'export'])->name('export');
            Route::post('/import', [StockController::class, 'import'])->name('import');
            Route::get('/template', [StockController::class, 'downloadTemplate'])->name('template');

            Route::put('/{id}', [StockController::class, 'update'])->name('update');
            Route::delete('/{id}', [StockController::class, 'destroy'])->name('destroy');

            Route::get('/mutasi', [StockController::class, 'mutasi'])->name('mutasi');
            Route::post('/mutasi/store', [StockController::class, 'storeMutasi'])->name('mutasi.store');
            Route::get('/mutasi/export', [StockController::class, 'exportMutasi'])->name('mutasi.export');

            Route::post('/add-stock/{id}', [StockController::class, 'addStock'])
                ->name('add-stock');
            Route::post('/adjust/{id}', [StockController::class, 'adjustStock'])
                ->name('adjust');
        });
    });

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
