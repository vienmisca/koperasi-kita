<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    use HasFactory;

    protected $table = 'stok_mutasi';
    protected $primaryKey = 'id_mutasi';
    
    protected $fillable = [
        'id_barang',
        'no_mutasi',
        'tanggal',
        'jenis',
        'jumlah',
        'satuan',
        'stok_sebelum',
        'stok_sesudah',
        'sumber',
        'lokasi',
        'ref_id',
        'keterangan',
        'id_user'
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'integer',
            'stok_sebelum' => 'integer',
            'stok_sesudah' => 'integer'
        ];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}