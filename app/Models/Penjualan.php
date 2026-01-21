<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    
    protected $fillable = [
        'no_penjualan',
        'tanggal',
        'id_user',
        'id_pelanggan',
        'total',
        'bayar',
        'kembalian',
        'metode_bayar',
        'status',
        'keterangan'
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total' => 'decimal:2',
            'bayar' => 'decimal:2',
            'kembalian' => 'decimal:2'
        ];
    }

    // Auto generate no_penjualan
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $prefix = 'TRX-' . date('Ymd') . '-';
            $last = self::where('no_penjualan', 'like', $prefix . '%')
                ->orderBy('no_penjualan', 'desc')
                ->first();
            
            $number = $last ? intval(substr($last->no_penjualan, -4)) + 1 : 1;
            $model->no_penjualan = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
    }
}