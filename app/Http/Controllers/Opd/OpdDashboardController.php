<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\BuktiTindakLanjut;
use App\Models\RincianPenyetoranTl;
use App\Models\TindakLanjut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpdDashboardController extends Controller
{
    /**
     * Dashboard OPD: Daftar LHP dan Matriks 4 Status Rekomendasi Pengawasan.
     */
    public function index(Request $request): View
    {
        $user = auth('opd')->user();
        $objekId = $user->objek_penugasan_id;
        $search = $request->input('search');
        $tahun = $request->input('tahun');

        $query = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'buktiTindakLanjut.arsipDigital', 'rincianPenyetoran'])
            ->whereHas('penugasan.objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $objekId));

        if ($tahun) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('tgl_lhp', $tahun)
                  ->orWhereYear('created_at', $tahun)
                  ->orWhereHas('penugasan', fn($pq) => $pq->whereYear('tanggal_mulai', $tahun));
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_lhp', 'like', "%{$search}%")
                  ->orWhere('judul_lhp', 'like', "%{$search}%")
                  ->orWhere('uraian_temuan', 'like', "%{$search}%")
                  ->orWhere('rekomendasi', 'like', "%{$search}%");
            });
        }

        $allRekomendasi = $query->orderBy('created_at', 'desc')->get();

        // 📌 Grouping per Dokumen LHP agar 1 baris tabel = 1 Dokumen LHP
        $groupedLhp = $allRekomendasi->groupBy(function ($item) {
            return $item->no_lhp ? ('LHP:' . $item->no_lhp) : ('SPT:' . $item->penugasan_id);
        })->map(function ($items, $key) {
            $first = $items->first();
            $countSesuai      = $items->where('status_tindak_lanjut', 'selesai')->count();
            $countBelumSesuai = $items->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi', 'dikembalikan'])->count();
            $countBelum       = $items->where('status_tindak_lanjut', 'belum')->count();
            $countTdt         = $items->where('status_tindak_lanjut', 'tdt')->count();

            $totalNilaiTarget = $items->sum('nilai_rekomendasi_rp');
            $totalSetorRp     = $items->sum(fn($tl) => $tl->rincianPenyetoran->sum('nilai_setor_rp'));
            $sisaSetorRp      = max(0, $totalNilaiTarget - $totalSetorRp);
            $totalRekomendasi = $items->count();
            $persenSelesai    = $totalRekomendasi > 0 ? round(($countSesuai / $totalRekomendasi) * 100) : 0;

            return (object) [
                'key'                    => $key,
                'first_id'               => $first->id,
                'no_lhp'                 => $first->no_lhp ?: ('SPT: ' . ($first->penugasan?->no_spt ?? '-')),
                'judul_lhp'              => $first->judul_lhp ?: ($first->penugasan?->uraian_penugasan ?? 'Laporan Hasil Pengawasan'),
                'tgl_lhp'                => $first->tgl_lhp ?: $first->created_at,
                'penugasan'              => $first->penugasan,
                'berkas_dasar_lhp'       => $first->berkas_dasar_lhp,
                'items'                  => $items,
                'total_rekomendasi'      => $totalRekomendasi,
                'count_sesuai'           => $countSesuai,
                'count_belum_sesuai'     => $countBelumSesuai,
                'count_belum'            => $countBelum,
                'count_tdt'              => $countTdt,
                'total_nilai_target'     => $totalNilaiTarget,
                'total_setor_rp'         => $totalSetorRp,
                'sisa_setor_rp'          => $sisaSetorRp,
                'persen_selesai'         => $persenSelesai,
                'formatted_nilai_target' => 'Rp ' . number_format($totalNilaiTarget, 0, ',', '.'),
                'formatted_total_setor'  => 'Rp ' . number_format($totalSetorRp, 0, ',', '.'),
                'formatted_sisa_setor'   => 'Rp ' . number_format($sisaSetorRp, 0, ',', '.'),
            ];
        })->values();

        // Rekapitulasi Global untuk Banner Cards
        $totalSesuai      = $allRekomendasi->where('status_tindak_lanjut', 'selesai')->count();
        $totalBelumSesuai = $allRekomendasi->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi', 'dikembalikan'])->count();
        $totalBelum       = $allRekomendasi->where('status_tindak_lanjut', 'belum')->count();
        $totalTdt         = $allRekomendasi->where('status_tindak_lanjut', 'tdt')->count();

        $rekap = [
            'total_lhp'          => $groupedLhp->count(),
            'total_rekomendasi'  => $allRekomendasi->count(),
            'sesuai'             => $totalSesuai,
            'belum_sesuai'       => $totalBelumSesuai,
            'belum'              => $totalBelum,
            'tdt'                => $totalTdt,
            'total_target_rp'    => $allRekomendasi->sum('nilai_rekomendasi_rp'),
            'total_setor_rp'     => $allRekomendasi->sum(fn($tl) => $tl->rincianPenyetoran->sum('nilai_setor_rp')),
        ];

        $rekap['sisa_setor_rp'] = max(0, $rekap['total_target_rp'] - $rekap['total_setor_rp']);
        $rekap['persen_selesai'] = $rekap['total_rekomendasi'] > 0 ? round(($totalSesuai / $rekap['total_rekomendasi']) * 100) : 0;

        $availableYears = range(date('Y') + 1, 2022);

        return view('opd.dashboard', compact('user', 'groupedLhp', 'rekap', 'search', 'tahun', 'availableYears'));
    }

    /**
     * Halaman Rincian LHP: Menyajikan seluruh rekomendasi dalam 1 LHP dan form respon tindak lanjut.
     */
    public function showLhp(TindakLanjut $tindakLanjut): View|RedirectResponse
    {
        $user = auth('opd')->user();
        $isMilik = $tindakLanjut->penugasan->objekPenugasan->contains('id', $user->objek_penugasan_id);

        if (! $isMilik) {
            return redirect()->route('opd.dashboard')->with('error', 'Akses ditolak. LHP ini ditujukan untuk instansi lain.');
        }

        // Ambil seluruh rekomendasi yang tergabung dalam LHP yang sama
        $query = TindakLanjut::with(['penugasan.irban', 'buktiTindakLanjut.arsipDigital', 'buktiTindakLanjut.verifikator', 'rincianPenyetoran.pembuatData'])
            ->whereHas('penugasan.objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $user->objek_penugasan_id));

        if ($tindakLanjut->no_lhp) {
            $query->where('no_lhp', $tindakLanjut->no_lhp);
        } else {
            $query->where('penugasan_id', $tindakLanjut->penugasan_id)->whereNull('no_lhp');
        }

        $items = $query->orderBy('id', 'asc')->get();

        $countSesuai      = $items->where('status_tindak_lanjut', 'selesai')->count();
        $countBelumSesuai = $items->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi', 'dikembalikan'])->count();
        $countBelum       = $items->where('status_tindak_lanjut', 'belum')->count();
        $countTdt         = $items->where('status_tindak_lanjut', 'tdt')->count();

        $totalTarget = $items->sum('nilai_rekomendasi_rp');
        $totalSetor  = $items->sum(fn($tl) => $tl->rincianPenyetoran->sum('nilai_setor_rp'));
        $sisaSetor   = max(0, $totalTarget - $totalSetor);

        $lhpSummary = (object) [
            'id'                     => $tindakLanjut->id,
            'no_lhp'                 => $tindakLanjut->no_lhp ?: ('SPT: ' . ($tindakLanjut->penugasan?->no_spt ?? '-')),
            'judul_lhp'              => $tindakLanjut->judul_lhp ?: ($tindakLanjut->penugasan?->uraian_penugasan ?? 'Laporan Hasil Pengawasan'),
            'tgl_lhp'                => $tindakLanjut->tgl_lhp ?: $tindakLanjut->created_at,
            'penugasan'              => $tindakLanjut->penugasan,
            'berkas_dasar_lhp'       => $tindakLanjut->berkas_dasar_lhp,
            'total_rekomendasi'      => $items->count(),
            'count_sesuai'           => $countSesuai,
            'count_belum_sesuai'     => $countBelumSesuai,
            'count_belum'            => $countBelum,
            'count_tdt'              => $countTdt,
            'total_target_rp'        => $totalTarget,
            'total_setor_rp'         => $totalSetor,
            'sisa_setor_rp'          => $sisaSetor,
            'formatted_total_target' => 'Rp ' . number_format($totalTarget, 0, ',', '.'),
            'formatted_total_setor'  => 'Rp ' . number_format($totalSetor, 0, ',', '.'),
            'formatted_sisa_setor'   => 'Rp ' . number_format($sisaSetor, 0, ',', '.'),
            'persen_selesai'         => $items->count() > 0 ? round(($countSesuai / $items->count()) * 100) : 0,
        ];

        return view('opd.detail-lhp', compact('user', 'tindakLanjut', 'items', 'lhpSummary'));
    }

    /**
     * Detail Rekomendasi Tunggal & Riwayat Bukti.
     */
    public function show(TindakLanjut $tindakLanjut): View|RedirectResponse
    {
        $user = auth('opd')->user();
        $isMilik = $tindakLanjut->penugasan->objekPenugasan->contains('id', $user->objek_penugasan_id);

        if (! $isMilik) {
            return redirect()->route('opd.dashboard')->with('error', 'Akses ditolak. Rekomendasi ini ditujukan untuk OPD lain.');
        }

        $tindakLanjut->load(['penugasan', 'buktiTindakLanjut.arsipDigital', 'buktiTindakLanjut.verifikator', 'rincianPenyetoran.pembuatData']);

        return view('opd.detail-rekomendasi', compact('user', 'tindakLanjut'));
    }

    /**
     * OPD Mengunggah Bukti Tindak Lanjut baru + Input Setoran Kasda (Rp).
     */
    public function storeBukti(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $user = auth('opd')->user();
        $isMilik = $tindakLanjut->penugasan->objekPenugasan->contains('id', $user->objek_penugasan_id);

        if (! $isMilik) {
            return redirect()->route('opd.dashboard')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'catatan_opd'       => ['required', 'string'],
            'file'              => ['nullable', 'file', 'mimes:pdf,docx,xlsx,jpg,jpeg,png', 'max:10240'],
            'nilai_setor_rp'    => ['nullable'],
            'no_referensi_ntpn' => ['nullable', 'string', 'max:100'],
            'tgl_setor'         => ['nullable', 'date'],
            'nama_bank'         => ['nullable', 'string', 'max:100'],
        ]);

        $bukti = BuktiTindakLanjut::create([
            'tindak_lanjut_id'  => $tindakLanjut->id,
            'diunggah_oleh'     => $user->id,
            'catatan_opd'       => $validated['catatan_opd'],
            'status_verifikasi' => 'menunggu',
        ]);

        // Simpan file jika ada
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->extension();
            $path = $file->storeAs('arsip/' . date('Y/m'), $fileName, 'public');

            ArsipDigital::create([
                'penugasan_id'           => $tindakLanjut->penugasan_id,
                'tindak_lanjut_id'       => $tindakLanjut->id,
                'bukti_tindak_lanjut_id' => $bukti->id,
                'nama_file'              => $file->getClientOriginalName(),
                'path_file'              => $path,
                'ukuran_kb'              => round($file->getSize() / 1024) . ' KB',
                'mime_type'              => $file->getMimeType(),
                'kategori'               => 'Bukti Tindak Lanjut OPD',
                'diunggah_oleh'          => $user->id,
            ]);
        }

        // Simpan Setoran Finansial jika diisi
        $nilaiSetor = $this->parseNominalRp($request->input('nilai_setor_rp', 0));
        if ($nilaiSetor > 0) {
            RincianPenyetoranTl::create([
                'tindak_lanjut_id'  => $tindakLanjut->id,
                'mata_uang'         => 'IDR',
                'nilai_setor_rp'     => $nilaiSetor,
                'nama_bank'          => $request->input('nama_bank') ?: 'Bank Jatim / Kas Daerah',
                'no_referensi_ntpn'  => $request->input('no_referensi_ntpn'),
                'tgl_setor'          => $request->input('tgl_setor') ?: now()->toDateString(),
                'keterangan'         => 'Penyetoran kas daerah diinput melalui Portal OPD.',
                'dibuat_oleh'        => $user->id,
            ]);
        }

        // Update status rekomendasi menjadi 'menunggu_verifikasi'
        $tindakLanjut->update(['status_tindak_lanjut' => 'menunggu_verifikasi']);

        ActivityLog::catat('bukti_tindak_lanjut', $bukti->id, 'create', null, $bukti->toArray());

        // Kirim Notifikasi ke Tim Irban / Auditor Pengawas
        $tindakLanjut->load(['penugasan.irban', 'penugasan.tim.user', 'penugasan.pembuatData']);
        $namaOpd = $user->objekPenugasan?->nama ?? $user->nama_display ?? 'OPD';

        $recipients = collect();
        if ($tindakLanjut->penugasan?->pembuatData) {
            $recipients->push($tindakLanjut->penugasan->pembuatData);
        }
        foreach ($tindakLanjut->penugasan?->tim ?? [] as $tm) {
            if ($tm->user && in_array($tm->peran, ['ketua_tim', 'pengendali_teknis'])) {
                $recipients->push($tm->user);
            }
        }
        $recipients = $recipients->unique('id');

        foreach ($recipients as $auditor) {
            \App\Models\Notifikasi::create([
                'user_id'      => $auditor->id,
                'penugasan_id' => $tindakLanjut->penugasan_id,
                'jenis'        => 'bukti_diunggah',
                'judul'        => "Tindak Lanjut Baru dari {$namaOpd}",
                'pesan'        => "PIC {$namaOpd} mengirim respon/bukti baru untuk LHP {$tindakLanjut->no_lhp}." . ($nilaiSetor > 0 ? " Setoran Kasda: Rp " . number_format($nilaiSetor, 0, ',', '.') : ""),
                'url_target'   => route('tindak-lanjut.show', $tindakLanjut->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $auditor->notify(new \App\Notifications\BuktiBaruDiunggahNotification($bukti, $namaOpd));
            } catch (\Throwable $e) {
                // Ignore log if notification driver not configured
            }
        }

        $pesanSetor = $nilaiSetor > 0 ? " serta setoran Kasda Rp " . number_format($nilaiSetor, 0, ',', '.') : "";
        return back()->with('status', 'Tindak lanjut' . $pesanSetor . ' & berkas bukti berhasil dikirim! Menunggu verifikasi dari Inspektorat.');
    }

    /**
     * Helper sanitasi input nominal Rp dari format 15.000.000 ke float 15000000
     */
    private function parseNominalRp($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        return $clean ? (float) $clean : 0;
    }
}

