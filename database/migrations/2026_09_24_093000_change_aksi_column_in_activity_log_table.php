<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom aksi dari ENUM('create','update','delete') menjadi VARCHAR(60)
        // Gunakan raw query agar kompatibel dengan MySQL tanpa require doctrine/dbal
        try {
            DB::statement("ALTER TABLE activity_log MODIFY aksi VARCHAR(60) NOT NULL");
        } catch (\Throwable $e) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->string('aksi', 60)->change();
            });
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE activity_log MODIFY aksi ENUM('create', 'update', 'delete') NOT NULL");
        } catch (\Throwable $e) {
            // fallback
        }
    }
};
