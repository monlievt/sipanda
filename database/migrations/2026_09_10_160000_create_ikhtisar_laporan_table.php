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
        Schema::create('ikhtisar_laporan', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('periode', 30); // triwulan_1, triwulan_2, triwulan_3, triwulan_4, semester_1, semester_2, tahunan
            $table->string('judul', 255);
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_laporan');
            $table->date('tanggal_awal_periode');
            $table->date('tanggal_akhir_periode');
            $table->text('simpulan')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tahun', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikhtisar_laporan');
    }
};
