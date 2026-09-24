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
        if (!Schema::hasTable('kelompok_pengawasan')) {
            Schema::create('kelompok_pengawasan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kelompok');
                $table->string('kode_kelompok', 50)->nullable();
                $table->text('deskripsi_singkat')->nullable();
                $table->text('bentuk_pengawasan')->nullable();
                $table->integer('urutan')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('pkppt') && !Schema::hasColumn('pkppt', 'kelompok_pengawasan_id')) {
            Schema::table('pkppt', function (Blueprint $table) {
                $table->foreignId('kelompok_pengawasan_id')->nullable()->after('jenis_pengawasan_id')->constrained('kelompok_pengawasan')->nullOnDelete();
            });
        }

        if (Schema::hasTable('penugasan') && !Schema::hasColumn('penugasan', 'kelompok_pengawasan_id')) {
            Schema::table('penugasan', function (Blueprint $table) {
                $table->foreignId('kelompok_pengawasan_id')->nullable()->after('jenis_penugasan_id')->constrained('kelompok_pengawasan')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('penugasan') && Schema::hasColumn('penugasan', 'kelompok_pengawasan_id')) {
            Schema::table('penugasan', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kelompok_pengawasan_id');
            });
        }

        if (Schema::hasTable('pkppt') && Schema::hasColumn('pkppt', 'kelompok_pengawasan_id')) {
            Schema::table('pkppt', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kelompok_pengawasan_id');
            });
        }

        Schema::dropIfExists('kelompok_pengawasan');
    }
};
