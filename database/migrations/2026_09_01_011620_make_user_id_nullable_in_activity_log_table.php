<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat user_id nullable di activity_log agar ActivityLog::catat() bisa dipanggil
     * dari Laravel Scheduler/Console tanpa sesi pengguna yang aktif.
     * Tambahkan index pada kolom 'tabel' dan 'created_at' untuk mempercepat query audit log.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Hapus foreign key lama dulu, lalu buat nullable + cascade
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Index tambahan untuk filter di halaman audit log
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dropIndex(['created_at']);
        });
    }
};
