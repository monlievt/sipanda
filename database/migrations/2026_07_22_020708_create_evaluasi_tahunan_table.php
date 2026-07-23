<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_tahunan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun_evaluasi');
            $table->foreignId('irban_id')->nullable()->constrained('irbans')->nullOnDelete(); // null = seluruh instansi
            $table->decimal('persen_objek_terealisasi', 5, 2)->default(0);
            $table->decimal('persen_laporan_tepat_waktu', 5, 2)->default(0);
            $table->decimal('persen_tindak_lanjut_selesai', 5, 2)->default(0);
            $table->text('catatan_evaluasi')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->index(['tahun_evaluasi', 'irban_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_tahunan');
    }
};
