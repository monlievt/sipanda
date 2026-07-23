<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BuktiTindakLanjut;
use App\Models\TindakLanjut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiBuktiController extends Controller
{
    /**
     * Tampilkan daftar pengajuan bukti tindak lanjut dari OPD yang menunggu verifikasi.
     */
    public function index(): View
    {
        $user = auth()->user();

        $query = BuktiTindakLanjut::with([
            'tindakLanjut.penugasan.irban',
            'tindakLanjut.penugasan.objekPenugasan',
            'pengunggah',
            'arsipDigital'
        ]);

        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $query->whereHas('tindakLanjut.penugasan', fn($q) => $q->where('irban_id', $user->irban_id));
        }

        $listBukti = $query->orderBy('status_verifikasi', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tindak-lanjut.verifikasi-bukti', compact('listBukti'));
    }

    /**
     * Proses Verifikasi (Terima = Selesai/Sesuai, Tolak = Belum Sesuai dengan Catatan Revisi).
     */
    public function verifikasi(Request $request, BuktiTindakLanjut $bukti): RedirectResponse
    {
        $validated = $request->validate([
            'status_verifikasi'  => ['required', 'in:diterima,ditolak,tdt'],
            'catatan_verifikasi' => ['required_if:status_verifikasi,ditolak', 'nullable', 'string'],
        ]);

        $sebelum = $bukti->toArray();

        $bukti->update([
            'status_verifikasi'  => $validated['status_verifikasi'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
            'diverifikasi_oleh' => auth()->id() ?? 1,
            'diverifikasi_pada' => now(),
        ]);

        $tl = $bukti->tindakLanjut;

        if ($validated['status_verifikasi'] === 'diterima') {
            $tl->update([
                'status_tindak_lanjut'   => 'selesai',
                'tanggal_selesai_aktual' => now()->toDateString(),
            ]);
            $pesan = 'Hasil evaluasi: DITERIMA. Status rekomendasi kini SESUAI.';
        } elseif ($validated['status_verifikasi'] === 'tdt') {
            $tl->update([
                'status_tindak_lanjut' => 'tdt',
            ]);
            $pesan = 'Hasil evaluasi: TIDAK DAPAT DITINDAKLANJUTI (TDT). Status diatur ke TDT.';
        } else {
            $tl->update([
                'status_tindak_lanjut' => 'proses',
            ]);
            $pesan = 'Hasil evaluasi: MEMERLUKAN REVISI. Status diatur ke BELUM SESUAI agar OPD dapat melakukan perbaikan & penambahan tindak lanjut.';
        }

        ActivityLog::catat('bukti_tindak_lanjut', $bukti->id, 'update', $sebelum, $bukti->toArray());

        return back()->with('status', $pesan);
    }
}
