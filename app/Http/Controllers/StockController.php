<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockController extends Controller
{
    /**
     * Menampilkan halaman utama stok barang
     */
    /**
     * Menampilkan halaman utama stok barang
     */
    public function index(Request $request)
    {
        $query = Barang::with('kategori')->orderBy('nama_barang');

        // Filter Search (Kode / Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        // Filter Kategori
        if ($request->filled('kategori')) {
            $kat = $request->kategori;
            if (is_numeric($kat)) {
                $query->where('id_kategori', $kat);
            } else {
                 // Handle name string to prevent ID crash
                 $query->whereHas('kategori', function($q) use ($kat) {
                     $q->where('nama_kategori', $kat);
                 });
            }
        }

        // Filter Status Stok
        if ($request->filled('stok_status')) {
            if ($request->stok_status == 'habis') {
                $query->where('stok', 0);
            } elseif ($request->stok_status == 'menipis') {
                $query->whereColumn('stok', '<=', 'stok_minimal')->where('stok', '>', 0);
            } elseif ($request->stok_status == 'aman') {
                $query->whereColumn('stok', '>', 'stok_minimal');
            }
        }
        
        // Clone query for stats vs pagination if needed, but here we paginate result
        // Note: The stats cards (Total Barang, Total Stok) usually show GLOBAL stats, 
        // not filtered stats. Unless the user wants filtered stats?
        // Usually dashboard stats are global. I will keep stats global for now, 
        // asking the user if they want filtered stats is safer but I'll stick to global as is standard.
        // However, the *list* must be filtered.

        $barang = $query->paginate(20)->withQueryString();
        
        // Ambil kategori untuk dropdown
        $kategori = Kategori::all();
        
        // Statistik (Global)
        $totalBarang = Barang::count();
        $totalStok = Barang::sum('stok');
        $totalNilaiStok = Barang::sum(DB::raw('stok * harga_beli'));
        $barangHampirHabis = Barang::whereColumn('stok', '<=', 'stok_minimal')
            ->where('stok', '>', 0)
            ->count();
        $barangKosong = Barang::where('stok', 0)->count();
        
        return view('admin.stock.index', compact(
            'barang',
            'kategori',
            'totalBarang',
            'totalStok',
            'totalNilaiStok',
            'barangHampirHabis',
            'barangKosong'
        ));
    }

    // ... store method ...

    // ... mutasi method already updated ...

    // ... other methods ...

    /**
     * Export data stok ke Excel (.xlsx) dengan Format Baru
     */
    public function export(Request $request)
    {
        $query = Barang::with(['kategori', 'supplier'])->orderBy('nama_barang');

        // Apply Filters (Same as Index)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($s) use ($search) {
                        $s->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->filled('supplier_id')) {
            $query->where('id_supplier', $request->supplier_id);
        }

        if ($request->filled('stok_status')) {
            if ($request->stok_status == 'habis') {
                $query->where('stok', 0);
            } elseif ($request->stok_status == 'menipis') {
                $query->whereColumn('stok', '<=', 'stok_minimal')->where('stok', '>', 0);
            } elseif ($request->stok_status == 'aman') {
                $query->whereColumn('stok', '>', 'stok_minimal');
            }
        }

        $barang = $query->get();
        $fileName = 'Data_Stok_Barang_' . date('d-m-Y_H-i') . '.xlsx';
        
        // --- DATA PREPARATION ---
        $rows = [];

        // 1. HEADERS & SUMMARY (Non-Importable Area)
        $rows[] = ['<b>DATA BARANG KOPERASI</b>'];
        $rows[] = ['Tanggal Export: ' . date('d F Y H:i')];
        $rows[] = ['Dicetak Oleh: ' . (Auth::user()->name ?? 'System')];
        $rows[] = []; // Spacer
        
        // Ringkasan
        $totalItems = $barang->count();
        $totalStok = $barang->sum('stok');
        $totalValue = $barang->sum(function($item) { return $item->stok * $item->harga_beli; });

        $rows[] = ['<b>RINGKASAN STATUS</b>'];
        $rows[] = ['Total Item', $totalItems];
        $rows[] = ['Total Fisik Stok', $totalStok];
        $rows[] = ['Total Nilai Aset', $totalValue];
        $rows[] = []; // Spacer before table

        // 2. DATA TABLE (The Importable Area)
        // Header Row (Blue background simulated by structure, BOLD tag is supported)
        $headerRow = [
            '<b>Kode Barang</b>', 
            '<b>Nama Barang</b>', 
            '<b>Kategori</b>', 
            '<b>Satuan</b>', 
            '<b>Harga Beli</b>', 
            '<b>Harga Jual</b>', 
            '<b>Stok</b>', 
            '<b>Supplier</b>', // New Column
            '<b>Tanggal Update</b>', 
            '<b>Keterangan</b>'
        ];
        $rows[] = $headerRow;

        foreach ($barang as $item) {
            $rows[] = [
                (string)$item->kode_barang, // Force string to prevent scientific notation
                $item->nama_barang,
                $item->kategori->nama_kategori ?? 'Umum',
                $item->satuan,
                (float) $item->harga_beli,
                (float) $item->harga_jual,
                (int) $item->stok,
                $item->supplier->nama ?? '', // Supplier Name
                $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : '',
                $item->deskripsi ?? ''
            ];
        }

        // GENERATE
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);
        $xlsx->downloadAs($fileName);
        exit;
    }

    /**
     * Menyimpan data barang masuk (barang baru atau tambah stok)
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'tanggal_masuk' => 'required|date',
                'keterangan' => 'nullable|string',
                'mode' => 'required|in:existing,new'
            ]);
            
            if ($request->mode === 'existing') {
                // Validasi untuk barang existing
                $request->validate([
                    'id_barang' => 'required|exists:barang,id_barang',
                    'jumlah' => 'required|integer|min:1',
                    'harga_beli_baru' => 'nullable|numeric|min:0'
                ]);
                
                $barang = Barang::find($request->id_barang);
                $stokSebelum = $barang->stok;
                $stokSesudah = $stokSebelum + $request->jumlah;
                
                // Update stok
                $barang->stok = $stokSesudah;
                
                // Update harga beli jika berbeda
                if ($request->filled('harga_beli_baru') && $barang->harga_beli != $request->harga_beli_baru) {
                    $barang->harga_beli = $request->harga_beli_baru;
                }
                
                $barang->save();
                
                // Catat mutasi stok
                StokMutasi::create([
                    'id_barang' => $barang->id_barang,
                    'tanggal' => $request->tanggal_masuk,
                    'jenis' => 'MASUK',
                    'jumlah' => $request->jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'sumber' => 'adjustment',
                    'ref_id' => null,
                    'keterangan' => $request->keterangan ?: 'Penambahan stok: ' . $barang->nama_barang,
                    'id_user' => Auth::id()
                ]);
                
                $message = 'Stok ' . $barang->nama_barang . ' berhasil ditambahkan!';
                
            } else {
                // Validasi untuk barang baru
                $request->validate([
                    'kode_barang' => 'required|unique:barang,kode_barang',
                    'nama_barang' => 'required|string|max:255',
                    'id_kategori' => 'required|exists:kategori,id_kategori',
                    'harga_beli' => 'required|numeric|min:0',
                    'harga_jual' => 'required|numeric|min:0',
                    'stok_awal' => 'required|integer|min:0',
                    'stok_minimal' => 'required|integer|min:1',
                    'satuan' => 'required|string',
                    'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // ← validasi gambar
                ]);
                
                if ($request->hasFile('gambar')) {
                    $path = $request->file('gambar')->store('barang', 'public');
                } else {
                    $path = null;
                }
                
                $barang = Barang::create([
                    'kode_barang' => $request->kode_barang,
                    'nama_barang' => $request->nama_barang,
                    'id_kategori' => $request->id_kategori,
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                    'stok' => $request->stok_awal,
                    'satuan' => $request->satuan,
                    'stok_minimal' => $request->stok_minimal,
                    'deskripsi' => $request->deskripsi,
                    'status' => 'aktif',
                    'gambar' => $path,
                ]);

                // Catat mutasi stok awal
                if ($request->stok_awal > 0) {
                    StokMutasi::create([
                        'id_barang' => $barang->id_barang,
                        'tanggal' => $request->tanggal_masuk,
                        'jenis' => 'MASUK',
                        'jumlah' => $request->stok_awal,
                        'stok_sebelum' => 0,
                        'stok_sesudah' => $request->stok_awal,
                        'sumber' => 'adjustment',
                        'ref_id' => null,
                        'keterangan' => $request->keterangan ?: 'Barang baru: ' . $barang->nama_barang,
                        'id_user' => Auth::id()
                    ]);
                }
                
                $message = 'Barang baru ' . $barang->nama_barang . ' berhasil ditambahkan!';
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $barang
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman mutasi stok
     */
    public function mutasi(Request $request)
    {
        $query = StokMutasi::with(['barang.kategori', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter Search (Global)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($query) use ($q) {
                $query->where('no_mutasi', 'like', "%{$q}%")
                      ->orWhere('ref_id', 'like', "%{$q}%")
                      ->orWhere('keterangan', 'like', "%{$q}%")
                      ->orWhereHas('barang', function($sub) use ($q) {
                          $sub->where('nama_barang', 'like', "%{$q}%")
                              ->orWhere('kode_barang', 'like', "%{$q}%");
                      });
            });
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        
        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter user
        if ($request->filled('user')) {
             $query->where('id_user', $request->user);
        }
        
        // Stats Today
        $today = date('Y-m-d');
        $todayStats = StokMutasi::whereDate('tanggal', $today)->get();
        
        $todayCount = $todayStats->count();
        $todayIn = $todayStats->whereIn('jenis', ['MASUK', 'ADJUSTMENT'])->where('jumlah', '>', 0)->sum('jumlah');
        // Note: Adjustment implies movement, if diff > 0 it is MASUK logic
        // But in DB I might store adjustment as separate logic.
        // For simplicity:
        $todayIn = $todayStats->where('stok_sesudah', '>', 'stok_sebelum')->sum('jumlah'); 
        // This is safer: Sum of diff where after > before.
        // Wait, 'jumlah' column is absolute value of change?
        // In my logic: yes.
        
        $todayOut = $todayStats->where('stok_sesudah', '<', 'stok_sebelum')->sum('jumlah');

        // Estimate value (rough)
        $todayValue = 0;
        foreach($todayStats as $ts) {
             // N+1 problem here but negligible for daily stats usually
             $price = $ts->barang->harga_beli ?? 0;
             $todayValue += ($ts->jumlah * $price);
        }

        $mutasi = $query->paginate(20);
        $barangList = Barang::select('id_barang', 'kode_barang', 'nama_barang', 'stok', 'satuan')
                        ->where('status', 'aktif')
                        ->orderBy('nama_barang')
                        ->get();
        $users = \App\Models\User::all(); // For filter
        
        if ($request->ajax()) {
            return view('admin.stock.partials.mutasi-table', compact('mutasi'))->render();
        }
        
        return view('admin.stock.mutasi', compact(
            'mutasi', 'barangList', 'users', 
            'todayCount', 'todayIn', 'todayOut', 'todayValue'
        ));
    }

    /**
     * Menampilkan form tambah barang (jika terpisah)
     */
    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.stock.create', compact('kategori'));
    }

    /**
     * Menambah stok untuk barang yang sudah ada
     */
    public function addStock(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'harga_beli_baru' => 'nullable|numeric|min:0',
            'keterangan' => 'required|string|min:3'
        ]);
        
        $barang = Barang::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $stokSebelum = $barang->stok;
            $stokSesudah = $stokSebelum + $request->jumlah;
            
            // Update stok
            $barang->stok = $stokSesudah;
            
            // Update harga beli jika diisi
            if ($request->filled('harga_beli_baru')) {
                $barang->harga_beli = $request->harga_beli_baru;
            }
            
            $barang->save();
            
            // Catat mutasi stok
            StokMutasi::create([
                'id_barang' => $barang->id_barang,
                'tanggal' => now(),
                'jenis' => 'MASUK',
                'jumlah' => $request->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'sumber' => 'adjustment',
                'ref_id' => null,
                'keterangan' => $request->keterangan,
                'id_user' => Auth::id()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan!',
                'stok_baru' => $stokSesudah
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust stok manual
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'stok_baru' => 'required|integer|min:0',
            'alasan' => 'required|string|min:3'
        ]);
        
        $barang = Barang::findOrFail($id);
        $stokSebelum = $barang->stok;
        $stokBaru = $request->stok_baru;
        $selisih = $stokBaru - $stokSebelum;
        
        if ($selisih != 0) {
            DB::beginTransaction();
            
            try {
                // Update stok barang
                $barang->stok = $stokBaru;
                $barang->save();
                
                // Catat mutasi
                StokMutasi::create([
                    'id_barang' => $barang->id_barang,
                    'tanggal' => now(),
                    'jenis' => $selisih > 0 ? 'MASUK' : 'KELUAR',
                    'jumlah' => abs($selisih),
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokBaru,
                    'sumber' => 'adjustment',
                    'ref_id' => null,
                    'keterangan' => $request->alasan . ' (Adjustment manual)',
                    'id_user' => Auth::id()
                ]);
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Stok berhasil diadjust!',
                    'stok_baru' => $stokBaru
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengadjust stok: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada perubahan stok'
        ]);
    }

    /**
     * Menampilkan detail barang
     */
    public function show($id)
    {
        $barang = Barang::with(['kategori', 'stokMutasi.user'])
            ->findOrFail($id);
        
        return view('admin.stock.show', compact('barang'));
    }

    /**
     * Update data barang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang,'.$id.',id_barang',
            'nama_barang' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $barang = Barang::findOrFail($id);
        
        $data = [
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'id_kategori' => $request->id_kategori,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok_minimal' => $request->stok_minimal,
            'satuan' => $request->satuan,
            'status' => $request->status,
        ];

        // Handle Image Upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($barang->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($barang->gambar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($barang->gambar);
            }
            
            $path = $request->file('gambar')->store('barang', 'public');
            $data['gambar'] = $path;
        }

        $barang->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diupdate!'
        ]);
    }

    /**
     * Hapus barang
     */
    /**
     * Hapus barang (Soft Delete jika ada history, Force Delete jika bersih)
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        DB::beginTransaction();

        try {
            // Cek apakah barang memiliki riwayat transaksi atau mutasi stok
            $hasHistory = $barang->detailPenjualan()->exists() || $barang->stokMutasi()->exists();

            if ($hasHistory) {
                // SOFT DELETE: Hanya menandai sebagai deleted, data tetap ada
                $barang->delete(); 

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Barang dinonaktifkan (Soft Delete) karena memiliki riwayat transaksi. Data aman.'
                ]);
            } else {
                // FORCE DELETE: Bersih total karena belum pernah dipakai
                $barang->forceDelete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Barang dihapus permanen (Data bersih).'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API untuk get barang by kategori (untuk dropdown)
     */
    public function getBarangByKategori($kategoriId)
    {
        $barang = Barang::where('id_kategori', $kategoriId)
            ->where('status', 'aktif')
            ->select('id_barang', 'nama_barang', 'kode_barang', 'stok', 'harga_beli', 'harga_jual')
            ->get();
        
        return response()->json($barang);
    }

    /**
     * API untuk search barang
     */
    public function searchBarang(Request $request)
    {
        $query = Barang::with('kategori')
            ->where('status', 'aktif');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }
        
        $barang = $query->orderBy('nama_barang')
            ->limit(20)
            ->get();
        
        return response()->json($barang);
    }



    /**
     * Download Template Import (.xlsx)
     */
    public function downloadTemplate()
    {
        $fileName = 'Template_Import_Stok.xlsx';

        $header = [
            '<b>Kode Barang</b>', 
            '<b>Nama Barang</b>', 
            '<b>Kategori</b>', 
            '<b>Satuan</b>', 
            '<b>Harga Beli</b>', 
            '<b>Harga Jual</b>', 
            '<b>Stok</b>', 
            '<b>Supplier</b>', 
            '<b>Tanggal Update</b>', 
            '<b>Keterangan</b>'
        ];
        
        $example = [
            'BRG001', 'Contoh Barang', 'Umum', 'pcs', 5000, 7500, 100, 'PT. Supplier Jaya', date('Y-m-d'), 'Barang Contoh'
        ];

        $sheet1 = [$header, $example];
        
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($sheet1, 'Data Barang');
        $xlsx->downloadAs($fileName);
        exit;
    }

    /**
     * Import data stok dari Excel (.xlsx) dengan Logic Baru
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);

        $file = $request->file('file');
        
        if (! $xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
             return response()->json(['success' => false, 'message' => 'Gagal membaca Excel: ' . \Shuchkin\SimpleXLSX::parseError()], 400);
        }

        $rows = $xlsx->rows();

        if (empty($rows)) {
             return response()->json(['success' => false, 'message' => 'File kosong.'], 400);
        }

        DB::beginTransaction();
        
        try {
            $headerFound = false;
            $idx = [];
            $imported = 0;
            $updated = 0;
            $newSuppliers = 0;

            foreach ($rows as $rowIndex => $row) {
                // Pre-process row content for matching
                $cleanRow = array_map(function($item) {
                     return strtolower(trim((string)$item));
                }, $row);

                // --- PHASE 1: FIND HEADER ROW ---
                if (!$headerFound) {
                    // Start looking for "Kode Barang" or "Nama Barang" to identify header
                    $keyKode = false;
                    foreach($cleanRow as $k => $v) {
                        // Strip HTML tags like <b> from header check
                        $vClean = strip_tags($v);
                        if(str_contains($vClean, 'kode barang') || $vClean === 'kode') $keyKode = $k;
                    }

                    if ($keyKode !== false) {
                        $headerFound = true;
                        // Map Columns
                        $idx['kode'] = $keyKode;
                        
                        foreach($cleanRow as $k => $v) {
                            $vClean = strip_tags($v);
                            if(str_contains($vClean, 'nama') && !str_contains($vClean, 'supplier')) $idx['nama'] = $k;
                            if(str_contains($vClean, 'kategori')) $idx['kategori'] = $k;
                            if($vClean === 'stok') $idx['stok'] = $k;
                            if($vClean === 'satuan') $idx['satuan'] = $k;
                            if(str_contains($vClean, 'harga beli') || $vClean === 'beli') $idx['beli'] = $k;
                            if(str_contains($vClean, 'harga jual') || $vClean === 'jual') $idx['jual'] = $k;
                            if(str_contains($vClean, 'supplier')) $idx['supplier'] = $k; // New
                            if(str_contains($vClean, 'keterangan') || $vClean === 'deskripsi') $idx['desc'] = $k;
                        }
                    }
                    continue; // Skip the header row itself
                }

                // --- PHASE 2: PROCESS DATA ---
                
                // Validate Row
                if (!isset($idx['kode']) || !isset($row[$idx['kode']])) continue;
                $kode = trim((string)$row[$idx['kode']]);
                if (empty($kode)) continue; // Skip empty rows

                // Extract Data
                $nama = isset($idx['nama']) ? trim($row[$idx['nama']]) : 'Unnamed';
                $kategoriName = isset($idx['kategori']) ? trim($row[$idx['kategori']]) : 'Umum';
                $stokImport = isset($idx['stok']) ? $this->cleanNumber($row[$idx['stok']]) : 0;
                $satuan = isset($idx['satuan']) ? trim($row[$idx['satuan']]) : 'pcs';
                $hargaBeli = isset($idx['beli']) ? $this->cleanNumber($row[$idx['beli']]) : 0;
                $hargaJual = isset($idx['jual']) ? $this->cleanNumber($row[$idx['jual']]) : 0;
                $deskripsi = isset($idx['desc']) ? trim($row[$idx['desc']]) : '';
                $supplierName = isset($idx['supplier']) ? trim($row[$idx['supplier']]) : '';

                // 1. Handle Kategori (Auto Create)
                $kategori = Kategori::firstOrCreate(
                    ['nama_kategori' => $kategoriName],
                    ['kode_kategori' => strtoupper(substr($kategoriName, 0, 3)), 'deskripsi' => 'Auto Import']
                );

                // 2. Handle Supplier (Auto Create)
                $supplierID = null;
                if (!empty($supplierName)) {
                    $supplier = \App\Models\Supplier::firstOrCreate(
                        ['nama' => $supplierName],
                        ['is_active' => true, 'catatan' => 'Auto Created from Import']
                    );
                    if ($supplier->wasRecentlyCreated) $newSuppliers++;
                    $supplierID = $supplier->id;
                }

                // 3. Find or Create Barang
                $barang = Barang::where('kode_barang', $kode)->first();

                if ($barang) {
                    // UPDATE
                    $barang->nama_barang = $nama;
                    $barang->id_kategori = $kategori->id_kategori;
                    $barang->harga_beli = $hargaBeli;
                    $barang->harga_jual = $hargaJual;
                    $barang->satuan = $satuan;
                    
                    if($supplierID) $barang->id_supplier = $supplierID;
                    if (!empty($deskripsi)) $barang->deskripsi = $deskripsi;
                    
                    // Logic Update Stok (Apakah nimpa atau adjust?)
                    // Aturan 6 user: "Jika Kode Barang sudah ada, data akan di-update (termasuk stok penyesuaian)"
                    // Asumsi: Nilai di excel adalah STOK TERAKHIR (Opname).
                    if ($barang->stok != $stokImport) {
                        $diff = $stokImport - $barang->stok;
                        
                        StokMutasi::create([
                            'id_barang' => $barang->id_barang,
                            'tanggal' => now(),
                            'jenis' => $diff > 0 ? 'MASUK' : 'KELUAR',
                            'jumlah' => abs($diff),
                            'stok_sebelum' => $barang->stok,
                            'stok_sesudah' => $stokImport,
                            'sumber' => 'import',
                            'keterangan' => 'Import Update (Opname)',
                            'id_user' => Auth::id()
                        ]);
                        $barang->stok = $stokImport;
                    }

                    $barang->save();
                    $updated++;
                } else {
                    // INSERT NEW
                    $newBarang = Barang::create([
                        'kode_barang' => $kode,
                        'nama_barang' => $nama,
                        'id_kategori' => $kategori->id_kategori,
                        'id_supplier' => $supplierID,
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'stok' => $stokImport,
                        'satuan' => $satuan,
                        'stok_minimal' => 5, // Default
                        'deskripsi' => $deskripsi,
                        'status' => 'aktif'
                    ]);

                    // Log Initial Stock
                    if ($stokImport > 0) {
                        StokMutasi::create([
                            'id_barang' => $newBarang->id_barang,
                            'tanggal' => now(),
                            'jenis' => 'MASUK',
                            'jumlah' => $stokImport,
                            'stok_sebelum' => 0,
                            'stok_sesudah' => $stokImport,
                            'sumber' => 'import',
                            'keterangan' => 'Import Awal',
                            'id_user' => Auth::id()
                        ]);
                    }
                    $imported++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import Selesai! Data Baru: $imported, Diupdate: $updated, Supplier Baru: $newSuppliers"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error Import: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store Mutasi Baru
     */
    public function storeMutasi(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jenis' => 'required|in:MASUK,KELUAR,ADJUSTMENT,TRANSFER',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string',
            'keterangan' => 'nullable|string',
            // If transfer, maybe 'tujuan' logic but for now simple
        ]);

        DB::beginTransaction();

        try {
            $barang = Barang::findOrFail($request->id_barang);
            $stokSebelum = $barang->stok;
            $qty = $request->jumlah;
            $jenis = $request->jenis; // ENUM needs match or string
            
            // Generate No Mutasi: MUT/20260205/0001
            $prefix = 'MUT/' . date('Ymd') . '/';
            $lastMutasi = StokMutasi::where('no_mutasi', 'like', $prefix . '%')->orderBy('id_mutasi', 'desc')->first();
            $nextNo = 1;
            if ($lastMutasi) {
                $parts = explode('/', $lastMutasi->no_mutasi);
                $nextNo = (int)end($parts) + 1;
            }
            $noMutasi = $prefix . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

            $stokSesudah = $stokSebelum;
            $sumber = 'manual'; // Default

            // Logic Perhitungan
            if ($jenis == 'MASUK') {
                $stokSesudah = $stokSebelum + $qty;
                $sumber = 'pembelian'; // Or manual
            } elseif ($jenis == 'KELUAR') {
                if ($stokSebelum < $qty) {
                    throw new \Exception("Stok tidak cukup! Stok saat ini: $stokSebelum");
                }
                $stokSesudah = $stokSebelum - $qty;
                $sumber = 'penjualan'; // or manual/rusak
            } elseif ($jenis == 'ADJUSTMENT') {
                // User input is ACTUAL QTY? or Adjustment QTY?
                // My View handles it as "Jumlah" input. 
                // Usually "Adjustment" means "I count X, system says Y".
                // But here UI says "Input Qty".
                // If I assume users input the DIFFERENCE:
                // Let's assume view logic: User inputs "Jumlah". 
                // If view allows +/-: User inputs positive number always.
                // Let's assume Adjustment in this context (simple form) means "Set Stock to X" or "Add/Sub X".
                // Looking at View: "Input qty masuk / keluar".
                // If I select Adjustment, usually I want to correct stock.
                // Let's treat Adjustment as "Set Stock To..."? No, input is "Jumlah".
                // Okay, if Adjustment, I'll treat "Jumlah" as the "New Stock" (Opname result). 
                // Logic in View: if Adjustment return qty. so qty IS the generic "target".
                $stokSesudah = $qty; 
                $diff = $stokSesudah - $stokSebelum;
                $qty = abs($diff); // The change amount
                $jenis = $diff >= 0 ? 'MASUK' : 'KELUAR';
                $sumber = 'adjustment';
                // Wait, if I change $jenis here, DB will save MASUK/KELUAR.
                // But user selected ADJUSTMENT.
                // If DB `jenis` is varchar, I can save 'ADJUSTMENT'.
                // But `jumlah` (change amount) needs to be positive usually.
                // Revert: I will save 'ADJUSTMENT' in DB, but update Stok with diff.
                $jenis = 'ADJUSTMENT'; // Save as adjustment
                // $qty is already set to absolute diff? No.
                // If user entered 50 (Actual), and Old is 40. Diff is +10.
                // I record: Jenis=ADJUSTMENT, StokBefore=40, StokAfter=50, Jumlah=10.
            } elseif ($request->jenis == 'TRANSFER') {
                 // Transfer logic: Deduct from 'Lokasi A'.
                 // In this single-stock system, "Transfer Out" means items leave.
                 if ($stokSebelum < $qty) {
                    throw new \Exception("Stok tidak cukup untuk transfer!");
                 }
                 $stokSesudah = $stokSebelum - $qty; // Deduct?
                 // Or just Log?
                 // User said "Transfer Gudang" -> "Stock Before & After".
                 // If I dont deduct, stock remains.
                 // Let's deduct (Transfer Out).
                 $sumber = 'transfer';
            }

            // Correction for ADJUSTMENT specific logic
            // If View sends "target stock", calculate diff.
            if ($request->jenis == 'ADJUSTMENT') {
                $target = $request->jumlah; // This is "Stok saat ini" entered by user?
                // View says "Input qty masuk / keluar".
                // But for Adjustment, usually it's "Hasil Opname".
                // Let's assume user inputs the DIFFERENCE if they select Adjustment? 
                // Or user inputs the REAL count.
                // View script: `if (type == ADJUSTMENT) return qty` -> Suggests `qty` IS the target.
                // OK, treat `$request->jumlah` as TARGET STOCK for Adjustment.
                $stokSesudah = $request->jumlah;
                $qtyChange = $stokSesudah - $stokSebelum;
                $qty = abs($qtyChange); // Store absolute change
                
                // If no change
                if ($qtyChange == 0) {
                     throw new \Exception("Tidak ada perubahan stok (jumlah sama).");
                }
            } else {
                // For Masuk/Keluar/Transfer, $qty is the delta.
                // Update $barang->stok
                 $barang->stok = $stokSesudah;
            }
            
            $barang->stok = $stokSesudah;
            $barang->save();

            // Create Mutation
            StokMutasi::create([
                'id_barang' => $barang->id_barang,
                'no_mutasi' => $noMutasi,
                'tanggal' => $request->tanggal,
                'jenis' => $request->jenis == 'ADJUSTMENT' 
                            ? ($stokSesudah >= $stokSebelum ? 'MASUK' : 'KELUAR') 
                            : $request->jenis,
                'jumlah' => $qty,
                'satuan' => $barang->satuan,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'sumber' => $request->jenis == 'ADJUSTMENT' ? 'adjustment' : strtolower($request->jenis),
                'lokasi' => $request->lokasi,
                'keterangan' => $request->keterangan ?: 'Mutasi ' . $request->jenis,
                'id_user' => Auth::id()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Mutasi stok berhasil disimpan! No: ' . $noMutasi);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Export Mutasi Excel
     */
    public function exportMutasi(Request $request)
    {
        $query = StokMutasi::with(['barang', 'user'])
            ->orderBy('tanggal', 'desc');

        // Apply same filters
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($query) use ($q) {
                $query->where('no_mutasi', 'like', "%{$q}%")
                      ->orWhere('ref_id', 'like', "%{$q}%")
                      ->orWhere('keterangan', 'like', "%{$q}%")
                      ->orWhereHas('barang', function($sub) use ($q) {
                          $sub->where('nama_barang', 'like', "%{$q}%")
                              ->orWhere('kode_barang', 'like', "%{$q}%");
                      });
            });
        }
        if ($request->filled('start_date')) $query->whereDate('tanggal', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('tanggal', '<=', $request->end_date);
        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('user')) $query->where('id_user', $request->user);
        
        $data = $query->get();
        
        $spreadsheet = [];
        $spreadsheet[] = ['Tanggal', 'No Mutasi', 'Kode Barang', 'Nama Barang', 'Jenis', 'Jumlah', 'Satuan', 'Stok Awal', 'Stok Akhir', 'Lokasi', 'Keterangan', 'User'];

        foreach ($data as $d) {
            $spreadsheet[] = [
                $d->tanggal->format('Y-m-d'),
                $d->no_mutasi,
                $d->barang->kode_barang ?? '-',
                $d->barang->nama_barang ?? '-',
                $d->jenis . ' (' . $d->sumber . ')',
                $d->jenis == 'KELUAR' || ($d->jenis == 'TRANSFER') ? -1 * $d->jumlah : $d->jumlah,
                $d->satuan,
                $d->stok_sebelum,
                $d->stok_sesudah,
                $d->lokasi,
                $d->keterangan,
                $d->user->name ?? 'System'
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($spreadsheet, 'Mutasi Stok');
        $xlsx->downloadAs('Mutasi_Stok_' . date('Ymd_His') . '.xlsx');
        exit;
    }

    private function cleanNumber($val) {
        if (is_numeric($val)) return $val;
        return (float) preg_replace('/[^0-9.]/', '', $val);
    }
}