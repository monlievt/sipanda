<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\Konsultasi;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    /**
     * Landing Page & Public Transparency Dashboard (Tanpa Login)
     */
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun', date('Y'));

        // Realisasi PKPPT & Penugasan
        $totalPkppt = Pkppt::where('tahun', $tahun)->sum('target_laporan');
        $totalPenugasan = Penugasan::whereYear('tanggal_mulai', $tahun)->count();
        $penugasanBerjalan = Penugasan::whereYear('tanggal_mulai', $tahun)->where('status', 'berjalan')->count();
        $penugasanSelesai = Penugasan::whereYear('tanggal_mulai', $tahun)->where('status', 'selesai')->count();

        // Total Nilai Anggaran APBD/APBDes yang Diawasi
        $totalNilaiDiawasi = TindakLanjut::whereYear('created_at', $tahun)->max('nilai_diawasi_rp') ?? 0;
        if ($totalNilaiDiawasi == 0) {
            $totalNilaiDiawasi = TindakLanjut::max('nilai_diawasi_rp') ?? 0;
        }

        // Rekap Tindak Lanjut Rekomendasi
        $totalRekomendasi = TindakLanjut::count();
        $countSelesai     = TindakLanjut::where('status_tindak_lanjut', 'selesai')->count();
        $countProses      = TindakLanjut::whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
        $countBelum       = TindakLanjut::where('status_tindak_lanjut', 'belum')->count();

        $persenSelesai = $totalRekomendasi > 0 ? round(($countSelesai / $totalRekomendasi) * 100, 1) : 0;

        // QnA FAQ Publik Terbaru (Maks. 4 Item)
        $publicFaqs = Konsultasi::where('is_faq_public', true)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(4)
            ->get();

        $irbans = Irban::all();

        return view('welcome', compact(
            'tahun', 'totalPkppt', 'totalPenugasan', 'penugasanBerjalan', 'penugasanSelesai',
            'totalNilaiDiawasi', 'totalRekomendasi', 'countSelesai', 'countProses', 'countBelum',
            'persenSelesai', 'publicFaqs', 'irbans'
        ));
    }
}
