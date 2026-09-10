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
        // 1. Tambahkan kolom approval workflow pada tabel penugasan (SPT)
        Schema::table('penugasan', function (Blueprint $table) {
            $table->enum('status_persetujuan', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('disetujui')->after('status');
            $table->text('catatan_revisi_spt')->nullable()->after('status_persetujuan');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_revisi_spt')->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');
            $table->enum('jenis_pengawasan_spt', ['assurance', 'pemantauan_tl', 'lainnya'])->default('assurance')->after('jenis_penugasan_id');
            $table->index('status_persetujuan');
        });

        // 2. Tambahkan kolom approval telaah berjenjang pada tabel tindak_lanjut (TLHP)
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->foreignId('st_pemantauan_id')->nullable()->after('penugasan_id')->constrained('penugasan')->nullOnDelete();
            $table->enum('status_telaah', [
                'draft', 
                'diajukan_irban', 
                'revisi_irban', 
                'diajukan_inspektur', 
                'revisi_inspektur', 
                'disetujui_inspektur'
            ])->default('draft')->after('status_tindak_lanjut');

            $table->text('hasil_telaah_tim')->nullable()->after('status_telaah');
            $table->foreignId('telaah_oleh')->nullable()->after('hasil_telaah_tim')->constrained('users')->nullOnDelete();
            $table->timestamp('telaah_pada')->nullable()->after('telaah_oleh');

            $table->text('catatan_irban')->nullable()->after('telaah_pada');
            $table->foreignId('irban_disetujui_oleh')->nullable()->after('catatan_irban')->constrained('users')->nullOnDelete();
            $table->timestamp('irban_disetujui_pada')->nullable()->after('irban_disetujui_oleh');

            $table->text('catatan_inspektur')->nullable()->after('irban_disetujui_pada');
            $table->foreignId('inspektur_disetujui_oleh')->nullable()->after('catatan_inspektur')->constrained('users')->nullOnDelete();
            $table->timestamp('inspektur_disetujui_pada')->nullable()->after('inspektur_disetujui_oleh');

            // Data Surat Pengantar Matriks TL
            $table->string('no_surat_pengantar', 100)->nullable()->after('inspektur_disetujui_pada');
            $table->date('tgl_surat_pengantar')->nullable()->after('no_surat_pengantar');
            $table->string('tujuan_surat_pengantar', 255)->nullable()->after('tgl_surat_pengantar');

            $table->index('status_telaah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropForeign(['st_pemantauan_id']);
            $table->dropForeign(['telaah_oleh']);
            $table->dropForeign(['irban_disetujui_oleh']);
            $table->dropForeign(['inspektur_disetujui_oleh']);

            $table->dropColumn([
                'st_pemantauan_id',
                'status_telaah',
                'hasil_telaah_tim',
                'telaah_oleh',
                'telaah_pada',
                'catatan_irban',
                'irban_disetujui_oleh',
                'irban_disetujui_pada',
                'catatan_inspektur',
                'inspektur_disetujui_oleh',
                'inspektur_disetujui_pada',
                'no_surat_pengantar',
                'tgl_surat_pengantar',
                'tujuan_surat_pengantar',
            ]);
        });

        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'status_persetujuan',
                'catatan_revisi_spt',
                'diverifikasi_oleh',
                'diverifikasi_pada',
                'jenis_pengawasan_spt',
            ]);
        });
    }
};
