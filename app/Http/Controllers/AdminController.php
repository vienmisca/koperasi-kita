<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\User;
use App\Models\StokMutasi;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Total Stok (Sum of all stock)
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

        // 5. Recent Activity
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

    public function laporan(Request $request)
    {
        // 1. Filter Logic (Basic implementation, defaults to all time or this month)
        // For now, let's grab everything for "Lengkap" request
        
        $query = Penjualan::where('status', 'selesai');
        
        // 2. Total Stats
        $totalPendapatan = $query->sum('total');
        $totalTransaksi = $query->count();
        $totalBarangTerjual = DetailPenjualan::whereHas('penjualan', function($q) {
            $q->where('status', 'selesai');
        })->sum('jumlah');

        // 3. Calculate Profit: (Selling Price - Buying Price) * Qty
        // We need to join tables to calculate this efficiently
        $totalKeuntungan = DetailPenjualan::select(
                DB::raw('SUM((detail_penjualan.harga - barang.harga_beli) * detail_penjualan.jumlah) as profit')
            )
            ->join('barang', 'detail_penjualan.id_barang', '=', 'barang.id_barang')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('penjualan.status', 'selesai')
            ->value('profit');

        // 4. Top Selling Products
        $topProducts = DetailPenjualan::select(
                'barang.nama_barang', 
                'barang.kode_barang',
                DB::raw('SUM(detail_penjualan.jumlah) as total_qty'),
                DB::raw('SUM(detail_penjualan.subtotal) as total_omset')
            )
            ->join('barang', 'detail_penjualan.id_barang', '=', 'barang.id_barang')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('penjualan.status', 'selesai')
            ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.kode_barang')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 5. Recent Stock In (Barang Masuk)
        $recentStockIn = StokMutasi::with('barang', 'user')
            ->where('jenis', 'MASUK')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Full Sales History (Paginated)
        $salesHistory = Penjualan::with(['user', 'details.barang'])
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.laporan', compact(
            'totalPendapatan',
            'totalTransaksi',
            'totalBarangTerjual',
            'totalKeuntungan',
            'topProducts',
            'recentStockIn',
            'salesHistory'
        ));
    }
}
