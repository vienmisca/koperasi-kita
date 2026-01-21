<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->string('no_penjualan')->unique();
            $table->date('tanggal');
            $table->foreignId('id_user')->constrained('users', 'id');
            $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggan', 'id_pelanggan');
            $table->decimal('total', 12, 2);
            $table->decimal('bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'bon'])->default('tunai');
            $table->enum('status', ['selesai', 'pending', 'batal'])->default('selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};