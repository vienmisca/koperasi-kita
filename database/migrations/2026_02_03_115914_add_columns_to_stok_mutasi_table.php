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
        Schema::table('stok_mutasi', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_mutasi', 'no_mutasi')) {
                $table->string('no_mutasi')->nullable()->after('id_mutasi')->index();
            }
            if (!Schema::hasColumn('stok_mutasi', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('stok_sesudah');
            }
            if (!Schema::hasColumn('stok_mutasi', 'satuan')) {
                $table->string('satuan')->nullable()->after('jumlah');
            }
        });

        // Safe Modify for Postgres & MySQL
        $driver = DB::getDriverName();
        try {
            if ($driver === 'pgsql') {
                 // Postgres Syntax
                 DB::statement("ALTER TABLE stok_mutasi ALTER COLUMN jenis TYPE VARCHAR(50)");
                 DB::statement("ALTER TABLE stok_mutasi ALTER COLUMN sumber TYPE VARCHAR(50)");
            } else {
                 // MySQL Syntax
                 DB::statement("ALTER TABLE stok_mutasi MODIFY COLUMN jenis VARCHAR(50) NOT NULL");
                 DB::statement("ALTER TABLE stok_mutasi MODIFY COLUMN sumber VARCHAR(50) NOT NULL");
            }
        } catch (\Exception $e) {
            // Ignore if already changed or error, but at least columns are added
        }
    }

    public function down(): void
    {
        Schema::table('stok_mutasi', function (Blueprint $table) {
            $table->dropColumn(['no_mutasi', 'lokasi', 'satuan']);
        });
    }
};
