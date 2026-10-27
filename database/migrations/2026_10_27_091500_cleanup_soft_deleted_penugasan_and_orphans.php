<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bersihkan record penugasan yang sebelumnya di-soft-delete agar nomor SPT dapat digunakan kembali
        if (Schema::hasTable('penugasan')) {
            // Ambil ID penugasan yang soft-deleted
            $softDeletedIds = DB::table('penugasan')->whereNotNull('deleted_at')->pluck('id')->toArray();

            if (!empty($softDeletedIds)) {
                // Hapus relasi pivot terlebih dahulu
                if (Schema::hasTable('penugasan_tim')) {
                    DB::table('penugasan_tim')->whereIn('penugasan_id', $softDeletedIds)->delete();
                }
                if (Schema::hasTable('penugasan_objek')) {
                    DB::table('penugasan_objek')->whereIn('penugasan_id', $softDeletedIds)->delete();
                }
                if (Schema::hasTable('penugasan_irban')) {
                    DB::table('penugasan_irban')->whereIn('penugasan_id', $softDeletedIds)->delete();
                }

                // Hapus penugasan soft-deleted secara permanen
                DB::table('penugasan')->whereIn('id', $softDeletedIds)->delete();
            }
        }
    }

    public function down(): void
    {
        // Tidak perlu aksi rollback untuk pembersihan data usang
    }
};
