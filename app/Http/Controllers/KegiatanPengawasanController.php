<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\Pkppt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanPengawasanController extends Controller
{
    /**
     * Tampilkan tabel perbandingan otomatis rencana PKPPT vs realisasi penugasan.
     */
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Pkppt::with([
            'irban',
            'penugasan.jenisPenugasan',
            'penugasan.objekPenugasan',
            'penugasan.tim.user',
            'penugasan.tindakLanjut',
        ])->where('tahun', $tahun);

        if ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        $listPkppt = $query->orderBy('rencana_mulai', 'asc')->get();

        // Hitung Indikator Status & Rekap
        $rekap = [
            'total_rencana'  => $listPkppt->count(),
            'total_laporan'  => $listPkppt->sum('jumlah_laporan_rencana'),
            'total_realisasi'=> 0,
            'realisasi_selesai' => 0,
            'indikator_hijau'=> 0,
            'indikator_kuning'=> 0,
            'indikator_merah'=> 0,
        ];

        $today = now()->startOfDay();

        foreach ($listPkppt as $item) {
            $realisasi = $item->penugasan;
            $countReal = $realisasi->count();
            $countSelesai = $realisasi->where('status', 'selesai')->count();

            $rekap['total_realisasi'] += $countReal;
            $rekap['realisasi_selesai'] += $countSelesai;

            // Logika Indikator Warna
            if ($countSelesai >= $item->jumlah_laporan_rencana) {
                $item->indikator = 'hijau'; // Selesai memenuhi target
                $item->indikator_label = 'Selesai Sesuai Target';
                $rekap['indikator_hijau']++;
            } elseif ($today->gt($item->rencana_selesai_laporan) && $countSelesai < $item->jumlah_laporan_rencana) {
                $item->indikator = 'kuning'; // Lewat target rencana selesai tapi belum selesai
                $item->indikator_label = 'Terlambat / Melewati Rencana';
                $rekap['indikator_kuning']++;
            } elseif ($today->gt($item->rencana_mulai) && $countReal === 0) {
                $item->indikator = 'merah'; // Melewati rencana mulai tapi belum ada SPT
                $item->indikator_label = 'Belum Dimulai (Lewat Jadwal)';
                $rekap['indikator_merah']++;
            } else {
                $item->indikator = 'biru'; // Dalam jadwal rencana
                $item->indikator_label = 'Dalam Jadwal Rencana';
            }
        }

        $irbans = Irban::all();
        $tahunList = range(date('Y') + 1, 2022);

        return view('kegiatan-pengawasan.index', compact(
            'listPkppt', 'rekap', 'irbans', 'tahun', 'irbanId', 'tahunList'
        ));
    }
}
