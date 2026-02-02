<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Menampilkan halaman utama stok barang
     */
    public function index()
    {
        // Ambil semua barang dengan kategori
        $barang = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();
        
        // Ambil kategori untuk dropdown
        $kategori = Kategori::all();
        
        // Statistik
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
        
        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        // Filter barang
        if ($request->filled('barang')) {
            $query->where('id_barang', $request->barang);
        }
        
        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $mutasi = $query->paginate(20);
        $barangList = Barang::orderBy('nama_barang')->get();
        
        return view('admin.stock.mutasi', compact('mutasi', 'barangList'));
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
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        DB::beginTransaction();

        try {
            // ❗ HAPUS DULU MUTASI STOK BARANG INI
            \App\Models\StokMutasi::where('id_barang', $id)->delete();

            // Baru hapus barangnya
            $barang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang dan riwayat mutasinya berhasil dihapus!'
            ]);
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
     * Export data stok ke Excel
     */
    public function export()
    {
        $barang = Barang::with('kategori')->orderBy('nama_barang')->get();
        $fileName = 'Laporan_Stok_Barang_' . date('d-m-Y') . '.xls';

        return response(view('admin.stock.laporan_excel', compact('barang')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }

    /**
     * Download Template Import CSV
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Import_Stok.csv"',
        ];

        $columns = ['Kode Barang', 'Nama Barang', 'Kategori', 'Stok', 'Satuan', 'Harga Beli', 'Harga Jual'];
        $example = ['BRG001', 'Contoh Barang', 'Umum', '10', 'pcs', '5000', '7000'];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import data stok dari CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        
        // Baca file csv
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        
        // Helper untuk parse CSV line yang mungkin pakai semicolon
        $parseLine = function($line) {
             if (count($line) == 1 && strpos($line[0], ';') !== false) {
                 return explode(';', $line[0]);
             }
             return $line;
        };

        if (empty($csvData)) {
             return response()->json(['success' => false, 'message' => 'File kosong.'], 400);
        }

        DB::beginTransaction();
        
        try {
            $headerFound = false;
            $idx = [];
            $imported = 0;
            $updated = 0;

            foreach ($csvData as $rawRow) {
                // Parse delimiter row
                $row = $parseLine($rawRow);
                
                // Bersihkan Input
                $cleanRow = array_map(function($item) {
                     return strtolower(trim($item));
                }, $row);

                // Deteksi Header
                if (!$headerFound) {
                    // Cari kata kunci 'kode barang'
                    $keyKode = false;
                    foreach($cleanRow as $k => $v) {
                        if($v === 'kode barang' || $v === 'kode' || $v === 'kode_barang') $keyKode = $k;
                    }

                    if ($keyKode !== false) {
                        $headerFound = true;
                        // Mapping Index
                        $idx['kode'] = $keyKode;
                        
                        // Cari index lain berdasarkan header
                         foreach($cleanRow as $k => $v) {
                            if($v === 'nama barang' || $v === 'nama') $idx['nama'] = $k;
                            if($v === 'kategori') $idx['kategori'] = $k;
                            if($v === 'stok') $idx['stok'] = $k;
                            if($v === 'satuan') $idx['satuan'] = $k;
                            if($v === 'harga beli' || $v === 'beli') $idx['beli'] = $k;
                            if($v === 'harga jual' || $v === 'jual') $idx['jual'] = $k;
                         }
                    }
                    continue; 
                }

                // Proses Data Row
                // Pastikan index kode ada
                if (!isset($idx['kode']) || !isset($row[$idx['kode']])) continue;
                
                $kode = trim($row[$idx['kode']]);
                if (empty($kode)) continue;

                $nama = isset($idx['nama']) ? $row[$idx['nama']] : 'Unnamed';
                $kategoriName = isset($idx['kategori']) ? $row[$idx['kategori']] : 'Umum';
                $stokCsv = isset($idx['stok']) ? $this->cleanNumber($row[$idx['stok']]) : 0;
                $satuan = isset($idx['satuan']) ? $row[$idx['satuan']] : 'pcs';
                $hargaBeli = isset($idx['beli']) ? $this->cleanNumber($row[$idx['beli']]) : 0;
                $hargaJual = isset($idx['jual']) ? $this->cleanNumber($row[$idx['jual']]) : 0;

                // 1. Handle Kategori
                $kategori = Kategori::firstOrCreate(
                    ['nama_kategori' => $kategoriName],
                    ['kode_kategori' => strtoupper(substr($kategoriName, 0, 3)), 'deskripsi' => 'Auto Import']
                );

                // 2. Cek Barang
                $barang = Barang::where('kode_barang', $kode)->first();

                if ($barang) {
                    // Update
                    $barang->nama_barang = $nama;
                    $barang->id_kategori = $kategori->id_kategori;
                    $barang->harga_beli = $hargaBeli;
                    $barang->harga_jual = $hargaJual;
                    $barang->satuan = $satuan;
                    
                    // Cek Stok
                    if ($barang->stok != $stokCsv) {
                        $diff = $stokCsv - $barang->stok;
                        $oldStok = $barang->stok;
                        $barang->stok = $stokCsv;

                        // Log Mutasi
                        StokMutasi::create([
                            'id_barang' => $barang->id_barang,
                            'tanggal' => now(),
                            'jenis' => $diff > 0 ? 'MASUK' : 'KELUAR',
                            'jumlah' => abs($diff),
                            'stok_sebelum' => $oldStok,
                            'stok_sesudah' => $stokCsv,
                            'sumber' => 'adjustment',
                            'keterangan' => 'Import CSV Update',
                            'id_user' => Auth::id() ?? 1
                        ]);
                    }
                    $barang->save();
                    $updated++;
                } else {
                    // Create
                    $barang = Barang::create([
                        'kode_barang' => $kode,
                        'nama_barang' => $nama,
                        'id_kategori' => $kategori->id_kategori,
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'stok' => $stokCsv,
                        'satuan' => $satuan,
                        'stok_minimal' => 5,
                        'status' => 'aktif'
                    ]);

                    if ($stokCsv > 0) {
                        StokMutasi::create([
                            'id_barang' => $barang->id_barang,
                            'tanggal' => now(),
                            'jenis' => 'MASUK',
                            'jumlah' => $stokCsv,
                            'stok_sebelum' => 0,
                            'stok_sesudah' => $stokCsv,
                            'sumber' => 'adjustment',
                            'keterangan' => 'Import CSV New',
                            'id_user' => Auth::id() ?? 1
                        ]);
                    }
                    $imported++;
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Import Berhasil! $imported baru, $updated diupdate."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function cleanNumber($val) {
        if (is_numeric($val)) return $val;
        return (int) preg_replace('/[^0-9]/', '', $val);
    }
}