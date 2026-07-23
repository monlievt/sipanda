<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_penugasan', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['assurance', 'consulting']);
            $table->string('nama', 60);
            // Assurance: Monitoring, Evaluasi, Monitoring dan Evaluasi, Reviu, Audit
            // Consulting: Advisory, Facilitative Role, Training Role
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_penugasan');
    }
};
