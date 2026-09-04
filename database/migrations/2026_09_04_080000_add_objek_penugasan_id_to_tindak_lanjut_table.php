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
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->foreignId('objek_penugasan_id')
                ->nullable()
                ->after('penugasan_id')
                ->constrained('objek_penugasan')
                ->nullOnDelete();
        });

        // Backfill data lama: jika penugasan hanya memiliki 1 objek penugasan, kaitkan langsung
        try {
            $penugasanObjekMap = DB::table('penugasan_objek')
                ->select('penugasan_id', DB::raw('COUNT(*) as total_objek'), DB::raw('MIN(objek_penugasan_id) as single_objek_id'))
                ->groupBy('penugasan_id')
                ->having('total_objek', '=', 1)
                ->get();

            foreach ($penugasanObjekMap as $map) {
                DB::table('tindak_lanjut')
                    ->where('penugasan_id', $map->penugasan_id)
                    ->whereNull('objek_penugasan_id')
                    ->update(['objek_penugasan_id' => $map->single_objek_id]);
            }
        } catch (\Throwable $e) {
            // Abaikan jika data kosong saat migrasi fresh
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropForeign(['objek_penugasan_id']);
            $table->dropColumn('objek_penugasan_id');
        });
    }
};
