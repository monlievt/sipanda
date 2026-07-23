<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->string('no_spt', 60)->unique();
            $table->foreignId('pkppt_id')->nullable()->constrained('pkppt')->nullOnDelete();
            $table->boolean('is_sesuai_pkppt')->default(false);
            $table->text('uraian_penugasan');
            $table->foreignId('sumber_penugasan_id')->constrained('sumber_penugasan');
            $table->foreignId('jenis_penugasan_id')->constrained('jenis_penugasan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['belum_berjalan', 'berjalan', 'selesai'])->default('belum_berjalan');
            $table->tinyInteger('progres_persen')->default(0);
            $table->text('keterangan_hasil')->nullable();
            $table->foreignId('irban_id')->constrained('irbans');
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            // Index untuk performa dashboard
            $table->index(['tanggal_mulai', 'irban_id']);
            $table->index(['pkppt_id', 'status']);
            $table->index(['is_sesuai_pkppt', 'tanggal_mulai']);
            $table->index('dibuat_oleh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};
