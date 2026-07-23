<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objek_penugasan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->enum('kategori', ['opd', 'kecamatan', 'desa', 'kelurahan', 'lainnya'])->default('opd');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('kategori');
        });
        // FK dari users.objek_penugasan_id ke tabel ini sudah didefinisikan
        // sebagai kolom unsignedBigInteger di users migration — constraint FK-nya
        // akan ditegakkan oleh MySQL di production (SQLite dev tidak strict).
    }

    public function down(): void
    {
        Schema::dropIfExists('objek_penugasan');
    }
};
