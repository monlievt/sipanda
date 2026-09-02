<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BebanKerjaController extends Controller
{
    /**
     * Tampilkan rekapitulasi beban kerja personil per periode.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tglAwal = $request->input('tanggal_awal', date('Y-01-01'));
        $tglAkhir = $request->input('tanggal_akhir', date('Y-12-31'));
        $irbanId = $request->input('irban_id');
        $statusFilter = $request->input('status_ketersediaan');

        $queryUsers = User::aktif()->internal()->with(['irban', 'penugasanSebagaiTim' => function ($q) use ($tglAwal, $tglAkhir) {
            $q->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir]);
        }]);

        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $queryUsers->where('irban_id', $user->irban_id);
            $irbanId = $user->irban_id;
        } elseif ($irbanId) {
            $queryUsers->where('irban_id', $irbanId);
        }

        if ($selectedUserId) {
            $queryUsers->where('id', $selectedUserId);
        }

        $allPersonil = $queryUsers->orderBy('nama')->get();

        foreach ($allPersonil as $personil) {
            $penugasan = $personil->penugasanSebagaiTim;
            $personil->total_penugasan = $penugasan->count();
            $personil->penugasan_selesai = $penugasan->where('status', 'selesai')->count();
            $penugasanAktif = $penugasan->whereIn('status', ['belum_berjalan', 'berjalan'])->values();
            $personil->penugasan_aktif = $penugasanAktif->count();
            $personil->daftar_penugasan_aktif = $penugasanAktif;

            if ($personil->penugasan_aktif === 0) {
                $personil->status_ketersediaan = 'Tersedia';
                $personil->status_label = 'Siap Ditugaskan';
                $personil->status_badge = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800';
                $personil->status_dot = 'bg-emerald-500';
            } elseif ($personil->penugasan_aktif <= 2) {
                $personil->status_ketersediaan = 'Optimal';
                $personil->status_label = 'Sedang Bertugas';
                $personil->status_badge = 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-300 dark:border-blue-800';
                $personil->status_dot = 'bg-blue-500';
            } else {
                $personil->status_ketersediaan = 'Beban Tinggi';
                $personil->status_label = 'Beban Tinggi (Overload)';
                $personil->status_badge = 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-300 dark:border-rose-800';
                $personil->status_dot = 'bg-rose-500 animate-pulse';
            }
        }

        // Summary KPI
        $countTotal = $allPersonil->count();
        $countTersedia = $allPersonil->where('status_ketersediaan', 'Tersedia')->count();
        $countOptimal = $allPersonil->where('status_ketersediaan', 'Optimal')->count();
        $countOverload = $allPersonil->where('status_ketersediaan', 'Beban Tinggi')->count();

        // Terapkan filter status ketersediaan jika ada
        if ($statusFilter && in_array($statusFilter, ['Tersedia', 'Optimal', 'Beban Tinggi'])) {
            $listPersonil = $allPersonil->where('status_ketersediaan', $statusFilter)->values();
        } else {
            $listPersonil = $allPersonil;
        }

        $irbans = Irban::where('nama_irban', '!=', 'Sekretariat')->get();
        $allUsers = User::aktif()->internal()->orderBy('nama')->get();

        return view('beban-kerja.index', compact(
            'listPersonil', 'irbans', 'allUsers', 'tglAwal', 'tglAkhir', 'irbanId', 'selectedUserId',
            'statusFilter', 'countTotal', 'countTersedia', 'countOptimal', 'countOverload'
        ));
    }
}
