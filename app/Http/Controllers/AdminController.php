<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\User;
use App\Models\StokMutasi;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'week');

        // 1. Total Stok (Sum of all stock)
        $totalStok = Barang::sum('stok');
        
        // 2. Nilai Persediaan (Stok * Harga Beli)
        $nilaiPersediaan = Barang::select(DB::raw('SUM(stok * harga_beli) as total_value'))->value('total_value') ?? 0;

        // Prepare Date Query
        $dateQuery = function ($query) use ($period) {
            if ($period === 'week') {
                $query->whereDate('tanggal', '>=', now()->subDays(6)); // Last 7 days including today
            } elseif ($period === 'month') {
                $query->whereMonth('tanggal', now()->month)
                      ->whereYear('tanggal', now()->year);
            } else {
                // Default today
                $query->whereDate('tanggal', today());
            }
        };

        // 3. Pendapatan (Based on Period)
        $pendapatan = Penjualan::where('status', 'selesai')
            ->where($dateQuery)
            ->sum('total');

        // 4. Transaksi Count (Based on Period)
        $transaksiCount = Penjualan::where('status', 'selesai')
            ->where($dateQuery)
            ->count();
            
        // 5. Produk Terjual (Items Sold)
        $produkTerjual = \App\Models\DetailPenjualan::whereHas('penjualan', function($q) use ($dateQuery) {
                $q->where('status', 'selesai')->where($dateQuery);
            })->sum('jumlah');

        // 6. Total User Aktif
        $totalUser = User::count();

        // 7. Recent Activity
        $recentActivities = Penjualan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 8. Low Stock Alerts & Count
        $lowStockQuery = Barang::whereColumn('stok', '<=', 'stok_minimal');
        $lowStockCount = $lowStockQuery->count();
        $lowStockItems = $lowStockQuery->orderBy('stok', 'asc')
            ->limit(5)
            ->get();
            
        // 9. Chart Data (Tren Penjualan)
        $chartLabels = [];
        $chartData = [];
        $chartCount = [];
        
        if ($period === 'today') {
            // Hourly graph for today (00-23) - PGSQL compatible
            $hourlyStats = Penjualan::select(
                    DB::raw('EXTRACT(HOUR FROM created_at) as hour'), 
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereDate('tanggal', today())
                ->where('status', 'selesai')
                ->groupBy('hour')
                ->get()
                ->keyBy(function($item) { return (int)$item->hour; });
            
            for ($i = 8; $i <= 22; $i++) { // Show 08:00 to 22:00
                $chartLabels[] = sprintf('%02d:00', $i);
                $row = $hourlyStats->get($i);
                $chartData[] = $row ? $row->total : 0;
                $chartCount[] = $row ? $row->count : 0;
            }
        } elseif ($period === 'month') {
             // Daily graph for this month - PGSQL compatible
             $daysInMonth = now()->daysInMonth;
             $dailyStats = Penjualan::select(
                    DB::raw('EXTRACT(DAY FROM tanggal) as day'), 
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->where('status', 'selesai')
                ->groupBy('day')
                ->get()
                ->keyBy(function($item) { return (int)$item->day; });
                
             for ($i = 1; $i <= $daysInMonth; $i++) {
                 $chartLabels[] = $i;
                 $row = $dailyStats->get($i);
                 $chartData[] = $row ? $row->total : 0;
                 $chartCount[] = $row ? $row->count : 0;
             }
        } else {
             // Last 7 Days (Default for week) - PGSQL compatible
             $dailyStats = Penjualan::select(
                    DB::raw('tanggal::date as date'), 
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereDate('tanggal', '>=', now()->subDays(6))
                ->where('status', 'selesai')
                ->groupBy('date')
                ->get()
                ->keyBy('date');
            
            for ($i = 6; $i >= 0; $i--) {
                $dateObj = now()->subDays($i);
                $dateStr = $dateObj->format('Y-m-d');
                $chartLabels[] = $dateObj->format('d M');
                $row = $dailyStats->get($dateStr); // Key is string Y-m-d
                $chartData[] = $row ? $row->total : 0;
                $chartCount[] = $row ? $row->count : 0;
            }
        }

        return view('admin.dashboard', compact(
            'totalStok',
            'nilaiPersediaan',
            'pendapatan',
            'transaksiCount',
            'produkTerjual',
            'totalUser',
            'recentActivities',
            'lowStockItems',
            'lowStockCount',
            'period',
            'chartLabels',
            'chartData',
            'chartCount'
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
