<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\BuktiTindakLanjut;
use App\Models\Notifikasi;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\RincianPenyetoranTl;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TindakLanjutController extends Controller
{
    public function index(Request $request): View
    {
        $user   = auth()->user();
        $status = $request->input('status');
        $search = $request->input('search');
        $tahun  = $request->input('tahun');

        $query = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan', 'objekPenugasan', 'buktiTindakLanjut.pengunggah', 'rincianPenyetoran']);

        if (! $user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris'])) {
            $query->whereHas('penugasan', function ($pq) use ($user) {
                $pq->accessibleBy($user);
            });
        }

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
                      $pq->whereYear('tanggal_mulai', $tahun);
                  });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('uraian_temuan', 'like', "%{$search}%")
                  ->orWhere('rekomendasi', 'like', "%{$search}%")
                  ->orWhere('no_lhp', 'like', "%{$search}%")
                  ->orWhere('judul_lhp', 'like', "%{$search}%")
                  ->orWhereHas('objekPenugasan', fn($oq) => $oq->where('nama', 'like', "%{$search}%"));
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

        $penugasanList = Penugasan::accessibleBy($user)
            ->with(['irban', 'objekPenugasan'])
            ->select(['id', 'no_spt', 'uraian_penugasan', 'irban_id'])
            ->orderBy('no_spt', 'desc')
            ->get();

        // Daftar Pilihan Tahun untuk Filter
        $availableYears = range(date('Y') + 1, 2020);

        // 📊 Metrik Ringkasan Banner Atas (Di-cache 60 detik)
        $metrics = \Illuminate\Support\Facades\Cache::remember('tlhp_top_banner_metrics', 60, function () {
            return [
                'totalRekomendasi'      => TindakLanjut::count(),
                'countSesuai'           => TindakLanjut::where('status_tindak_lanjut', 'selesai')->count(),
                'countBelumSesuai'      => TindakLanjut::whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count(),
                'countBelum'            => TindakLanjut::where('status_tindak_lanjut', 'belum')->count(),
                'countTdt'              => TindakLanjut::where('status_tindak_lanjut', 'tdt')->count(),
                'totalNilaiRekomendasi' => (float) TindakLanjut::sum('nilai_rekomendasi_rp'),
                'totalRealisasiSetor'   => (float) RincianPenyetoranTl::sum('nilai_setor_rp'),
            ];
        });

        return view('tindak-lanjut.index', array_merge($metrics, compact(
            'groupedLhp', 'penugasanList', 'status', 'search', 'tahun', 'availableYears'
        )));
    }

    /**
     * Halaman Detail Terpisah (Buka di Tab Baru / Halaman Baru) untuk Dokumen LHP Lengkap (Semua Rekomendasi).
     */
    public function show(TindakLanjut $tindakLanjut): View
    {
        $user = auth()->user();

        // Otorisasi akses dokumen LHP
        if ($tindakLanjut->penugasan && ! $tindakLanjut->penugasan->canAccess($user)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk membuka Dokumen LHP penugasan ini.');
        }
        $tindakLanjut->load([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'stPemantauan',
            'penelaah',
            'irbanPenyetuju',
            'inspekturPenyetuju',
            'tujuanSuratObjek',
            'buktiTindakLanjut.pengunggah',
            'rincianPenyetoran.pembuatData',
            'pembuatData',
        ]);

        // Ambil seluruh rekomendasi yang masuk dalam Dokumen LHP / Penugasan yang sama
        $lhpItems = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'stPemantauan',
            'penelaah',
            'irbanPenyetuju',
            'inspekturPenyetuju',
            'tujuanSuratObjek',
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

        $stPemantauanList = Penugasan::where('status_persetujuan', 'disetujui')
            ->orderBy('no_spt', 'desc')
            ->take(100)
            ->get();

        $objekList = ObjekPenugasan::aktif()->orderBy('nama')->get();

        return view('tindak-lanjut.show', compact(
            'tindakLanjut', 'lhpItems',
            'countSesuai', 'countBelumSesuai', 'countBelum', 'countTdt',
            'totalNilaiTarget', 'totalSetorRp', 'stPemantauanList', 'objekList'
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
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('bukti_tl/' . date('Y/m'), $fileName, 'public');

            ArsipDigital::create([
                'penugasan_id'          => $tindakLanjut->penugasan_id,
                'tindak_lanjut_id'      => $tindakLanjut->id,
                'bukti_tindak_lanjut_id'=> $bukti->id,
                'nama_file'             => $file->getClientOriginalName(), // nama asli disimpan di DB untuk display
                'path_file'             => $filePath,
                'ukuran_kb'             => round($file->getSize() / 1024) . ' KB',
                'mime_type'             => $file->getMimeType(), // getMimeType() baca dari server, bukan client
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

        $penugasan = Penugasan::with('objekPenugasan')->findOrFail($request->penugasan_id);
        $defaultObjekId  = $penugasan->objekPenugasan->count() === 1 ? $penugasan->objekPenugasan->first()->id : null;
        $noLhp           = trim($request->input('no_lhp'));
        $judulLhp        = trim($request->input('judul_lhp'));
        $tglLhp          = $request->input('tgl_lhp');
        $nilaiDiawasiLhp = $this->parseNominalRp($request->input('nilai_diawasi_rp') ?? 0);
        $userId          = auth()->id() ?? (auth()->guard('opd')->id() ?? 1);

        $filePath = null;

        if ($request->hasFile('berkas_dasar_lhp')) {
            $file = $request->file('berkas_dasar_lhp');
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('berkas_lhp/' . date('Y/m'), $fileName, 'public');

            ArsipDigital::create([
                'penugasan_id'  => $penugasan->id,
                'nama_file'     => $file->getClientOriginalName(), // nama asli untuk display
                'path_file'     => $filePath,
                'ukuran_kb'     => round($file->getSize() / 1024) . ' KB',
                'mime_type'     => $file->getMimeType(),
                'kategori'      => 'Laporan Hasil Pengawasan (LHP)',
                'diunggah_oleh' => $userId,
            ]);
        }

        $createdCount = 0;

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $tIndex => $tData) {
                $uraianTemuan = "Temuan " . ($tIndex + 1) . ": " . trim($tData['temuan']);

                // Tentukan Objek/OPD sasaran untuk Temuan ini
                $objekSasaranId = !empty($tData['objek_penugasan_id']) 
                    ? (int) $tData['objek_penugasan_id'] 
                    : ($request->filled('objek_penugasan_id') ? (int) $request->input('objek_penugasan_id') : $defaultObjekId);

                if (isset($tData['rekomendasi']) && is_array($tData['rekomendasi'])) {
                    foreach ($tData['rekomendasi'] as $rIndex => $rData) {
                        $uraianRekomendasi = "Rekomendasi " . ($rIndex + 1) . ": " . trim($rData['uraian']);
                        $nilaiRp           = $this->parseNominalRp($rData['nilai_rekomendasi_rp'] ?? 0);

                        $tl = TindakLanjut::create([
                            'penugasan_id'         => $penugasan->id,
                            'objek_penugasan_id'   => $objekSasaranId,
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
            $singleObjekId = $request->filled('objek_penugasan_id') ? (int) $request->input('objek_penugasan_id') : $defaultObjekId;
            $tl = TindakLanjut::create([
                'penugasan_id'         => $penugasan->id,
                'objek_penugasan_id'   => $singleObjekId,
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

        // 🔄 Skema B: Auto-update status Penugasan menjadi 'selesai' (100%) karena LHP telah terbit
        if ($penugasan->status !== 'selesai') {
            $penugasan->update([
                'status'           => 'selesai',
                'progres_persen'   => 100,
                'keterangan_hasil' => $penugasan->keterangan_hasil ?: "LHP {$noLhp} telah diterbitkan.",
            ]);
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
            'objek_penugasan_id'   => ['nullable', 'exists:objek_penugasan,id'],
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
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('berkas_lhp/' . date('Y/m'), $fileName, 'public');
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

    /**
     * Tim Pemantauan TL mengajukan hasil telaah atas TL yang diinput OPD.
     */
    public function ajukanTelaah(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $validated = $request->validate([
            'st_pemantauan_id'   => ['nullable', 'exists:penugasan,id'],
            'catatan_telaah_tim' => ['required', 'string'],
            'status_rekomendasi' => ['nullable', 'in:belum,proses,menunggu_verifikasi,selesai,tdt'],
        ], [
            'catatan_telaah_tim.required' => 'Catatan hasil telaah tim pemantauan wajib diisi.',
        ]);

        $sebelum = $tindakLanjut->toArray();

        $updateData = [
            'st_pemantauan_id'   => $validated['st_pemantauan_id'] ?? $tindakLanjut->st_pemantauan_id,
            'catatan_telaah_tim' => $validated['catatan_telaah_tim'],
            'status_telaah'      => 'diajukan_irban',
            'ditelaah_oleh'      => auth()->id(),
            'ditelaah_pada'      => now(),
        ];

        if (!empty($validated['status_rekomendasi'])) {
            $updateData['status_tindak_lanjut'] = $validated['status_rekomendasi'];
            if ($validated['status_rekomendasi'] === 'selesai' && !$tindakLanjut->tanggal_selesai_aktual) {
                $updateData['tanggal_selesai_aktual'] = now()->toDateString();
            }
        }

        $tindakLanjut->update($updateData);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'ajukan_telaah', $sebelum, $tindakLanjut->toArray());

        // Kirim Notifikasi ke Irban terkait
        $irbanUser = null;
        if ($tindakLanjut->penugasan?->irban_id) {
            $irbanUser = User::role('irban')->where('irban_id', $tindakLanjut->penugasan->irban_id)->first();
        }
        if (!$irbanUser) {
            $irbanUser = User::role('irban')->first();
        }

        if ($irbanUser) {
            Notifikasi::create([
                'user_id'      => $irbanUser->id,
                'penugasan_id' => $tindakLanjut->penugasan_id,
                'jenis'        => 'info_lain',
                'judul'        => 'Pengajuan Telaah TL: ' . ($tindakLanjut->no_lhp ?? $tindakLanjut->penugasan?->no_spt),
                'pesan'        => "Tim Pemantauan telah mengajukan hasil telaah tindak lanjut LHP untuk diverifikasi oleh Irban.",
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);
        }

        return back()->with('status', 'Hasil telaah tim berhasil disimpan dan diajukan ke Irban untuk diverifikasi.');
    }

    /**
     * Irban memverifikasi telaah tim: Setujui (usulkan ke Inspektur) atau Tolak (kembalikan ke tim).
     */
    public function verifikasiTelaahIrban(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban', 'admin_irban'])) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk memverifikasi telaah ini.');
        }

        $validated = $request->validate([
            'aksi'                      => ['required', 'in:setujui,tolak'],
            'catatan_verifikasi_irban'  => ['nullable', 'string'],
        ]);

        if ($validated['aksi'] === 'tolak' && empty($validated['catatan_verifikasi_irban'])) {
            return back()->with('error', 'Catatan revisi wajib diisi jika Irban menolak/mengembalikan hasil telaah ke Tim.');
        }

        $sebelum = $tindakLanjut->toArray();

        $statusTelaah = ($validated['aksi'] === 'setujui') ? 'diajukan_inspektur' : 'ditolak_irban';

        $tindakLanjut->update([
            'status_telaah'             => $statusTelaah,
            'catatan_verifikasi_irban'  => $validated['catatan_verifikasi_irban'] ?? null,
            'irban_penyetuju_id'        => $user->id,
            'diverifikasi_irban_pada'   => now(),
        ]);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'verifikasi_irban_telaah', $sebelum, $tindakLanjut->toArray());

        // Notifikasi
        if ($validated['aksi'] === 'setujui') {
            $inspektur = User::role('inspektur')->first();
            if ($inspektur) {
                Notifikasi::create([
                    'user_id'      => $inspektur->id,
                    'penugasan_id' => $tindakLanjut->penugasan_id,
                    'jenis'        => 'info_lain',
                    'judul'        => 'Usulan Persetujuan Telaah TL dari Irban',
                    'pesan'        => "Irban telah memverifikasi telaah TL ({$tindakLanjut->no_lhp}) dan mengusulkannya kepada Inspektur untuk disetujui.",
                    'status'       => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
            }
            $pesan = 'Hasil telaah disetujui Irban dan diteruskan kepada Inspektur untuk persetujuan final.';
        } else {
            if ($tindakLanjut->ditelaah_oleh) {
                Notifikasi::create([
                    'user_id'      => $tindakLanjut->ditelaah_oleh,
                    'penugasan_id' => $tindakLanjut->penugasan_id,
                    'jenis'        => 'info_lain',
                    'judul'        => 'Telaah TL Dikembalikan oleh Irban',
                    'pesan'        => "Hasil telaah TL dikembalikan oleh Irban untuk diperbaiki. Catatan: {$validated['catatan_verifikasi_irban']}",
                    'status'       => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
            }
            $pesan = 'Hasil telaah ditolak dan dikembalikan ke Tim Pemantauan untuk diperbaiki.';
        }

        return back()->with('status', $pesan);
    }

    /**
     * Inspektur memberikan persetujuan final atas telaah TL (atau menolak untuk disesuaikan Irban & Tim).
     */
    public function persetujuanTelaahInspektur(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'administrator', 'inspektur'])) {
            return back()->with('error', 'Hanya Inspektur atau Administrator yang dapat menyetujui persetujuan final telaah ini.');
        }

        $validated = $request->validate([
            'aksi'                            => ['required', 'in:setujui,tolak'],
            'catatan_persetujuan_inspektur'   => ['nullable', 'string'],
        ]);

        if ($validated['aksi'] === 'tolak' && empty($validated['catatan_persetujuan_inspektur'])) {
            return back()->with('error', 'Catatan revisi wajib diisi jika Inspektur menolak/mengembalikan usulan telaah.');
        }

        $sebelum = $tindakLanjut->toArray();

        $statusTelaah = ($validated['aksi'] === 'setujui') ? 'disetujui_inspektur' : 'ditolak_inspektur';

        $tindakLanjut->update([
            'status_telaah'                   => $statusTelaah,
            'catatan_persetujuan_inspektur'   => $validated['catatan_persetujuan_inspektur'] ?? null,
            'inspektur_penyetuju_id'          => $user->id,
            'disetujui_inspektur_pada'        => now(),
        ]);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'persetujuan_inspektur_telaah', $sebelum, $tindakLanjut->toArray());

        // Notifikasi ke Irban
        if ($tindakLanjut->irban_penyetuju_id) {
            $statusText = ($validated['aksi'] === 'setujui') ? 'Disetujui Final' : 'Ditolak/Perlu Penyesuaian';
            Notifikasi::create([
                'user_id'      => $tindakLanjut->irban_penyetuju_id,
                'penugasan_id' => $tindakLanjut->penugasan_id,
                'jenis'        => 'info_lain',
                'judul'        => "Persetujuan Akhir Telaah TL: {$statusText}",
                'pesan'        => "Inspektur telah memproses usulan telaah TL ({$tindakLanjut->no_lhp}) dengan status: {$statusText}." . (!empty($validated['catatan_persetujuan_inspektur']) ? " Catatan: {$validated['catatan_persetujuan_inspektur']}" : ""),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);
        }

        $pesan = ($validated['aksi'] === 'setujui')
            ? '✓ Hasil telaah telah Disetujui Final oleh Inspektur! Matriks Tindak Lanjut & Surat Pengantar siap digenerate/dicetak.'
            : 'Hasil telaah ditolak oleh Inspektur dan dikembalikan kepada Irban beserta Tim untuk disesuaikan.';

        return back()->with('status', $pesan);
    }

    /**
     * Simpan Nomor Surat Pengantar, Tanggal, dan Pilihan Instansi/OPD Tujuan (Master Objek).
     */
    public function simpanDataSuratPengantar(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $validated = $request->validate([
            'no_surat_pengantar'     => ['required', 'string', 'max:100'],
            'tgl_surat_pengantar'    => ['required', 'date'],
            'tujuan_surat_objek_id'  => ['required', 'exists:objek_penugasan,id'],
            'sifat_surat'            => ['nullable', 'string', 'max:50'],
            'hal_surat'              => ['nullable', 'string', 'max:255'],
        ], [
            'no_surat_pengantar.required'    => 'Nomor Surat Pengantar wajib diisi.',
            'tgl_surat_pengantar.required'   => 'Tanggal Surat Pengantar wajib diisi.',
            'tujuan_surat_objek_id.required' => 'Pilihan Perangkat Daerah / Instansi tujuan surat wajib dipilih dari daftar master.',
        ]);

        $sebelum = $tindakLanjut->toArray();

        // Update semua record rekomendasi dalam LHP yang sama
        $lhpQuery = TindakLanjut::where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        });

        $lhpQuery->update([
            'no_surat_pengantar'     => $validated['no_surat_pengantar'],
            'tgl_surat_pengantar'    => $validated['tgl_surat_pengantar'],
            'tujuan_surat_objek_id'  => $validated['tujuan_surat_objek_id'],
            'sifat_surat'            => $validated['sifat_surat'] ?? 'Biasa',
            'hal_surat'              => $validated['hal_surat'] ?? 'Penyampaian Matriks Tindak Lanjut Hasil Pengawasan',
        ]);

        ActivityLog::catat('tindak_lanjut', $tindakLanjut->id, 'update_surat_pengantar', $sebelum, $validated);

        return back()->with('status', 'Data Nomor, Tanggal, dan Instansi Tujuan Surat Pengantar berhasil disimpan!');
    }

    /**
     * Cetak Naskah Dinas Resmi Matriks Tindak Lanjut Hasil Pengawasan (PDF / Print View).
     */
    public function cetakMatriksPdf(Request $request, TindakLanjut $tindakLanjut): View|RedirectResponse
    {
        $user = auth()->user();
        if ($tindakLanjut->status_telaah !== 'disetujui_inspektur' && !$user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban'])) {
            return back()->with('error', 'Matriks Tindak Lanjut belum dapat digenerate karena telaah belum disetujui final oleh Inspektur.');
        }

        $lhpItems = TindakLanjut::with([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'stPemantauan',
            'penelaah',
            'irbanPenyetuju',
            'inspekturPenyetuju',
            'tujuanSuratObjek',
            'buktiTindakLanjut',
            'rincianPenyetoran',
        ])->where(function ($q) use ($tindakLanjut) {
            if ($tindakLanjut->no_lhp) {
                $q->where('no_lhp', $tindakLanjut->no_lhp);
            } else {
                $q->where('penugasan_id', $tindakLanjut->penugasan_id);
            }
        })->orderBy('id', 'asc')->get();

        $inspektur = User::role('inspektur')->first();

        return view('tindak-lanjut.cetak-matriks', compact('tindakLanjut', 'lhpItems', 'inspektur'));
    }

    /**
     * Cetak Naskah Dinas Resmi Surat Pengantar Matriks Tindak Lanjut (PDF / Print View).
     */
    public function cetakSuratPengantarPdf(Request $request, TindakLanjut $tindakLanjut): View|RedirectResponse
    {
        $user = auth()->user();
        if ($tindakLanjut->status_telaah !== 'disetujui_inspektur' && !$user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban'])) {
            return back()->with('error', 'Surat Pengantar Matriks belum dapat digenerate karena telaah belum disetujui final oleh Inspektur.');
        }

        $tindakLanjut->load([
            'penugasan.irban',
            'penugasan.objekPenugasan',
            'stPemantauan',
            'penelaah',
            'irbanPenyetuju',
            'inspekturPenyetuju',
            'tujuanSuratObjek',
        ]);

        $inspektur = User::role('inspektur')->first();

        return view('tindak-lanjut.cetak-surat-pengantar', compact('tindakLanjut', 'inspektur'));
    }
}

