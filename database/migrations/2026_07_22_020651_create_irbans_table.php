<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('irbans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_irban', 50); // mis. "Irban I", "Sekretariat"
            $table->text('wilayah_keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('irbans');
    }
};
