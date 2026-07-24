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
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('objek_penugasan_id')->nullable()->constrained('objek_penugasan')->onDelete('cascade');
            $table->foreignId('irban_id')->nullable()->constrained('irbans')->onDelete('cascade');
            $table->string('area_konsultasi', 100);
            $table->string('judul_permasalahan', 255);
            $table->text('uraian_permasalahan');
            $table->string('berkas_pendukung')->nullable();
            $table->enum('preferensi_metode', ['online', 'offline'])->default('online');
            $table->enum('metode_disetujui', ['online', 'offline'])->nullable();
            $table->dateTime('tanggal_tatap_muka')->nullable();
            $table->string('lokasi_tatap_muka', 150)->nullable();
            $table->enum('status', ['menunggu_disposisi', 'berjalan', 'selesai', 'ditolak'])->default('menunggu_disposisi');
            $table->text('kesimpulan_advis')->nullable();
            $table->string('berita_acara_pdf')->nullable();
            $table->boolean('is_faq_public')->default(false);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('konsultasi_tim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('peran', ['penanggung_jawab', 'pengendali_teknis', 'ketua_tim', 'anggota_tim']);
            $table->timestamps();
        });

        Schema::create('konsultasi_chat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe_pengirim', ['opd', 'apip'])->default('opd');
            $table->text('pesan');
            $table->string('lampiran_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi_chat');
        Schema::dropIfExists('konsultasi_tim');
        Schema::dropIfExists('konsultasi');
    }
};
