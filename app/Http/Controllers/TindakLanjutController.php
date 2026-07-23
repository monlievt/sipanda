<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\BuktiTindakLanjut;
use App\Models\Penugasan;
use App\Models\RincianPenyetoranTl;
use App\Models\TindakLanjut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TindakLanjutController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $tahun  = $request->input('tahun');

        $query = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'buktiTindakLanjut.pengunggah', 'rincianPenyetoran']);

        if ($status) {
            if ($status === 'proses') {
                $query->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi']);
            } else {
                $query->where('status_tindak_lanjut', $status);
            }
        }

        if ($tahun) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('tgl_lhp', $tahun)
                  ->orWhereYear('created_at', $tahun)
                  ->orWhereHas('penugasan', function ($pq) use ($tahun) {
                      $pq->whereYear('tanggal_spt', $tahun);
                  });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('uraian_temuan', 'like', "%{$search}%")
                  ->orWhere('rekomendasi', 'like', "%{$search}%")
                  ->orWhere('no_lhp', 'like', "%{$search}%")
                  ->orWhere('judul_lhp', 'like', "%{$search}%");
            });
        }

        $allTindakLanjut = $query->orderBy('created_at', 'desc')->get();

        // 📌 Grouping per Dokumen LHP / SPT agar 1 baris tabel = 1 LHP dengan 4 Kolom Jumlah Rekomendasi
        $groupedLhp = $allTindakLanjut->groupBy(function ($item) {
            return $item->no_lhp ? ('LHP:' . $item->no_lhp) : ('SPT:' . $item->penugasan_id);
        })->map(function ($items, $key) {
            $first = $items->first();
            $countSesuai      = $items->where('status_tindak_lanjut', 'selesai')->count();
            $countBelumSesuai = $items->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
            $countBelum       = $items->where('status_tindak_lanjut', 'belum')->count();
            $countTdt         = $items->where('status_tindak_lanjut', 'tdt')->count();

            $totalNilaiDiawasi = $items->max('nilai_diawasi_rp') ?? 0;
            $totalNilaiTarget  = $items->sum('nilai_rekomendasi_rp');
            $totalSetorRp      = $items->sum(function ($tl) {
                return $tl->rincianPenyetoran->sum('nilai_setor_rp');
            });

            return (object) [
                'key'                    => $key,
                'first_id'               => $first->id,
                'no_lhp'                 => $first->no_lhp,
                'judul_lhp'              => $first->judul_lhp,
                'tgl_lhp'                => $first->tgl_lhp,
                'penugasan'              => $first->penugasan,
                'berkas_dasar_lhp'       => $first->berkas_dasar_lhp,
                'items'                  => $items,
                'total_rekomendasi'      => $items->count(),
                'count_sesuai'           => $countSesuai,
                'count_belum_sesuai'     => $countBelumSesuai,
                'count_belum'            => $countBelum,
                'count_tdt'              => $countTdt,
                'total_nilai_diawasi'    => $totalNilaiDiawasi,
                'total_nilai_target'     => $totalNilaiTarget,
                'total_setor_rp'         => $totalSetorRp,
                'formatted_nilai_diawasi'=> 'Rp ' . number_format($totalNilaiDiawasi, 0, ',', '.'),
                'formatted_nilai_target' => 'Rp ' . number_format($totalNilaiTarget, 0, ',', '.'),
                'formatted_total_setor'  => 'Rp ' . number_format($totalSetorRp, 0, ',', '.'),
            ];
        })->values();

        $penugasanList = Penugasan::orderBy('no_spt', 'desc')->get();

        // Daftar Pilihan Tahun untuk Filter
        $availableYears = TindakLanjut::whereNotNull('tgl_lhp')
            ->pluck('tgl_lhp')
            ->map(fn($d) => $d->format('Y'))
            ->merge([date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3])
            ->unique()
            ->sortDesc()
            ->values();

        // 📊 Metrik Ringkasan Banner Atas
        $allTl = TindakLanjut::with('rincianPenyetoran')->get();
        $totalRekomendasi  = $allTl->count();
        $countSesuai       = $allTl->where('status_tindak_lanjut', 'selesai')->count();
        $countBelumSesuai  = $allTl->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
        $countBelum        = $allTl->where('status_tindak_lanjut', 'belum')->count();
        $countTdt          = $allTl->where('status_tindak_lanjut', 'tdt')->count();

        $totalNilaiRekomendasi = $allTl->sum('nilai_rekomendasi_rp');
        $totalRealisasiSetor   = RincianPenyetoranTl::sum('nilai_setor_rp');

        return view('tindak-lanjut.index', compact(
            'groupedLhp', 'penugasanList', 'status', 'search', 'tahun', 'availableYears',
            'totalRekomendasi', 'countSesuai', 'countBelumSesuai', 'countBelum', 'countTdt',
            'totalNilaiRekomendasi', 'totalRealisasiSetor'
        ));
    }

    /**
     * Halaman Detail Terpisah (Buka di Tab Baru / Halaman Baru) untuk Dokumen LHP Lengkap (Semua Rekomendasi).
     */
    public function show(TindakLanjut $tindakLanjut): View
    {
        $tindakLanjut->load([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.pengunggah',
            'rincianPenyetoran.pembuatData',
            'pembuatData',
        ]);

        // Ambil seluruh rekomendasi yang masuk dalam Dokumen LHP / Penugasan yang sama
        $lhpItems = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'buktiTindakLanjut.pengunggah',
            'buktiTindakLanjut.arsipDigital',
            'rincianPenyetoran.pembuatData',
            'pembuatData',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        $countSesuai      = $lhpItems->where('status_tindak_lanjut', 'selesai')->count();
        $countBelumSesuai = $lhpItems->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count();
        $countBelum       = $lhpItems->where('status_tindak_lanjut', 'belum')->count();
        $countTdt         = $lhpItems->where('status_tindak_lanjut', 'tdt')->count();

        $totalNilaiTarget = $lhpItems->sum('nilai_rekomendasi_rp');
        $totalSetorRp     = $lhpItems->sum(function ($tl) {
            return $tl->rincianPenyetoran->sum('nilai_setor_rp');
        });

        return view('tindak-lanjut.show', compact(
            'tindakLanjut', 'lhpItems',
            'countSesuai', 'countBelumSesuai', 'countBelum', 'countTdt',
            'totalNilaiTarget', 'totalSetorRp'
        ));
    }

    /**
     * Input Uraian Tindak Lanjut, Setoran Kasda (NTPN), & Upload Berkas Bukti (Admin/Tim Pemeriksa/OPD).
     */
    public function storeRespon(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $request->validate([
            'catatan_opd'          => ['required', 'string'],
            'berkas_bukti'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,zip', 'max:10240'],
            'status_tindak_lanjut' => ['nullable', 'in:belum,proses,selesai,tdt'],
            'nilai_setor_rp'       => ['nullable'],
            'no_referensi_ntpn'    => ['nullable', 'string', 'max:100'],
            'nama_bank'            => ['nullable', 'string', 'max:100'],
            'tgl_setor'            => ['nullable', 'date'],
        ], [
            'catatan_opd.required'  => 'Uraian tindak lanjut / jawaban wajib diisi.',
            'berkas_bukti.mimes'    => 'Format file bukti harus PDF, JPG, PNG, atau ZIP.',
            'berkas_bukti.max'      => 'Ukuran berkas bukti maksimal 10 MB.',
        ]);

        $isOpd  = auth()->guard('opd')->check() || auth()->user()?->hasRole(['opd']);
        $userId = auth()->id() ?? (auth()->guard('opd')->id() ?? 1);

        // 1. Simpan Uraian Respon Bukti Tindak Lanjut
        $bukti = BuktiTindakLanjut::create([
            'tindak_lanjut_id'   => $tindakLanjut->id,
            'diunggah_oleh'      => $userId,
            'catatan_opd'        => $request->input('catatan_opd'),
            'status_verifikasi'  => $isOpd ? 'menunggu' : 'diterima',
            'catatan_verifikasi' => $isOpd ? null : ('Diinput langsung oleh ' . (auth()->user()?->name ?? 'Admin/Tim Pemeriksa')),
            'diverifikasi_oleh'  => $isOpd ? null : $userId,
            'diverifikasi_pada'  => $isOpd ? null : now(),
        ]);

        // 2. Simpan Lampiran File Bukti
        if ($request->hasFile('berkas_bukti')) {
            $file = $request->file('berkas_bukti');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('bukti_tl', $fileName, 'public');

            ArsipDigital::create([
                'penugasan_id'          => $tindakLanjut->penugasan_id,
                'tindak_lanjut_id'      => $tindakLanjut->id,
                'bukti_tindak_lanjut_id'=> $bukti->id,
                'nama_file'             => $fileName,
                'path_file'             => $filePath,
                'ukuran_kb'             => round($file->getSize() / 1024) . ' KB',
                'mime_type'             => $file->getClientMimeType(),
                'kategori'              => 'Bukti Tindak Lanjut',
                'diunggah_oleh'         => $userId,
            ]);
        }

        // 3. Simpan Penyetoran Uang ke Kas Daerah jika diisi
        $nilaiSetor = $this->parseNominalRp($request->input('nilai_setor_rp', 0));
        if ($nilaiSetor > 0) {
            $tglSetor = $request->input('tgl_setor') ?? now()->toDateString();
            RincianPenyetoranTl::create([
                'tindak_lanjut_id'  => $tindakLanjut->id,
                'mata_uang'         => 'IDR',
                'nilai_setor_rp'     => $nilaiSetor,
                'nama_bank'          => $request->input('nama_bank') ?? 'Kas Daerah',
                'no_referensi_ntpn'  => $request->input('no_referensi_ntpn'),
                'tgl_setor'          => $tglSetor,
                'keterangan'         => 'Setoran Kasda disertakan bersama uraian tindak lanjut.',
                'dibuat_oleh'        => $userId,
            ]);

            $totalSetor = $tindakLanjut->rincianPenyetoran()->sum('nilai_setor_rp');
            if ($tindakLanjut->nilai_rekomendasi_rp > 0 && $totalSetor >= $tindakLanjut->nilai_rekomendasi_rp) {
                $tindakLanjut->update([
                    'status_tindak_lanjut'   => 'selesai',
                    'tanggal_selesai_aktual' => now()->toDateString(),
                ]);
            }
        }

        // 4. Update Status Tindak Lanjut jika diisi secara eksplisit
        $newStatus = $request->input('status_tindak_lanjut');
        if ($newStatus) {
            $tindakLanjut->update(['status_tindak_lanjut' => $newStatus]);
        } elseif ($tindakLanjut->status_tindak_lanjut === 'belum') {
            $tindakLanjut->update(['status_tindak_lanjut' => 'proses']);
        }

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'create', null, $bukti->toArray());

        $pesanSetor = $nilaiSetor > 0 ? " serta setoran Kasda Rp " . number_format($nilaiSetor, 0, ',', '.') : "";

        return back()->with('status', 'Uraian tindak lanjut' . $pesanSetor . ' & berkas bukti berhasil dicatat.');
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

    /**
     * Simpan Catatan Temuan & Rekomendasi (+ No LHP, Judul LHP, Tgl LHP, Nilai Rp & Lampiran PDF LHP).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'penugasan_id'                               => ['required', 'exists:penugasan,id'],
            'no_lhp'                                     => ['nullable', 'string', 'max:100'],
            'judul_lhp'                                  => ['nullable', 'string', 'max:255'],
            'nilai_diawasi_rp'                           => ['nullable'],
            'tgl_lhp'                                    => ['nullable', 'date'],
            'berkas_dasar_lhp'                           => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'items'                                      => ['nullable', 'array', 'min:1'],
            'items.*.temuan'                             => ['required_with:items', 'string'],
            'items.*.rekomendasi'                        => ['required_with:items', 'array', 'min:1'],
            'items.*.rekomendasi.*.uraian'               => ['required_with:items', 'string'],
            'items.*.rekomendasi.*.nilai_rekomendasi_rp' => ['nullable'],
            'items.*.rekomendasi.*.tanggal_target'       => ['nullable', 'date'],
        ], [
            'penugasan_id.required'  => 'Nomor SPT Penugasan wajib dipilih.',
            'berkas_dasar_lhp.mimes' => 'Berkas lampiran dokumen dasar harus berformat PDF.',
            'berkas_dasar_lhp.max'   => 'Ukuran berkas lampiran maksimal 10 MB.',
        ]);

        $penugasan = Penugasan::findOrFail($request->penugasan_id);
        $noLhp           = trim($request->input('no_lhp'));
        $judulLhp        = trim($request->input('judul_lhp'));
        $tglLhp          = $request->input('tgl_lhp');
        $nilaiDiawasiLhp = $this->parseNominalRp($request->input('nilai_diawasi_rp') ?? 0);
        $userId          = auth()->id() ?? (auth()->guard('opd')->id() ?? 1);

        $filePath = null;

        if ($request->hasFile('berkas_dasar_lhp')) {
            $file = $request->file('berkas_dasar_lhp');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('berkas_lhp', $fileName, 'public');

            ArsipDigital::create([
                'penugasan_id'  => $penugasan->id,
                'nama_file'     => $fileName,
                'path_file'     => $filePath,
                'ukuran_kb'     => round($file->getSize() / 1024) . ' KB',
                'mime_type'     => $file->getClientMimeType(),
                'kategori'      => 'Laporan Hasil Pengawasan (LHP)',
                'diunggah_oleh' => $userId,
            ]);
        }

        $createdCount = 0;

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $tIndex => $tData) {
                $uraianTemuan = "Temuan " . ($tIndex + 1) . ": " . trim($tData['temuan']);

                if (isset($tData['rekomendasi']) && is_array($tData['rekomendasi'])) {
                    foreach ($tData['rekomendasi'] as $rIndex => $rData) {
                        $uraianRekomendasi = "Rekomendasi " . ($rIndex + 1) . ": " . trim($rData['uraian']);
                        $nilaiRp           = $this->parseNominalRp($rData['nilai_rekomendasi_rp'] ?? 0);

                        $tl = TindakLanjut::create([
                            'penugasan_id'         => $penugasan->id,
                            'no_lhp'               => $noLhp,
                            'judul_lhp'            => $judulLhp,
                            'tgl_lhp'              => $tglLhp,
                            'uraian_temuan'        => $uraianTemuan,
                            'rekomendasi'          => $uraianRekomendasi,
                            'nilai_diawasi_rp'     => $nilaiDiawasiLhp,
                            'nilai_rekomendasi_rp' => $nilaiRp,
                            'berkas_dasar_lhp'     => $filePath,
                            'status_tindak_lanjut' => 'belum',
                            'tanggal_target'       => $rData['tanggal_target'] ?? null,
                            'dibuat_oleh'          => $userId,
                        ]);

                        ActivityLog::catat('tindak_lanjut', $tl->id, 'create', null, $tl->toArray());
                        $createdCount++;
                    }
                }
            }
        } else {
            $tl = TindakLanjut::create([
                'penugasan_id'         => $penugasan->id,
                'no_lhp'               => $noLhp,
                'judul_lhp'            => $judulLhp,
                'tgl_lhp'              => $tglLhp,
                'uraian_temuan'        => $request->input('uraian_temuan', 'Temuan Hasil Pengawasan'),
                'rekomendasi'          => $request->input('rekomendasi', 'Rekomendasi Perbaikan'),
                'nilai_rekomendasi_rp' => $this->parseNominalRp($request->input('nilai_rekomendasi_rp', 0)),
                'berkas_dasar_lhp'     => $filePath,
                'status_tindak_lanjut' => 'belum',
                'tanggal_target'       => $request->input('tanggal_target'),
                'dibuat_oleh'          => $userId,
            ]);

            ActivityLog::catat('tindak_lanjut', $tl->id, 'create', null, $tl->toArray());
            $createdCount++;
        }

        $pesanBerkas = $filePath ? " beserta lampiran berkas PDF LHP." : ".";

        return redirect()->route('tindak-lanjut.index')
            ->with('status', "Berhasil menambahkan {$createdCount} catatan temuan & rekomendasi{$pesanBerkas}");
    }

    /**
     * Tambah Rincian Penyetoran Kas Daerah (Pengembalian Finansial).
     */
    public function storeRincianSetor(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $request->validate([
            'nilai_setor_rp'    => ['required'],
            'nama_bank'         => ['nullable', 'string', 'max:100'],
            'no_referensi_ntpn' => ['nullable', 'string', 'max:100'],
            'tgl_setor'         => ['required', 'date'],
            'keterangan'        => ['nullable', 'string'],
        ]);

        $nilaiSetor = $this->parseNominalRp($request->input('nilai_setor_rp'));
        $userId     = auth()->id() ?? (auth()->guard('opd')->id() ?? 1);

        $setoran = RincianPenyetoranTl::create([
            'tindak_lanjut_id'  => $tindakLanjut->id,
            'mata_uang'         => 'IDR',
            'nilai_setor_rp'     => $nilaiSetor,
            'nama_bank'          => $request->input('nama_bank'),
            'no_referensi_ntpn'  => $request->input('no_referensi_ntpn'),
            'tgl_setor'          => $request->input('tgl_setor'),
            'keterangan'         => $request->input('keterangan'),
            'dibuat_oleh'        => $userId,
        ]);

        if ($tindakLanjut->nilai_rekomendasi_rp > 0 && $tindakLanjut->total_setor_rp >= $tindakLanjut->nilai_rekomendasi_rp) {
            $tindakLanjut->update([
                'status_tindak_lanjut'    => 'selesai',
                'tanggal_selesai_aktual' => now()->toDateString(),
            ]);
        } elseif ($tindakLanjut->status_tindak_lanjut === 'belum') {
            $tindakLanjut->update(['status_tindak_lanjut' => 'proses']);
        }

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'create', null, $setoran->toArray());

        return back()->with('status', 'Rincian penyetoran Kas Daerah berhasil dicatat.');
    }

    /**
     * Update data temuan, rekomendasi, LHP metadata, nilai Rp, target waktu, dan lampiran PDF.
     */
    public function update(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $validated = $request->validate([
            'no_lhp'               => ['nullable', 'string', 'max:100'],
            'judul_lhp'            => ['nullable', 'string', 'max:255'],
            'tgl_lhp'              => ['nullable', 'date'],
            'uraian_temuan'        => ['required', 'string'],
            'rekomendasi'          => ['required', 'string'],
            'nilai_diawasi_rp'     => ['nullable'],
            'nilai_rekomendasi_rp' => ['nullable'],
            'tanggal_target'       => ['nullable', 'date'],
            'status_tindak_lanjut' => ['required', 'in:belum,proses,menunggu_verifikasi,selesai,tdt'],
            'berkas_dasar_lhp'     => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $sebelum = $tindakLanjut->toArray();

        if ($request->hasFile('berkas_dasar_lhp')) {
            $file = $request->file('berkas_dasar_lhp');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('berkas_lhp', $fileName, 'public');
            $validated['berkas_dasar_lhp'] = $filePath;
        }

        $validated['nilai_diawasi_rp']     = $this->parseNominalRp($request->input('nilai_diawasi_rp'));
        $validated['nilai_rekomendasi_rp'] = $this->parseNominalRp($request->input('nilai_rekomendasi_rp'));

        if ($validated['status_tindak_lanjut'] === 'selesai' && ! $tindakLanjut->tanggal_selesai_aktual) {
            $validated['tanggal_selesai_aktual'] = now()->toDateString();
        }

        $tindakLanjut->update($validated);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'update', $sebelum, $tindakLanjut->toArray());

        return back()->with('status', 'Data temuan & rekomendasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $validated = $request->validate([
            'status_tindak_lanjut' => ['required', 'in:belum,proses,menunggu_verifikasi,selesai,tdt'],
        ]);

        $sebelum = $tindakLanjut->toArray();
        if ($validated['status_tindak_lanjut'] === 'selesai' && ! $tindakLanjut->tanggal_selesai_aktual) {
            $validated['tanggal_selesai_aktual'] = now()->toDateString();
        }

        $tindakLanjut->update($validated);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'update', $sebelum, $tindakLanjut->toArray());

        return back()->with('status', 'Status tindak lanjut berhasil diperbarui.');
    }

    public function destroy(TindakLanjut $tindakLanjut): RedirectResponse
    {
        $sebelum = $tindakLanjut->toArray();
        $tindakLanjut->delete();

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'delete', $sebelum, null);

        return back()->with('status', 'Catatan temuan & rekomendasi berhasil dihapus.');
    }
}
