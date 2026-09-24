<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BuktiTindakLanjut;
use App\Models\TindakLanjut;
use App\Notifications\BuktiVerifikasiNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * Proses Evaluasi Bukti Tindak Lanjut (Menyimpan telaah & catatan evaluasi tim pemeriksa).
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
            'diverifikasi_oleh'  => auth()->id() ?? 1,
            'diverifikasi_pada'  => now(),
        ]);

        $tl = $bukti->tindakLanjut;

        // Catat usulan status telaah tim tanpa langsung meresmikan ke OPD sebelum persetujuan Inspektur
        $usulanStatus = match($validated['status_verifikasi']) {
            'diterima' => 'selesai',
            'tdt'      => 'tdt',
            default    => 'proses',
        };

        $tl->update([
            'status_rekomendasi_usulan' => $usulanStatus,
            'hasil_telaah_tim'          => $validated['catatan_verifikasi'] ?: $tl->hasil_telaah_tim,
            'telaah_oleh'               => auth()->id(),
            'telaah_pada'               => now(),
        ]);

        ActivityLog::catat('bukti_tindak_lanjut', $bukti->id, 'update', $sebelum, $bukti->toArray());

        $pesan = match($validated['status_verifikasi']) {
            'diterima' => '✓ Evaluasi bukti: DITERIMA (Usulan: SESUAI). Hasil tersimpan sebagai bahan telaah tim untuk diajukan ke Irban & Inspektur.',
            'tdt'      => '✓ Evaluasi bukti: TIDAK DAPAT DITINDAKLANJUTI (TDT). Hasil tersimpan sebagai usulan telaah tim.',
            default    => '✓ Evaluasi bukti: MEMERLUKAN PERBAIKAN OPD. Catatan evaluasi tersimpan sebagai usulan telaah tim.',
        };

        return back()->with('status', $pesan);
    }
}
