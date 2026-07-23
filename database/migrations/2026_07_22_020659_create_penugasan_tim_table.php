<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_tim', function (Blueprint $table) {
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('peran', ['wakil_penanggung_jawab', 'pengendali_teknis', 'ketua_tim', 'anggota_tim']);
            // Satu orang bisa punya lebih dari satu peran di penugasan yang sama (jika berbeda peran)
            $table->primary(['penugasan_id', 'user_id', 'peran']);

            $table->index('user_id');
            $table->index(['penugasan_id', 'peran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_tim');
    }
};
