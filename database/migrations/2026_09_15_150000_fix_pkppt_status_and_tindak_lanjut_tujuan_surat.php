<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah column status pada tabel pkppt agar mendukung 'diarsipkan' dan revisi versi
        if (Schema::hasTable('pkppt')) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `pkppt` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'draft'");
            } else {
                Schema::table('pkppt', function (Blueprint $table) {
                    $table->string('status', 50)->default('draft')->change();
                });
            }
        }

        // 2. Tambah column tujuan_surat_objek_id pada tabel tindak_lanjut
        if (Schema::hasTable('tindak_lanjut')) {
            if (!Schema::hasColumn('tindak_lanjut', 'tujuan_surat_objek_id')) {
                Schema::table('tindak_lanjut', function (Blueprint $table) {
                    $table->foreignId('tujuan_surat_objek_id')
                        ->nullable()
                        ->after('tujuan_surat_pengantar')
                        ->constrained('objek_penugasan')
                        ->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tindak_lanjut') && Schema::hasColumn('tindak_lanjut', 'tujuan_surat_objek_id')) {
            Schema::table('tindak_lanjut', function (Blueprint $table) {
                $table->dropForeign(['tujuan_surat_objek_id']);
                $table->dropColumn('tujuan_surat_objek_id');
            });
        }
    }
};
