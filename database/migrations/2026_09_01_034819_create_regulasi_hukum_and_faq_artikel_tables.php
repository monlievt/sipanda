<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Bank Regulasi & Dokumen Dasar Hukum APIP
        Schema::create('regulasi_hukum', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('nomor_regulasi', 100);
            $table->unsignedSmallInteger('tahun');
            $table->string('jenis_regulasi', 50); // uu, pp, perpres, permendagri, perda, perbup, se, juknis, dll.
            $table->string('kategori', 50)->default('umum'); // keuangan, pbj, desa, aset, kepegawaian, investigasi, umum
            $table->text('ringkasan_eksekutif')->nullable(); // Intisari pasal-pasal kunci
            $table->string('file_path', 255)->nullable();
            $table->string('nama_file_asli', 255)->nullable();
            $table->string('ukuran_kb', 30)->nullable();
            $table->longText('teks_konten')->nullable(); // Teks dokumen yang diekstrak untuk AI Grounding & RAG
            $table->enum('visibilitas', ['publik', 'opd', 'internal'])->default('publik');
            $table->unsignedInteger('diunduh_count')->default(0);
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kategori', 'visibilitas']);
            $table->index(['tahun', 'jenis_regulasi']);
        });

        // 2. Tabel Bank Artikel FAQ / QnA Resmi APIP
        Schema::create('faq_artikel', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan', 255);
            $table->longText('jawaban');
            $table->string('kategori', 50)->default('umum');
            $table->foreignId('regulasi_hukum_id')->nullable()->constrained('regulasi_hukum')->nullOnDelete();
            $table->string('dasar_hukum_rujukan', 255)->nullable(); // contoh: "Pasal 12 Perbup Trenggalek No. 45/2025"
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->unsignedInteger('dilihat_count')->default(0);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kategori', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_artikel');
        Schema::dropIfExists('regulasi_hukum');
    }
};
