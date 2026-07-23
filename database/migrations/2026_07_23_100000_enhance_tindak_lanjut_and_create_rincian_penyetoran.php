<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Rincian Penyetoran Keuangan Kas Daerah (Penyelesaian Finansial)
        Schema::create('rincian_penyetoran_tl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tindak_lanjut_id')->constrained('tindak_lanjut')->cascadeOnDelete();
            $table->string('mata_uang', 10)->default('IDR');
            $table->decimal('nilai_setor_rp', 15, 2);
            $table->string('nama_bank', 100)->nullable();
            $table->string('no_referensi_ntpn', 100)->nullable();
            $table->date('tgl_setor');
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->index('tindak_lanjut_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_penyetoran_tl');
    }
};
