<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\KonsultasiChat;
use App\Models\ObjekPenugasan;
use App\Models\Notifikasi;
use App\Models\User;
use App\Notifications\KonsultasiNotifikasiNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpdKonsultasiController extends Controller
{
    /**
     * Daftar Konsultasi milik OPD
     */
    public function index(Request $request): View
    {
        $opdUser = auth()->guard('opd')->user();
        $objekId = $opdUser->objek_penugasan_id;

        $listKonsultasi = Konsultasi::with(['objekPenugasan', 'irban', 'timUsers'])
            ->where(function ($q) use ($opdUser, $objekId) {
                $q->where('user_id', $opdUser->id);
                if ($objekId) {
                    $q->orWhere('objek_penugasan_id', $objekId);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('opd.konsultasi.index', compact('listKonsultasi', 'opdUser'));
    }

    /**
     * Form Pengajuan Konsultasi Baru
     */
    public function create(): View
    {
        $opdUser = auth()->guard('opd')->user();
        $objek = $opdUser->objekPenugasan;

        $areas = [
            'Pengadaan Barang & Jasa',
            'Keuangan APBD/APBDes',
            'Tata Kelola Desa & Alokasi Dana',
            'Aset & Barang Milik Daerah (BMD)',
            'Akuntabilitas Kinerja (SAKIP/LAKIP)',
            'Sistem Pengendalian Intern (SPIP)',
            'Manajemen Risiko & Kepatuhan',
            'Lainnya',
        ];

        return view('opd.konsultasi.create', compact('opdUser', 'objek', 'areas'));
    }

    /**
     * Simpan Pengajuan Konsultasi OPD
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'area_konsultasi'     => ['required', 'string'],
            'judul_permasalahan'  => ['required', 'string', 'max:255'],
            'uraian_permasalahan' => ['required', 'string'],
            'preferensi_metode'   => ['required', 'in:online,offline'],
            'berkas_pendukung'    => ['nullable', 'file', 'mimes:pdf,zip,jpg,jpeg,png,doc,docx', 'max:10240'],
        ], [
            'area_konsultasi.required'     => 'Area / Topik konsultasi wajib dipilih.',
            'judul_permasalahan.required'  => 'Judul permasalahan wajib diisi.',
            'uraian_permasalahan.required' => 'Uraian permasalahan wajib diisi.',
            'preferensi_metode.required'   => 'Preferensi metode konsultasi wajib dipilih.',
            'berkas_pendukung.max'         => 'Ukuran berkas pendukung maksimal 10 MB.',
        ]);

        $opdUser = auth()->guard('opd')->user();
        $objek   = $opdUser->objekPenugasan;

        // Auto Generate Nomor Tiket: CONS/2026/09/001
        $count = Konsultasi::withTrashed()->whereYear('created_at', date('Y'))->count() + 1;
        $nomorTiket = 'CONS/' . date('Y') . '/' . date('m') . '/' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('berkas_pendukung')) {
            $file = $request->file('berkas_pendukung');
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('berkas_konsultasi/' . date('Y/m'), $fileName);
        }

        $konsultasi = Konsultasi::create([
            'nomor_tiket'          => $nomorTiket,
            'user_id'              => $opdUser->id,
            'objek_penugasan_id'   => $objek?->id,
            'irban_id'             => $objek?->irban_id ?? 1,
            'area_konsultasi'      => $request->area_konsultasi,
            'judul_permasalahan'   => trim($request->judul_permasalahan),
            'uraian_permasalahan'  => trim($request->uraian_permasalahan),
            'berkas_pendukung'     => $filePath,
            'preferensi_metode'    => $request->preferensi_metode,
            'status'               => 'menunggu_disposisi',
            'dibuat_oleh'          => $opdUser->id,
        ]);

        // Simpan pesan awal ke chat room
        KonsultasiChat::create([
            'konsultasi_id' => $konsultasi->id,
            'user_id'       => $opdUser->id,
            'tipe_pengirim' => 'opd',
            'pesan'         => "Permohonan Konsultasi Diajukan.\nJudul: {$konsultasi->judul_permasalahan}\n\n{$konsultasi->uraian_permasalahan}",
            'lampiran_file' => $filePath,
        ]);

        // 🔔 Kirim Notifikasi (Web + WA + Email) ke Inspektur Daerah & Super Admin
        $inspekturs = User::whereHas('roles', fn($r) => $r->whereIn('name', ['inspektur', 'super_admin']))
            ->aktif()
            ->get();

        foreach ($inspekturs as $inspektur) {
            Notifikasi::create([
                'user_id'      => $inspektur->id,
                'jenis'        => 'info_lain',
                'judul'        => 'Permohonan Konsultasi Baru dari OPD',
                'pesan'        => "Permohonan #{$nomorTiket} dari {$objek?->nama}: '{$konsultasi->judul_permasalahan}'. Menunggu disposisi arahan Inspektur.",
                'url_target'   => route('konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $inspektur->notify(new KonsultasiNotifikasiNotification(
                    $konsultasi,
                    'permohonan_baru',
                    $opdUser->nama_display ?? $objek?->nama ?? 'OPD'
                ));
            } catch (\Throwable $e) {
                Log::warning("[SIPANDA Notification] Gagal kirim notif konsultasi baru ke Inspektur {$inspektur->id}: " . $e->getMessage());
            }
        }

        return redirect()->route('opd.konsultasi.show', $konsultasi->id)
            ->with('status', "Permohonan Konsultasi berhasil diajukan dengan Nomor Tiket: {$nomorTiket}. Permohonan telah diteruskan ke Inspektur untuk disposisi ke Irban.");
    }

    /**
     * Detail Konsultasi & Chat Room Sisi OPD
     */
    public function show(Konsultasi $konsultasi): View
    {
        $opdUser = auth()->guard('opd')->user();

        // Otorisasi: Pastikan ini konsultasi milik OPD
        if ($konsultasi->user_id !== $opdUser->id && $konsultasi->objek_penugasan_id !== $opdUser->objek_penugasan_id) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        $konsultasi->load([
            'objekPenugasan',
            'irban',
            'inspekturPemberiDisposisi',
            'tim.user',
            'chats.sender',
        ]);

        return view('opd.konsultasi.show', compact('konsultasi', 'opdUser'));
    }

    /**
     * Kirim Pesan Chat Sisi OPD
     */
    public function sendChat(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        $opdUser = auth()->guard('opd')->user();

        if ($konsultasi->user_id !== $opdUser->id && $konsultasi->objek_penugasan_id !== $opdUser->objek_penugasan_id) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        $request->validate([
            'pesan'         => ['required', 'string'],
            'lampiran_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('lampiran_file')) {
            $file = $request->file('lampiran_file');
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('berkas_konsultasi/' . date('Y/m'), $fileName);
        }

        KonsultasiChat::create([
            'konsultasi_id' => $konsultasi->id,
            'user_id'       => $opdUser->id,
            'tipe_pengirim' => 'opd',
            'pesan'         => $request->pesan,
            'lampiran_file' => $filePath,
        ]);

        // 🔔 Kirim Notifikasi ke Tim Konsultasi APIP & Irban Terkait
        $timUserIds = $konsultasi->timUsers->pluck('id')->toArray();
        if (empty($timUserIds) && $konsultasi->irban_id) {
            $timUserIds = User::where('irban_id', $konsultasi->irban_id)->pluck('id')->toArray();
        }

        $apipRecipients = User::whereIn('id', $timUserIds)->aktif()->get();
        foreach ($apipRecipients as $apip) {
            Notifikasi::create([
                'user_id'      => $apip->id,
                'jenis'        => 'info_lain',
                'judul'        => 'Pesan Konsultasi Baru dari OPD',
                'pesan'        => "Pesan baru dari " . ($opdUser->nama_display ?? 'OPD') . ": \"" . Str::limit($request->pesan, 80) . "\"",
                'url_target'   => route('konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $apip->notify(new KonsultasiNotifikasiNotification(
                    $konsultasi,
                    'chat_baru',
                    $opdUser->nama_display ?? 'OPD',
                    $request->pesan
                ));
            } catch (\Throwable $e) {
                // Log and continue
            }
        }

        return back()->with('status', 'Pesan konsultasi berhasil dikirim.');
    }
}
