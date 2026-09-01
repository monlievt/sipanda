<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\BuktiTindakLanjut;
use App\Models\TindakLanjut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            $fileName = Str::uuid() . '.' . $file->extension();
            $path = $file->storeAs('arsip/' . date('Y/m'), $fileName);

            ArsipDigital::create([
                'penugasan_id'           => $tindakLanjut->penugasan_id,
                'tindak_lanjut_id'       => $tindakLanjut->id,
                'bukti_tindak_lanjut_id' => $bukti->id,
                'nama_file'              => $file->getClientOriginalName(), // nama asli untuk display
                'path_file'              => $path,
                'ukuran_kb'              => round($file->getSize() / 1024) . ' KB',
                'mime_type'              => $file->getMimeType(),
                'kategori'               => 'Bukti Tindak Lanjut OPD',
                'diunggah_oleh'          => $user->id,
            ]);
        }

        // Update status rekomendasi menjadi 'menunggu_verifikasi'
        $tindakLanjut->update(['status_tindak_lanjut' => 'menunggu_verifikasi']);

        ActivityLog::catat('bukti_tindak_lanjut', $bukti->id, 'create', null, $bukti->toArray());

        // Kirim Notifikasi (Email + WhatsApp + In-App) ke Tim Irban / Pembuat Penugasan
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
            // In-App Notification
            \App\Models\Notifikasi::create([
                'user_id'      => $auditor->id,
                'penugasan_id' => $tindakLanjut->penugasan_id,
                'jenis'        => 'bukti_diunggah',
                'judul'        => "Bukti Baru dari {$namaOpd}",
                'pesan'        => "PIC {$namaOpd} mengunggah bukti baru untuk rekomendasi LHP {$tindakLanjut->no_lhp}.",
                'url_target'   => route('tindak-lanjut.show', $tindakLanjut->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            // Email & WhatsApp
            try {
                $auditor->notify(new \App\Notifications\BuktiBaruDiunggahNotification($bukti, $namaOpd));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif bukti baru ke auditor {$auditor->id}: " . $e->getMessage());
            }
        }

        return back()->with('status', 'Bukti tindak lanjut berhasil dikirim! Menunggu verifikasi dari Inspektorat.');
    }
}
