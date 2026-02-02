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
        
        // Real sales data for today
        $pembelianToday = \App\Models\Penjualan::whereDate('tanggal', date('Y-m-d'));
        
        $penjualanHariIni = (clone $pembelianToday)->sum('total'); 
        $transaksiHariIni = (clone $pembelianToday)->count();

        // Recent Transactions (Limit 5)
        $recentTransactions = \App\Models\Penjualan::with(['details.barang'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact('totalBarang', 'stokRendah', 'penjualanHariIni', 'transaksiHariIni', 'recentTransactions'));
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
     * Laporan Penjualan Real-time
     */
    public function laporan(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Base Query
        $query = \App\Models\Penjualan::with('user', 'details.barang')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate);

        // Stats Calculation
        $totalPendapatan = (clone $query)->sum('total');
        $totalTransaksi = (clone $query)->count();
        $rataRata = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // Get Paginated Data
        $laporan = $query->latest('tanggal')->latest('created_at')->paginate(10);
        $laporan->appends(['start_date' => $startDate, 'end_date' => $endDate]);

        return view('kasir.laporan', compact(
            'laporan', 
            'totalPendapatan', 
            'totalTransaksi', 
            'rataRata',
            'startDate',
            'endDate'
        ));
    }

    public function exportLaporan(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        
        $fileName = 'Laporan_Penjualan_' . date('d-m-Y', strtotime($startDate)) . '_sd_' . date('d-m-Y', strtotime($endDate)) . '.xls';

        $data = \App\Models\Penjualan::with('user', 'details.barang')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return response(view('kasir.laporan_excel', compact('data', 'startDate', 'endDate')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }
}
