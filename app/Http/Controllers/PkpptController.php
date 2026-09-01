<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\Pkppt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PkpptController extends Controller
{
    /**
     * Tampilkan daftar rencana PKPPT tahunan beserta status versi & alur reviu.
     */
    public function index(Request $request): View
    {
        $tahun   = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');
        $status  = $request->input('status');

        $query = Pkppt::with([
            'irban',
            'pembuatData',
            'penugasan',
            'pkpptInduk',
            'direviuOleh',
            'ditetapkanOleh',
            'riwayatRevisi'
        ])->where('tahun', $tahun);

        if ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        if ($status) {
            $query->where('status', $status);
        } else {
            // Sembunyikan versi lama yang sudah diarsipkan secara default
            $query->where('status', '!=', 'diarsipkan');
        }

        $listPkppt = $query->orderBy('rencana_mulai', 'asc')->get();
        $irbans = Irban::all();
        $tahunList = range(date('Y') + 1, 2022);
        $jenisList = \App\Models\JenisPenugasan::orderBy('kategori')->orderBy('nama')->get();

        return view('pkppt.index', compact('listPkppt', 'irbans', 'tahun', 'irbanId', 'status', 'tahunList', 'jenisList'));
    }

    /**
     * Simpan baris PKPPT baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun'                   => ['required', 'integer', 'min:2020', 'max:2035'],
            'area_pengawasan'         => ['required', 'string', 'max:150'],
            'jenis_pengawasan'        => ['required', 'string', 'max:100'],
            'sasaran'                 => ['nullable', 'string', 'max:150'],
            'rencana_mulai'           => ['required', 'date'],
            'rencana_selesai_laporan' => ['required', 'date', 'after_or_equal:rencana_mulai'],
            'jumlah_laporan_rencana'  => ['required', 'integer', 'min:1'],
            'irban_id'                => ['nullable', 'exists:irbans,id'],
        ]);

        $validated['dibuat_oleh']  = auth()->id();
        $validated['status']       = 'draft';
        $validated['versi_revisi'] = 1;

        $pkppt = Pkppt::create($validated);

        ActivityLog::catat('pkppt', $pkppt->id, 'create', null, $pkppt->toArray());

        return redirect()->route('pkppt.index', ['tahun' => $pkppt->tahun])
            ->with('status', 'Rencana PKPPT berhasil ditambahkan.');
    }

    /**
     * Update baris PKPPT.
     */
    public function update(Request $request, Pkppt $pkppt): RedirectResponse
    {
        $validated = $request->validate([
            'area_pengawasan'         => ['required', 'string', 'max:150'],
            'jenis_pengawasan'        => ['required', 'string', 'max:100'],
            'sasaran'                 => ['nullable', 'string', 'max:150'],
            'rencana_mulai'           => ['required', 'date'],
            'rencana_selesai_laporan' => ['required', 'date', 'after_or_equal:rencana_mulai'],
            'jumlah_laporan_rencana'  => ['required', 'integer', 'min:1'],
            'irban_id'                => ['nullable', 'exists:irbans,id'],
        ]);

        $sebelum = $pkppt->toArray();
        $pkppt->update($validated);

        ActivityLog::catat('pkppt', $pkppt->id, 'update', $sebelum, $pkppt->toArray());

        return redirect()->route('pkppt.index', ['tahun' => $pkppt->tahun])
            ->with('status', 'Rencana PKPPT berhasil diperbarui.');
    }

    /**
     * Buat revisi / penyesuaian PKPPT (Histori Versi).
     * PKPPT versi sebelumnya diarsipkan, dibuatkan versi baru (v+1) berstatus draft.
     */
    public function revisi(Request $request, Pkppt $pkppt): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_revisi'          => ['required', 'string'],
            'area_pengawasan'         => ['required', 'string', 'max:150'],
            'jenis_pengawasan'        => ['required', 'string', 'max:100'],
            'sasaran'                 => ['nullable', 'string', 'max:150'],
            'rencana_mulai'           => ['required', 'date'],
            'rencana_selesai_laporan' => ['required', 'date', 'after_or_equal:rencana_mulai'],
            'jumlah_laporan_rencana'  => ['required', 'integer', 'min:1'],
            'irban_id'                => ['nullable', 'exists:irbans,id'],
        ]);

        $sebelumLama = $pkppt->toArray();

        // 1. Arsipkan versi lama
        $pkppt->update(['status' => 'diarsipkan']);
        ActivityLog::catat('pkppt', $pkppt->id, 'update', $sebelumLama, $pkppt->toArray());

        // 2. Buat versi baru
        $versiBaru = Pkppt::create([
            'tahun'                   => $pkppt->tahun,
            'area_pengawasan'         => $validated['area_pengawasan'],
            'jenis_pengawasan'        => $validated['jenis_pengawasan'],
            'sasaran'                 => $validated['sasaran'],
            'rencana_mulai'           => $validated['rencana_mulai'],
            'rencana_selesai_laporan' => $validated['rencana_selesai_laporan'],
            'jumlah_laporan_rencana'  => $validated['jumlah_laporan_rencana'],
            'irban_id'                => $validated['irban_id'],
            'skor_risiko_acuan'       => $pkppt->skor_risiko_acuan,
            'status'                  => 'draft',
            'versi_revisi'            => $pkppt->versi_revisi + 1,
            'pkppt_induk_id'          => $pkppt->id,
            'catatan_revisi'          => $validated['catatan_revisi'],
            'dibuat_oleh'             => auth()->id(),
        ]);

        ActivityLog::catat('pkppt', $versiBaru->id, 'create', null, $versiBaru->toArray());

        return redirect()->route('pkppt.index', ['tahun' => $versiBaru->tahun])
            ->with('status', "Revisi PKPPT Versi {$versiBaru->versi_revisi} berhasil dibuat (Draft). Silakan diusulkan kembali.");
    }

    /**
     * Hapus baris PKPPT (hanya jika belum ada penugasan terkait).
     */
    public function destroy(Pkppt $pkppt): RedirectResponse
    {
        if ($pkppt->penugasan()->exists()) {
            return back()->with('error', 'PKPPT ini tidak dapat dihapus karena sudah memiliki data penugasan (SPT) terkait.');
        }

        $tahun = $pkppt->tahun;
        $sebelum = $pkppt->toArray();

        $pkppt->delete();

        ActivityLog::catat('pkppt', $pkppt->id, 'delete', $sebelum, null);

        return redirect()->route('pkppt.index', ['tahun' => $tahun])
            ->with('status', 'Rencana PKPPT berhasil dihapus.');
    }
}

