<?php

use App\Models\Irban;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memperbaiki pemetaan irban_id seluruh user berdasarkan data-pegawai.csv
     */
    public function up(): void
    {
        $irbanMap = Irban::pluck('id', 'nama_irban');
        // Map romawi ke id
        $romanMap = [
            'I'   => $irbanMap['Inspektur Pembantu I'] ?? 1,
            'II'  => $irbanMap['Inspektur Pembantu II'] ?? 2,
            'III' => $irbanMap['Inspektur Pembantu III'] ?? 3,
            'IV'  => $irbanMap['Inspektur Pembantu IV'] ?? 4,
        ];
        $sekretariatId = $irbanMap['Sekretariat'] ?? 5;

        // 1. Perbaiki spesifik pejabat Irban Utama
        // Irban II: Sigit Prasetyo (NIP: 197310211993101001)
        User::where('nip', '197310211993101001')->update(['irban_id' => $romanMap['II']]);
        $uSigit = User::where('nip', '197310211993101001')->first();
        if ($uSigit && ! $uSigit->hasRole('irban')) {
            $uSigit->assignRole('irban');
        }

        // Irban III: Suyatno (NIP: 196906221992021001)
        User::where('nip', '196906221992021001')->update(['irban_id' => $romanMap['III']]);
        $uSuyatno = User::where('nip', '196906221992021001')->first();
        if ($uSuyatno && ! $uSuyatno->hasRole('irban')) {
            $uSuyatno->assignRole('irban');
        }

        // Irban IV: Didik Agit Wahyudianto (NIP: 196612061992031009)
        User::where('nip', '196612061992031009')->update(['irban_id' => $romanMap['IV']]);
        $uDidik = User::where('nip', '196612061992031009')->first();
        if ($uDidik && ! $uDidik->hasRole('irban')) {
            $uDidik->assignRole('irban');
        }

        // Irban I (Plt): Eko Darminto (NIP: 197203171998031007)
        User::where('nip', '197203171998031007')->update(['irban_id' => $romanMap['I']]);
        $uEko = User::where('nip', '197203171998031007')->first();
        if ($uEko && ! $uEko->hasRole('irban')) {
            $uEko->assignRole('irban');
        }

        // 2. Baca file data-pegawai.csv jika ada untuk menyelaraskan seluruh pegawai
        $csvPath = base_path('docs/data-pegawai.csv');
        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            fgetcsv($handle); // skip header

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 12) continue;
                $nip    = trim($row[4] ?? '');
                $bidang = trim($row[8] ?? '');
                if (empty($nip)) continue;

                $targetIrbanId = null;
                if (preg_match('/\b(IV|III|II|I)\b/i', $bidang, $m)) {
                    $roman = strtoupper($m[1]);
                    $targetIrbanId = $romanMap[$roman] ?? null;
                } elseif (stripos($bidang, 'SEKRETARIAT') !== false || stripos($bidang, 'SEKRETARIS') !== false || stripos($bidang, 'KEPEGAWAIAN') !== false) {
                    $targetIrbanId = $sekretariatId;
                }

                if ($targetIrbanId) {
                    User::where('nip', $nip)->update(['irban_id' => $targetIrbanId]);
                }
            }
            fclose($handle);
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
