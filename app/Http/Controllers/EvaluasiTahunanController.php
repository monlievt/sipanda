<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiTahunan;
use App\Models\Irban;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluasiTahunanController extends Controller
{
    /**
     * Tampilkan Dashboard Evaluasi Tahunan PKPT (Siklus N+1).
     */
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun', date('Y'));
        $irbans = Irban::where('nama_irban', '!=', 'Sekretariat')->get();

        // Hitung statistik evaluasi live
        $totalPkppt = Pkppt::tahun($tahun)->count();
        $pkpptTerealisasi = Pkppt::tahun($tahun)->has('penugasan')->count();
        $persenObjek = $totalPkppt > 0 ? round(($pkpptTerealisasi / $totalPkppt) * 100, 1) : 0;

        $totalSPT = Penugasan::tahun($tahun)->count();
        $sptSelesaiTepatWaktu = Penugasan::tahun($tahun)->where('status', 'selesai')->count();
        $persenTepatWaktu = $totalSPT > 0 ? round(($sptSelesaiTepatWaktu / $totalSPT) * 100, 1) : 0;

        $totalTL = TindakLanjut::whereHas('penugasan', fn($q) => $q->tahun($tahun))->count();
        $tlSelesai = TindakLanjut::whereHas('penugasan', fn($q) => $q->tahun($tahun))->where('status_tindak_lanjut', 'selesai')->count();
        $persenTLSelesai = $totalTL > 0 ? round(($tlSelesai / $totalTL) * 100, 1) : 0;

        $evaluasiHistory = EvaluasiTahunan::with('irban')->orderBy('tahun_evaluasi', 'desc')->get();
        $tahunList = range(date('Y'), 2023);

        return view('evaluasi.index', compact(
            'tahun', 'totalPkppt', 'pkpptTerealisasi', 'persenObjek',
            'totalSPT', 'sptSelesaiTepatWaktu', 'persenTepatWaktu',
            'totalTL', 'tlSelesai', 'persenTLSelesai',
            'evaluasiHistory', 'irbans', 'tahunList'
        ));
    }

    /**
     * Hitung & Simpan Ringkasan Evaluasi Tahunan.
     */
    public function generate(Request $request): RedirectResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $totalPkppt = Pkppt::tahun($tahun)->count();
        $pkpptTerealisasi = Pkppt::tahun($tahun)->has('penugasan')->count();
        $persenObjek = $totalPkppt > 0 ? round(($pkpptTerealisasi / $totalPkppt) * 100, 1) : 0;

        $totalSPT = Penugasan::tahun($tahun)->count();
        $sptSelesai = Penugasan::tahun($tahun)->where('status', 'selesai')->count();
        $persenTepatWaktu = $totalSPT > 0 ? round(($sptSelesai / $totalSPT) * 100, 1) : 0;

        $totalTL = TindakLanjut::whereHas('penugasan', fn($q) => $q->tahun($tahun))->count();
        $tlSelesai = TindakLanjut::whereHas('penugasan', fn($q) => $q->tahun($tahun))->where('status_tindak_lanjut', 'selesai')->count();
        $persenTLSelesai = $totalTL > 0 ? round(($tlSelesai / $totalTL) * 100, 1) : 0;

        EvaluasiTahunan::updateOrCreate(
            [
                'tahun_evaluasi' => $tahun,
                'irban_id'       => null, // instansi keseluruhan
            ],
            [
                'persen_objek_terealisasi'      => $persenObjek,
                'persen_laporan_tepat_waktu'    => $persenTepatWaktu,
                'persen_tindak_lanjut_selesai'  => $persenTLSelesai,
                'catatan_evaluasi'              => "Rekapitulasi otomatis capaian PKPT tahun {$tahun}.",
                'dibuat_oleh'                   => auth()->id(),
            ]
        );

        return back()->with('status', "Evaluasi tahunan untuk tahun {$tahun} berhasil disimpan.");
    }
}
