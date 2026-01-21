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
        // Simple stats for dashboard
        $totalBarang = Barang::count();
        $stokRendah = Barang::whereColumn('stok', '<=', 'stok_minimal')->count();
        // Placeholder for sales data
        $penjualanHariIni = 0; 
        $transaksiHariIni = 0;

        return view('kasir.dashboard', compact('totalBarang', 'stokRendah', 'penjualanHariIni', 'transaksiHariIni'));
    }

    /**
     * Halaman Transaksi (POS)
     */
    public function transaksi()
    {
        $products = Barang::where('status', 'aktif')->where('stok', '>', 0)->get();
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
    public function laporan()
    {
        // Placeholder data for reports
        // In real app, fetch from Transaksi model
        $laporan = []; 
        
        return view('kasir.laporan', compact('laporan'));
    }
}
