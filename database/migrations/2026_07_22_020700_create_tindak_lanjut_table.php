<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->text('uraian_temuan');
            $table->text('rekomendasi');
            $table->enum('status_tindak_lanjut', [
                'belum',
                'proses',
                'menunggu_verifikasi',
                'selesai',
                'dikembalikan'
            ])->default('belum');
            $table->date('tanggal_target')->nullable();
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index('penugasan_id');
            $table->index('status_tindak_lanjut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
