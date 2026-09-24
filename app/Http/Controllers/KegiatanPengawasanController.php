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
        $user = auth()->user();
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Pkppt::with([
            'irban',
            'penugasan.jenisPenugasan',
            'penugasan.objekPenugasan',
            'penugasan.tim.user',
            'penugasan.tindakLanjut',
        ])->where('tahun', $tahun);

        if (! $user->isPimpinanOrAdmin() && $user->irban_id) {
            $query->where('irban_id', $user->irban_id);
            $irbanId = $user->irban_id;
        } elseif ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        $listPkppt = $query->orderBy('rencana_mulai', 'asc')->get();

        // Hitung Indikator Status & Rekap
        $rekap = [
            'total_rencana'      => $listPkppt->count(),
            'total_laporan'      => $listPkppt->sum('jumlah_laporan_rencana'),
            'total_realisasi'    => 0,
            'realisasi_selesai'  => 0,
            'indikator_hijau'    => 0, // Selesai Sesuai Target
            'indikator_biru'     => 0, // Sedang Dalam Pelaksanaan
            'indikator_kuning'   => 0, // Pelaksanaan Terlambat / Selesai Terlambat
            'indikator_merah'    => 0, // Belum Dimulai (Lewat Jadwal)
            'indikator_abu'      => 0, // Belum Dimulai (Terjadwal)
        ];

        $today = now()->startOfDay();

        foreach ($listPkppt as $item) {
            $realisasi = $item->penugasan;
            $countReal = $realisasi->count();
            $sptsSelesai = $realisasi->where('status', 'selesai');
            $sptsAktif   = $realisasi->whereNotIn('status', ['selesai', 'dibatalkan']);
            $countSelesai = $sptsSelesai->count();
            $countAktif   = $sptsAktif->count();
            $jumlahRencana = max(1, (int) $item->jumlah_laporan_rencana);

            $rekap['total_realisasi'] += $countReal;
            $rekap['realisasi_selesai'] += $countSelesai;

            // Pengecekan Tanggal Rencana
            $tglRencanaMulai   = $item->rencana_mulai ? $item->rencana_mulai->startOfDay() : null;
            $tglRencanaSelesai = $item->rencana_selesai_laporan ? $item->rencana_selesai_laporan->startOfDay() : null;

            if ($countReal === 0) {
                // KASUS 1: Belum ada SPT sama sekali
                if ($tglRencanaMulai && $today->gt($tglRencanaMulai)) {
                    $item->indikator = 'merah';
                    $item->indikator_label = 'Belum Dimulai (Lewat Jadwal)';
                    $rekap['indikator_merah']++;
                } else {
                    $item->indikator = 'abu';
                    $item->indikator_label = 'Belum Dimulai (Terjadwal)';
                    $rekap['indikator_abu']++;
                }
            } elseif ($countAktif > 0 || ($countSelesai < $jumlahRencana && $tglRencanaSelesai && $today->lte($tglRencanaSelesai))) {
                // KASUS 2: Masih ada SPT aktif yang sedang berjalan atau target laporan belum penuh tapi masih dalam masa jadwal
                if ($tglRencanaSelesai && $today->gt($tglRencanaSelesai)) {
                    $item->indikator = 'kuning';
                    $item->indikator_label = 'Pelaksanaan Terlambat';
                    $rekap['indikator_kuning']++;
                } else {
                    $item->indikator = 'biru';
                    $item->indikator_label = 'Sedang Dalam Pelaksanaan';
                    $rekap['indikator_biru']++;
                }
            } elseif ($countSelesai < $jumlahRencana && $tglRencanaSelesai && $today->gt($tglRencanaSelesai)) {
                // KASUS 3: Tidak ada SPT aktif, target belum terpenuhi, dan jadwal rencana sudah habis
                $item->indikator = 'kuning';
                $item->indikator_label = 'Pelaksanaan Terlambat';
                $rekap['indikator_kuning']++;
            } else {
                // KASUS 4: Semua SPT telah SELESAI dan target laporan tercapai
                $isLewatJadwal = false;
                if ($tglRencanaSelesai && $sptsSelesai->isNotEmpty()) {
                    foreach ($sptsSelesai as $spt) {
                        // Cek tanggal LHP resmi jika ada
                        $tglLhp = $spt->tindakLanjut?->pluck('tgl_lhp')->filter()->max();
                        if ($tglLhp) {
                            $tglLhpDate = \Carbon\Carbon::parse($tglLhp)->startOfDay();
                            if ($tglLhpDate->gt($tglRencanaSelesai)) {
                                $isLewatJadwal = true;
                                break;
                            }
                        } else {
                            // Jika belum/tidak ada LHP, cek tanggal penyelesaian aktual (updated_at) dan jadwal SPT
                            $tglActualSelesai = $spt->updated_at ? $spt->updated_at->startOfDay() : null;
                            $tglSptSelesai    = $spt->tanggal_selesai ? $spt->tanggal_selesai->startOfDay() : null;

                            // Jika tanggal saat diselesaikan (updated_at) <= rencana selesai, maka TEPAT WAKTU
                            if ($tglActualSelesai && $tglActualSelesai->lte($tglRencanaSelesai)) {
                                continue;
                            }

                            // Jika jadwal batas SPT <= rencana selesai, maka TEPAT WAKTU
                            if ($tglSptSelesai && $tglSptSelesai->lte($tglRencanaSelesai)) {
                                continue;
                            }

                            // Melewati jadwal hanya jika keduanya melebihi tanggal rencana selesai
                            $isLewatJadwal = true;
                            break;
                        }
                    }
                }

                if ($isLewatJadwal) {
                    $item->indikator = 'kuning';
                    $item->indikator_label = 'Selesai (Melewati Jadwal)';
                    $rekap['indikator_kuning']++;
                } else {
                    $item->indikator = 'hijau';
                    $item->indikator_label = 'Selesai Sesuai Target';
                    $rekap['indikator_hijau']++;
                }
            }
        }

        $irbans = Irban::all();
        $tahunList = range(date('Y') + 1, 2022);

        return view('kegiatan-pengawasan.index', compact(
            'listPkppt', 'rekap', 'irbans', 'tahun', 'irbanId', 'tahunList'
        ));
    }
}
