<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\IkhtisarLaporan;
use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\RegulasiHukum;
use App\Models\RincianPenyetoranTl;
use App\Models\SumberPenugasan;
use App\Models\TindakLanjut;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IkhtisarLaporanController extends Controller
{
    /**
     * Daftar Laporan Ikhtisar Hasil Pengawasan (ILHP) yang tersimpan.
     */
    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $periode = $request->input('periode');

        $query = IkhtisarLaporan::with('pembuat')->where('tahun', $tahun);

        if ($periode) {
            $query->where('periode', $periode);
        }

        $listLaporan = $query->orderBy('tanggal_laporan', 'desc')->paginate(10)->withQueryString();
        $tahunList = range(date('Y') + 1, 2022);

        return view('ikhtisar-laporan.index', compact('listLaporan', 'tahun', 'periode', 'tahunList'));
    }

    /**
     * Pastikan hanya Sekretariat / Tim Pelaporan dan Admin yang dapat menyusun / mengedit ILHP.
     */
    protected function authorizePenyusun(): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['admin', 'sekretariat', 'superadmin'])) {
            abort(403, 'Akses Terbatas: Penyusunan, pengeditan, dan penghapusan Ikhtisar Laporan Hasil Pengawasan (ILHP) hanya dapat dilakukan oleh Tim Evaluasi dan Pelaporan (Sekretariat).');
        }
    }

    /**
     * Form Generator / Pembuatan Ikhtisar Baru (Dengan Kompilasi Data Otomatis).
     */
    public function create(Request $request): View
    {
        $this->authorizePenyusun();

        $tahun = (int) $request->input('tahun', date('Y'));
        $periode = $request->input('periode', 'triwulan_1');

        $range = $this->calculateDateRange($tahun, $periode);
        $compiledData = $this->compileIlhpData($tahun, $periode, $range['start'], $range['end']);

        $periodeTitle = $this->getPeriodeTitle($periode);
        $defaultJudul = "Ikhtisar Laporan Hasil Pengawasan " . $periodeTitle . " Tahun Anggaran " . $tahun;
        $tahunList = range(date('Y') + 1, 2022);

        return view('ikhtisar-laporan.create', compact('tahun', 'periode', 'periodeTitle', 'range', 'compiledData', 'defaultJudul', 'tahunList'));
    }

    /**
     * Simpan Draf / Dokumen Ikhtisar Laporan.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizePenyusun();
        $validated = $request->validate([
            'tahun'           => ['required', 'integer', 'min:2020', 'max:2035'],
            'periode'         => ['required', 'string', 'in:triwulan_1,triwulan_2,triwulan_3,triwulan_4,semester_1,semester_2,tahunan'],
            'judul'           => ['required', 'string', 'max:255'],
            'nomor_surat'     => ['nullable', 'string', 'max:100'],
            'tanggal_laporan' => ['required', 'date'],
            'simpulan'        => ['nullable', 'string'],
            'hambatan'        => ['nullable', 'string'],
            'rekomendasi'     => ['nullable', 'string'],
            'catatan_khusus'  => ['nullable', 'string'],
            'status'          => ['required', 'in:draft,final'],
        ]);

        $range = $this->calculateDateRange($validated['tahun'], $validated['periode']);

        $ikhtisar = IkhtisarLaporan::create([
            'tahun'                 => $validated['tahun'],
            'periode'               => $validated['periode'],
            'judul'                 => $validated['judul'],
            'nomor_surat'           => $validated['nomor_surat'],
            'tanggal_laporan'       => $validated['tanggal_laporan'],
            'tanggal_awal_periode'  => $range['start'],
            'tanggal_akhir_periode' => $range['end'],
            'simpulan'              => $validated['simpulan'],
            'hambatan'              => $validated['hambatan'],
            'rekomendasi'           => $validated['rekomendasi'],
            'catatan_khusus'        => $validated['catatan_khusus'],
            'status'                => $validated['status'],
            'dibuat_oleh'           => auth()->id(),
        ]);

        ActivityLog::catat('ikhtisar_laporan', $ikhtisar->id, 'create', null, $ikhtisar->toArray());

        return redirect()->route('ikhtisar-laporan.show', $ikhtisar)
            ->with('status', '✓ Dokumen Ikhtisar Laporan Hasil Pengawasan berhasil dikompilasi dan disimpan.');
    }

    /**
     * Tampilkan Dokumen Lengkap Ikhtisar Hasil Pengawasan (BAB I s/d BAB V).
     */
    public function show(IkhtisarLaporan $ikhtisarLaporan): View
    {
        $compiledData = $this->compileIlhpData(
            $ikhtisarLaporan->tahun,
            $ikhtisarLaporan->periode,
            $ikhtisarLaporan->tanggal_awal_periode,
            $ikhtisarLaporan->tanggal_akhir_periode
        );

        $inspektur = User::role('inspektur')->first();

        return view('ikhtisar-laporan.show', compact('ikhtisarLaporan', 'compiledData', 'inspektur'));
    }

    /**
     * Tampilan Cetak Resmi / Naskah Dinas Eksekutif untuk Bupati (Print View & PDF).
     */
    public function cetak(IkhtisarLaporan $ikhtisarLaporan): View
    {
        $compiledData = $this->compileIlhpData(
            $ikhtisarLaporan->tahun,
            $ikhtisarLaporan->periode,
            $ikhtisarLaporan->tanggal_awal_periode,
            $ikhtisarLaporan->tanggal_akhir_periode
        );

        $inspektur = User::role('inspektur')->first();

        return view('ikhtisar-laporan.cetak', compact('ikhtisarLaporan', 'compiledData', 'inspektur'));
    }

    /**
     * Form Edit Narasi & Catatan Ikhtisar.
     */
    public function edit(IkhtisarLaporan $ikhtisarLaporan): View
    {
        $this->authorizePenyusun();

        return view('ikhtisar-laporan.edit', compact('ikhtisarLaporan'));
    }

    /**
     * Update Narasi & Status Ikhtisar.
     */
    public function update(Request $request, IkhtisarLaporan $ikhtisarLaporan): RedirectResponse
    {
        $this->authorizePenyusun();

        $validated = $request->validate([
            'judul'           => ['required', 'string', 'max:255'],
            'nomor_surat'     => ['nullable', 'string', 'max:100'],
            'tanggal_laporan' => ['required', 'date'],
            'simpulan'        => ['nullable', 'string'],
            'hambatan'        => ['nullable', 'string'],
            'rekomendasi'     => ['nullable', 'string'],
            'catatan_khusus'  => ['nullable', 'string'],
            'status'          => ['required', 'in:draft,final'],
        ]);

        $sebelum = $ikhtisarLaporan->toArray();
        $ikhtisarLaporan->update($validated);

        ActivityLog::catat('ikhtisar_laporan', $ikhtisarLaporan->id, 'update', $sebelum, $ikhtisarLaporan->toArray());

        return redirect()->route('ikhtisar-laporan.show', $ikhtisarLaporan)
            ->with('status', '✓ Perubahan dokumen Ikhtisar Laporan berhasil disimpan.');
    }

    /**
     * Hapus Dokumen Ikhtisar.
     */
    public function destroy(IkhtisarLaporan $ikhtisarLaporan): RedirectResponse
    {
        $this->authorizePenyusun();

        $sebelum = $ikhtisarLaporan->toArray();
        $ikhtisarLaporan->delete();

        ActivityLog::catat('ikhtisar_laporan', $ikhtisarLaporan->id, 'delete', $sebelum, null);

        return redirect()->route('ikhtisar-laporan.index')
            ->with('status', 'Dokumen Ikhtisar Laporan berhasil dihapus.');
    }

    // ─── ENGINE KOMPILASI DATA OTOMATIS (BAB I s/d BAB IV) ──────────────────────────

    /**
     * Hitung tanggal awal & akhir berdasarkan periode.
     */
    protected function calculateDateRange(int $tahun, string $periode): array
    {
        return match($periode) {
            'triwulan_1' => ['start' => "{$tahun}-01-01", 'end' => "{$tahun}-03-31"],
            'triwulan_2' => ['start' => "{$tahun}-04-01", 'end' => "{$tahun}-06-30"],
            'triwulan_3' => ['start' => "{$tahun}-07-01", 'end' => "{$tahun}-09-30"],
            'triwulan_4' => ['start' => "{$tahun}-10-01", 'end' => "{$tahun}-12-31"],
            'semester_1' => ['start' => "{$tahun}-01-01", 'end' => "{$tahun}-06-30"],
            'semester_2' => ['start' => "{$tahun}-07-01", 'end' => "{$tahun}-12-31"],
            default      => ['start' => "{$tahun}-01-01", 'end' => "{$tahun}-12-31"],
        };
    }

    protected function getPeriodeTitle(string $periode): string
    {
        return match($periode) {
            'triwulan_1' => 'Triwulan I',
            'triwulan_2' => 'Triwulan II',
            'triwulan_3' => 'Triwulan III',
            'triwulan_4' => 'Triwulan IV',
            'semester_1' => 'Semester I',
            'semester_2' => 'Semester II',
            default      => 'Tahunan',
        };
    }

    /**
     * Kompilasi seluruh metrik, tabel rekapitulasi, dan rincian BAB I - IV dari database.
     */
    protected function compileIlhpData(int $tahun, string $periode, $startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // ── BAB I: INFORMASI UMUM ──
        $dasarHukum = RegulasiHukum::dasarSptBaku()->get()->sortBy('hierarki_order')->values();
        if ($dasarHukum->isEmpty()) {
            $dasarHukum = RegulasiHukum::orderBy('tahun', 'desc')->take(5)->get();
        }

        $irbans = Irban::with(['users' => fn($q) => $q->aktif()])->get();
        $totalPersonilAktif = User::aktif()->internal()->count();
        $totalAuditor = User::aktif()->where('jabatan', 'like', '%auditor%')->count();
        $totalPpupd   = User::aktif()->where('jabatan', 'like', '%ppupd%')->count();
        
        // Fallback jika jabatan belum diisi spesifik
        if ($totalAuditor === 0 && $totalPpupd === 0) {
            $totalAuditor = User::aktif()->role('auditor')->count();
        }
        $totalStaf    = max(0, $totalPersonilAktif - $totalAuditor - $totalPpupd);

        // Capaian Program PKPPT dalam periode
        $pkpptList = Pkppt::with('penugasan')->where('tahun', $tahun)->get();
        $totalTargetPkppt = $pkpptList->count();
        $totalTargetLaporan = $pkpptList->sum('jumlah_laporan_rencana');

        // Seluruh Penugasan SPT yang berada dalam rentang tanggal periode
        $allPenugasanPeriode = Penugasan::with([
            'irban', 'irbans', 'jenisPenugasan', 'sumberPenugasan', 'objekPenugasan', 'tim.user'
        ])->where(function ($q) use ($start, $end) {
            $q->whereBetween('tanggal_mulai', [$start, $end])
              ->orWhereBetween('tanggal_selesai', [$start, $end]);
        })->orderBy('tanggal_mulai', 'asc')->get();

        $totalSptTerbit = $allPenugasanPeriode->count();
        $totalSptSelesai = $allPenugasanPeriode->where('status', 'selesai')->count();
        $totalSptBerjalan = $allPenugasanPeriode->where('status', 'berjalan')->count();
        $totalSptBelum = $allPenugasanPeriode->where('status', 'belum_berjalan')->count();
        $persenRealisasiPkppt = $totalTargetPkppt > 0 ? round(($totalSptTerbit / $totalTargetPkppt) * 100, 1) : 0;

        // ── BAB II: HASIL PENGAWASAN PER JENIS PENGAWASAN ──
        $kategoriAudit = [];
        $kategoriReviu = [];
        $kategoriEvaluasi = [];
        $kategoriPemantauan = [];
        $kategoriLainnya = [];

        foreach ($allPenugasanPeriode as $spt) {
            $namaJenis = strtolower($spt->jenisPenugasan?->nama ?? '');
            $katJenis  = strtolower($spt->jenisPenugasan?->kategori ?? '');

            if (str_contains($namaJenis, 'audit') || $katJenis === 'audit') {
                if (str_contains($namaJenis, 'kinerja')) {
                    $kategoriAudit['audit_kinerja'][] = $spt;
                } else {
                    $kategoriAudit['audit_dtt'][] = $spt;
                }
            } elseif (str_contains($namaJenis, 'reviu') || $katJenis === 'reviu') {
                $kategoriReviu[] = $spt;
            } elseif (str_contains($namaJenis, 'evaluasi') || $katJenis === 'evaluasi') {
                $kategoriEvaluasi[] = $spt;
            } elseif (str_contains($namaJenis, 'pemantauan') || str_contains($namaJenis, 'monitoring') || $katJenis === 'pemantauan') {
                $kategoriPemantauan[] = $spt;
            } else {
                $kategoriLainnya[] = $spt;
            }
        }

        // ── BAB III: PEMANTAUAN TINDAK LANJUT HASIL PENGAWASAN ──
        $allTindakLanjut = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'objekPenugasan', 'rincianPenyetoran'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tgl_lhp', [$start, $end])
                  ->orWhereBetween('created_at', [$start, $end])
                  ->orWhereHas('penugasan', fn($pq) => $pq->whereBetween('tanggal_mulai', [$start, $end]));
            })->get();

        if ($allTindakLanjut->isEmpty()) {
            // Fallback: Ambil seluruh data TL tahun terkait jika periode berjalan baru dimulai
            $allTindakLanjut = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'objekPenugasan', 'rincianPenyetoran'])
                ->whereYear('created_at', $tahun)
                ->get();
        }

        $tlCountTotal       = $allTindakLanjut->count();
        $tlCountSelesai     = $allTindakLanjut->where('status_tindak_lanjut', 'selesai')->count();
        $tlCountBelumSesuai = $allTindakLanjut->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
        $tlCountBelum       = $allTindakLanjut->where('status_tindak_lanjut', 'belum')->count();
        $tlCountTdt         = $allTindakLanjut->where('status_tindak_lanjut', 'tdt')->count();

        $tlTotalTargetRp = (float) $allTindakLanjut->sum('nilai_rekomendasi_rp');
        $tlTotalSetorRp  = (float) $allTindakLanjut->sum(fn($tl) => $tl->rincianPenyetoran->sum('nilai_setor_rp'));
        $tlSisaSetorRp   = max(0, $tlTotalTargetRp - $tlTotalSetorRp);
        $tlPersenSelesai = $tlCountTotal > 0 ? round(($tlCountSelesai / $tlCountTotal) * 100, 1) : 0;

        // Grouping Matrix per Objek Penugasan (OPD)
        $matrixOpd = $allTindakLanjut->groupBy(function ($it) {
            return $it->objekPenugasan?->nama ?? ($it->penugasan?->objekPenugasan?->first()?->nama ?? 'Umum / Lainnya');
        })->map(function ($items, $opdName) {
            $total = $items->count();
            $ss    = $items->where('status_tindak_lanjut', 'selesai')->count();
            $bs    = $items->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
            $btl   = $items->where('status_tindak_lanjut', 'belum')->count();
            $tdt   = $items->where('status_tindak_lanjut', 'tdt')->count();
            $target = $items->sum('nilai_rekomendasi_rp');
            $setor  = $items->sum(fn($it) => $it->rincianPenyetoran->sum('nilai_setor_rp'));
            $sisa   = max(0, $target - $setor);
            $persen = $total > 0 ? round(($ss / $total) * 100, 1) : 0;

            return (object) [
                'nama_opd'     => $opdName,
                'total'        => $total,
                'ss'           => $ss,
                'bs'           => $bs,
                'btl'          => $btl,
                'tdt'          => $tdt,
                'persen'       => $persen,
                'target_rp'    => $target,
                'setor_rp'     => $setor,
                'sisa_rp'      => $sisa,
            ];
        })->sortByDesc('total')->values();

        // ── BAB IV: HASIL PENANGANAN PENGADUAN MASYARAKAT (DUMAS & INVESTIGASI) ──
        $sptDumas = $allPenugasanPeriode->filter(function ($spt) {
            $sumberNama = strtolower($spt->sumberPenugasan?->nama ?? '');
            $jenisNama  = strtolower($spt->jenisPenugasan?->nama ?? '');
            return str_contains($sumberNama, 'pengaduan') || str_contains($sumberNama, 'aph') || str_contains($sumberNama, 'wbs')
                || str_contains($jenisNama, 'investigasi') || str_contains($jenisNama, 'khusus');
        })->values();

        return [
            'dasarHukum'           => $dasarHukum,
            'irbans'               => $irbans,
            'totalPersonilAktif'   => $totalPersonilAktif,
            'totalAuditor'         => $totalAuditor,
            'totalPpupd'           => $totalPpupd,
            'totalStaf'            => $totalStaf,
            'totalTargetPkppt'     => $totalTargetPkppt,
            'totalTargetLaporan'   => $totalTargetLaporan,
            'totalSptTerbit'       => $totalSptTerbit,
            'totalSptSelesai'      => $totalSptSelesai,
            'totalSptBerjalan'     => $totalSptBerjalan,
            'totalSptBelum'        => $totalSptBelum,
            'persenRealisasiPkppt' => $persenRealisasiPkppt,
            'kategoriAudit'        => $kategoriAudit,
            'kategoriReviu'        => $kategoriReviu,
            'kategoriEvaluasi'     => $kategoriEvaluasi,
            'kategoriPemantauan'   => $kategoriPemantauan,
            'kategoriLainnya'      => $kategoriLainnya,
            'tlCountTotal'         => $tlCountTotal,
            'tlCountSelesai'       => $tlCountSelesai,
            'tlCountBelumSesuai'   => $tlCountBelumSesuai,
            'tlCountBelum'         => $tlCountBelum,
            'tlCountTdt'           => $tlCountTdt,
            'tlTotalTargetRp'      => $tlTotalTargetRp,
            'tlTotalSetorRp'       => $tlTotalSetorRp,
            'tlSisaSetorRp'        => $tlSisaSetorRp,
            'tlPersenSelesai'      => $tlPersenSelesai,
            'matrixOpd'            => $matrixOpd,
            'sptDumas'             => $sptDumas,
        ];
    }
}
