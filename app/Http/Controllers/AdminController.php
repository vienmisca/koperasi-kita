<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\User;
use App\Models\StokMutasi;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Total Stok (Sum of all stock)
        // Use DB::raw for performance if large dataset, but Eloquent is fine for now
        $totalStok = Barang::sum('stok');

        // 2. Pendapatan Hari Ini
        $pendapatanHariIni = Penjualan::whereDate('tanggal', today())
            ->where('status', 'selesai')
            ->sum('total');

        // 3. Transaksi Hari Ini (Count)
        $transaksiHariIni = Penjualan::whereDate('tanggal', today())
            ->where('status', 'selesai')
            ->count();

        // 4. Total User Aktif
        $totalUser = User::count();

        // 5. Recent Activity (Gabungan Penjualan & Mutasi could be complex, let's show latest sales)
        $recentActivities = Penjualan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Low Stock Alerts
        $lowStockItems = Barang::whereColumn('stok', '<=', 'stok_minimal')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStok',
            'pendapatanHariIni',
            'transaksiHariIni',
            'totalUser',
            'recentActivities',
            'lowStockItems'
        ));
    }
}
