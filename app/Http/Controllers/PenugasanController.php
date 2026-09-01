<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\PenugasanTim;
use App\Models\Pkppt;
use App\Models\SumberPenugasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    /**
     * Tampilkan tabel seluruh penugasan (PKPPT & Non-PKPPT) dengan filter.
     */
    public function index(Request $request): View
    {
        // 🔄 Auto-sync status penugasan: jika tanggal_mulai sudah tiba (<= hari ini) & status masih 'belum_berjalan', ubah otomatis ke 'berjalan'
        Penugasan::where('status', 'belum_berjalan')
            ->where('tanggal_mulai', '<=', now()->startOfDay())
            ->update(['status' => 'berjalan']);

        $user = auth()->user();
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');
        $status = $request->input('status');
        $jenisId = $request->input('jenis_penugasan_id');
        $sesuaiPkppt = $request->input('is_sesuai_pkppt');
        $search = $request->input('search');

        $query = Penugasan::with([
            'irban', 'irbans', 'jenisPenugasan', 'sumberPenugasan',
            'objekPenugasan', 'timUsers', 'pkppt', 'pembuatData',
            'penugasanInduk', 'stPerpanjangan'
        ])->tahun($tahun);

        // Auto-scope Irban jika user adalah Irban / Admin Irban
        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $query->irban($user->irban_id);
        } elseif ($irbanId) {
            $query->irban($irbanId);
        }

        if ($status) {
            $query->status($status);
        }

        if ($jenisId) {
            $query->where('jenis_penugasan_id', $jenisId);
        }

        if ($sesuaiPkppt !== null && $sesuaiPkppt !== '') {
            $query->where('is_sesuai_pkppt', (bool) $sesuaiPkppt);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_spt', 'like', "%{$search}%")
                  ->orWhere('uraian_penugasan', 'like', "%{$search}%");
            });
        }

        $listPenugasan = $query->orderBy('tanggal_mulai', 'desc')->paginate(15)->withQueryString();

        $irbans = Irban::all();
        $jenisList = JenisPenugasan::all();
        $tahunList = range(date('Y') + 1, 2022);

        return view('penugasan.index', compact(
            'listPenugasan', 'irbans', 'jenisList', 'tahun', 'irbanId',
            'status', 'jenisId', 'sesuaiPkppt', 'search', 'tahunList'
        ));
    }

    /**
     * Tampilkan detail rincian lengkap isi Surat Tugas Penugasan (SPT).
     */
    public function show(Penugasan $penugasan): View
    {
        $penugasan->load([
            'irban', 'irbans', 'jenisPenugasan', 'sumberPenugasan',
            'objekPenugasan', 'tim.user', 'pkppt', 'pembuatData',
            'penugasanInduk', 'stPerpanjangan', 'tindakLanjut'
        ]);

        return view('penugasan.show', compact('penugasan'));
    }

    /**
     * Cetak Naskah Dinas Surat Perintah Tugas (SPT) Resmi Format Pemkab Trenggalek.
     */
    public function cetak(Penugasan $penugasan): View
    {
        $penugasan->load([
            'irban', 'irbans', 'jenisPenugasan', 'sumberPenugasan',
            'objekPenugasan', 'tim.user', 'pkppt', 'pembuatData',
            'penugasanInduk'
        ]);

        $inspektur = User::role('inspektur')->first();

        return view('penugasan.cetak-spt', compact('penugasan', 'inspektur'));
    }

    /**
     * Tampilkan form Input Penugasan baru.
     */
    public function create(): View
    {
        $user = auth()->user();

        $objekList = ObjekPenugasan::aktif()->orderBy('nama')->get();
        $jenisList = JenisPenugasan::orderBy('kategori')->orderBy('nama')->get();
        $sumberList = SumberPenugasan::all();
        $irbans = Irban::all();
        $usersList = User::aktif()->internal()->orderBy('nama')->get();

        $pkpptList = Pkppt::tahun(date('Y'))->orderBy('area_pengawasan')->get();

        // Daftar ST Induk yang bisa diperpanjang
        $parentStList = Penugasan::orderBy('no_spt', 'desc')->take(50)->get();

        return view('penugasan.create', compact(
            'objekList', 'jenisList', 'sumberList', 'irbans', 'usersList', 'pkpptList', 'parentStList'
        ));
    }

    /**
     * Simpan penugasan baru (+ Multi-Irban + ST Perpanjangan + tim + objek).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'no_spt'              => ['required', 'string', 'max:60', 'unique:penugasan,no_spt'],
            'uraian_penugasan'    => ['required', 'string'],
            'sumber_penugasan_id' => ['required', 'exists:sumber_penugasan,id'],
            'jenis_penugasan_id'  => ['required', 'exists:jenis_penugasan,id'],
            'tanggal_mulai'       => ['required', 'date'],
            'tanggal_selesai'     => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_sesuai_pkppt'     => ['nullable', 'boolean'],
            'pkppt_id'            => ['nullable', 'exists:pkppt,id'],
            'is_perpanjangan'     => ['required', 'boolean'],
            'penugasan_induk_id'  => ['nullable', 'required_if:is_perpanjangan,1', 'exists:penugasan,id'],
            // Multi-Irban Selection
            'irban_ids'           => ['required', 'array', 'min:1'],
            'irban_ids.*'         => ['exists:irbans,id'],
            // Objek Multi-Select
            'objek_ids'           => ['required', 'array', 'min:1'],
            'objek_ids.*'         => ['exists:objek_penugasan,id'],
            // Tim multi-select
            'tim_wakil_pj'        => ['required', 'array', 'min:1'],
            'tim_wakil_pj.*'      => ['exists:users,id'],
            'tim_daltek'          => ['required', 'array', 'min:1'],
            'tim_daltek.*'        => ['exists:users,id'],
            'tim_ketua'           => ['required', 'array', 'min:1'],
            'tim_ketua.*'         => ['exists:users,id'],
            'tim_anggota'         => ['required', 'array', 'min:1'],
            'tim_anggota.*'       => ['exists:users,id'],
        ], [
            'no_spt.required'               => 'Nomor SPT wajib diisi.',
            'no_spt.unique'                 => 'Nomor SPT ini sudah terdaftar. Mohon gunakan nomor yang lain.',
            'uraian_penugasan.required'     => 'Uraian penugasan wajib diisi.',
            'jenis_penugasan_id.required'   => 'Jenis penugasan wajib dipilih.',
            'sumber_penugasan_id.required'  => 'Sumber penugasan wajib dipilih.',
            'tanggal_mulai.required'        => 'Tanggal mulai penugasan wajib diisi.',
            'tanggal_selesai.required'      => 'Tanggal selesai penugasan wajib diisi.',
            'tanggal_selesai.after_or_equal'=> 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'penugasan_induk_id.required_if'=> 'Karena ini adalah ST Perpanjangan, Anda wajib memilih Surat Tugas Indikator (ST Induk) yang diperpanjang.',
            'irban_ids.required'            => 'Minimal 1 Irban Penanggung Jawab wajib dipilih.',
            'objek_ids.required'            => 'Minimal 1 Objek Penugasan (OPD/Kecamatan) wajib dipilih.',
            'tim_wakil_pj.required'         => 'Wakil Penanggung Jawab wajib dipilih.',
            'tim_daltek.required'           => 'Pengendali Teknis wajib dipilih.',
            'tim_ketua.required'            => 'Ketua Tim wajib dipilih.',
            'tim_anggota.required'          => 'Anggota Tim wajib dipilih.',
        ]);

        $primaryIrbanId = $validated['irban_ids'][0];

        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            if (! in_array($user->irban_id, $validated['irban_ids'])) {
                $validated['irban_ids'][] = $user->irban_id;
            }
            $primaryIrbanId = $user->irban_id;
        }

        $tglMulai = \Carbon\Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $statusOtomatis = now()->startOfDay()->gte($tglMulai) ? 'berjalan' : 'belum_berjalan';

        // Jika ST Perpanjangan, otomatis warisi relasi PKPPT dari ST Induk
        if ($validated['is_perpanjangan'] && !empty($validated['penugasan_induk_id'])) {
            $parentSt = Penugasan::find($validated['penugasan_induk_id']);
            $isSesuaiPkppt = $parentSt ? (bool) $parentSt->is_sesuai_pkppt : false;
            $pkpptId = $parentSt ? $parentSt->pkppt_id : null;
        } else {
            $isSesuaiPkppt = (bool) ($validated['is_sesuai_pkppt'] ?? false);
            $pkpptId = $isSesuaiPkppt ? ($validated['pkppt_id'] ?? null) : null;
        }

        $penugasan = Penugasan::create([
            'no_spt'              => $validated['no_spt'],
            'uraian_penugasan'    => $validated['uraian_penugasan'],
            'sumber_penugasan_id' => $validated['sumber_penugasan_id'],
            'jenis_penugasan_id'  => $validated['jenis_penugasan_id'],
            'tanggal_mulai'       => $validated['tanggal_mulai'],
            'tanggal_selesai'     => $validated['tanggal_selesai'],
            'status'              => $statusOtomatis,
            'progres_persen'      => 0,
            'is_sesuai_pkppt'     => $isSesuaiPkppt,
            'pkppt_id'            => $pkpptId,
            'penugasan_induk_id'  => $validated['is_perpanjangan'] ? $validated['penugasan_induk_id'] : null,
            'irban_id'            => $primaryIrbanId,
            'dibuat_oleh'         => $user->id,
        ]);

        $penugasan->irbans()->sync($validated['irban_ids']);
        $penugasan->objekPenugasan()->sync($validated['objek_ids']);

        $timData = [];
        $peranMap = [
            'tim_wakil_pj' => 'wakil_penanggung_jawab',
            'tim_daltek'   => 'pengendali_teknis',
            'tim_ketua'    => 'ketua_tim',
            'tim_anggota'  => 'anggota_tim',
        ];

        foreach ($peranMap as $field => $peran) {
            foreach ($validated[$field] as $userId) {
                $timData[] = [
                    'penugasan_id' => $penugasan->id,
                    'user_id'      => $userId,
                    'peran'        => $peran,
                ];
            }
        }
        PenugasanTim::insertOrIgnore($timData);

        ActivityLog::catat('penugasan', $penugasan->id, 'create', null, $penugasan->toArray());

        // Kirim Notifikasi (Email + WhatsApp + In-App) ke Seluruh Anggota Tim
        $penugasan->load(['objekPenugasan', 'tim.user']);
        foreach ($penugasan->tim as $member) {
            if ($member->user) {
                $peranTitle = match($member->peran) {
                    'wakil_penanggung_jawab' => 'Wakil Penanggung Jawab',
                    'pengendali_teknis'      => 'Pengendali Teknis',
                    'ketua_tim'              => 'Ketua Tim',
                    default                  => 'Anggota Tim',
                };

                // In-App Notification
                \App\Models\Notifikasi::create([
                    'user_id'      => $member->user_id,
                    'penugasan_id' => $penugasan->id,
                    'jenis'        => 'info_lain',
                    'judul'        => 'Penerbitan SPT Baru: ' . $penugasan->no_spt,
                    'pesan'        => "Anda ditugaskan sebagai {$peranTitle} pada penugasan {$penugasan->no_spt} ({$penugasan->uraian_penugasan}).",
                    'status'       => 'terkirim',
                    'dikirim_pada' => now(),
                ]);

                // Email & WhatsApp Notification
                try {
                    $member->user->notify(new \App\Notifications\PenugasanBaruNotification($penugasan, $peranTitle));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif SPT baru ke user {$member->user_id}: " . $e->getMessage());
                }
            }
        }

        $pesanPerpanjangan = $penugasan->penugasan_induk_id ? " (Merupakan ST Perpanjangan dari No. SPT {$penugasan->penugasanInduk?->no_spt})" : "";

        return redirect()->route('penugasan.index')
            ->with('status', "Penugasan dengan No. SPT {$penugasan->no_spt} berhasil ditambahkan!{$pesanPerpanjangan}");
    }

    /**
     * Tampilkan form Edit Penugasan (SPT).
     */
    public function edit(Penugasan $penugasan): View
    {
        $penugasan->load(['irbans', 'objekPenugasan', 'tim']);

        $objekList = ObjekPenugasan::aktif()->orderBy('nama')->get();
        $jenisList = JenisPenugasan::orderBy('kategori')->orderBy('nama')->get();
        $sumberList = SumberPenugasan::all();
        $irbans = Irban::all();
        $usersList = User::aktif()->internal()->orderBy('nama')->get();
        $pkpptList = Pkppt::tahun($penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y'))->orderBy('area_pengawasan')->get();
        $parentStList = Penugasan::where('id', '!=', $penugasan->id)->orderBy('no_spt', 'desc')->take(50)->get();

        $selectedIrbanIds = $penugasan->irbans->pluck('id')->toArray();
        if (empty($selectedIrbanIds) && $penugasan->irban_id) {
            $selectedIrbanIds = [$penugasan->irban_id];
        }

        $selectedObjekIds = $penugasan->objekPenugasan->pluck('id')->toArray();

        $selectedTim = [
            'tim_wakil_pj' => $penugasan->tim->where('peran', 'wakil_penanggung_jawab')->pluck('user_id')->toArray(),
            'tim_daltek'   => $penugasan->tim->where('peran', 'pengendali_teknis')->pluck('user_id')->toArray(),
            'tim_ketua'    => $penugasan->tim->where('peran', 'ketua_tim')->pluck('user_id')->toArray(),
            'tim_anggota'  => $penugasan->tim->where('peran', 'anggota_tim')->pluck('user_id')->toArray(),
        ];

        return view('penugasan.edit', compact(
            'penugasan', 'objekList', 'jenisList', 'sumberList', 'irbans',
            'usersList', 'pkpptList', 'parentStList', 'selectedIrbanIds',
            'selectedObjekIds', 'selectedTim'
        ));
    }

    /**
     * Perbarui data penugasan (SPT) beserta susunan tim.
     */
    public function update(Request $request, Penugasan $penugasan): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'no_spt'              => ['required', 'string', 'max:60', 'unique:penugasan,no_spt,' . $penugasan->id],
            'uraian_penugasan'    => ['required', 'string'],
            'sumber_penugasan_id' => ['required', 'exists:sumber_penugasan,id'],
            'jenis_penugasan_id'  => ['required', 'exists:jenis_penugasan,id'],
            'tanggal_mulai'       => ['required', 'date'],
            'tanggal_selesai'     => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'              => ['required', 'in:belum_berjalan,berjalan,selesai'],
            'is_sesuai_pkppt'     => ['nullable', 'boolean'],
            'pkppt_id'            => ['nullable', 'exists:pkppt,id'],
            'is_perpanjangan'     => ['required', 'boolean'],
            'penugasan_induk_id'  => ['nullable', 'required_if:is_perpanjangan,1', 'exists:penugasan,id'],
            'irban_ids'           => ['required', 'array', 'min:1'],
            'irban_ids.*'         => ['exists:irbans,id'],
            'objek_ids'           => ['required', 'array', 'min:1'],
            'objek_ids.*'         => ['exists:objek_penugasan,id'],
            'tim_wakil_pj'        => ['required', 'array', 'min:1'],
            'tim_wakil_pj.*'      => ['exists:users,id'],
            'tim_daltek'          => ['required', 'array', 'min:1'],
            'tim_daltek.*'        => ['exists:users,id'],
            'tim_ketua'           => ['required', 'array', 'min:1'],
            'tim_ketua.*'         => ['exists:users,id'],
            'tim_anggota'         => ['required', 'array', 'min:1'],
            'tim_anggota.*'       => ['exists:users,id'],
        ]);

        $sebelum = $penugasan->toArray();
        $primaryIrbanId = $validated['irban_ids'][0];

        // Jika ST Perpanjangan, otomatis warisi relasi PKPPT dari ST Induk
        if ($validated['is_perpanjangan'] && !empty($validated['penugasan_induk_id'])) {
            $parentSt = Penugasan::find($validated['penugasan_induk_id']);
            $isSesuaiPkppt = $parentSt ? (bool) $parentSt->is_sesuai_pkppt : false;
            $pkpptId = $parentSt ? $parentSt->pkppt_id : null;
        } else {
            $isSesuaiPkppt = (bool) ($validated['is_sesuai_pkppt'] ?? false);
            $pkpptId = $isSesuaiPkppt ? ($validated['pkppt_id'] ?? null) : null;
        }

        $penugasan->update([
            'no_spt'              => $validated['no_spt'],
            'uraian_penugasan'    => $validated['uraian_penugasan'],
            'sumber_penugasan_id' => $validated['sumber_penugasan_id'],
            'jenis_penugasan_id'  => $validated['jenis_penugasan_id'],
            'tanggal_mulai'       => $validated['tanggal_mulai'],
            'tanggal_selesai'     => $validated['tanggal_selesai'],
            'status'              => $validated['status'],
            'is_sesuai_pkppt'     => $isSesuaiPkppt,
            'pkppt_id'            => $pkpptId,
            'penugasan_induk_id'  => $validated['is_perpanjangan'] ? $validated['penugasan_induk_id'] : null,
            'irban_id'            => $primaryIrbanId,
            'diperbarui_oleh'     => $user->id,
        ]);

        $penugasan->irbans()->sync($validated['irban_ids']);
        $penugasan->objekPenugasan()->sync($validated['objek_ids']);

        // Hapus & re-insert susunan tim
        PenugasanTim::where('penugasan_id', $penugasan->id)->delete();

        $timData = [];
        $peranMap = [
            'tim_wakil_pj' => 'wakil_penanggung_jawab',
            'tim_daltek'   => 'pengendali_teknis',
            'tim_ketua'    => 'ketua_tim',
            'tim_anggota'  => 'anggota_tim',
        ];

        foreach ($peranMap as $field => $peran) {
            foreach ($validated[$field] as $userId) {
                $timData[] = [
                    'penugasan_id' => $penugasan->id,
                    'user_id'      => $userId,
                    'peran'        => $peran,
                ];
            }
        }
        PenugasanTim::insertOrIgnore($timData);

        ActivityLog::catat('penugasan', $penugasan->id, 'update', $sebelum, $penugasan->toArray());

        return redirect()->route('penugasan.show', $penugasan->id)
            ->with('status', "Surat Tugas No. SPT {$penugasan->no_spt} berhasil diperbarui.");
    }

    /**
     * Hapus penugasan.
     */
    public function destroy(Penugasan $penugasan): RedirectResponse
    {
        $noSpt = $penugasan->no_spt;
        $sebelum = $penugasan->toArray();

        $penugasan->delete();

        ActivityLog::catat('penugasan', $penugasan->id, 'delete', $sebelum, null);

        return redirect()->route('penugasan.index')
            ->with('status', "Penugasan dengan No. SPT {$noSpt} berhasil dihapus.");
    }

    /**
     * Update cepat status, progres %, dan keterangan hasil penugasan.
     */
    public function updateStatus(Request $request, Penugasan $penugasan): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => ['required', 'in:belum_berjalan,berjalan,selesai'],
            'progres_persen'   => ['required', 'integer', 'min:0', 'max:100'],
            'keterangan_hasil' => ['nullable', 'required_if:status,selesai', 'string'],
        ]);

        $sebelum = $penugasan->toArray();

        $validated['diperbarui_oleh'] = auth()->id();
        $penugasan->update($validated);

        ActivityLog::catat('penugasan', $penugasan->id, 'update', $sebelum, $penugasan->toArray());

        return back()->with('status', "Status penugasan {$penugasan->no_spt} berhasil diperbarui.");
    }
}
