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
        // 1. Master Kode Rekomendasi (PermenPAN-RB No. 42 Tahun 2011)
        Schema::create('kode_atribut_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // '01', '02', ..., '14'
            $table->text('deskripsi');
            $table->timestamps();
        });

        // 2. Master Kode Atribut Temuan (PermenPAN-RB No. 42 Tahun 2011)
        Schema::create('kode_atribut_temuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelompok', 5); // '1', '2', '3'
            $table->string('nama_kelompok', 255);
            $table->string('kode_sub_kelompok', 10); // '01', '02', ...
            $table->string('nama_sub_kelompok', 255);
            $table->string('kode_jenis', 10); // '01', '02', ...
            $table->string('kode_lengkap', 30)->index(); // '1.01.01', '2.01.03'
            $table->text('deskripsi');
            $table->string('alternatif_rekomendasi', 255)->nullable(); // '1, 5, 9, 11, 12'
            $table->timestamps();
        });

        // 3. Tambah kolom atribut pada tabel tindak_lanjut
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->foreignId('kode_atribut_temuan_id')->nullable()->after('judul_lhp')->constrained('kode_atribut_temuan')->nullOnDelete();
            $table->foreignId('kode_atribut_rekomendasi_id')->nullable()->after('kode_atribut_temuan_id')->constrained('kode_atribut_rekomendasi')->nullOnDelete();
            $table->string('kode_temuan_lengkap', 30)->nullable()->after('kode_atribut_rekomendasi_id');
            $table->string('kode_rekomendasi', 10)->nullable()->after('kode_temuan_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropForeign(['kode_atribut_temuan_id']);
            $table->dropForeign(['kode_atribut_rekomendasi_id']);
            $table->dropColumn([
                'kode_atribut_temuan_id',
                'kode_atribut_rekomendasi_id',
                'kode_temuan_lengkap',
                'kode_rekomendasi',
            ]);
        });

        Schema::dropIfExists('kode_atribut_temuan');
        Schema::dropIfExists('kode_atribut_rekomendasi');
    }
};
