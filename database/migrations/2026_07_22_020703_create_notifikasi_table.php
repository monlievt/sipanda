<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penugasan_id')->nullable()->constrained('penugasan')->nullOnDelete();
            $table->enum('jenis', ['reminder_h3', 'reminder_h1', 'bukti_diunggah', 'bukti_diterima', 'bukti_ditolak', 'info_lain']);
            $table->text('pesan');
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamps();

            $table->index(['status', 'dikirim_pada']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
