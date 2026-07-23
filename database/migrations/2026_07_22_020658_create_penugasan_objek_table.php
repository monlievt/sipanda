<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_objek', function (Blueprint $table) {
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->foreignId('objek_penugasan_id')->constrained('objek_penugasan')->cascadeOnDelete();
            $table->primary(['penugasan_id', 'objek_penugasan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_objek');
    }
};
