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
        Schema::table('penugasan', function (Blueprint $table) {
            $table->date('tanggal_st')->nullable()->after('no_spt');
            $table->string('jenis_st', 20)->default('reguler')->after('penugasan_induk_id');
            $table->string('file_st', 255)->nullable()->after('keterangan_hasil');
            $table->string('file_st_nama', 255)->nullable()->after('file_st');
            $table->dateTime('file_st_diunggah_pada')->nullable()->after('file_st_nama');
            $table->foreignId('file_st_diunggah_oleh')->nullable()->constrained('users')->nullOnDelete()->after('file_st_diunggah_pada');

            $table->index('jenis_st');
            $table->index('tanggal_st');
        });

        // Set jenis_st = 'perpanjangan' untuk data ST perpanjangan lama
        \Illuminate\Support\Facades\DB::table('penugasan')
            ->whereNotNull('penugasan_induk_id')
            ->update(['jenis_st' => 'perpanjangan']);

        // Defaultkan tanggal_st dari tanggal_mulai untuk data lama
        \Illuminate\Support\Facades\DB::table('penugasan')
            ->whereNull('tanggal_st')
            ->update(['tanggal_st' => \Illuminate\Support\Facades\DB::raw('tanggal_mulai')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropForeign(['file_st_diunggah_oleh']);
            $table->dropColumn([
                'tanggal_st',
                'jenis_st',
                'file_st',
                'file_st_nama',
                'file_st_diunggah_pada',
                'file_st_diunggah_oleh',
            ]);
        });
    }
};
