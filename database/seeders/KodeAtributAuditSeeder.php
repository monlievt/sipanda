<?php

namespace Database\Seeders;

use App\Models\KodeAtributRekomendasi;
use App\Models\KodeAtributTemuan;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KodeAtributAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('docs/template/kode_atribut_audit.xlsx');

        if (! file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);

        // 1. Seed Rekomendasi
        $sheetRekomendasi = $spreadsheet->getSheetByName('Rekomendasi');
        if ($sheetRekomendasi) {
            $highestRow = $sheetRekomendasi->getHighestRow();
            for ($r = 2; $r <= $highestRow; $r++) {
                $cVal = trim((string) $sheetRekomendasi->getCell('C' . $r)->getValue());
                $dVal = trim((string) $sheetRekomendasi->getCell('D' . $r)->getValue());

                if ($cVal !== '' && $cVal !== '00' && $cVal !== 'Jenis' && $dVal !== '') {
                    $kode = str_pad($cVal, 2, '0', STR_PAD_LEFT);
                    KodeAtributRekomendasi::updateOrCreate(
                        ['kode' => $kode],
                        ['deskripsi' => $dVal]
                    );
                }
            }
            $this->command->info('Berhasil menyemai data Kode Atribut Rekomendasi.');
        }

        // 2. Seed Temuan
        $sheetTemuan = $spreadsheet->getSheetByName('Temuan');
        if ($sheetTemuan) {
            $highestRow = $sheetTemuan->getHighestRow();
            $curKelKode = '';
            $curKelNama = '';
            $curSubKelKode = '';
            $curSubKelNama = '';

            for ($r = 2; $r <= $highestRow; $r++) {
                $cA = trim((string) $sheetTemuan->getCell('A' . $r)->getValue());
                $cB = trim((string) $sheetTemuan->getCell('B' . $r)->getValue());
                $cC = trim((string) $sheetTemuan->getCell('C' . $r)->getValue());
                $cD = trim((string) $sheetTemuan->getCell('D' . $r)->getValue());
                $cE = trim((string) $sheetTemuan->getCell('E' . $r)->getValue());

                // Header Kelompok
                if ($cA !== '' && $cD !== '' && $cB === '' && $cC === '') {
                    $curKelKode = $cA;
                    $curKelNama = $cD;
                    continue;
                }

                // Header Sub Kelompok
                if ($cB !== '' && $cD !== '' && $cA === '' && $cC === '') {
                    $curSubKelKode = str_pad($cB, 2, '0', STR_PAD_LEFT);
                    $curSubKelNama = $cD;
                    continue;
                }

                // Item Jenis Temuan
                if ($cC !== '' && $cD !== '') {
                    $kodeJenis = str_pad($cC, 2, '0', STR_PAD_LEFT);
                    $kodeLengkap = "{$curKelKode}.{$curSubKelKode}.{$kodeJenis}";

                    KodeAtributTemuan::updateOrCreate(
                        ['kode_lengkap' => $kodeLengkap],
                        [
                            'kode_kelompok'         => $curKelKode,
                            'nama_kelompok'         => $curKelNama,
                            'kode_sub_kelompok'     => $curSubKelKode,
                            'nama_sub_kelompok'     => $curSubKelNama,
                            'kode_jenis'            => $kodeJenis,
                            'deskripsi'             => $cD,
                            'alternatif_rekomendasi'=> $cE ?: null,
                        ]
                    );
                }
            }
            $this->command->info('Berhasil menyemai data Kode Atribut Temuan.');
        }
    }
}
