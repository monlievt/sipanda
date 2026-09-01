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
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->index(['no_lhp', 'status_tindak_lanjut']);
            $table->index(['tgl_lhp', 'status_tindak_lanjut']);
            $table->index(['penugasan_id', 'status_tindak_lanjut']);
        });

        Schema::table('penugasan', function (Blueprint $table) {
            $table->index(['status', 'irban_id']);
            $table->index(['tanggal_mulai', 'status']);
        });

        Schema::table('pkppt', function (Blueprint $table) {
            $table->index(['tahun', 'irban_id', 'status']);
        });

        Schema::table('bukti_tindak_lanjut', function (Blueprint $table) {
            $table->index(['tindak_lanjut_id', 'status_verifikasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukti_tindak_lanjut', function (Blueprint $table) {
            $table->dropIndex(['tindak_lanjut_id', 'status_verifikasi']);
        });

        Schema::table('pkppt', function (Blueprint $table) {
            $table->dropIndex(['tahun', 'irban_id', 'status']);
        });

        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropIndex(['status', 'irban_id']);
            $table->dropIndex(['tanggal_mulai', 'status']);
        });

        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropIndex(['no_lhp', 'status_tindak_lanjut']);
            $table->dropIndex(['tgl_lhp', 'status_tindak_lanjut']);
            $table->dropIndex(['penugasan_id', 'status_tindak_lanjut']);
        });
    }
};
