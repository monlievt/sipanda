<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kapasitas_sdm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('irban_id')->constrained('irbans')->cascadeOnDelete();
            $table->year('tahun_perencanaan');
            $table->unsignedInteger('jumlah_hari_tersedia');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['irban_id', 'tahun_perencanaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kapasitas_sdm');
    }
};
