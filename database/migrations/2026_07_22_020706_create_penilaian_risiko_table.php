<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_risiko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objek_penugasan_id')->constrained('objek_penugasan')->cascadeOnDelete();
            $table->year('tahun_perencanaan');
            $table->decimal('skor_aging', 3, 1)->default(0);            // dari MAX(tanggal_selesai) penugasan
            $table->decimal('skor_anggaran', 3, 1)->default(0);         // input manual Sekretariat
            $table->decimal('skor_temuan', 3, 1)->default(0);           // dari riwayat tindak_lanjut
            $table->decimal('skor_tindak_lanjut_mandek', 3, 1)->default(0); // % tindak lanjut belum selesai
            $table->decimal('skor_pengaduan_khusus', 3, 1)->default(0); // dari sumber "Permintaan"/APH
            $table->decimal('skor_total', 4, 2)->default(0);            // 30/25/20/15/10%
            $table->text('catatan_penyesuaian_manual')->nullable();
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();

            $table->unique(['objek_penugasan_id', 'tahun_perencanaan']);
            $table->index(['tahun_perencanaan', 'skor_total']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_risiko');
    }
};
