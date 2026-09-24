<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\User;
use Carbon\Carbon;
use ZipArchive;

class SuratTugasDocxService
{
    /**
     * Generate Surat Tugas DOCX file from template and return path to generated temp file.
     */
    public function generate(Penugasan $penugasan): string
    {
        $templatePath = base_path('docs/template/surat_tugas.docx');
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template surat tugas tidak ditemukan di: {$templatePath}");
        }

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        $tempPath = $tempDir . '/spt_' . uniqid() . '_' . time() . '.docx';
        copy($templatePath, $tempPath);

        $zip = new ZipArchive();
        if ($zip->open($tempPath) !== true) {
            throw new \RuntimeException("Gagal membuka file template DOCX.");
        }

        $xml = $zip->getFromName('word/document.xml');

        // Data Inspektur / Penandatangan
        try {
            $inspektur = User::role('inspektur')->first();
        } catch (\Throwable $e) {
            $inspektur = null;
        }

        if (!$inspektur) {
            $inspektur = User::where('jabatan', 'like', '%inspektur%')->first();
        }

        $inspekturNama = $inspektur?->nama ?? 'Ir. WIJIONO, S.T., M.MKes.';
        $inspekturNip  = $inspektur?->nip ?? '197308051997031007';
        $inspekturPangkat = $inspektur?->pangkat ?? ($inspektur?->golongan ? 'Pembina Utama Muda (' . $inspektur->golongan . ')' : 'Pembina Utama Muda (IV/c)');

