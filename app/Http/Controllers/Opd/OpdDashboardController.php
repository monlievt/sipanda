<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\BuktiTindakLanjut;
use App\Models\TindakLanjut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpdDashboardController extends Controller
{
    /**
     * Dashboard OPD: Daftar rekomendasi yang ditujukan ke objek penugasan ini.
     */
    public function index(): View
    {
        $user = auth('opd')->user();
        $objekId = $user->objek_penugasan_id;

        $listRekomendasi = TindakLanjut::with(['penugasan.irban', 'buktiTindakLanjut.arsipDigital'])
            ->whereHas('penugasan.objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $objekId))
            ->orderBy('created_at', 'desc')
            ->get();

        $rekap = [
            'total'     => $listRekomendasi->count(),
            'belum'     => $listRekomendasi->whereIn('status_tindak_lanjut', ['belum', 'proses', 'dikembalikan'])->count(),
            'menunggu'  => $listRekomendasi->where('status_tindak_lanjut', 'menunggu_verifikasi')->count(),
            'selesai'   => $listRekomendasi->where('status_tindak_lanjut', 'selesai')->count(),
        ];

        return view('opd.dashboard', compact('user', 'listRekomendasi', 'rekap'));
    }

    /**
     * Detail Rekomendasi & Riwayat Bukti.
     */
    public function show(TindakLanjut $tindakLanjut): View|RedirectResponse
    {
        $user = auth('opd')->user();
        // Memastikan OPD hanya bisa mengakses rekomendasi miliknya
        $isMilishing = $tindakLanjut->penugasan->objekPenugasan->contains('id', $user->objek_penugasan_id);

        if (! $isMilishing) {
            return redirect()->route('opd.dashboard')->with('error', 'Akses ditolak. Rekomendasi ini ditujukan untuk OPD lain.');
        }

        $tindakLanjut->load(['penugasan', 'buktiTindakLanjut.arsipDigital', 'buktiTindakLanjut.verifikator']);

        return view('opd.detail-rekomendasi', compact('user', 'tindakLanjut'));
    }

    /**
     * OPD Mengunggah Bukti Tindak Lanjut baru.
     */
    public function storeBukti(Request $request, TindakLanjut $tindakLanjut): RedirectResponse
    {
        $user = auth('opd')->user();
        $isMilishing = $tindakLanjut->penugasan->objekPenugasan->contains('id', $user->objek_penugasan_id);

        if (! $isMilishing) {
            return redirect()->route('opd.dashboard')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'catatan_opd' => ['required', 'string'],
            'file'        => ['nullable', 'file', 'mimes:pdf,docx,xlsx,jpg,png', 'max:10240'],
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
            $path = $file->store('arsip', 'local');

            ArsipDigital::create([
                'penugasan_id'           => $tindakLanjut->penugasan_id,
                'tindak_lanjut_id'       => $tindakLanjut->id,
                'bukti_tindak_lanjut_id' => $bukti->id,
                'nama_file'              => $file->getClientOriginalName(),
                'path_file'              => $path,
                'ukuran_kb'              => round($file->getSize() / 1024) . ' KB',
                'mime_type'              => $file->getClientMimeType(),
                'kategori'               => 'Bukti Tindak Lanjut OPD',
                'diunggah_oleh'          => $user->id,
            ]);
        }

        // Update status rekomendasi menjadi 'menunggu_verifikasi'
        $tindakLanjut->update(['status_tindak_lanjut' => 'menunggu_verifikasi']);

        ActivityLog::catat('bukti_tindak_lanjut', $bukti->id, 'create', null, $bukti->toArray());

        return back()->with('status', 'Bukti tindak lanjut berhasil dikirim! Menunggu verifikasi dari Inspektorat.');
    }
}
