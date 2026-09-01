<?php

namespace App\Http\Controllers;

use App\Models\Irban;
use App\Models\Konsultasi;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    /**
     * Landing Page & Public Transparency Dashboard (Tanpa Login) dengan Caching.
     */
    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        $data = Cache::remember("public_dashboard_stats_{$tahun}", 300, function () use ($tahun) {
            // Realisasi PKPPT & Penugasan
            $totalPkppt = Pkppt::where('tahun', $tahun)->sum('jumlah_laporan_rencana');
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

            return [
                'totalPkppt'        => $totalPkppt,
                'totalPenugasan'    => $totalPenugasan,
                'penugasanBerjalan' => $penugasanBerjalan,
                'penugasanSelesai'  => $penugasanSelesai,
                'totalNilaiDiawasi' => $totalNilaiDiawasi,
                'totalRekomendasi'  => $totalRekomendasi,
                'countSelesai'      => $countSelesai,
                'countProses'       => $countProses,
                'countBelum'        => $countBelum,
                'persenSelesai'     => $persenSelesai,
            ];
        });

        // QnA FAQ Publik Terbaru (Maks. 4 Item)
        $publicFaqs = Konsultasi::where('is_faq_public', true)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(4)
            ->get();

        if ($publicFaqs->isEmpty()) {
            $publicFaqs = \App\Models\FaqArtikel::published()
                ->orderBy('urutan', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'area_konsultasi'    => ucfirst($item->kategori),
                        'judul_permasalahan' => $item->pertanyaan,
                        'uraian_permasalahan'=> $item->jawaban,
                        'kesimpulan_advis'   => $item->dasar_hukum_rujukan ? "Dasar Hukum: {$item->dasar_hukum_rujukan}\n\n{$item->jawaban}" : $item->jawaban,
                        'updated_at'         => $item->updated_at,
                    ];
                });
        }

        $irbans = Irban::all();

        return view('welcome', array_merge($data, compact('tahun', 'publicFaqs', 'irbans')));
    }
}

