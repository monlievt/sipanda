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
        // Ubah nama Inspektur Pembantu IV menjadi Inspektur Pembantu Khusus
        DB::table('irbans')
            ->where('nama_irban', 'like', '%Pembantu IV%')
            ->orWhere('nama_irban', 'like', '%Irban IV%')
            ->update([
                'nama_irban'         => 'Inspektur Pembantu Khusus',
                'wilayah_keterangan' => 'Irban Khusus (Investigasi & Khusus)',
                'updated_at'         => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('irbans')
            ->where('nama_irban', 'Inspektur Pembantu Khusus')
            ->update([
                'nama_irban'         => 'Inspektur Pembantu IV',
                'wilayah_keterangan' => 'Irban IV (Khusus)',
                'updated_at'         => now(),
            ]);
    }
};
