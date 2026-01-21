<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_mutasi', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang');
            $table->date('tanggal');
            $table->enum('jenis', ['MASUK', 'KELUAR']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->enum('sumber', ['penjualan', 'adjustment', 'retur']);
            $table->string('ref_id')->nullable();
            $table->text('keterangan');
            $table->foreignId('id_user')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_mutasi');
    }
};