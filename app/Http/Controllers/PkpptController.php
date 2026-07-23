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
     * Tampilkan daftar rencana PKPPT tahunan.
     */
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun', date('Y'));
        $irbanId = $request->input('irban_id');

        $query = Pkppt::with(['irban', 'pembuatData', 'penugasan'])
            ->where('tahun', $tahun);

        if ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        $listPkppt = $query->orderBy('rencana_mulai', 'asc')->get();
        $irbans = Irban::all();
        $tahunList = range(date('Y') + 1, 2022);

        return view('pkppt.index', compact('listPkppt', 'irbans', 'tahun', 'irbanId', 'tahunList'));
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

        $validated['dibuat_oleh'] = auth()->id();
        $validated['status'] = 'draft';

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
