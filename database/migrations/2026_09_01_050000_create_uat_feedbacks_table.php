<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uat_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('guard_type', 20)->default('web'); // web, opd, guest
            $table->string('nama_pelapor', 150);
            $table->string('email_pelapor', 150)->nullable();
            $table->string('no_hp_pelapor', 30)->nullable();
            $table->string('role_pelapor', 50)->nullable();
            $table->enum('kategori', ['bug', 'saran', 'pertanyaan', 'apresiasi'])->default('bug');
            $table->enum('urgensi', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->string('url_halaman', 500)->nullable();
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->string('screenshot_path', 500)->nullable();
            $table->text('browser_info')->nullable();
            $table->enum('status', ['baru', 'sedang_ditelaah', 'diperbaiki', 'ditutup'])->default('baru');
            $table->text('catatan_admin')->nullable();
            $table->unsignedBigInteger('ditindaklanjuti_oleh')->nullable();
            $table->timestamp('ditindaklanjuti_pada')->nullable();
            $table->timestamps();

            $table->index(['status', 'kategori']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uat_feedbacks');
    }
};
