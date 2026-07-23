<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Realtime SIPANDA Web.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        // Auto scope if user has irban role
        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $irbanId = $user->irban_id;
        }

        $queryPenugasan = Penugasan::tahun($tahun);
        if ($irbanId) {
            $queryPenugasan->where('irban_id', $irbanId);
        }

        $allPenugasan = $queryPenugasan->get();

        // 1. Ringkasan Status
        $totalPenugasan = $allPenugasan->count();
        $totalSelesai   = $allPenugasan->where('status', 'selesai')->count();
        $totalBerjalan  = $allPenugasan->where('status', 'berjalan')->count();
        $totalBelum     = $allPenugasan->where('status', 'belum_berjalan')->count();

        $persenSelesai  = $totalPenugasan > 0 ? round(($totalSelesai / $totalPenugasan) * 100, 1) : 0;
        $persenBerjalan = $totalPenugasan > 0 ? round(($totalBerjalan / $totalPenugasan) * 100, 1) : 0;
        $persenBelum    = $totalPenugasan > 0 ? round(($totalBelum / $totalPenugasan) * 100, 1) : 0;

        // 2. Breakdown Realisasi per Jenis Penugasan (Assurance & Consulting)
        $jenisList = JenisPenugasan::orderBy('kategori')->orderBy('nama')->get();
        $breakdownJenis = [];

        foreach ($jenisList as $j) {
            $penugasanJenis = $allPenugasan->where('jenis_penugasan_id', $j->id);
            $breakdownJenis[] = [
                'kategori'     => ucfirst($j->kategori),
                'nama'         => $j->nama,
                'selesai'      => $penugasanJenis->where('status', 'selesai')->count(),
                'dalam_proses' => $penugasanJenis->whereIn('status', ['berjalan', 'belum_berjalan'])->count(),
                'total'        => $penugasanJenis->count(),
            ];
        }

        // 3. PKPPT Summary
        $queryPkppt = Pkppt::tahun($tahun);
        if ($irbanId) {
            $queryPkppt->where('irban_id', $irbanId);
        }
        $totalPkppt = $queryPkppt->count();

        $irbans = Irban::all();
        $tahunList = range(date('Y') + 1, 2022);

        return view('dashboard', compact(
            'totalPenugasan', 'totalSelesai', 'totalBerjalan', 'totalBelum',
            'persenSelesai', 'persenBerjalan', 'persenBelum',
            'breakdownJenis', 'totalPkppt', 'irbans', 'tahun', 'irbanId', 'tahunList'
        ));
    }
}
