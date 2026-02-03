<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->string('nama_barang_snapshot')->nullable()->after('id_barang');
            $table->string('kode_barang_snapshot')->nullable()->after('nama_barang_snapshot');
            $table->decimal('harga_beli_snapshot', 15, 2)->nullable()->after('kode_barang_snapshot');
            $table->decimal('harga_jual_snapshot', 15, 2)->nullable()->after('harga_beli_snapshot'); // Just in case price changes
            $table->string('kategori_snapshot')->nullable()->after('harga_jual_snapshot');
            $table->string('satuan_snapshot')->nullable()->after('kategori_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropColumn([
                'nama_barang_snapshot',
                'kode_barang_snapshot',
                'harga_beli_snapshot',
                'harga_jual_snapshot',
                'kategori_snapshot',
                'satuan_snapshot'
            ]);
        });
    }
};
