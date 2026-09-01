<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\SumberPenugasan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CsvImportService
{
    /**
     * Parse baris CSV dari file yang diunggah dengan auto-detect delimiter (koma atau titik koma).
     */
    public function parseCsv(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $content = file_get_contents($path);

        // Hapus UTF-8 BOM jika ada
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^{$bom}/", '', $content);

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [];
        }

        // Deteksi delimiter dari baris pertama
        $firstLine = $lines[0];
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $rows = [];
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        while (($data = fgetcsv($stream, 0, $delimiter)) !== false) {
            // Trim seluruh kolom
            $rows[] = array_map('trim', $data);
        }
        fclose($stream);

        return $rows;
    }

    /**
     * 1. Import Data Penugasan (SPT)
     */
    public function importPenugasan(array $rows, int $userId): array
    {
        if (count($rows) <= 1) {
            return ['success' => 0, 'errors' => ['File CSV kosong atau hanya berisi header.']];
        }

        $header = array_shift($rows);
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $lineNum = $index + 2;
                if (empty(array_filter($row))) {
                    continue; // Abaikan baris kosong
                }

                // Struktur kolom yang diharapkan:
                // 0: No SPT (wajib)
                // 1: Uraian Penugasan (wajib)
                // 2: Nama Irban (mis. Irban I)
                // 3: Jenis Penugasan (mis. Audit Kinerja)
                // 4: Sumber Penugasan (mis. PKPT / Mandatori)
                // 5: Objek Penugasan (dipisah koma jika > 1)
                // 6: Tanggal Mulai (Y-m-d atau d/m/Y)
                // 7: Tanggal Selesai (Y-m-d atau d/m/Y)
                // 8: Status (belum_berjalan, berjalan, penyusunan_laporan, reviu, selesai)

                $noSpt = $row[0] ?? null;
                $uraian = $row[1] ?? null;

                if (! $noSpt || ! $uraian) {
                    $errors[] = "Baris {$lineNum}: No. SPT dan Uraian Penugasan wajib diisi.";
                    continue;
                }

                // Cari Irban
                $irbanNama = $row[2] ?? '';
                $irban = $irbanNama ? Irban::where('nama_irban', 'like', "%{$irbanNama}%")->first() : null;

                // Cari atau buat Jenis Penugasan
                $jenisNama = $row[3] ?? 'Audit Operasional';
                $jenis = JenisPenugasan::firstOrCreate(
                    ['nama' => $jenisNama],
                    ['kategori' => 'assurance']
                );

                // Cari atau buat Sumber Penugasan
                $sumberNama = $row[4] ?? 'PKPT';
                $sumber = SumberPenugasan::firstOrCreate(
                    ['nama' => $sumberNama],
                    ['kode' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $sumberNama), 0, 5))]
                );

                // Parse Tanggal
                $tglMulai = $this->parseDate($row[6] ?? null) ?? now()->toDateString();
                $tglSelesai = $this->parseDate($row[7] ?? null) ?? Carbon::parse($tglMulai)->addDays(14)->toDateString();

                // Status SPT
                $status = strtolower($row[8] ?? 'selesai');
                $allowedStatus = ['belum_berjalan', 'berjalan', 'penyusunan_laporan', 'reviu', 'selesai'];
                if (! in_array($status, $allowedStatus)) {
                    $status = 'selesai';
                }

                $penugasan = Penugasan::updateOrCreate(
                    ['no_spt' => $noSpt],
                    [
                        'uraian_penugasan'    => $uraian,
                        'irban_id'            => $irban?->id ?? 1,
                        'jenis_penugasan_id'  => $jenis->id,
                        'sumber_penugasan_id' => $sumber->id,
                        'tanggal_mulai'       => $tglMulai,
                        'tanggal_selesai'     => $tglSelesai,
                        'status'              => $status,
                        'progres_persen'      => $status === 'selesai' ? 100 : 0,
                        'is_sesuai_pkppt'     => true,
                        'dibuat_oleh'         => $userId,
                    ]
                );

                // Relasi Objek Penugasan
                if (! empty($row[5])) {
                    $objekNames = explode(',', $row[5]);
                    $objekIds = [];
                    foreach ($objekNames as $oName) {
                        $oName = trim($oName);
                        if ($oName) {
                            $obj = ObjekPenugasan::firstOrCreate(
                                ['nama' => $oName],
                                ['kategori' => 'opd', 'is_active' => true]
                            );
                            $objekIds[] = $obj->id;
                        }
                    }
                    if (! empty($objekIds)) {
                        $penugasan->objekPenugasan()->sync($objekIds);
                    }
                }

                $success++;
            }

            DB::commit();
            ActivityLog::catat('penugasan', 0, 'create', null, ['import_type' => 'spt_csv', 'count' => $success]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[SIPANDA Import SPT] Error: ' . $e->getMessage());
            return ['success' => 0, 'errors' => ['Gagal memproses import data penugasan: ' . $e->getMessage()]];
        }

        return ['success' => $success, 'errors' => $errors];
    }

    /**
     * 2. Import Data Matriks Tindak Lanjut (LHP)
     */
    public function importTindakLanjut(array $rows, int $userId): array
    {
        if (count($rows) <= 1) {
            return ['success' => 0, 'errors' => ['File CSV kosong atau hanya berisi header.']];
        }

        $header = array_shift($rows);
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $lineNum = $index + 2;
                if (empty(array_filter($row))) {
                    continue;
                }

                // Struktur kolom yang diharapkan:
                // 0: No LHP (wajib)
                // 1: No SPT (relasi ke penugasan)
                // 2: Uraian Temuan (wajib)
                // 3: Rekomendasi Wajib (wajib)
                // 4: Nilai Rekomendasi Rp (mis. 15000000 atau 15.000.000)
                // 5: Target Penyelesaian (Y-m-d atau d/m/Y)
                // 6: Status Tindak Lanjut (selesai, proses, belum, tdt)
                // 7: Judul LHP (opsional)
                // 8: Tanggal LHP (opsional)

                $noLhp = $row[0] ?? null;
                $uraianTemuan = $row[2] ?? null;
                $rekomendasi = $row[3] ?? null;

                if (! $uraianTemuan || ! $rekomendasi) {
                    $errors[] = "Baris {$lineNum}: Uraian Temuan dan Rekomendasi wajib diisi.";
                    continue;
                }

                // Cari Penugasan berdasarkan No SPT
                $noSpt = $row[1] ?? null;
                $penugasan = null;
                if ($noSpt) {
                    $penugasan = Penugasan::where('no_spt', $noSpt)->first();
                }

                if (! $penugasan) {
                    // Buat dummy penugasan jika belum ada
                    $penugasan = Penugasan::firstOrCreate(
                        ['no_spt' => $noSpt ?: ('HISTORIS-' . ($noLhp ?: date('Ymd-His')))],
                        [
                            'uraian_penugasan'    => 'Penugasan Hasil LHP ' . ($noLhp ?: 'Historis'),
                            'irban_id'            => 1,
                            'jenis_penugasan_id'  => 1,
                            'sumber_penugasan_id' => 1,
                            'tanggal_mulai'       => now()->toDateString(),
                            'tanggal_selesai'     => now()->addDays(14)->toDateString(),
                            'status'              => 'selesai',
                            'dibuat_oleh'         => $userId,
                        ]
                    );
                }

                // Parse Nilai Rupiah
                $nilaiRp = $this->parseRupiah($row[4] ?? 0);

                // Parse Status
                $rawStatus = strtolower($row[6] ?? 'belum');
                $statusTL = match($rawStatus) {
                    'sesuai', 'selesai'   => 'selesai',
                    'proses', 'belum sesuai' => 'proses',
                    'tdt', 'tidak dapat ditindaklanjuti' => 'tdt',
                    default => 'belum',
                };

                // Parse Target Date
                $targetDate = $this->parseDate($row[5] ?? null);
                $tglLhp     = $this->parseDate($row[8] ?? null) ?? now()->toDateString();
                $judulLhp   = $row[7] ?? ($penugasan ? $penugasan->uraian_penugasan : ('LHP No. ' . $noLhp));

                TindakLanjut::create([
                    'penugasan_id'         => $penugasan->id,
                    'no_lhp'               => $noLhp,
                    'judul_lhp'            => $judulLhp,
                    'tgl_lhp'              => $tglLhp,
                    'uraian_temuan'        => $uraianTemuan,
                    'rekomendasi'          => $rekomendasi,
                    'nilai_rekomendasi_rp' => $nilaiRp,
                    'tanggal_target'       => $targetDate,
                    'status_tindak_lanjut' => $statusTL,
                    'dibuat_oleh'          => $userId,
                ]);

                $success++;
            }

            DB::commit();
            ActivityLog::catat('tindak_lanjut', 0, 'create', null, ['import_type' => 'lhp_csv', 'count' => $success]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[SIPANDA Import TL] Error: ' . $e->getMessage());
            return ['success' => 0, 'errors' => ['Gagal memproses import data tindak lanjut: ' . $e->getMessage()]];
        }

        return ['success' => $success, 'errors' => $errors];
    }

    /**
     * 3. Import Data Master Objek Pengawasan
     */
    public function importObjek(array $rows, int $userId): array
    {
        if (count($rows) <= 1) {
            return ['success' => 0, 'errors' => ['File CSV kosong atau hanya berisi header.']];
        }

        $header = array_shift($rows);
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $lineNum = $index + 2;
                if (empty(array_filter($row))) {
                    continue;
                }

                // 0: Nama Objek (wajib)
                // 1: Kategori (opd, kecamatan, desa, kelurahan, lainnya)
                // 2: Status (aktif/nonaktif)

                $nama = $row[0] ?? null;
                if (! $nama) {
                    $errors[] = "Baris {$lineNum}: Nama Instansi / Objek wajib diisi.";
                    continue;
                }

                $kategori = strtolower($row[1] ?? 'opd');
                $allowedKategori = ['opd', 'kecamatan', 'desa', 'kelurahan', 'lainnya'];
                if (! in_array($kategori, $allowedKategori)) {
                    $kategori = 'opd';
                }

                $rawStatus = strtolower($row[2] ?? 'aktif');
                $isActive = in_array($rawStatus, ['aktif', '1', 'true', 'active']);

                ObjekPenugasan::updateOrCreate(
                    ['nama' => $nama],
                    [
                        'kategori'  => $kategori,
                        'is_active' => $isActive,
                    ]
                );

                $success++;
            }

            DB::commit();
            ActivityLog::catat('objek_penugasan', 0, 'create', null, ['import_type' => 'objek_csv', 'count' => $success]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[SIPANDA Import Objek] Error: ' . $e->getMessage());
            return ['success' => 0, 'errors' => ['Gagal memproses import data objek pengawasan: ' . $e->getMessage()]];
        }

        return ['success' => $success, 'errors' => $errors];
    }

    /**
     * Helper pembersih format angka Rupiah
     */
    private function parseRupiah($val): float
    {
        if (is_numeric($val)) {
            return (float) $val;
        }
        $clean = preg_replace('/[^\d]/', '', (string) $val);
        return (float) ($clean ?: 0);
    }

    /**
     * Helper parser tanggal multi-format (Y-m-d, d/m/Y, d-m-Y)
     */
    private function parseDate(?string $dateStr): ?string
    {
        if (! $dateStr) {
            return null;
        }

        $dateStr = trim($dateStr);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd.m.Y'];

        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $dateStr);
                if ($d !== false) {
                    return $d->toDateString();
                }
            } catch (\Exception $e) {
                // Lanjut ke format berikutnya
            }
        }

        try {
            return Carbon::parse($dateStr)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
