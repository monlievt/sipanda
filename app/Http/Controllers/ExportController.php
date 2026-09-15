<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Helper styling untuk header tabel.
     */
    private function styleTableHeader($sheet, string $range, string $bgColor = '0F172A', string $fontColor = 'FFFFFF'): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => $fontColor],
                'size'  => 10,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bgColor],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);
    }

    /**
     * Helper styling garis border tabel data.
     */
    private function styleTableData($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);
    }

    /**
     * Export Dokumen Matriks Tindak Lanjut Hasil Pengawasan (LHP Spesifik) ke Excel .XLSX Asli
     * Menggunakan Template Baku Resmi Inspektorat Kabupaten Trenggalek.
     */
    public function exportLhpMatrix(TindakLanjut $tindakLanjut): StreamedResponse
    {
        $tindakLanjut->load([
            'penugasan.irban.users',
            'penugasan.objekPenugasan',
            'tujuanSuratObjek',
            'objekPenugasan',
            'kodeAtributTemuan',
            'kodeAtributRekomendasi',
            'buktiTindakLanjut.arsipDigital',
            'rincianPenyetoran',
        ]);

        $items = TindakLanjut::with([
            'penugasan.irban.users',
            'penugasan.objekPenugasan',
            'tujuanSuratObjek',
            'objekPenugasan',
            'kodeAtributTemuan',
            'kodeAtributRekomendasi',
            'buktiTindakLanjut',
            'rincianPenyetoran',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        $templatePath = base_path('docs/template/Template Matriks Tindak Lanjut Inspektorat Trenggalek per Laporan Hasil Pengawasan.xlsx');
        if (!file_exists($templatePath)) {
            $templatePath = resource_path('templates/Template_Matriks_Tindak_Lanjut_Inspektorat_Trenggalek.xlsx');
        }

        if (file_exists($templatePath)) {
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // 1. Header Dokumen Sesuai Template Baku Trenggalek
            $judulLhp = strtoupper($tindakLanjut->judul_lhp ?? ($tindakLanjut->penugasan?->uraian_penugasan ?? 'LAPORAN HASIL PENGAWASAN'));
            $sheet->setCellValue('A8', 'ATAS ' . $judulLhp);
            $sheet->setCellValue('C10', ': ' . ($tindakLanjut->no_lhp ?? ('SPT ' . $tindakLanjut->penugasan?->no_spt)));
            $sheet->setCellValue('C11', ': ' . ($tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->translatedFormat('d F Y') : '-'));
            
            $namaObjek = $tindakLanjut->tujuanSuratObjek?->nama 
                ?? $tindakLanjut->objekPenugasan?->nama 
                ?? ($tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?: '-');
            $sheet->setCellValue('C12', ': ' . $namaObjek);

            // Bersihkan baris template lama dari baris 16 ke bawah
            $highestRow = $sheet->getHighestRow();
            if ($highestRow >= 16) {
                $sheet->removeRow(16, $highestRow - 15);
            }

            $startRow = 16;
            $currentRow = $startRow;

            $thinBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            foreach ($items as $idx => $item) {
                $no = $idx + 1;
                
                // Temuan
                $uraianTemuan = $item->uraian_temuan ?? '-';
                if ($item->kodeAtributTemuan) {
                    $uraianTemuan = "[{$item->kodeAtributTemuan->kode}] {$item->kodeAtributTemuan->nama}\n\n" . $uraianTemuan;
                }

                // Rekomendasi
                $rekomendasi = $item->rekomendasi ?? '-';
                if ($item->kodeAtributRekomendasi) {
                    $rekomendasi = "[{$item->kodeAtributRekomendasi->kode}] {$item->kodeAtributRekomendasi->nama}\n\n" . $rekomendasi;
                }

                $nilaiRekomendasi = (float) $item->nilai_rekomendasi_rp;
                $totalSetor = (float) $item->rincianPenyetoran->sum('nilai_setor_rp');

                $catatanOpd = $item->buktiTindakLanjut->pluck('catatan_opd')->filter()->implode("\n");
                if (empty($catatanOpd) && $item->status_tindak_lanjut === 'selesai') {
                    $catatanOpd = 'Telah ditindaklanjuti sesuai rekomendasi.';
                }

                $catatanVerifikasi = $item->hasil_telaah_tim 
                    ?: ($item->buktiTindakLanjut->pluck('catatan_verifikasi')->filter()->implode("\n") 
                    ?: ($item->status_telaah === 'disetujui_inspektur' ? 'Disetujui Inspektur' : '-'));

                // Status checklist (Sesuai, Belum Sesuai, Belum di TL, Tidak Dapat di TL)
                $isSesuai      = ($item->status_tindak_lanjut === 'selesai');
                $isBelumSesuai = in_array($item->status_tindak_lanjut, ['proses', 'menunggu_verifikasi', 'dalam_proses']);
                $isBelumTl     = in_array($item->status_tindak_lanjut, ['belum', 'belum_ditindaklanjuti']);
                $isTdt         = in_array($item->status_tindak_lanjut, ['tdt', 'tidak_dapat_ditindaklanjuti']);

                $sheet->setCellValue('A' . $currentRow, $no);
                $sheet->setCellValue('B' . $currentRow, $uraianTemuan);
                $sheet->setCellValue('C' . $currentRow, $rekomendasi);
                $sheet->setCellValue('D' . $currentRow, $nilaiRekomendasi);
                $sheet->setCellValue('E' . $currentRow, $catatanOpd ?: '-');
                $sheet->setCellValue('F' . $currentRow, $isSesuai ? 'V' : '');
                $sheet->setCellValue('G' . $currentRow, $isBelumSesuai ? 'V' : '');
                $sheet->setCellValue('H' . $currentRow, $isBelumTl ? 'V' : '');
                $sheet->setCellValue('I' . $currentRow, $isTdt ? 'V' : '');
                $sheet->setCellValue('J' . $currentRow, $totalSetor);
                $sheet->setCellValue('K' . $currentRow, $catatanVerifikasi);

                // Styling
                $sheet->getStyle('A' . $currentRow . ':K' . $currentRow)->applyFromArray($thinBorder);
                $sheet->getStyle('A' . $currentRow . ':K' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $currentRow . ':I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $currentRow . ':I' . $currentRow)->getFont()->setBold(true);
                
                $sheet->getStyle('D' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('E' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('K' . $currentRow)->getAlignment()->setWrapText(true);

                $currentRow++;
            }

            $lastDataRow = $currentRow - 1;

            // 2. Baris TOTAL / REKAPITULASI
            $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'JUMLAH');
            $sheet->setCellValue('D' . $currentRow, "=SUM(D{$startRow}:D{$lastDataRow})");
            $sheet->setCellValue('J' . $currentRow, "=SUM(J{$startRow}:J{$lastDataRow})");

            $sheet->getStyle('A' . $currentRow . ':K' . $currentRow)->applyFromArray($thinBorder);
            $sheet->getStyle('A' . $currentRow . ':K' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

            // 3. TANDA TANGAN (Inspektur & Irban)
            $signStartRow = $currentRow + 3;
            
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

            $irban = $tindakLanjut->penugasan?->irban;
            $irbanUser = $irban ? $irban->users()->whereHas('roles', fn($q) => $q->where('name', 'irban'))->first() : null;
            $namaIrban = $irban?->nama_irban ?? 'Inspektur Pembantu';
            $irbanNama = $irbanUser?->nama ?? '..................................';
            $irbanNip  = $irbanUser?->nip ?? '......................';
            $irbanPangkat = $irbanUser?->pangkat ?? ($irbanUser?->golongan ? 'Pembina (' . $irbanUser->golongan . ')' : 'Pembina Tingkat I');

            $tglTtd = $tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->translatedFormat('d F Y') : now()->translatedFormat('d F Y');

            // Left: Inspektur
            $sheet->setCellValue('B' . $signStartRow, "Mengetahui,");
            $sheet->setCellValue('B' . ($signStartRow + 1), "Plt. INSPEKTUR KABUPATEN TRENGGALEK");
            $sheet->setCellValue('B' . ($signStartRow + 5), $inspekturNama);
            $sheet->setCellValue('B' . ($signStartRow + 6), $inspekturPangkat);
            $sheet->setCellValue('B' . ($signStartRow + 7), "NIP. " . $inspekturNip);

            $sheet->getStyle('B' . ($signStartRow + 5))->getFont()->setBold(true)->setUnderline(true);

            // Right: Irban (Columns I / J)
            $sheet->setCellValue('I' . $signStartRow, "Trenggalek, " . $tglTtd);
            $sheet->setCellValue('I' . ($signStartRow + 1), $namaIrban);
            $sheet->setCellValue('I' . ($signStartRow + 5), $irbanNama);
            $sheet->setCellValue('I' . ($signStartRow + 6), $irbanPangkat);
            $sheet->setCellValue('I' . ($signStartRow + 7), "NIP. " . $irbanNip);

            $sheet->getStyle('I' . ($signStartRow + 5))->getFont()->setBold(true)->setUnderline(true);

            // Atur nama file output
            $noLhpClean = preg_replace('/[^\w\-]/', '_', $tindakLanjut->no_lhp ?? ('SPT_' . $tindakLanjut->penugasan?->no_spt));
            $filename = "Matriks_TLHP_{$noLhpClean}.xlsx";

            return response()->stream(function () use ($spreadsheet) {
                $writer = new XlsxWriter($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control'       => 'max-age=0',
            ]);
        }

        // Fallback jika template belum ada
        return $this->exportKompilasiDaerahExcel(request());
    }

    /**
     * Export Seluruh Rekapitulasi Matriks LHP ke File Excel .XLSX Asli
     * Berdasarkan Template Resmi Inspektorat Trenggalek (Rekap Triwulan, Semester, atau Tahunan).
     */
    public function exportAllLhpMatrix(Request $request): StreamedResponse
    {
        $status = $request->input('status');
        $tahun  = $request->input('tahun');

        $templatePath = base_path('docs/template/Template Matriks Tindak Lanjut Inspektorat Trenggalek Rekap Triwulan Semester atau Tahunan.xlsx');

        if (file_exists($templatePath)) {
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Rekapitulasi TLHP');

            // Update merge header dokumen mencakup kolom L (12 kolom total)
            $sheet->unmergeCells('A1:K1'); $sheet->mergeCells('A1:L1');
            $sheet->unmergeCells('A2:K2'); $sheet->mergeCells('A2:L2');
            $sheet->unmergeCells('A3:K3'); $sheet->mergeCells('A3:L3');
            $sheet->unmergeCells('A4:K4'); $sheet->mergeCells('A4:L4');
            $sheet->unmergeCells('A6:K6'); $sheet->mergeCells('A6:L6');
            $sheet->unmergeCells('A7:K7'); $sheet->mergeCells('A7:L7');

            // Setup Header Kolom L: Sisa Pengembalian (Rp)
            $sheet->mergeCells('L10:L11');
            $sheet->setCellValue('L10', "Sisa Pengembalian (Rp)");
            $sheet->getColumnDimension('L')->setWidth(17.55);
            $sheet->getStyle('L10:L11')->applyFromArray([
                'font'      => ['name' => 'Arial', 'size' => 10, 'bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            // Query Data Tindak Lanjut
            $query = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'rincianPenyetoran']);

            if ($status) {
                if ($status === 'proses') {
                    $query->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi', 'dalam_proses', 'belum_sesuai']);
                } elseif ($status === 'selesai' || $status === 'sesuai') {
                    $query->whereIn('status_tindak_lanjut', ['selesai', 'sesuai']);
                } elseif ($status === 'belum' || $status === 'belum_ditindaklanjuti') {
                    $query->whereIn('status_tindak_lanjut', ['belum', 'belum_ditindaklanjuti', 'belum_tl']);
                } elseif ($status === 'tdt') {
                    $query->whereIn('status_tindak_lanjut', ['tdt', 'tidak_dapat_ditindaklanjuti']);
                } else {
                    $query->where('status_tindak_lanjut', $status);
                }
            }

            if ($tahun && is_numeric($tahun)) {
                $query->where(function ($q) use ($tahun) {
                    $q->whereYear('tgl_lhp', $tahun)
                      ->orWhereYear('created_at', $tahun)
                      ->orWhereHas('penugasan', fn($pq) => $pq->whereYear('tanggal_mulai', $tahun));
                });
            }

            $allTindakLanjut = $query->get();

            $byYear = $allTindakLanjut->groupBy(function ($item) {
                if (!empty($item->tgl_lhp)) {
                    return (int) \Carbon\Carbon::parse($item->tgl_lhp)->year;
                }
                if (!empty($item->penugasan?->tanggal_mulai)) {
                    return (int) \Carbon\Carbon::parse($item->penugasan->tanggal_mulai)->year;
                }
                return (int) \Carbon\Carbon::parse($item->created_at)->year;
            })->sortKeys();

            if ($tahun && is_numeric($tahun)) {
                $years = [(int) $tahun];
                $sheet->setCellValue('A7', 'PERIODE TAHUN ' . $tahun);
            } else {
                $years = $byYear->keys()->toArray();
                if (empty($years)) {
                    $years = [(int) date('Y')];
                }
                sort($years);
                if (count($years) === 1) {
                    $sheet->setCellValue('A7', 'PERIODE TAHUN ' . reset($years));
                } else {
                    $sheet->setCellValue('A7', 'PERIODE TAHUN ' . min($years) . ' s.d. ' . max($years));
                }
            }

            // Hapus baris placeholder template (baris 12 s.d 21 = 10 baris)
            $sheet->removeRow(12, 10);

            $currentRow = 12;
            $no = 1;

            foreach ($years as $year) {
                $sheet->insertNewRowBefore($currentRow, 1);

                /** @var \Illuminate\Support\Collection $yearItems */
                $yearItems = $byYear->get($year) ?? collect();

                // Group LHP untuk menghitung total LHP dan total nilai yang dilakukan pengawasan
                $groupedLhp = $yearItems->groupBy(fn($i) => $i->no_lhp ?: ('SPT:' . $i->penugasan_id));
                $totalLhp = $groupedLhp->count();
                $nilaiPengawasan = (float) $groupedLhp->sum(fn($g) => $g->max('nilai_diawasi_rp') ?: 0);

                // Total saran / rekomendasi
                $totalRek = $yearItems->count();
                $nilaiRek = (float) $yearItems->sum('nilai_rekomendasi_rp');

                // Breakdown Status TL
                $sesuai = $yearItems->filter(fn($i) => in_array($i->status_tindak_lanjut, ['sesuai', 'selesai']))->count();
                $belumSesuai = $yearItems->filter(fn($i) => in_array($i->status_tindak_lanjut, ['belum_sesuai', 'proses', 'menunggu_verifikasi', 'dalam_proses']))->count();
                $belumTl = $yearItems->filter(fn($i) => in_array($i->status_tindak_lanjut, ['belum', 'belum_ditindaklanjuti', 'belum_tl']))->count();
                $tdt = $yearItems->filter(fn($i) => in_array($i->status_tindak_lanjut, ['tdt', 'tidak_dapat_ditindaklanjuti']))->count();

                // Nilai pengembalian ke kas daerah / negara
                $nilaiPengembalian = (float) $yearItems->sum(function ($it) {
                    $sumRincian = $it->rincianPenyetoran ? $it->rincianPenyetoran->sum('jumlah_setor') : 0;
                    return $sumRincian > 0 ? $sumRincian : ($it->nilai_setor ?: 0);
                });

                $sheet->setCellValue("A{$currentRow}", $no++);
                $sheet->setCellValue("B{$currentRow}", $year);
                $sheet->setCellValue("C{$currentRow}", $totalLhp);
                $sheet->setCellValue("D{$currentRow}", $nilaiPengawasan);
                $sheet->setCellValue("E{$currentRow}", $totalRek);
                $sheet->setCellValue("F{$currentRow}", $nilaiRek);
                $sheet->setCellValue("G{$currentRow}", $sesuai);
                $sheet->setCellValue("H{$currentRow}", $belumSesuai);
                $sheet->setCellValue("I{$currentRow}", $belumTl);
                $sheet->setCellValue("J{$currentRow}", $tdt);
                $sheet->setCellValue("K{$currentRow}", $nilaiPengembalian);
                $sheet->setCellValue("L{$currentRow}", "=F{$currentRow}-K{$currentRow}");

                // Format & Borders
                $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray([
                    'font'    => ['name' => 'Arial', 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("K{$currentRow}:L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

                $currentRow++;
            }

            // Summary Row (Jumlah)
            $lastDataRow = $currentRow - 1;
            $sheet->mergeCells("A{$currentRow}:B{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", "Jumlah");
            $sheet->setCellValue("C{$currentRow}", "=SUM(C12:C{$lastDataRow})");
            $sheet->setCellValue("D{$currentRow}", "=SUM(D12:D{$lastDataRow})");
            $sheet->setCellValue("E{$currentRow}", "=SUM(E12:E{$lastDataRow})");
            $sheet->setCellValue("F{$currentRow}", "=SUM(F12:F{$lastDataRow})");
            $sheet->setCellValue("G{$currentRow}", "=SUM(G12:G{$lastDataRow})");
            $sheet->setCellValue("H{$currentRow}", "=SUM(H12:H{$lastDataRow})");
            $sheet->setCellValue("I{$currentRow}", "=SUM(I12:I{$lastDataRow})");
            $sheet->setCellValue("J{$currentRow}", "=SUM(J12:J{$lastDataRow})");
            $sheet->setCellValue("K{$currentRow}", "=SUM(K12:K{$lastDataRow})");
            $sheet->setCellValue("L{$currentRow}", "=SUM(L12:L{$lastDataRow})");

            $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray([
                'font'    => ['name' => 'Arial', 'size' => 10, 'bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("K{$currentRow}:L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

            // Signatures Inspektur
            $inspektur = User::role('inspektur')->first() ?? User::where('jabatan', 'like', '%inspektur%')->first();
            $inspekturNama = $inspektur?->nama ?? 'Ir. WIJIONO, S.T., M.Mkes.';
            $inspekturJabatan = $inspektur?->jabatan ?? 'Plt. Inspektur Daerah';
            $inspekturNip = $inspektur?->nip ?? '197001011995011001';

            $signStart = $currentRow + 2;
            $sheet->setCellValue("H{$signStart}", "Trenggalek, " . now()->translatedFormat('d F Y'));
            $sheet->setCellValue("H" . ($signStart + 2), $inspekturJabatan);
            $sheet->setCellValue("H" . ($signStart + 3), "Kabupaten Trenggalek");
            $sheet->setCellValue("H" . ($signStart + 7), $inspekturNama);
            $sheet->setCellValue("H" . ($signStart + 8), "NIP. " . $inspekturNip);
            $sheet->getStyle("H" . ($signStart + 7))->getFont()->setBold(true)->setUnderline(true);

            $filename = "Rekap_Matriks_TLHP_Trenggalek_" . ($tahun ? "Tahun_{$tahun}" : "Semua_Tahun") . ".xlsx";

            return response()->stream(function () use ($spreadsheet) {
                $writer = new XlsxWriter($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control'       => 'max-age=0',
            ]);
        }

        // Fallback jika template belum ada
        return $this->exportKompilasiDaerahExcel($request);
    }

    /**
     * Export Dokumen Kompilasi Matriks Pemantauan Tindak Lanjut Hasil Pengawasan
     * Seluruh Perangkat Daerah se-Kabupaten Trenggalek ke File Excel .XLSX Asli (Standar BPKP).
     */
    public function exportKompilasiDaerahExcel(Request $request): StreamedResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kompilasi TLHP Trenggalek');

        // Judul Header Dokumen Resmi
        $sheet->setCellValue('A1', 'KOMPILASI PEMANTAUAN TINDAK LANJUT HASIL PENGAWASAN (TLHP) SE-KABUPATEN TRENGGALEK');
        $sheet->setCellValue('A2', 'STANDAR EVALUASI DAN REKONSILIASI BPKP & KEMENDAGRI RI');
        $sheet->setCellValue('A3', 'Tahun Anggaran Pemantauan: ' . ($tahun == 'semua' ? 'Seluruh Tahun Anggaran' : $tahun));
        $sheet->setCellValue('A4', 'Tanggal Cetak: ' . now()->translatedFormat('d F Y H:i') . ' WIB');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0F172A'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0369A1'));
        $sheet->getStyle('A3:A4')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Header Kolom Tabel
        $headers = [
            'A6' => 'NO',
            'B6' => 'WILAYAH PENGAWASAN (IRBAN)',
            'C6' => 'PERANGKAT DAERAH (OPD / UNIT KERJA)',
            'D6' => 'JML LHP',
            'E6' => 'JML REKOMENDASI',
            'F6' => 'SESUAI (SS)',
            'G6' => 'BELUM SESUAI (BS)',
            'H6' => 'BELUM DI-TL (BTL)',
            'I6' => 'TDT',
            'J6' => '% SELESAI',
            'K6' => 'TOTAL REKOMENDASI (RP)',
            'L6' => 'REALISASI KASDA (RP)',
            'M6' => 'SISA KURANG SETOR (RP)',
            'N6' => '% RECOVERY',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $this->styleTableHeader($sheet, 'A6:N6', '0F172A', 'FFFFFF');

        $irbans = Irban::orderBy('id', 'asc')->get();

        $noUrut = 1;
        $row = 7;
        $grandTotalLhp = 0;
        $grandTotalRekomendasi = 0;
        $grandTotalSs = 0;
        $grandTotalBs = 0;
        $grandTotalBtl = 0;
        $grandTotalTdt = 0;
        $grandTotalNilai = 0;
        $grandTotalSetor = 0;

        foreach ($irbans as $irban) {
            $subTotalLhp = 0;
            $subTotalRekomendasi = 0;
            $subTotalSs = 0;
            $subTotalBs = 0;
            $subTotalBtl = 0;
            $subTotalTdt = 0;
            $subTotalNilai = 0;
            $subTotalSetor = 0;

            // Cari seluruh penugasan pada Irban ini
            $irbanPenugasanIds = Penugasan::where(function ($q) use ($irban) {
                $q->where('irban_id', $irban->id)
                  ->orWhereHas('irbans', fn($m) => $m->where('irbans.id', $irban->id));
            })->pluck('id');

            // Ambil daftar unik OPD yang pernah diawasi oleh Irban ini
            $opdList = ObjekPenugasan::whereHas('penugasan', function ($q) use ($irbanPenugasanIds) {
                $q->whereIn('penugasan.id', $irbanPenugasanIds);
            })->orderBy('nama', 'asc')->get();

            foreach ($opdList as $opd) {
                $penugasanIds = Penugasan::where(function ($q) use ($irban) {
                    $q->where('irban_id', $irban->id)
                      ->orWhereHas('irbans', fn($m) => $m->where('irbans.id', $irban->id));
                })->whereHas('objekPenugasan', function ($q) use ($opd) {
                    $q->where('objek_penugasan.id', $opd->id);
                })->pluck('id');

                $tlQuery = TindakLanjut::with('rincianPenyetoran')->whereIn('penugasan_id', $penugasanIds);

                if ($tahun && $tahun !== 'semua') {
                    $tlQuery->whereYear('tgl_lhp', $tahun);
                }

                $rekomendasiList = $tlQuery->get();

                $jmlLhp = $rekomendasiList->pluck('no_lhp')->filter()->unique()->count();
                if ($jmlLhp === 0 && $rekomendasiList->count() > 0) {
                    $jmlLhp = $rekomendasiList->pluck('penugasan_id')->unique()->count();
                }

                $jmlRekomendasi = $rekomendasiList->count();
                $jmlSs  = $rekomendasiList->where('status_tindak_lanjut', 'selesai')->count();
                $jmlBs  = $rekomendasiList->where('status_tindak_lanjut', 'dalam_proses')->count();
                $jmlBtl = $rekomendasiList->where('status_tindak_lanjut', 'belum_ditindaklanjuti')->count();
                $jmlTdt = $rekomendasiList->where('status_tindak_lanjut', 'tdt')->count();

                $nilaiRekomendasi = (float) $rekomendasiList->sum('nilai_rekomendasi_rp');
                $nilaiSetor = (float) $rekomendasiList->sum(function ($r) {
                    return $r->rincianPenyetoran->sum('nilai_setor_rp');
                });
                $sisaSetor = max(0, $nilaiRekomendasi - $nilaiSetor);

                $persenSelesai = $jmlRekomendasi > 0 ? round(($jmlSs / $jmlRekomendasi) * 100, 1) : 0;
                $persenPulih   = $nilaiRekomendasi > 0 ? round(($nilaiSetor / $nilaiRekomendasi) * 100, 1) : 0;

                $sheet->setCellValue('A' . $row, $noUrut++);
                $sheet->setCellValue('B' . $row, $irban->nama_irban);
                $sheet->setCellValue('C' . $row, $opd->nama);
                $sheet->setCellValue('D' . $row, $jmlLhp);
                $sheet->setCellValue('E' . $row, $jmlRekomendasi);
                $sheet->setCellValue('F' . $row, $jmlSs);
                $sheet->setCellValue('G' . $row, $jmlBs);
                $sheet->setCellValue('H' . $row, $jmlBtl);
                $sheet->setCellValue('I' . $row, $jmlTdt);
                $sheet->setCellValue('J' . $row, $persenSelesai . '%');

                $sheet->setCellValue('K' . $row, $nilaiRekomendasi);
                $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('L' . $row, $nilaiSetor);
                $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('M' . $row, $sisaSetor);
                $sheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('N' . $row, $persenPulih . '%');

                $subTotalLhp += $jmlLhp;
                $subTotalRekomendasi += $jmlRekomendasi;
                $subTotalSs += $jmlSs;
                $subTotalBs += $jmlBs;
                $subTotalBtl += $jmlBtl;
                $subTotalTdt += $jmlTdt;
                $subTotalNilai += $nilaiRekomendasi;
                $subTotalSetor += $nilaiSetor;

                $row++;
            }

            // Baris Sub-Total per Irban
            $subSisa = max(0, $subTotalNilai - $subTotalSetor);
            $subPersenSelesai = $subTotalRekomendasi > 0 ? round(($subTotalSs / $subTotalRekomendasi) * 100, 1) : 0;
            $subPersenPulih   = $subTotalNilai > 0 ? round(($subTotalSetor / $subTotalNilai) * 100, 1) : 0;

            $sheet->setCellValue('B' . $row, 'SUBTOTAL ' . strtoupper($irban->nama_irban));
            $sheet->setCellValue('D' . $row, $subTotalLhp);
            $sheet->setCellValue('E' . $row, $subTotalRekomendasi);
            $sheet->setCellValue('F' . $row, $subTotalSs);
            $sheet->setCellValue('G' . $row, $subTotalBs);
            $sheet->setCellValue('H' . $row, $subTotalBtl);
            $sheet->setCellValue('I' . $row, $subTotalTdt);
            $sheet->setCellValue('J' . $row, $subPersenSelesai . '%');

            $sheet->setCellValue('K' . $row, $subTotalNilai);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('L' . $row, $subTotalSetor);
            $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('M' . $row, $subSisa);
            $sheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('N' . $row, $subPersenPulih . '%');

            $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            ]);

            $grandTotalLhp += $subTotalLhp;
            $grandTotalRekomendasi += $subTotalRekomendasi;
            $grandTotalSs += $subTotalSs;
            $grandTotalBs += $subTotalBs;
            $grandTotalBtl += $subTotalBtl;
            $grandTotalTdt += $subTotalTdt;
            $grandTotalNilai += $subTotalNilai;
            $grandTotalSetor += $subTotalSetor;

            $row++;
        }

        // Baris GRAND TOTAL Se-Kabupaten Trenggalek
        $grandSisa = max(0, $grandTotalNilai - $grandTotalSetor);
        $grandPersenSelesai = $grandTotalRekomendasi > 0 ? round(($grandTotalSs / $grandTotalRekomendasi) * 100, 1) : 0;
        $grandPersenPulih   = $grandTotalNilai > 0 ? round(($grandTotalSetor / $grandTotalNilai) * 100, 1) : 0;

        $sheet->setCellValue('B' . $row, 'TOTAL SE-KABUPATEN TRENGGALEK');
        $sheet->setCellValue('D' . $row, $grandTotalLhp);
        $sheet->setCellValue('E' . $row, $grandTotalRekomendasi);
        $sheet->setCellValue('F' . $row, $grandTotalSs);
        $sheet->setCellValue('G' . $row, $grandTotalBs);
        $sheet->setCellValue('H' . $row, $grandTotalBtl);
        $sheet->setCellValue('I' . $row, $grandTotalTdt);
        $sheet->setCellValue('J' . $row, $grandPersenSelesai . '%');

        $sheet->setCellValue('K' . $row, $grandTotalNilai);
        $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('L' . $row, $grandTotalSetor);
        $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('M' . $row, $grandSisa);
        $sheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('N' . $row, $grandPersenPulih . '%');

        $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
        ]);

        $this->styleTableData($sheet, 'A7:N' . $row);

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Kompilasi_TLHP_Kab_Trenggalek_" . ($tahun == 'semua' ? 'Semua_Tahun' : $tahun) . ".xlsx";

        return response()->stream(function () use ($spreadsheet) {
            $writer = new XlsxWriter($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Export Data Penugasan ke File Excel .XLSX.
     */
    public function exportPenugasan(Request $request): StreamedResponse
    {
        $user    = auth()->user();
        $tahun   = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Penugasan::with(['irban', 'jenisPenugasan', 'sumberPenugasan', 'objekPenugasan'])
            ->whereYear('tanggal_mulai', $tahun);

        if (! $user->isPimpinanOrAdmin()) {
            $query->accessibleBy($user);
        } elseif ($irbanId) {
            $query->irban($irbanId);
        }

        $listPenugasan = $query->orderBy('tanggal_mulai', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Penugasan');

        $sheet->setCellValue('A1', 'DAFTAR SURAT PERINTAH TUGAS (SPT) PENGAWASAN');
        $sheet->setCellValue('A2', 'INSPEKTORAT DAERAH KABUPATEN TRENGGALEK TAHUN ' . $tahun);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11);

        $headers = [
            'A4' => 'NO. SPT',
            'B4' => 'KATEGORI PKPPT',
            'C4' => 'URAIAN PENUGASAN',
            'D4' => 'IRBAN PENGAWAS',
            'E4' => 'JENIS PENUGASAN',
            'F4' => 'SUMBER PENUGASAN',
            'G4' => 'OBJEK PENGAWASAN',
            'H4' => 'TGL MULAI',
            'I4' => 'TGL SELESAI',
            'J4' => 'STATUS',
            'K4' => 'PROGRES %',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $this->styleTableHeader($sheet, 'A4:K4', '1E293B', 'FFFFFF');

        $row = 5;
        foreach ($listPenugasan as $item) {
            $objekNames = $item->objekPenugasan->pluck('nama')->implode(', ');

            $sheet->setCellValue('A' . $row, $item->no_spt);
            $sheet->setCellValue('B' . $row, $item->is_sesuai_pkppt ? 'Sesuai PKPPT' : 'Non-PKPPT');
            $sheet->setCellValue('C' . $row, $item->uraian_penugasan);
            $sheet->setCellValue('D' . $row, $item->irban?->nama_irban ?? '-');
            $sheet->setCellValue('E' . $row, $item->jenisPenugasan?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $item->sumberPenugasan?->nama ?? '-');
            $sheet->setCellValue('G' . $row, $objekNames);
            $sheet->setCellValue('H' . $row, $item->tanggal_mulai->format('d/m/Y'));
            $sheet->setCellValue('I' . $row, $item->tanggal_selesai->format('d/m/Y'));
            $sheet->setCellValue('J' . $row, $item->status_label);
            $sheet->setCellValue('K' . $row, $item->progres_persen . '%');
            $row++;
        }

        $this->styleTableData($sheet, 'A5:K' . max(5, $row - 1));

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "SIPANDA_Penugasan_{$tahun}.xlsx";

        return response()->stream(function () use ($spreadsheet) {
            $writer = new XlsxWriter($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Export Rencana PKPPT Tahunan ke Excel .XLSX.
     */
    public function exportPkppt(Request $request): StreamedResponse
    {
        $user    = auth()->user();
        $tahun   = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Pkppt::with(['irban', 'penugasan'])
            ->tahun($tahun)
            ->orderBy('rencana_mulai', 'asc');

        if (! $user->isPimpinanOrAdmin() && $user->irban_id) {
            $query->where('irban_id', $user->irban_id);
        } elseif ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        $listPkppt = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PKPPT ' . $tahun);

        $sheet->setCellValue('A1', 'PROGRAM KERJA PENGAWASAN TAHUNAN (PKPT) BERBASIS RISIKO');
        $sheet->setCellValue('A2', 'INSPEKTORAT DAERAH KABUPATEN TRENGGALEK TAHUN ANGGARAN ' . $tahun);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(11);

        $headers = [
            'A4' => 'TAHUN',
            'B4' => 'AREA PENGAWASAN',
            'C4' => 'JENIS PENGAWASAN',
            'D4' => 'SASARAN',
            'E4' => 'IRBAN PELAKSANA',
            'F4' => 'RENCANA MULAI',
            'G4' => 'RENCANA SELESAI',
            'H4' => 'TARGET LAPORAN',
            'I4' => 'REALISASI SPT',
            'J4' => 'STATUS',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $this->styleTableHeader($sheet, 'A4:J4', '0F172A', 'FFFFFF');

        $row = 5;
        foreach ($listPkppt as $item) {
            $sheet->setCellValue('A' . $row, $item->tahun);
            $sheet->setCellValue('B' . $row, $item->area_pengawasan);
            $sheet->setCellValue('C' . $row, $item->jenis_pengawasan);
            $sheet->setCellValue('D' . $row, $item->sasaran ?? '-');
            $sheet->setCellValue('E' . $row, $item->irban?->nama_irban ?? 'Semua Irban');
            $sheet->setCellValue('F' . $row, $item->rencana_mulai->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $item->rencana_selesai_laporan->format('d/m/Y'));
            $sheet->setCellValue('H' . $row, $item->jumlah_laporan_rencana);
            $sheet->setCellValue('I' . $row, $item->penugasan->count());
            $sheet->setCellValue('J' . $row, strtoupper($item->status));
            $row++;
        }

        $this->styleTableData($sheet, 'A5:J' . max(5, $row - 1));

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "SIPANDA_PKPPT_{$tahun}.xlsx";

        return response()->stream(function () use ($spreadsheet) {
            $writer = new XlsxWriter($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
