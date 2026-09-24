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
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            if (!Schema::hasColumn('tindak_lanjut', 'status_rekomendasi_usulan')) {
                $table->string('status_rekomendasi_usulan', 50)->nullable()->after('hasil_telaah_tim');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            if (Schema::hasColumn('tindak_lanjut', 'status_rekomendasi_usulan')) {
                $table->dropColumn('status_rekomendasi_usulan');
            }
        });
    }
};
