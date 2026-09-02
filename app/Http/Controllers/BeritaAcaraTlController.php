<?php

namespace App\Http\Controllers;

use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaAcaraTlController extends Controller
{
    /**
     * Cetak Naskah Dinas Resmi Berita Acara Rekonsiliasi Tindak Lanjut per Dokumen LHP.
     */
    public function cetakLhp(TindakLanjut $tindakLanjut): View
    {
        $tindakLanjut->load([
            'penugasan.irban',
            'penugasan.irbans',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.arsipDigital',
            'rincianPenyetoran',
        ]);

        // Seluruh item rekomendasi dalam LHP ini
        $items = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.arsipDigital',
            'rincianPenyetoran',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        // Rekapitulasi Status
        $countTotal = $items->count();
        $countSesuai = $items->where('status_tindak_lanjut', 'selesai')->count();
        $countBelumSesuai = $items->where('status_tindak_lanjut', 'dalam_proses')->count();
        $countBelumTl = $items->where('status_tindak_lanjut', 'belum_ditindaklanjuti')->count();
        $countTdt = $items->where('status_tindak_lanjut', 'tdt')->count();

        // Rekapitulasi Finansial
        $totalTargetRp = $items->sum('nilai_rekomendasi_rp');
        $totalSetorRp = $items->sum(function ($i) {
            return $i->rincianPenyetoran->sum('nilai_setor_rp');
        });
        $sisaKurangSetorRp = max(0, $totalTargetRp - $totalSetorRp);

        $opd = $tindakLanjut->penugasan?->objekPenugasan->first();
        $irban = $tindakLanjut->penugasan?->irban;

        return view('tindak-lanjut.cetak-berita-acara', compact(
            'tindakLanjut', 'items', 'countTotal', 'countSesuai', 'countBelumSesuai',
            'countBelumTl', 'countTdt', 'totalTargetRp', 'totalSetorRp', 'sisaKurangSetorRp',
            'opd', 'irban'
        ));
    }

    /**
     * Cetak Berita Acara Rekonsiliasi Komprehensif per Satuan Kerja OPD.
     */
    public function cetakOpd(ObjekPenugasan $objek, Request $request): View
    {
        $tahun = $request->input('tahun', date('Y'));

        // Ambil seluruh tindak lanjut pada penugasan yang menyasar OPD ini
        $penugasanIds = Penugasan::whereHas('objekPenugasan', function ($q) use ($objek) {
            $q->where('objek_penugasan.id', $objek->id);
        })->pluck('id');

        $itemsQuery = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'rincianPenyetoran',
        ])->whereIn('penugasan_id', $penugasanIds);

        if ($tahun && $tahun !== 'semua') {
            $itemsQuery->whereYear('tgl_lhp', $tahun);
        }

        $items = $itemsQuery->orderBy('tgl_lhp', 'asc')->get();

        // Ambil LHP pertama sebagai rujukan header jika ada
        $tindakLanjut = $items->first() ?? new TindakLanjut();

        $countTotal = $items->count();
        $countSesuai = $items->where('status_tindak_lanjut', 'selesai')->count();
        $countBelumSesuai = $items->where('status_tindak_lanjut', 'dalam_proses')->count();
        $countBelumTl = $items->where('status_tindak_lanjut', 'belum_ditindaklanjuti')->count();
        $countTdt = $items->where('status_tindak_lanjut', 'tdt')->count();

        $totalTargetRp = $items->sum('nilai_rekomendasi_rp');
        $totalSetorRp = $items->sum(function ($i) {
            return $i->rincianPenyetoran->sum('nilai_setor_rp');
        });
        $sisaKurangSetorRp = max(0, $totalTargetRp - $totalSetorRp);

        $opd = $objek;
        $irban = $tindakLanjut->penugasan?->irban;

        return view('tindak-lanjut.cetak-berita-acara', compact(
            'tindakLanjut', 'items', 'countTotal', 'countSesuai', 'countBelumSesuai',
            'countBelumTl', 'countTdt', 'totalTargetRp', 'totalSetorRp', 'sisaKurangSetorRp',
            'opd', 'irban', 'tahun'
        ));
    }
}
