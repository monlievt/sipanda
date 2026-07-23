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
        if (!Schema::hasColumn('tindak_lanjut', 'nilai_diawasi_rp')) {
            Schema::table('tindak_lanjut', function (Blueprint $table) {
                $table->double('nilai_diawasi_rp')->default(0)->after('rekomendasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tindak_lanjut', 'nilai_diawasi_rp')) {
            Schema::table('tindak_lanjut', function (Blueprint $table) {
                $table->dropColumn('nilai_diawasi_rp');
            });
        }
    }
};
