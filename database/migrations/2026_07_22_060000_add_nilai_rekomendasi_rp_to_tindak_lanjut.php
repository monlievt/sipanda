<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->decimal('nilai_rekomendasi_rp', 15, 2)->nullable()->default(0)->after('rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropColumn('nilai_rekomendasi_rp');
        });
    }
};
