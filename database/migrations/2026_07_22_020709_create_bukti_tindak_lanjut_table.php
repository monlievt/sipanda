<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tindak_lanjut_id')->constrained('tindak_lanjut')->cascadeOnDelete();
            $table->foreignId('diunggah_oleh')->constrained('users'); // akun OPD
            $table->text('catatan_opd');
            $table->enum('status_verifikasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();

            $table->index('status_verifikasi');
            $table->index('tindak_lanjut_id');
        });

        // Tambah FK bukti_tindak_lanjut_id ke arsip_digital
        Schema::table('arsip_digital', function (Blueprint $table) {
            $table->foreign('bukti_tindak_lanjut_id')
                  ->references('id')
                  ->on('bukti_tindak_lanjut')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('arsip_digital', function (Blueprint $table) {
            $table->dropForeign(['bukti_tindak_lanjut_id']);
        });
        Schema::dropIfExists('bukti_tindak_lanjut');
    }
};
