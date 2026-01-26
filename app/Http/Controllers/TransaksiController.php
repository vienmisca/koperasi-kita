<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\StokMutasi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // Halaman kasir
    public function index()
    {
        $barang = Barang::where('status', 'aktif')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();
        
        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();
        
        return view('kasir.transaksi', compact('barang', 'pelanggan'));
    }

    // Proses transaksi
    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'payment_method' => 'required',
            'bayar' => 'required|numeric|min:0',
            'cart' => 'required|array|min:1',
            'cart.*.id_barang' => 'required|exists:barang,id_barang',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Hitung total
            $total = 0;
            foreach ($request->cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Validasi pembayaran
            $bayar = $request->bayar;
            $kembalian = $bayar - $total;
            
            if ($bayar < $total) {
                throw new \Exception('Pembayaran kurang!');
            }
            
            // Buat penjualan
            $penjualan = Penjualan::create([
                'tanggal' => now(),
                'id_user' => Auth::id(),
                'id_pelanggan' => $request->id_pelanggan,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_bayar' => $request->payment_method,
                'status' => 'selesai',
                'keterangan' => $request->customer_name
            ]);
            
            // Simpan detail penjualan & update stok
            foreach ($request->cart as $item) {
                $barang = Barang::find($item['id_barang']);
                
                // Cek stok cukup
                if ($barang->stok < $item['quantity']) {
                    throw new \Exception('Stok ' . $barang->nama_barang . ' tidak cukup!');
                }
                
                $subtotal = $item['price'] * $item['quantity'];
                
                // Simpan detail
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['quantity'],
                    'harga' => $item['price'],
                    'subtotal' => $subtotal
                ]);
                
                // Update stok barang
                $stokSebelum = $barang->stok;
                $stokSesudah = $stokSebelum - $item['quantity'];
                $barang->stok = $stokSesudah;
                $barang->save();
                
                // Catat mutasi stok
                StokMutasi::create([
                    'id_barang' => $barang->id_barang,
                    'tanggal' => now(),
                    'jenis' => 'KELUAR',
                    'jumlah' => $item['quantity'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'sumber' => 'penjualan',
                    'ref_id' => $penjualan->id_penjualan,
                    'keterangan' => 'Penjualan: ' . $penjualan->no_penjualan,
                    'id_user' => Auth::id()
                ]);
            }
            
            DB::commit();
            
            // Load relationship for receipt
            $penjualan->load('details.barang', 'user');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'no_penjualan' => $penjualan->no_penjualan,
                'tanggal' => $penjualan->tanggal->format('d-m-Y H:i'),
                'kasir' => $penjualan->user->name,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'items' => $penjualan->details->map(function($detail) {
                    return [
                        'nama_barang' => $detail->barang->nama_barang,
                        'qty' => $detail->jumlah,
                        'harga' => $detail->harga,
                        'subtotal' => $detail->subtotal
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}