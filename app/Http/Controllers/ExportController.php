<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;
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
            'buktiTindakLanjut.arsipDigital',
            'rincianPenyetoran',
        ]);

        $items = TindakLanjut::with([
            'penugasan.irban.users',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut',
            'rincianPenyetoran',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        $templatePath = resource_path('templates/Template_Matriks_Tindak_Lanjut_Inspektorat_Trenggalek.xlsx');

        if (file_exists($templatePath)) {
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Isi Header Dokumen Sesuai Template Baku Trenggalek
            $sheet->setCellValue('A3', 'ATAS ' . strtoupper($tindakLanjut->judul_lhp ?? 'LAPORAN HASIL PENGAWASAN'));
            $sheet->setCellValue('C5', ': ' . ($tindakLanjut->no_lhp ?? ('SPT ' . $tindakLanjut->penugasan?->no_spt)));
            $sheet->setCellValue('C6', ': ' . ($tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->translatedFormat('d F Y') : '-'));
            $sheet->setCellValue('C7', ': ' . ($tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?: '-'));

            $startRow = 11;
            $currentRow = $startRow;

            foreach ($items as $idx => $item) {
                if ($idx > 0) {
                    $sheet->insertNewRowBefore($currentRow, 1);
                }

                $catatanOpd = $item->buktiTindakLanjut->pluck('catatan_opd')->filter()->implode("\n");
                $catatanVerifikasi = $item->buktiTindakLanjut->pluck('catatan_verifikasi')->filter()->implode("\n");
                $totalSetor = $item->rincianPenyetoran->sum('nilai_setor_rp');

                $sheet->setCellValue('A' . $currentRow, $idx + 1);
                $sheet->setCellValue('B' . $currentRow, $item->temuan_uraian ?: $item->uraian_temuan);
                $sheet->setCellValue('C' . $currentRow, $item->rekomendasi_uraian ?: $item->rekomendasi);
                
                $sheet->setCellValue('D' . $currentRow, (float) $item->nilai_rekomendasi_rp);
                $sheet->getStyle('D' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('E' . $currentRow, $catatanOpd ?: '-');

                // Tanda centang pada 4 sub-kolom status BPKP
                $sheet->setCellValue('F' . $currentRow, $item->status_tindak_lanjut === 'selesai' ? '✓' : '');
                $sheet->setCellValue('G' . $currentRow, $item->status_tindak_lanjut === 'dalam_proses' ? '✓' : '');
                $sheet->setCellValue('H' . $currentRow, $item->status_tindak_lanjut === 'belum_ditindaklanjuti' ? '✓' : '');
                $sheet->setCellValue('I' . $currentRow, $item->status_tindak_lanjut === 'tdt' ? '✓' : '');

                $sheet->setCellValue('J' . $currentRow, (float) $totalSetor);
                $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('K' . $currentRow, $catatanVerifikasi ?: '-');

                // Styling data baris
                $sheet->getStyle('A' . $currentRow . ':K' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $currentRow . ':I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('E' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('K' . $currentRow)->getAlignment()->setWrapText(true);

                $currentRow++;
            }

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
     * Export Seluruh Rekapitulasi Matriks LHP ke File Excel .XLSX Asli.
     */
    public function exportAllLhpMatrix(Request $request): StreamedResponse
    {
        $status = $request->input('status');
        $tahun  = $request->input('tahun');

        $query = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'rincianPenyetoran']);

        if ($status) {
            if ($status === 'proses') {
                $query->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi']);
            } else {
                $query->where('status_tindak_lanjut', $status);
            }
        }

        if ($tahun) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('tgl_lhp', $tahun)
                  ->orWhereYear('created_at', $tahun)
                  ->orWhereHas('penugasan', fn($pq) => $pq->whereYear('tanggal_mulai', $tahun));
            });
        }

        $allTindakLanjut = $query->orderBy('created_at', 'desc')->get();

        $grouped = $allTindakLanjut->groupBy(function ($item) {
            return $item->no_lhp ? ('LHP:' . $item->no_lhp) : ('SPT:' . $item->penugasan_id);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekapitulasi LHP');

        // Header Dokumen
        $sheet->setCellValue('A1', 'REKAPITULASI DOKUMEN LAPORAN HASIL PENGAWASAN (LHP)');
        $sheet->setCellValue('A2', 'INSPEKTORAT DAERAH KABUPATEN TRENGGALEK');
        $sheet->setCellValue('A3', 'Tahun Anggaran: ' . ($tahun ?: 'Semua') . ' | Tanggal Unduh: ' . now()->translatedFormat('d F Y H:i') . ' WIB');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Table Header
        $headers = [
            'A5' => 'NO',
            'B5' => 'NOMOR LHP',
            'C5' => 'TANGGAL LHP',
            'D5' => 'JUDUL LHP',
            'E5' => 'OBJEK PENGAWASAN',
            'F5' => 'IRBAN PENGAWAS',
            'G5' => 'JML REKOMENDASI',
            'H5' => 'SESUAI (SS)',
            'I5' => 'BELUM SESUAI (BS)',
            'J5' => 'BELUM TL (BTL)',
            'K5' => 'TDT',
            'L5' => '% SELESAI',
            'M5' => 'TARGET REKOMENDASI (RP)',
            'N5' => 'REALISASI SETOR (RP)',
            'O5' => 'SISA KURANG SETOR (RP)',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }
        $this->styleTableHeader($sheet, 'A5:O5', '1E293B', 'FFFFFF');

        $row = 6;
        $no = 1;

        foreach ($grouped as $key => $items) {
            $first = $items->first();
            $penugasan = $first->penugasan;
            $objekNames = $penugasan ? $penugasan->objekPenugasan->pluck('nama')->implode(', ') : '-';

            $countTotal = $items->count();
            $countSesuai = $items->where('status_tindak_lanjut', 'selesai')->count();
            $countBelumSesuai = $items->where('status_tindak_lanjut', 'dalam_proses')->count();
            $countBelumTl = $items->where('status_tindak_lanjut', 'belum_ditindaklanjuti')->count();
            $countTdt = $items->where('status_tindak_lanjut', 'tdt')->count();

            $totalTarget = $items->sum('nilai_rekomendasi_rp');
            $totalSetor = $items->sum(function ($it) {
                return $it->rincianPenyetoran->sum('nilai_setor_rp');
            });
            $sisaSetor = max(0, $totalTarget - $totalSetor);
            $persenSelesai = $countTotal > 0 ? round(($countSesuai / $countTotal) * 100, 1) : 0;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $first->no_lhp ?: ('SPT ' . ($penugasan?->no_spt ?? '-')));
            $sheet->setCellValue('C' . $row, $first->tgl_lhp ? $first->tgl_lhp->format('d/m/Y') : '-');
            $sheet->setCellValue('D' . $row, $first->judul_lhp ?: ($penugasan?->uraian_penugasan ?? '-'));
            $sheet->setCellValue('E' . $row, $objekNames);
            $sheet->setCellValue('F' . $row, $penugasan?->irban?->nama_irban ?? '-');
            $sheet->setCellValue('G' . $row, $countTotal);
            $sheet->setCellValue('H' . $row, $countSesuai);
            $sheet->setCellValue('I' . $row, $countBelumSesuai);
            $sheet->setCellValue('J' . $row, $countBelumTl);
            $sheet->setCellValue('K' . $row, $countTdt);
            $sheet->setCellValue('L' . $row, $persenSelesai . '%');

            $sheet->setCellValue('M' . $row, (float) $totalTarget);
            $sheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('N' . $row, (float) $totalSetor);
            $sheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('O' . $row, (float) $sisaSetor);
            $sheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        $this->styleTableData($sheet, 'A6:O' . max(6, $row - 1));

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Rekapitulasi_LHP_SIPANDA_" . date('Ymd_His') . ".xlsx";

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
        $tahun   = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Penugasan::with(['irban', 'jenisPenugasan', 'sumberPenugasan', 'objekPenugasan'])
            ->whereYear('tanggal_mulai', $tahun);

        if ($irbanId) {
            $query->where('irban_id', $irbanId);
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
        $tahun = $request->input('tahun', date('Y'));

        $listPkppt = Pkppt::with(['irban', 'penugasan'])
            ->tahun($tahun)
            ->orderBy('rencana_mulai', 'asc')
            ->get();

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
