<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'id_kategori',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'stok_minimal',
        'deskripsi',
        'status',
        'gambar'
    ];


    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok' => 'integer',
            'stok_minimal' => 'integer'
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_barang');
    }

    public function stokMutasi()
    {
        return $this->hasMany(StokMutasi::class, 'id_barang');
    }
}