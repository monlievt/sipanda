<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkppt', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('area_pengawasan', 150);
            $table->string('jenis_pengawasan', 100);
            $table->string('sasaran', 150)->nullable();
            $table->date('rencana_mulai');
            $table->date('rencana_selesai_laporan');
            $table->unsignedInteger('jumlah_laporan_rencana')->default(1);
            $table->foreignId('irban_id')->nullable()->constrained('irbans')->nullOnDelete();
            $table->enum('status', ['draft', 'diusulkan', 'direviu', 'ditetapkan'])->default('draft');
            $table->decimal('skor_risiko_acuan', 4, 2)->nullable();
            $table->foreignId('ditetapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_ditetapkan')->nullable();
            $table->unsignedSmallInteger('versi_revisi')->default(1);
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->index(['tahun', 'irban_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkppt');
    }
};
