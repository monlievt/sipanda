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
        $selectedUserId = $request->input('user_id');

        $queryUsers = User::aktif()->internal()->with(['irban', 'penugasanSebagaiTim' => function ($q) use ($tglAwal, $tglAkhir) {
            $q->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir]);
        }]);

        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $queryUsers->where('irban_id', $user->irban_id);
        } elseif ($irbanId) {
            $queryUsers->where('irban_id', $irbanId);
        }

        if ($selectedUserId) {
            $queryUsers->where('id', $selectedUserId);
        }

        $listPersonil = $queryUsers->orderBy('nama')->get();

        foreach ($listPersonil as $personil) {
            $penugasan = $personil->penugasanSebagaiTim;
            $personil->total_penugasan = $penugasan->count();
            $personil->penugasan_selesai = $penugasan->where('status', 'selesai')->count();
            $personil->penugasan_aktif = $penugasan->whereIn('status', ['belum_berjalan', 'berjalan'])->count();
        }

        $irbans = Irban::where('nama_irban', '!=', 'Sekretariat')->get();
        $allUsers = User::aktif()->internal()->orderBy('nama')->get();

        return view('beban-kerja.index', compact(
            'listPersonil', 'irbans', 'allUsers', 'tglAwal', 'tglAkhir', 'irbanId', 'selectedUserId'
        ));
    }
}
