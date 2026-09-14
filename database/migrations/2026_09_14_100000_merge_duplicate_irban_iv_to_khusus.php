<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cari record Inspektur Pembantu Khusus
        $irbanKhusus = DB::table('irbans')
            ->where('nama_irban', 'Inspektur Pembantu Khusus')
            ->orWhere('nama_irban', 'like', '%Khusus%')
            ->first();

        // 2. Cari record duplikat Inspektur Pembantu IV
        $irbanIvList = DB::table('irbans')
            ->where(function ($q) {
                $q->where('nama_irban', 'Inspektur Pembantu IV')
                  ->orWhere('nama_irban', 'Irban IV')
                  ->orWhere('nama_irban', 'like', '%Pembantu IV%');
            })
            ->when($irbanKhusus, fn($q) => $q->where('id', '!=', $irbanKhusus->id))
            ->get();

        if ($irbanIvList->isEmpty() && !$irbanKhusus) {
            // Belum ada sama sekali, buat Khusus
            DB::table('irbans')->insert([
                'nama_irban'         => 'Inspektur Pembantu Khusus',
                'wilayah_keterangan' => 'Irban Khusus (Investigasi & Khusus)',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            return;
        }

        if (!$irbanKhusus && $irbanIvList->isNotEmpty()) {
            // Ubah ID pertama menjadi Inspektur Pembantu Khusus
            $firstIv = $irbanIvList->first();
            DB::table('irbans')->where('id', $firstIv->id)->update([
                'nama_irban'         => 'Inspektur Pembantu Khusus',
                'wilayah_keterangan' => 'Irban Khusus (Investigasi & Khusus)',
                'updated_at'         => now(),
            ]);
            $irbanKhusus = DB::table('irbans')->where('id', $firstIv->id)->first();
            $irbanIvList = $irbanIvList->where('id', '!=', $firstIv->id);
        }

        if ($irbanKhusus && $irbanIvList->isNotEmpty()) {
            $khususId = $irbanKhusus->id;

            foreach ($irbanIvList as $oldIrban) {
                $oldId = $oldIrban->id;

                // Update users
                if (Schema::hasTable('users')) {
                    DB::table('users')->where('irban_id', $oldId)->update(['irban_id' => $khususId]);
                }

                // Update penugasan
                if (Schema::hasTable('penugasan')) {
                    DB::table('penugasan')->where('irban_id', $oldId)->update(['irban_id' => $khususId]);
                }

                // Update penugasan_irban (cek duplikasi primary key penugasan_id, irban_id)
                if (Schema::hasTable('penugasan_irban')) {
                    $existingPivots = DB::table('penugasan_irban')->where('irban_id', $oldId)->get();
                    foreach ($existingPivots as $pivot) {
                        $alreadyExists = DB::table('penugasan_irban')
                            ->where('penugasan_id', $pivot->penugasan_id)
                            ->where('irban_id', $khususId)
                            ->exists();
                        if (!$alreadyExists) {
                            DB::table('penugasan_irban')
                                ->where('penugasan_id', $pivot->penugasan_id)
                                ->where('irban_id', $oldId)
                                ->update(['irban_id' => $khususId]);
                        } else {
                            DB::table('penugasan_irban')
                                ->where('penugasan_id', $pivot->penugasan_id)
                                ->where('irban_id', $oldId)
                                ->delete();
                        }
                    }
                }

                // Update pkppt
                if (Schema::hasTable('pkppt')) {
                    DB::table('pkppt')->where('irban_id', $oldId)->update(['irban_id' => $khususId]);
                }

                // Update konsultasi
                if (Schema::hasTable('konsultasi')) {
                    DB::table('konsultasi')->where('irban_id', $oldId)->update(['irban_id' => $khususId]);
                }

                // Update kapasitas_sdm
                if (Schema::hasTable('kapasitas_sdm')) {
                    // Hindari unique constraint (irban_id, tahun_perencanaan)
                    $sdmRows = DB::table('kapasitas_sdm')->where('irban_id', $oldId)->get();
                    foreach ($sdmRows as $sdm) {
                        $exists = DB::table('kapasitas_sdm')
                            ->where('irban_id', $khususId)
                            ->where('tahun_perencanaan', $sdm->tahun_perencanaan)
                            ->exists();
                        if (!$exists) {
                            DB::table('kapasitas_sdm')->where('id', $sdm->id)->update(['irban_id' => $khususId]);
                        } else {
                            DB::table('kapasitas_sdm')->where('id', $sdm->id)->delete();
                        }
                    }
                }

                // Update evaluasi_tahunan
                if (Schema::hasTable('evaluasi_tahunan')) {
                    DB::table('evaluasi_tahunan')->where('irban_id', $oldId)->update(['irban_id' => $khususId]);
                }

                // Hapus row lama dari irbans
                DB::table('irbans')->where('id', $oldId)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
