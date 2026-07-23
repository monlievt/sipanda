<?php

namespace App\Http\Controllers;

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
                  ->orWhereHas('penugasan', fn($pq) => $pq->whereYear('tanggal_spt', $tahun));
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
}
