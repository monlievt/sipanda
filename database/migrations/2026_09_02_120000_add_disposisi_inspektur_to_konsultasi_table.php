<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->text('catatan_disposisi_inspektur')->nullable()->after('berkas_pendukung');
            $table->unsignedBigInteger('disposisi_inspektur_oleh')->nullable()->after('catatan_disposisi_inspektur');
            $table->timestamp('disposisi_inspektur_pada')->nullable()->after('disposisi_inspektur_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropColumn(['catatan_disposisi_inspektur', 'disposisi_inspektur_oleh', 'disposisi_inspektur_pada']);
        });
    }
};
