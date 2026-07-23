<?php

namespace Database\Seeders;

use App\Models\JenisPenugasan;
use App\Models\SumberPenugasan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Jenis Penugasan
        $jenis = [
            ['kategori' => 'assurance', 'nama' => 'Audit'],
            ['kategori' => 'assurance', 'nama' => 'Monitoring'],
            ['kategori' => 'assurance', 'nama' => 'Evaluasi'],
            ['kategori' => 'assurance', 'nama' => 'Monitoring dan Evaluasi'],
            ['kategori' => 'assurance', 'nama' => 'Reviu'],
            ['kategori' => 'consulting', 'nama' => 'Advisory'],
            ['kategori' => 'consulting', 'nama' => 'Facilitative Role'],
            ['kategori' => 'consulting', 'nama' => 'Training Role'],
        ];
        foreach ($jenis as $j) {
            JenisPenugasan::firstOrCreate(['nama' => $j['nama']], $j);
        }

        // Sumber Penugasan
        $sumber = ['Mandatory', 'Permintaan', 'Manajemen Risiko', 'Permintaan APH'];
        foreach ($sumber as $s) {
            SumberPenugasan::firstOrCreate(['nama' => $s]);
        }

        $this->command->info('✓ Master data jenis & sumber penugasan berhasil dibuat.');
    }
}
