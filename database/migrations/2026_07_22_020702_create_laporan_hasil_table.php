<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->string('nomor_laporan', 60)->nullable();
            $table->string('judul', 200);
            $table->date('tanggal_laporan');
            $table->string('path_file', 255)->nullable();
            $table->timestamps();

            $table->index('penugasan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil');
    }
};
