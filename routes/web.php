<?php

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
Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'isAdmin'])->name('admin.dashboard');

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

    // Group Routes Kasir
    Route::middleware('isKasir')->prefix('kasir')->name('kasir.')->group(function() {
        Route::get('/stock', [App\Http\Controllers\KasirController::class, 'stock'])->name('stock');
        Route::get('/laporan', [App\Http\Controllers\KasirController::class, 'laporan'])->name('laporan');
    });

    /*
    |---------------------------------------------------------------
    | ADMIN ONLY ROUTES
    |---------------------------------------------------------------
    */
    Route::middleware('isAdmin')->group(function () {

        // === KATEGORI ===
        Route::post('/kategori', [KategoriController::class, 'store'])
            ->name('kategori.store');

        // === STOCK ===
        Route::prefix('stock')->name('stock.')->group(function () {

            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::post('/', [StockController::class, 'store'])->name('store');

            Route::put('/{id}', [StockController::class, 'update'])->name('update');
            Route::delete('/{id}', [StockController::class, 'destroy'])->name('destroy');


            Route::get('/mutasi', [StockController::class, 'mutasi'])->name('mutasi');
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
