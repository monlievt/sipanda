<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export Dokumen Matriks Tindak Lanjut Hasil Pengawasan (LHP Spesifik) ke CSV / Excel.
     */
    public function exportLhpMatrix(TindakLanjut $tindakLanjut): StreamedResponse
    {
        $tindakLanjut->load([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.pengunggah',
            'rincianPenyetoran',
        ]);

        // Ambil seluruh rekomendasi dalam LHP yang sama
        $items = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.pengunggah',
            'rincianPenyetoran',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        $noLhpClean = preg_replace('/[^\w\-]/', '_', $tindakLanjut->no_lhp ?? ('SPT_' . $tindakLanjut->penugasan?->no_spt));
        $filename   = "Matriks_TLHP_{$noLhpClean}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($tindakLanjut, $items) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Meta Header Informasi Dokumen LHP
            fputcsv($handle, ['MATRIKS TINDAK LANJUT HASIL PENGAWASAN (LHP) INSPEKTORAT KABUPATEN TRENGGALEK']);
            fputcsv($handle, ['Nomor LHP', $tindakLanjut->no_lhp ?? '-']);
            fputcsv($handle, ['Judul LHP', $tindakLanjut->judul_lhp ?? '-']);
            fputcsv($handle, ['Tanggal LHP', $tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->format('d F Y') : '-']);
            fputcsv($handle, ['Nomor SPT', $tindakLanjut->penugasan?->no_spt ?? '-']);
            fputcsv($handle, ['Irban Pengawas', $tindakLanjut->penugasan?->irban?->nama_irban ?? '-']);
            fputcsv($handle, ['Objek OPD Target', $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-']);
            fputcsv($handle, []);

            // Column Header Standard Matriks BPK / Inspektorat
            fputcsv($handle, [
                'No Item',
                'Uraian Temuan',
                'Rekomendasi Wajib',
                'Nilai Target Rekomendasi (Rp)',
                'Target Waktu Penyelesaian',
                'Uraian / Jawaban Tindak Lanjut OPD',
                'Rincian Penyetoran Kasda (NTPN / Nominal Rp)',
                'Status Tindak Lanjut',
                'Catatan Evaluasi / Kekurangan Tim Auditor'
            ]);

            foreach ($items as $idx => $item) {
                // Formatting Respon OPD
                $catatanOpdText = $item->buktiTindakLanjut->pluck('catatan_opd')->filter()->implode("\n---\n");

                // Formatting Rincian Setor
                $setorText = $item->rincianPenyetoran->map(function ($s) {
                    return "NTPN: " . ($s->no_referensi_ntpn ?? '-') . " (Rp " . number_format($s->nilai_setor_rp, 0, ',', '.') . " - " . ($s->tgl_setor ? $s->tgl_setor->format('d/m/Y') : '') . ")";
                })->implode("\n");

                // Catatan Evaluasi Verifikasi
                $catatanVerifikasi = $item->buktiTindakLanjut->pluck('catatan_verifikasi')->filter()->implode("\n---\n");

                fputcsv($handle, [
                    $idx + 1,
                    $item->uraian_temuan,
                    $item->rekomendasi,
                    $item->nilai_rekomendasi_rp,
                    $item->tanggal_target ? $item->tanggal_target->format('Y-m-d') : '-',
                    $catatanOpdText ?: 'Belum ada uraian perbaikan OPD',
                    $setorText ?: 'Belum ada penyetoran Kasda',
                    strtoupper($item->status_label),
                    $catatanVerifikasi ?: 'Sesuai'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Seluruh Rekapitulasi Matriks LHP ke CSV / Excel.
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

        $filename = "Rekapitulasi_Matriks_LHP_" . ($tahun ? "Tahun_{$tahun}" : "Semua") . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($grouped) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No', 'Nomor LHP', 'Judul LHP', 'Tanggal LHP', 'Nomor SPT', 'Irban',
                'Objek OPD Target', 'Total Item Rekomendasi', 'SESUAI', 'BELUM SESUAI',
                'BELUM DITINDAKLANJUTI', 'TIDAK DAPAT DITINDAKLANJUTI',
                'Total Target Rp', 'Total Realisasi Setor Rp'
            ]);

            $no = 1;
            foreach ($grouped as $items) {
                $first = $items->first();
                $countSesuai      = $items->where('status_tindak_lanjut', 'selesai')->count();
                $countBelumSesuai = $items->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
                $countBelum       = $items->where('status_tindak_lanjut', 'belum')->count();
                $countTdt         = $items->where('status_tindak_lanjut', 'tdt')->count();

                $totalNilaiTarget = $items->sum('nilai_rekomendasi_rp');
                $totalSetorRp     = $items->sum(function ($tl) {
                    return $tl->rincianPenyetoran->sum('nilai_setor_rp');
                });

                fputcsv($handle, [
                    $no++,
                    $first->no_lhp ?? '-',
                    $first->judul_lhp ?? '-',
                    $first->tgl_lhp ? $first->tgl_lhp->format('Y-m-d') : '-',
                    $first->penugasan?->no_spt ?? '-',
                    $first->penugasan?->irban?->nama_irban ?? '-',
                    $first->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-',
                    $items->count(),
                    $countSesuai,
                    $countBelumSesuai,
                    $countBelum,
                    $countTdt,
                    $totalNilaiTarget,
                    $totalSetorRp,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Data Penugasan (SPT) ke CSV / Spreadsheet.
     */
    public function exportPenugasan(Request $request): StreamedResponse
    {
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Penugasan::with(['irban', 'jenisPenugasan', 'sumberPenugasan', 'objekPenugasan'])
            ->tahun($tahun);

        if ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        $listPenugasan = $query->orderBy('tanggal_mulai', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"SIPANDA_Penugasan_{$tahun}.csv\"",
        ];

        return response()->stream(function () use ($listPenugasan) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No. SPT', 'Kategori PKPPT', 'Uraian Penugasan', 'Irban',
                'Jenis Penugasan', 'Sumber Penugasan', 'Objek Penugasan',
                'Tanggal Mulai', 'Tanggal Selesai', 'Status', 'Progres %', 'Keterangan Hasil'
            ]);

            foreach ($listPenugasan as $item) {
                $objekNames = $item->objekPenugasan->pluck('nama')->implode(', ');

                fputcsv($handle, [
                    $item->no_spt,
                    $item->is_sesuai_pkppt ? 'Sesuai PKPPT' : 'Non-PKPPT',
                    $item->uraian_penugasan,
                    $item->irban?->nama_irban ?? '-',
                    $item->jenisPenugasan?->nama ?? '-',
                    $item->sumberPenugasan?->nama ?? '-',
                    $objekNames,
                    $item->tanggal_mulai->format('Y-m-d'),
                    $item->tanggal_selesai->format('Y-m-d'),
                    $item->status_label,
                    $item->progres_persen . '%',
                    $item->keterangan_hasil ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Rencana PKPPT Tahunan ke CSV.
     */
    public function exportPkppt(Request $request): StreamedResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $listPkppt = Pkppt::with(['irban', 'penugasan'])
            ->tahun($tahun)
            ->orderBy('rencana_mulai', 'asc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"SIPANDA_PKPPT_{$tahun}.csv\"",
        ];

        return response()->stream(function () use ($listPkppt) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Tahun', 'Area Pengawasan', 'Jenis Pengawasan', 'Sasaran',
                'Irban Pelaksana', 'Rencana Mulai', 'Rencana Selesai Laporan',
                'Target Laporan', 'Realisasi SPT', 'Status Alur'
            ]);

            foreach ($listPkppt as $item) {
                fputcsv($handle, [
                    $item->tahun,
                    $item->area_pengawasan,
                    $item->jenis_pengawasan,
                    $item->sasaran ?? '-',
                    $item->irban?->nama_irban ?? 'Semua Irban',
                    $item->rencana_mulai->format('Y-m-d'),
                    $item->rencana_selesai_laporan->format('Y-m-d'),
                    $item->jumlah_laporan_rencana,
                    $item->penugasan->count(),
                    strtoupper($item->status),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Dokumen Kompilasi Matriks Pemantauan Tindak Lanjut Hasil Pengawasan
     * Seluruh Perangkat Daerah se-Kabupaten Trenggalek (Format Standar BPKP / Kemendagri / Laporan Bupati).
     */
    public function exportKompilasiDaerahExcel(Request $request): StreamedResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $filename = "Kompilasi_TLHP_Kab_Trenggalek_" . ($tahun == 'semua' ? 'Semua_Tahun' : $tahun) . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($tahun) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM agar rapi di Microsoft Excel

            // Judul Header Dokumen
            fputcsv($handle, ['KOMPILASI PEMANTAUAN TINDAK LANJUT HASIL PENGAWASAN (TLHP) PEMERINTAH KABUPATEN TRENGGALEK']);
            fputcsv($handle, ['STANDAR EVALUASI DAN REKONSILIASI BPKP & KEMENDAGRI RI']);
            fputcsv($handle, ['Tahun Anggaran Pemantauan', $tahun == 'semua' ? 'Seluruh Tahun Anggaran' : $tahun]);
            fputcsv($handle, ['Tanggal Cetak Data', now()->translatedFormat('d F Y H:i') . ' WIB']);
            fputcsv($handle, []);

            // Header Kolom Tabel
            fputcsv($handle, [
                'No',
                'Wilayah Pengawasan (Irban)',
                'Nama Perangkat Daerah (OPD / Satuan Kerja)',
                'Jumlah Dokumen LHP',
                'Jumlah Rekomendasi',
                'Sesuai (SS)',
                'Belum Sesuai (BS)',
                'Belum Ditindaklanjuti (BTL)',
                'Tidak Dapat Ditindaklanjuti (TDT)',
                '% Penyelesaian Rekomendasi',
                'Total Nilai Rekomendasi (Rp)',
                'Realisasi Setor Kasda (Rp)',
                'Sisa Kurang Setor (Rp)',
                '% Pemulihan Keuangan Daerah',
            ]);

            $irbans = Irban::orderBy('id', 'asc')->get();

            $noUrut = 1;
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

                // Cari seluruh penugasan pada Irban ini (primer atau multi-irban)
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

                    $tlQuery = TindakLanjut::with('rincianPenyetoran')
                        ->whereIn('penugasan_id', $penugasanIds);

                    if ($tahun && $tahun !== 'semua') {
                        $tlQuery->whereYear('tgl_lhp', $tahun);
                    }

                    $rekomendasiList = $tlQuery->get();

                    // Hitung jumlah unik dokumen LHP
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

                    fputcsv($handle, [
                        $noUrut++,
                        $irban->nama_irban,
                        $opd->nama,
                        $jmlLhp,
                        $jmlRekomendasi,
                        $jmlSs,
                        $jmlBs,
                        $jmlBtl,
                        $jmlTdt,
                        $persenSelesai . '%',
                        $nilaiRekomendasi,
                        $nilaiSetor,
                        $sisaSetor,
                        $persenPulih . '%',
                    ]);

                    $subTotalLhp += $jmlLhp;
                    $subTotalRekomendasi += $jmlRekomendasi;
                    $subTotalSs += $jmlSs;
                    $subTotalBs += $jmlBs;
                    $subTotalBtl += $jmlBtl;
                    $subTotalTdt += $jmlTdt;
                    $subTotalNilai += $nilaiRekomendasi;
                    $subTotalSetor += $nilaiSetor;
                }

                // Baris Sub-Total per Irban
                $subSisa = max(0, $subTotalNilai - $subTotalSetor);
                $subPersenSelesai = $subTotalRekomendasi > 0 ? round(($subTotalSs / $subTotalRekomendasi) * 100, 1) : 0;
                $subPersenPulih   = $subTotalNilai > 0 ? round(($subTotalSetor / $subTotalNilai) * 100, 1) : 0;

                fputcsv($handle, [
                    '',
                    'SUBTOTAL ' . strtoupper($irban->nama_irban),
                    '',
                    $subTotalLhp,
                    $subTotalRekomendasi,
                    $subTotalSs,
                    $subTotalBs,
                    $subTotalBtl,
                    $subTotalTdt,
                    $subPersenSelesai . '%',
                    $subTotalNilai,
                    $subTotalSetor,
                    $subSisa,
                    $subPersenPulih . '%',
                ]);
                fputcsv($handle, []); // Baris kosong pemisah antar-irban

                $grandTotalLhp += $subTotalLhp;
                $grandTotalRekomendasi += $subTotalRekomendasi;
                $grandTotalSs += $subTotalSs;
                $grandTotalBs += $subTotalBs;
                $grandTotalBtl += $subTotalBtl;
                $grandTotalTdt += $subTotalTdt;
                $grandTotalNilai += $subTotalNilai;
                $grandTotalSetor += $subTotalSetor;
            }

            // Baris GRAND TOTAL se-Kabupaten Trenggalek
            $grandSisa = max(0, $grandTotalNilai - $grandTotalSetor);
            $grandPersenSelesai = $grandTotalRekomendasi > 0 ? round(($grandTotalSs / $grandTotalRekomendasi) * 100, 1) : 0;
            $grandPersenPulih   = $grandTotalNilai > 0 ? round(($grandTotalSetor / $grandTotalNilai) * 100, 1) : 0;

            fputcsv($handle, [
                'TOTAL',
                'SE-KABUPATEN TRENGGALEK',
                '',
                $grandTotalLhp,
                $grandTotalRekomendasi,
                $grandTotalSs,
                $grandTotalBs,
                $grandTotalBtl,
                $grandTotalTdt,
                $grandPersenSelesai . '%',
                $grandTotalNilai,
                $grandTotalSetor,
                $grandSisa,
                $grandPersenPulih . '%',
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
