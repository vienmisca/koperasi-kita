<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'kasir@toko.com')
            ->update(['role' => 'kasir']);
            
        // Also ensure admin has correct role just in case
        DB::table('users')
            ->where('email', 'admin@koperasi.com')
            ->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse really, but conceptually we could set it back to 'user'
    }
};
