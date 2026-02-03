<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id_detail';
    
    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'jumlah',
        'harga',
        'subtotal',
        'nama_barang_snapshot',
        'kode_barang_snapshot',
        'harga_beli_snapshot',
        'harga_jual_snapshot',
        'kategori_snapshot',
        'satuan_snapshot'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang')->withTrashed();
    }
}
