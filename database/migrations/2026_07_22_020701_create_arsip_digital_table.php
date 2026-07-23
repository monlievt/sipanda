<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->nullable()->constrained('penugasan')->nullOnDelete();
            $table->foreignId('tindak_lanjut_id')->nullable()->constrained('tindak_lanjut')->nullOnDelete();
            $table->foreignId('bukti_tindak_lanjut_id')->nullable(); // FK di-add setelah tabel bukti_tindak_lanjut ada
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->string('ukuran_kb', 20)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('kategori', 60)->nullable(); // "Surat Tugas", "Laporan Hasil", "Bukti Tindak Lanjut"
            $table->foreignId('diunggah_oleh')->constrained('users');
            $table->timestamps();

            $table->index('penugasan_id');
            $table->index('tindak_lanjut_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_digital');
    }
};