        // 1. Dasar Penugasan
        $dasarText = trim($penugasan->dasar_penugasan ?? '');
        $dasarList = [];
        if (!empty($dasarText)) {
            foreach (preg_split('/\r\n|\r|\n/', $dasarText) as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $cleaned = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                    $dasarList[] = $cleaned;
                }
            }
        }

        if (empty($dasarList)) {
            $tahunSpt = $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y');
            $dasarList = [
                'Peraturan Daerah Kabupaten Trenggalek tentang Pembentukan dan Susunan Perangkat Daerah;',
                'Peraturan Bupati Trenggalek tentang Kedudukan, Susunan Organisasi, Tugas dan Fungsi serta Tata Kerja Inspektorat Daerah Kabupaten Trenggalek;',
                "Program Kerja Pengawasan Tahunan (PKPT) Inspektorat Daerah Kabupaten Trenggalek Tahun Anggaran {$tahunSpt};",
            ];
            if ($penugasan->penugasanInduk) {
                $dasarList[] = "Surat Perintah Tugas Induk Nomor: " . ($penugasan->penugasanInduk->no_spt ?? '-') . ";";
            }
        }

        $dasarXml = '';
        foreach ($dasarList as $idx => $d) {
            $num = $idx + 1;
            $text = htmlspecialchars("{$num}. {$d}", ENT_XML1);
            $dasarXml .= '<w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:left="312" w:hanging="357"/><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>' . $text . '</w:t></w:r></w:p>';
        }

        // Ganti cell dasar pada tabel pertama
        $xml = preg_replace(
            '/(<w:tc><w:tcPr><w:tcW w:w="7905".*?<\/w:tcPr>).*?(<\/w:tc>)/s',
            '$1' . $dasarXml . '$2',
            $xml,
            1
        );

        // 2. Susunan Tim (Tabel Kepada)
        $timList = [];
        $sortedTim = $penugasan->tim->sortBy(function ($m) {
            $order = [
                'penanggung_jawab'       => 1,
                'wakil_penanggung_jawab' => 2,
                'pengendali_teknis'      => 3,
                'ketua_tim'              => 4,
                'anggota_tim'            => 5,
            ];
            return $order[$m->peran] ?? 6;
        });

        $hasPj = $sortedTim->contains(fn($m) => $m->peran === 'penanggung_jawab');
        if (!$hasPj) {
            $timList[] = [
                'nama'  => $inspekturNama,
                'nip'   => $inspekturNip,
                'peran' => 'Penanggung Jawab',
            ];
        }

        $hasWpj = $sortedTim->contains(fn($m) => $m->peran === 'wakil_penanggung_jawab');
        if (!$hasWpj && $penugasan->irban) {
            $irbanUser = $penugasan->irban->users()->whereHas('roles', fn($q) => $q->where('name', 'irban'))->first();
            if ($irbanUser) {
                $timList[] = [
                    'nama'  => $irbanUser->nama,
                    'nip'   => $irbanUser->nip,
                    'peran' => 'Wakil Penanggungjawab',
                ];
            }
        }

        foreach ($sortedTim as $member) {
            $peranLabel = match($member->peran) {
                'penanggung_jawab'       => 'Penanggung Jawab',
                'wakil_penanggung_jawab' => 'Wakil Penanggungjawab',
                'pengendali_teknis'      => 'Pengendali Teknis',
                'ketua_tim'              => 'Ketua Tim',
                'anggota_tim'            => 'Anggota Tim',
                default                  => ucwords(str_replace('_', ' ', $member->peran)),
            };

            $timList[] = [
                'nama'  => $member->user?->nama ?? '-',
                'nip'   => $member->user?->nip ?? '-',
                'peran' => $peranLabel,
            ];
        }

        $tableHeaderXml = '<w:tr><w:tc><w:tcPr><w:tcW w:w="738" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>No.</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3242" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>Nama</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2821" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>NIP</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2408" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>Jabatan</w:t></w:r></w:p></w:tc></w:tr>';

        $rowsXml = $tableHeaderXml;
        foreach ($timList as $idx => $person) {
            $num = $idx + 1;
            $nama = htmlspecialchars($person['nama'], ENT_XML1);
            $nip  = htmlspecialchars($person['nip'], ENT_XML1);
            $peran = htmlspecialchars($person['peran'], ENT_XML1);

            $rowsXml .= '<w:tr><w:tc><w:tcPr><w:tcW w:w="738" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>' . $num . '.</w:t></w:r></w:p></w:tc>';
            $rowsXml .= '<w:tc><w:tcPr><w:tcW w:w="3242" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>' . $nama . '</w:t></w:r></w:p></w:tc>';
            $rowsXml .= '<w:tc><w:tcPr><w:tcW w:w="2821" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>' . $nip . '</w:t></w:r></w:p></w:tc>';
            $rowsXml .= '<w:tc><w:tcPr><w:tcW w:w="2408" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:bCs/><w:lang w:val="id-ID"/></w:rPr><w:t>' . $peran . '</w:t></w:r></w:p></w:tc></w:tr>';
        }

        // Ganti tabel Tim Kepada (tabel kedua)
        $xml = preg_replace(
            '/(<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid".*?<\/w:tblGrid>).*?(<\/w:tbl>)/s',
            '$1' . $rowsXml . '$2',
            $xml,
            1
        );

        // 3. Mapping Penggantian Placeholder Teks
        $uraian = $penugasan->uraian_penugasan ?? 'Pelaksanaan Kegiatan Pengawasan';
        $objek  = $penugasan->objekPenugasan->pluck('nama')->implode(', ') ?: ($penugasan->irban?->nama_irban ?? 'Perangkat Daerah Kabupaten Trenggalek');
        $tglMulai   = $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : '-';
        $tglSelesai = $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->translatedFormat('d F Y') : '-';
        $tahunAnggaran = $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y');
        $tglPenetapan  = $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : Carbon::now()->translatedFormat('d F Y');

        $map = [
            'nomor'        => $penugasan->no_spt ?? '800.1.11.1 / ... / 406.050 / ' . date('Y'),
            'uraian'       => $uraian,
            'obyek'        => $objek,
            'objek'        => $objek,
            'mulai'        => $tglMulai,
            'selesai'      => $tglSelesai,
            'anggaran'     => $tahunAnggaran,
            'nama'         => $inspekturNama,
            'pangat'       => $inspekturPangkat,
            'pangkat'      => $inspekturPangkat,
            'nip'          => $inspekturNip,
        ];

        // Replace split macro runs using targeted non-crossing regex
        $pattern = '/(<w:r\b(?:(?!<w:r\b).)*?<w:t[^>]*>&lt;&lt;<\/w:t>.*?<\/w:r>)((?:(?!&lt;&lt;|&gt;&gt;).)*?)(<w:r\b(?:(?!<w:r\b).)*?<w:t[^>]*>&gt;&gt;<\/w:t>.*?<\/w:r>)/s';
        $xml = preg_replace_callback($pattern, function ($m) use ($map) {
            $rawInside = strtolower(strip_tags($m[2]));
            $rawInside = trim(html_entity_decode($rawInside));

            $matchedVal = null;
            foreach ($map as $key => $val) {
                if (strpos($rawInside, $key) !== false) {
                    $matchedVal = $val;
                    break;
                }
            }

            if ($matchedVal !== null) {
                preg_match('/<w:rPr>.*?<\/w:rPr>/s', $m[1], $rPrMatch);
                $rPr = $rPrMatch[0] ?? '';
                return '<w:r>' . $rPr . '<w:t xml:space="preserve">' . htmlspecialchars($matchedVal, ENT_XML1) . '</w:t></w:r>';
            }

            return $m[0];
        }, $xml);

        // Ganti tanggal penetapan "(tanggal) (bulan) (tahun)"
        $xml = str_replace('(tanggal) (bulan) (tahun)', htmlspecialchars($tglPenetapan, ENT_XML1), $xml);

        // Sesuaikan gelar penandatangan (Definitif / Plt. / Plh. / Pj.)
        $statusJabatan = strtolower($inspektur?->status_jabatan ?? 'plt');
        if ($statusJabatan === 'definitif') {
            $xml = preg_replace('/<w:t>Plt\.<\/w:t><\/w:r>.*?<w:tab\/><\/w:r>/s', '', $xml);
        } elseif ($statusJabatan === 'plh') {
            $xml = str_replace('<w:t>Plt.</w:t>', '<w:t>Plh.</w:t>', $xml);
        } elseif ($statusJabatan === 'pj') {
            $xml = str_replace('<w:t>Plt.</w:t>', '<w:t>Pj.</w:t>', $xml);
        }

        // Simpan kembali document.xml ke zip
        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        return $tempPath;
    }
}
