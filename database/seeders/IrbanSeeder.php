<?php

namespace Database\Seeders;

use App\Models\Irban;
use Illuminate\Database\Seeder;

class IrbanSeeder extends Seeder
{
    public function run(): void
    {
        $irbans = [
            ['nama_irban' => 'Inspektur Pembantu I',  'wilayah_keterangan' => 'Irban I — Wilayah I'],
            ['nama_irban' => 'Inspektur Pembantu II', 'wilayah_keterangan' => 'Irban II — Wilayah II'],
            ['nama_irban' => 'Inspektur Pembantu III','wilayah_keterangan' => 'Irban III — Wilayah III'],
            ['nama_irban' => 'Inspektur Pembantu IV', 'wilayah_keterangan' => 'Irban IV (Khusus)'],
            ['nama_irban' => 'Sekretariat',            'wilayah_keterangan' => 'Sekretariat Inspektorat'],
        ];

        foreach ($irbans as $irban) {
            Irban::firstOrCreate(['nama_irban' => $irban['nama_irban']], $irban);
        }

        $this->command->info('✓ 5 Irban/bidang berhasil dibuat (Irban I-IV + Sekretariat).');
    }
}
