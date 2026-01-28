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
        
        return view('stock.index', compact(
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
        
        return view('stock.mutasi', compact('mutasi', 'barangList'));
    }

    /**
     * Menampilkan form tambah barang (jika terpisah)
     */
    public function create()
    {
        $kategori = Kategori::all();
        return view('stock.create', compact('kategori'));
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
        
        return view('stock.show', compact('barang'));
    }

    /**
     * Update data barang
     */
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
}