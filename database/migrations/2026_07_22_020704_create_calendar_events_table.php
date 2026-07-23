<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('google_event_id', 150)->nullable();
            $table->datetime('tanggal_mulai');
            $table->datetime('tanggal_selesai');
            $table->enum('status_sinkron', ['belum', 'tersinkron', 'gagal'])->default('belum');
            $table->timestamps();

            $table->index(['penugasan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
