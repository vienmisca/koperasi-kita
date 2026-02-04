<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Dashboard Kasir
     */
    public function dashboard()
    {
        // Real-time stats
        $totalBarang = Barang::count();
        $stokRendah = Barang::whereColumn('stok', '<=', 'stok_minimal')->count();
        
        $transaksiHariIni = \App\Models\Penjualan::whereDate('tanggal', now())->count();
        $penjualanHariIni = \App\Models\Penjualan::whereDate('tanggal', now())->sum('total');

        // Recent transactions
        $recentTransactions = \App\Models\Penjualan::with('details.barang')
            ->latest()
            ->take(5)
            ->get();

        return view('kasir.dashboard', compact(
            'totalBarang', 
            'stokRendah', 
            'penjualanHariIni', 
            'transaksiHariIni',
            'recentTransactions'
        ));
    }

    /**
     * Halaman Transaksi (POS)
     */
    public function transaksi()
    {
        $products = Barang::with('kategori')->where('status', 'aktif')->get();
        return view('kasir.transaksi', compact('products'));
    }

    /**
     * Cek Stok Barang (Read Only)
     */
    public function stock()
    {
        $barang = Barang::with('kategori')->orderBy('nama_barang')->get();
        $kategori = Kategori::all();
        
        return view('kasir.stock-barang', compact('barang', 'kategori'));
    }

    /**
     * Laporan Penjualan
     */
    public function laporan(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        // Query dasar
        $query = \App\Models\Penjualan::with('details.barang')
            ->whereDate('tanggal', $date);

        // Stats (sebelum pagination)
        $totalPendapatan = $query->sum('total');
        $totalTransaksi = $query->count();
        $rataRata = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // Data Table
        $laporan = $query->latest()->paginate(10)->withQueryString();
        
        return view('kasir.laporan', compact(
            'laporan', 
            'totalPendapatan', 
            'totalTransaksi', 
            'rataRata',
            'date'
        ));
    }
}
