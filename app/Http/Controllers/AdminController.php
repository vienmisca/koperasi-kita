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
        // 1. Base Query for History (Used for both full page and AJAX table)
        $queryHistory = Penjualan::with(['user', 'details.barang' => function($q) { $q->withTrashed(); }])
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply Filters
        if ($request->filled('start_date')) {
            $queryHistory->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $queryHistory->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $queryHistory->where('id_user', $request->user_id);
        }
        if ($request->filled('metode_bayar')) {
            $queryHistory->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $queryHistory->where(function($q) use ($search) {
                $q->where('no_penjualan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Return Partial for AJAX
        if ($request->ajax() && $request->has('table_only')) {
            $salesHistory = $queryHistory->paginate(10);
            return view('admin.partials.laporan-table', compact('salesHistory'))->render();
        }

        // --- FULL PAGE LOAD LOGIC BELOW ---

        // 2. Stats (Independent of pagination, but should RESPECT filters for consistency?)
        // Usually "Laporan" stats respect the selected date range at least.
        
        $statsQuery = Penjualan::where('status', 'selesai');
        // Apply basic date filter if present to stats as well? 
        // Let's keep stats somewhat broad but maybe safe to apply date range if user intends to analyze a period.
        // For simplicity and matching current UI which has "Periode" filter at top, let's apply Date filters to stats too.
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('tanggal', '<=', $request->end_date);
        }
        
        $totalPendapatan = (clone $statsQuery)->sum('total');
        $totalTransaksi = (clone $statsQuery)->count();

        // Complex queries for products/profit also need to respect date range for accuracy
        $totalBarangTerjual = DetailPenjualan::whereHas('penjualan', function($q) use ($request) {
            $q->where('status', 'selesai');
            if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
            if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
        })->sum('jumlah');

        $totalKeuntungan = DetailPenjualan::select(
                DB::raw('SUM((detail_penjualan.harga - COALESCE(detail_penjualan.harga_beli_snapshot, barang.harga_beli, 0)) * detail_penjualan.jumlah) as profit')
            )
            ->leftJoin('barang', 'detail_penjualan.id_barang', '=', 'barang.id_barang')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('penjualan.status', 'selesai')
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('penjualan.tanggal', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('penjualan.tanggal', '<=', $request->end_date))
            ->value('profit');

        // Top Products (also filtered by date)
        $topProducts = DetailPenjualan::select(
                DB::raw('COALESCE(detail_penjualan.nama_barang_snapshot, barang.nama_barang) as nama_barang'), 
                DB::raw('COALESCE(detail_penjualan.kode_barang_snapshot, barang.kode_barang) as kode_barang'),
                DB::raw('SUM(detail_penjualan.jumlah) as total_qty'),
                DB::raw('SUM(detail_penjualan.subtotal) as total_omset')
            )
            ->leftJoin('barang', 'detail_penjualan.id_barang', '=', 'barang.id_barang')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('penjualan.status', 'selesai')
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('penjualan.tanggal', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('penjualan.tanggal', '<=', $request->end_date))
            ->groupBy(
                DB::raw('COALESCE(detail_penjualan.nama_barang_snapshot, barang.nama_barang)'), 
                DB::raw('COALESCE(detail_penjualan.kode_barang_snapshot, barang.kode_barang)')
            )
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Recent Stock In (Independent of sales filters, usually)
        $recentStockIn = StokMutasi::with(['barang' => function($q) { $q->withTrashed(); }, 'user'])
            ->where('jenis', 'MASUK')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get initial history (paginated)
        $salesHistory = $queryHistory->paginate(10);
        $users = User::all();

        return view('admin.laporan', compact(
            'totalPendapatan',
            'totalTransaksi',
            'totalBarangTerjual',
            'totalKeuntungan',
            'topProducts',
            'recentStockIn',
            'salesHistory',
            'users'
        ));
    }
    /**
     * API for Dashboard Charts (SIMPLIFIED)
     */
    public function dashboardStats(Request $request)
    {
        try {
            $range = (int) $request->get('range', 7);
            $startDate = now()->subDays($range)->startOfDay();

            \Illuminate\Support\Facades\Log::info("Fetching Dashboard Stats Safe Mode for Range: $range");

            // 1. Prepare Date Range Keys
            $dates = collect();
            for ($i = $range - 1; $i >= 0; $i--) {
                 $dates->push(now()->subDays($i)->format('Y-m-d'));
            }

            // 2. Fetch Sales Data (Raw Collection)
            // We fetch all records in range and process in PHP to avoid SQL Date Grouping issues
            $salesRaw = Penjualan::where('status', 'selesai')
                 ->whereDate('tanggal', '>=', $startDate)
                 ->get();
            
            // Group by Date (PHP)
            $salesGrouped = $salesRaw->groupBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });

            // 3. Fetch Details for Profit (Raw Collection with Relations)
            // Fetch necessary columns only for performance
            $detailsRaw = DetailPenjualan::with(['penjualan', 'barang' => function($q) { $q->withTrashed(); }])
                ->whereHas('penjualan', function($q) use ($startDate) {
                    $q->where('status', 'selesai')
                      ->whereDate('tanggal', '>=', $startDate);
                })
                ->get();

            $profitGrouped = $detailsRaw->groupBy(function($item) {
                return $item->penjualan->tanggal->format('Y-m-d');
            });

            // 4. Map to Arrays
            $dataTransactions = $dates->map(function($date) use ($salesGrouped) {
                return $salesGrouped->has($date) ? $salesGrouped->get($date)->count() : 0;
            });

            $dataRevenue = $dates->map(function($date) use ($salesGrouped) {
                return $salesGrouped->has($date) ? $salesGrouped->get($date)->sum('total') : 0;
            });

            $dataProfit = $dates->map(function($date) use ($profitGrouped) {
                if (!$profitGrouped->has($date)) return 0;
                
                return $profitGrouped->get($date)->sum(function($detail) {
                    $sell = $detail->harga;
                    // Logic: Snapshot -> Barang Current -> 0
                    $buy = $detail->harga_beli_snapshot ?? $detail->barang->harga_beli ?? 0;
                    return ($sell - $buy) * $detail->jumlah;
                });
            });

            $labels = $dates->map(fn($d) => date('d M', strtotime($d)));

            // 5. KPI
            $totalRevRange = $salesRaw->sum('total');
            $totalProfitRange = $dataProfit->sum(); // Sum of calculated daily profits
            $marginPercent = $totalRevRange > 0 ? round(($totalProfitRange / $totalRevRange) * 100, 1) : 0;
            $avgTrans = $range > 0 && $salesRaw->count() > 0 ? round($salesRaw->count() / $range, 1) : 0;

            return response()->json([
                'success' => true,
                'dates' => $labels->values(),
                'transactions' => $dataTransactions->values(),
                'revenue' => $dataRevenue->values(),
                'profit' => $dataProfit->values(),
                'kpi' => [
                    'margin' => $marginPercent,
                    'avg_trans' => $avgTrans,
                ],
                'has_data' => $salesRaw->isNotEmpty()
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Dashboard Stats Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API for Complete Report Charts
     */
    /**
     * API for Complete Report Charts (SIMPLIFIED)
     */
    public function laporanStats(Request $request)
    {
        try {
            $querySales = Penjualan::where('status', 'selesai');
            
            // Apply Filters
            if ($request->filled('start_date')) {
                $querySales->whereDate('tanggal', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $querySales->whereDate('tanggal', '<=', $request->end_date);
            }

            \Illuminate\Support\Facades\Log::info("Fetching Laporan Stats", $request->all());

            // 1. Sales per Category (Pie)
            $salesPerCat = DB::table('detail_penjualan')
                ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
                ->join('barang', 'detail_penjualan.id_barang', '=', 'barang.id_barang')
                ->join('kategori', 'barang.id_kategori', '=', 'kategori.id_kategori')
                ->where('penjualan.status', 'selesai')
                ->when($request->filled('start_date'), fn($q) => $q->whereDate('penjualan.tanggal', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->whereDate('penjualan.tanggal', '<=', $request->end_date))
                ->selectRaw('kategori.nama_kategori, SUM(detail_penjualan.subtotal) as total')
                ->groupBy('kategori.id_kategori', 'kategori.nama_kategori')
                ->orderByDesc('total') // Order by simplified
                ->get();

            // 2. Sales Trend (Line) - Aggregate by Date
            $salesTrend = $querySales->selectRaw('DATE(tanggal) as date, SUM(total) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'sales_by_category' => [
                    'labels' => $salesPerCat->pluck('nama_kategori'),
                    'data' => $salesPerCat->pluck('total')
                ],
                'sales_trend' => [
                    'labels' => $salesTrend->pluck('date'),
                    'data' => $salesTrend->pluck('revenue')
                ],
                'has_data' => $salesTrend->isNotEmpty()
                // Removed product_margin
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Laporan Stats Error: " . $e->getMessage());
             return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function exportLaporan(Request $request) 
    {
        // 1. Build Query with Filters
        $query = Penjualan::with(['user', 'details.barang' => function($q) { $q->withTrashed(); }])
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply Filters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $query->where('id_user', $request->user_id);
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('no_penjualan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Fetch Data
        $data = $query->get();

        // 3. Calculate Summary
        $totalPendapatan = $data->sum('total');
        $totalTransaksi = $data->count();
        $avgTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // 4. Prepare Excel Data
        $rows = [];
        
        // Header Info
        $pStart = $request->start_date ? date('d/m/Y', strtotime($request->start_date)) : '-';
        $pEnd = $request->end_date ? date('d/m/Y', strtotime($request->end_date)) : '-';
        $periode = ($pStart === '-' && $pEnd === '-') ? 'Semua Waktu' : "$pStart s/d $pEnd";

        // Bold Headers using HTML-like tags or just text? 
        // SimpleXLSXGen identifies styled text via <b> tags if strictly enabled? 
        // No, standard SimpleXLSXGen treats strings as strings. 
        // But let's stick to clean data. User requested "Header bold". 
        // SimpleXLSXGen v1.0+ supports `<b>Text</b>`.
        
        $rows[] = ['<b>LAPORAN PENJUALAN</b>'];
        $rows[] = ["Periode: $periode"];
        $rows[] = ['Dicetak pada: ' . date('d/m/Y H:i')];
        $rows[] = ['Dicetak oleh: ' . (auth()->user()->name ?? 'System')];
        $rows[] = []; // Spacer

        // Summary Table
        $rows[] = ['<b>RINGKASAN KINERJA</b>'];
        $rows[] = ['<b>Total Pendapatan</b>', '<b>Total Transaksi</b>', '<b>Rata-rata per Transaksi</b>'];
        // Currency formatting via explicit typing? 
        // SimpleXLSXGen doesn't support currency format string in array easily.
        // We will format as number but it might just show as number. 
        // To force currency, we might need a custom style which SimpleXLSXGen doesn't easily expose in `fromArray`.
        // However, showing as plain number is better than string for Excel math.
        $rows[] = [$totalPendapatan, $totalTransaksi, $avgTransaksi];
        $rows[] = []; // Spacer

        // Data Table Headers
        $rows[] = [
            '<b>No Transaksi</b>', 
            '<b>Tanggal</b>', 
            '<b>Jam</b>', 
            '<b>Kasir</b>', 
            '<b>Daftar Item</b>', 
            '<b>Jumlah Item</b>', 
            '<b>Total Transaksi</b>', 
            '<b>Metode Bayar</b>'
        ];

        // Data Rows
        foreach ($data as $d) {
            // Format Items: "Kopi (2), Gula (1)"
            $itemList = $d->details->map(function($detail) {
                $name = $detail->nama_barang_snapshot ?? $detail->barang?->nama_barang ?? 'Item Dihapus';
                return "$name ({$detail->jumlah})";
            })->join(', ');

            $rows[] = [
                $d->no_penjualan,
                $d->tanggal->format('Y-m-d'), // Excel friendly date
                $d->created_at->format('H:i'),
                $d->user->name ?? 'Unknown',
                $itemList,
                $d->details->sum('jumlah'),
                $d->total,
                ucfirst($d->metode_bayar ?? '-')
            ];
        }

        // 5. Generate Excel
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);
        
        // Advanced Filtering/Styling (Using Native Methods if possible or just standard)
        // Note: SimpleXLSXGen is lightweight. It might not support "Freeze Panes" easily.
        // But we can try to at least ensure <b> tags are parsed (standard behavior).
        
        $filename = 'Laporan_Penjualan_' . date('Ymd_His') . '.xlsx';
        $xlsx->downloadAs($filename);
        exit;
    }
}

