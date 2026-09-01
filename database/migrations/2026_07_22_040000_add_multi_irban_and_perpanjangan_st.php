<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pivot Table Multi-Irban Penanggung Jawab
        Schema::create('penugasan_irban', function (Blueprint $table) {
            $table->foreignId('penugasan_id')->constrained('penugasan')->onDelete('cascade');
            $table->foreignId('irban_id')->constrained('irbans')->onDelete('cascade');
            $table->primary(['penugasan_id', 'irban_id']);
        });

        // 2. Tambah penugasan_induk_id untuk ST Perpanjangan pada tabel penugasan
        Schema::table('penugasan', function (Blueprint $table) {
            $table->unsignedBigInteger('penugasan_induk_id')->nullable()->after('pkppt_id');
            $table->foreign('penugasan_induk_id')->references('id')->on('penugasan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropForeign(['penugasan_induk_id']);
            $table->dropColumn('penugasan_induk_id');
        });
        Schema::dropIfExists('penugasan_irban');
    }
};
