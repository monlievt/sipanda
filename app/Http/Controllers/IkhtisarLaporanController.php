<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\IkhtisarLaporan;
use App\Models\KelompokPengawasan;
use App\Models\Penugasan;
use App\Models\Pkppt;
use App\Models\RincianPenyetoranTl;
use App\Models\TindakLanjut;
use App\Models\User;
use App\Services\ApipAiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
        if (!$user || !$user->hasAnyRole(['admin', 'administrator', 'sekretariat', 'superadmin', 'inspektur', 'sekretaris'])) {
            abort(403, 'Akses Terbatas: Penyusunan, pengeditan, dan penghapusan Ikhtisar Laporan Hasil Pengawasan (ILHP) hanya dapat dilakukan oleh Tim Evaluasi dan Pelaporan / Administrator.');
        }
    }

    /**
     * Form Generator / Pembuatan Ikhtisar Baru (Dengan Kompilasi Data Otomatis 4 Bagian).
     */
    public function create(Request $request): View
    {
        $this->authorizePenyusun();

        $tahun = (int) $request->input('tahun', date('Y'));
        $periode = $request->input('periode', 'tahunan');

        $range = $this->calculateDateRange($tahun, $periode);
        $compiledData = $this->compileIlhpData($tahun, $periode, $range['start'], $range['end']);

        $periodeTitle = $this->getPeriodeTitle($periode);
        $defaultJudul = "Ikhtisar Laporan Hasil Pengawasan " . $periodeTitle . " Tahun Anggaran " . $tahun;
        $tahunList = range(date('Y') + 1, 2022);

        $geminiApiKey = AppSetting::get('gemini_api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', '')));
        $geminiModel  = AppSetting::get('gemini_model', config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash')));

        return view('ikhtisar-laporan.create', compact(
            'tahun', 'periode', 'periodeTitle', 'range', 'compiledData', 'defaultJudul', 'tahunList',
            'geminiApiKey', 'geminiModel'
        ));
    }

    /**
     * Simpan Draf / Dokumen Ikhtisar Laporan (Sistematika Baru).
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
            'resume_ai'       => ['nullable', 'string'],
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
            'resume_ai'             => $validated['resume_ai'] ?? null,
            'catatan_khusus'        => $validated['catatan_khusus'] ?? null,
            'status'                => $validated['status'],
            'dibuat_oleh'           => auth()->id(),
        ]);

        ActivityLog::catat('ikhtisar_laporan', $ikhtisar->id, 'create', null, $ikhtisar->toArray());

        return redirect()->route('ikhtisar-laporan.show', $ikhtisar)
            ->with('status', '✓ Dokumen Ikhtisar Laporan Hasil Pengawasan berhasil dikompilasi dan disimpan.');
    }

    /**
     * Tampilkan Dokumen Lengkap Ikhtisar Hasil Pengawasan (4 Bagian Utama).
     */
    public function show(IkhtisarLaporan $ikhtisarLaporan): View
    {
        $compiledData = $this->compileIlhpData(
            $ikhtisarLaporan->tahun,
            $ikhtisarLaporan->periode,
            $ikhtisarLaporan->tanggal_awal_periode,
            $ikhtisarLaporan->tanggal_akhir_periode
        );

        $inspektur = User::where('jabatan', 'like', '%inspektur%')
            ->where('jabatan', 'not like', '%pembantu%')
            ->first() ?? User::role('inspektur')->first();

        $geminiApiKey = AppSetting::get('gemini_api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', '')));
        $geminiModel  = AppSetting::get('gemini_model', config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash')));

        return view('ikhtisar-laporan.show', compact('ikhtisarLaporan', 'compiledData', 'inspektur', 'geminiApiKey', 'geminiModel'));
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

        $inspektur = User::where('jabatan', 'like', '%inspektur%')
            ->where('jabatan', 'not like', '%pembantu%')
            ->first() ?? User::role('inspektur')->first();

        return view('ikhtisar-laporan.cetak', compact('ikhtisarLaporan', 'compiledData', 'inspektur'));
    }

    /**
     * Form Edit Narasi Resume AI & Metadata Ikhtisar.
     */
    public function edit(IkhtisarLaporan $ikhtisarLaporan): View
    {
        $this->authorizePenyusun();

        $compiledData = $this->compileIlhpData(
            $ikhtisarLaporan->tahun,
            $ikhtisarLaporan->periode,
            $ikhtisarLaporan->tanggal_awal_periode,
            $ikhtisarLaporan->tanggal_akhir_periode
        );

        $geminiApiKey = AppSetting::get('gemini_api_key', config('services.gemini.api_key', env('GEMINI_API_KEY', '')));
        $geminiModel  = AppSetting::get('gemini_model', config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash')));

        return view('ikhtisar-laporan.edit', compact('ikhtisarLaporan', 'compiledData', 'geminiApiKey', 'geminiModel'));
    }

    /**
     * Update Narasi Resume AI & Metadata Ikhtisar.
     */
    public function update(Request $request, IkhtisarLaporan $ikhtisarLaporan): RedirectResponse
    {
        $this->authorizePenyusun();

        $validated = $request->validate([
            'judul'           => ['required', 'string', 'max:255'],
            'nomor_surat'     => ['nullable', 'string', 'max:100'],
            'tanggal_laporan' => ['required', 'date'],
            'resume_ai'       => ['nullable', 'string'],
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

    /**
     * AJAX Endpoint: Generate Ringkasan Resume AI dari Temuan & Rekomendasi.
     */
    public function generateResumeAi(Request $request, ApipAiService $aiService): JsonResponse
    {
        $this->authorizePenyusun();

        $tahun = (int) $request->input('tahun', date('Y'));
        $periode = $request->input('periode', 'tahunan');

        $range = $this->calculateDateRange($tahun, $periode);
        $compiled = $this->compileIlhpData($tahun, $periode, $range['start'], $range['end']);

        $periodeTitle = $this->getPeriodeTitle($periode);
        $result = $aiService->generateResumeIkhtisar($tahun, $periodeTitle, $compiled['listTemuanRekomendasi']);

        return response()->json($result);
    }

    /**
     * AJAX Endpoint: Simpan Pengaturan Gemini API Key & Model (AppSetting).
     */
    public function saveGeminiKey(Request $request): JsonResponse
    {
        $this->authorizePenyusun();

        $validated = $request->validate([
            'gemini_api_key' => ['required', 'string'],
            'gemini_model'   => ['nullable', 'string'],
        ]);

        AppSetting::set('gemini_api_key', trim($validated['gemini_api_key']));
        if (!empty($validated['gemini_model'])) {
            AppSetting::set('gemini_model', trim($validated['gemini_model']));
        }

        return response()->json([
            'success' => true,
            'message' => '✓ Pengaturan Google Gemini API Key berhasil disimpan.',
        ]);
    }

    // ─── ENGINE KOMPILASI DATA OTOMATIS 4 BAGIAN UTAMA ──────────────────────────

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
     * Kompilasi seluruh data 4 Bagian Utama ILHP sesuai pedoman template resmi.
     */
    protected function compileIlhpData(int $tahun, string $periode, $startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // ══════════════════════════════════════════════════════════════════════════
        // BAGIAN 1: SUMBER DAYA MANUSIA (SDM) BERDASARKAN JABATAN DEFINITIF
        // ══════════════════════════════════════════════════════════════════════════
        // Perhitungan:
        // - Data diambil dari jumlah pegawai aktif internal SIPANDA
        // - Didasarkan pada jabatan definitif yang diduduki
        // - Formula Unsur Kesekretariatan = Total Pegawai - Inspektur - Sekretaris - Irban - Auditor - PPUPD
        $usersAktif = User::where('is_active', true)->where('tipe_akun', 'internal')->get();
        $totalPegawaiAktif = $usersAktif->count();

        $countInspektur = $usersAktif->filter(function ($u) {
            $jab = strtolower($u->jabatan ?? '');
            $statusJab = strtolower($u->status_jabatan ?? 'definitif');
            return str_contains($jab, 'inspektur') && !str_contains($jab, 'pembantu') && !str_contains($jab, 'irban') && ($statusJab === 'definitif' || empty($u->status_jabatan));
        })->count();

        $countSekretaris = $usersAktif->filter(function ($u) {
            $jab = strtolower($u->jabatan ?? '');
            return str_contains($jab, 'sekretaris');
        })->count();

        $countIrban = $usersAktif->filter(function ($u) {
            $jab = strtolower($u->jabatan ?? '');
            return str_contains($jab, 'inspektur pembantu') || str_contains($jab, 'irban');
        })->count();

        $countAuditor = $usersAktif->filter(function ($u) {
            $jab = strtolower($u->jabatan ?? '');
            $isNotPimpinan = !str_contains($jab, 'inspektur') && !str_contains($jab, 'sekretaris');
            return $isNotPimpinan && (str_contains($jab, 'auditor') || $u->hasRole('auditor'));
        })->count();

        $countPpupd = $usersAktif->filter(function ($u) {
            $jab = strtolower($u->jabatan ?? '');
            $isNotPimpinan = !str_contains($jab, 'inspektur') && !str_contains($jab, 'sekretaris');
            return $isNotPimpinan && (str_contains($jab, 'ppupd') || $u->hasRole('ppupd'));
        })->count();

        $countKesekretariatan = max(0, $totalPegawaiAktif - ($countInspektur + $countSekretaris + $countIrban + $countAuditor + $countPpupd));

        $tabelSdm = [
            ['no' => 1, 'jabatan' => 'Inspektur', 'jumlah' => $countInspektur],
            ['no' => 2, 'jabatan' => 'Sekretaris', 'jumlah' => $countSekretaris],
            ['no' => 3, 'jabatan' => 'Inspektur Pembantu', 'jumlah' => $countIrban],
            ['no' => 4, 'jabatan' => 'Fungsional Auditor', 'jumlah' => $countAuditor],
            ['no' => 5, 'jabatan' => 'Fungsional PPUPD', 'jumlah' => $countPpupd],
            ['no' => 6, 'jabatan' => 'Unsur Kesekretariatan', 'jumlah' => $countKesekretariatan],
        ];

        // ══════════════════════════════════════════════════════════════════════════
        // BAGIAN 2: DATA CATATAN/TEMUAN & REKOMENDASI UNTUK RESUME AI
        // ══════════════════════════════════════════════════════════════════════════
        $allTindakLanjutPeriode = TindakLanjut::with(['penugasan.objekPenugasan', 'objekPenugasan', 'rincianPenyetoran'])
            ->where(function ($q) use ($start, $end, $tahun) {
                $q->whereBetween('tgl_lhp', [$start, $end])
                  ->orWhereBetween('created_at', [$start, $end])
                  ->orWhereHas('penugasan', fn($pq) => $pq->whereBetween('tanggal_mulai', [$start, $end]));
            })->get();

        if ($allTindakLanjutPeriode->isEmpty()) {
            $allTindakLanjutPeriode = TindakLanjut::with(['penugasan.objekPenugasan', 'objekPenugasan', 'rincianPenyetoran'])
                ->whereYear('created_at', $tahun)
                ->get();
        }

        $listTemuanRekomendasi = $allTindakLanjutPeriode->map(function ($tl) {
            return [
                'id'                   => $tl->id,
                'no_lhp'               => $tl->no_lhp ?? ($tl->penugasan?->no_spt ?? '-'),
                'judul_lhp'            => $tl->judul_lhp ?? '-',
                'objek'                => $tl->objekPenugasan?->nama ?? ($tl->penugasan?->objekPenugasan?->pluck('nama')->implode(', ') ?? 'Perangkat Daerah'),
                'uraian_temuan'        => $tl->uraian_temuan,
                'rekomendasi'          => $tl->rekomendasi,
                'nilai_rekomendasi_rp' => (float) $tl->nilai_rekomendasi_rp,
                'status'               => $tl->status_tindak_lanjut,
            ];
        })->values()->toArray();

        // ══════════════════════════════════════════════════════════════════════════
        // BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT)
        // ══════════════════════════════════════════════════════════════════════════
        // Tabel per Tahun: Total LHP, Nilai Pengawasan, Total Saran/Rekom, Nilai Rekom, Status TL (Sesuai, Belum Sesuai, Belum di TL, TDT), Nilai Pengembalian, Sisa Pengembalian
        $targetYears = range($tahun, max(2023, $tahun - 2));
        $rekapHasilPengawasanPerTahun = [];

        foreach ($targetYears as $idx => $t) {
            $tlYearQuery = TindakLanjut::with(['penugasan', 'rincianPenyetoran'])
                ->where(function ($q) use ($t) {
                    $q->whereYear('tgl_lhp', $t)
                      ->orWhereYear('created_at', $t)
                      ->orWhereHas('penugasan', fn($pq) => $pq->whereYear('tanggal_mulai', $t));
                })->get();

            $totalLhp = $tlYearQuery->pluck('no_lhp')->filter()->unique()->count();
            if ($totalLhp === 0) {
                $totalLhp = $tlYearQuery->pluck('penugasan_id')->filter()->unique()->count();
            }

            $nilaiDiawasi = (float) $tlYearQuery->groupBy(fn($i) => $i->no_lhp ?: $i->penugasan_id)->map(fn($g) => $g->max('nilai_diawasi_rp') ?? 0)->sum();
            $totalRekom = $tlYearQuery->count();
            $nilaiRekom = (float) $tlYearQuery->sum('nilai_rekomendasi_rp');

            $countSesuai      = $tlYearQuery->where('status_tindak_lanjut', 'selesai')->count();
            $countBelumSesuai = $tlYearQuery->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
            $countBelumDiTl   = $tlYearQuery->where('status_tindak_lanjut', 'belum')->count();
            $countTdt         = $tlYearQuery->where('status_tindak_lanjut', 'tdt')->count();

            $nilaiPengembalian = (float) $tlYearQuery->sum(fn($it) => $it->rincianPenyetoran->sum('nilai_setor_rp'));
            $sisaPengembalian  = max(0, $nilaiRekom - $nilaiPengembalian);

            $rekapHasilPengawasanPerTahun[] = [
                'no'                 => $idx + 1,
                'tahun'              => $t,
                'total_lhp'          => $totalLhp,
                'nilai_diawasi_rp'   => $nilaiDiawasi,
                'total_rekomendasi'  => $totalRekom,
                'nilai_rekomendasi_rp' => $nilaiRekom,
                'status_sesuai'      => $countSesuai,
                'status_belum_sesuai'=> $countBelumSesuai,
                'status_belum_tl'    => $countBelumDiTl,
                'status_tdt'         => $countTdt,
                'nilai_pengembalian_rp' => $nilaiPengembalian,
                'sisa_pengembalian_rp'  => $sisaPengembalian,
            ];
        }

        // ══════════════════════════════════════════════════════════════════════════
        // BAGIAN 4: RINCIAN PENGAWASAN SESUAI KELOMPOK / KLUSTER PENGAWASAN
        // ══════════════════════════════════════════════════════════════════════════
        // 6 Kluster Resmi:
        // 1. Pengawasan Proyek Strategis Daerah (PSD)
        // 2. Pengawasan Keuangan dan Aset Daerah
        // 3. Pengawasan Keuangan dan Aset Desa
        // 4. Pengawasan Kinerja
        // 5. Pengawasan Khusus
        // 6. Pengawasan dan Pembinaan Lainnya
        // Rencana: Dari PKPPT tahun berjalan
        // Realisasi: Dari SPT (Surat Tugas perpanjangan & bantuan tidak dihitung ganda / dihitung 1 dengan SPT induk)
        $masterKluster = [
            ['kode' => 'PSD',       'nama' => 'Pengawasan Proyek Strategis Daerah (PSD)'],
            ['kode' => 'KEU_ASET',  'nama' => 'Pengawasan Keuangan dan Aset Daerah'],
            ['kode' => 'KEU_DESA',  'nama' => 'Pengawasan Keuangan dan Aset Desa'],
            ['kode' => 'KINERJA',   'nama' => 'Pengawasan Kinerja'],
            ['kode' => 'KHUSUS',    'nama' => 'Pengawasan Khusus'],
            ['kode' => 'LAINNYA',   'nama' => 'Pengawasan dan Pembinaan Lainnya'],
        ];

        $pkpptTahunList = Pkppt::where('tahun', $tahun)->get();

        // Ambil penugasan tahun berjalan yang BUKAN merupakan surat tugas perpanjangan/bantuan (penugasan_induk_id IS NULL)
        $penugasanTahunList = Penugasan::with('pkppt')
            ->where(function ($q) use ($tahun) {
                $q->whereYear('tanggal_mulai', $tahun)
                  ->orWhereHas('pkppt', fn($pk) => $pk->where('tahun', $tahun));
            })
            ->whereNull('penugasan_induk_id')
            ->get();

        $tabelKlusterPengawasan = [];
        $totalRencanaKluster = 0;
        $totalRealisasiKluster = 0;

        foreach ($masterKluster as $idx => $kluster) {
            $kelompokModel = KelompokPengawasan::where('kode_kelompok', $kluster['kode'])
                ->orWhere('nama_kelompok', 'like', '%' . $kluster['nama'] . '%')
                ->first();

            $kelId = $kelompokModel?->id;

            // Rencana: Penjumlahan dari PKPPT tahun berjalan
            $rencana = $pkpptTahunList->filter(function ($p) use ($kelId, $kluster) {
                if ($kelId && $p->kelompok_pengawasan_id === $kelId) {
                    return true;
                }
                return str_contains(strtolower($p->area_pengawasan ?? ''), strtolower($kluster['kode']))
                    || str_contains(strtolower($p->jenis_pengawasan ?? ''), strtolower($kluster['kode']));
            })->sum('jumlah_laporan_rencana');

            // Realisasi: Penjumlahan dari SPT induk (1 penugasan induk = 1 realisasi kluster)
            $realisasi = $penugasanTahunList->filter(function ($s) use ($kelId, $kluster) {
                if ($kelId && $s->kelompok_pengawasan_id === $kelId) {
                    return true;
                }
                if ($s->pkppt_id) {
                    return $s->pkppt?->kelompok_pengawasan_id === $kelId;
                }
                $uraian = strtolower($s->uraian_penugasan ?? '');
                return str_contains($uraian, strtolower($kluster['kode']));
            })->count();

            $totalRencanaKluster += $rencana;
            $totalRealisasiKluster += $realisasi;

            $tabelKlusterPengawasan[] = [
                'no'        => $idx + 1,
                'kluster'   => $kluster['nama'],
                'kode'      => $kluster['kode'],
                'rencana'   => $rencana,
                'realisasi' => $realisasi,
            ];
        }

        return [
            'totalPegawaiAktif'             => $totalPegawaiAktif,
            'tabelSdm'                      => $tabelSdm,
            'listTemuanRekomendasi'         => $listTemuanRekomendasi,
            'rekapHasilPengawasanPerTahun'  => $rekapHasilPengawasanPerTahun,
            'tabelKlusterPengawasan'        => $tabelKlusterPengawasan,
            'totalRencanaKluster'           => $totalRencanaKluster,
            'totalRealisasiKluster'         => $totalRealisasiKluster,
            'tahun'                         => $tahun,
            'periode'                       => $periode,
        ];
    }
}
