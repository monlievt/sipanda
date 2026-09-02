<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\Konsultasi;
use App\Models\KonsultasiChat;
use App\Models\KonsultasiTim;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KonsultasiController extends Controller
{
    /**
     * Dashboard / List Layanan Konsultasi & Advisory APIP (Internal)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->input('status');
        $irbanId = $request->input('irban_id');
        $area = $request->input('area_konsultasi');
        $search = $request->input('search');

        $query = Konsultasi::with(['pemohon', 'objekPenugasan', 'irban', 'timUsers']);

        if ($user->hasRole(['irban', 'admin_irban']) && $user->irban_id) {
            $query->where('irban_id', $user->irban_id);
        } elseif ($irbanId) {
            $query->where('irban_id', $irbanId);
        }

        if ($status) {
            if ($status === 'menunggu_disposisi') {
                $query->whereIn('status', ['menunggu_disposisi', 'menunggu_disposisi_inspektur', 'menunggu_penugasan_tim']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($area) {
            $query->where('area_konsultasi', $area);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                  ->orWhere('judul_permasalahan', 'like', "%{$search}%")
                  ->orWhere('uraian_permasalahan', 'like', "%{$search}%");
            });
        }

        $listKonsultasi = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $irbans = Irban::all();
        $usersList = User::internal()->aktif()->get();

        $countMenunggu = Konsultasi::whereIn('status', ['menunggu_disposisi', 'menunggu_disposisi_inspektur', 'menunggu_penugasan_tim'])->count();
        $countBerjalan = Konsultasi::where('status', 'berjalan')->count();
        $countSelesai  = Konsultasi::where('status', 'selesai')->count();

        return view('konsultasi.index', compact(
            'listKonsultasi', 'irbans', 'usersList', 'status', 'irbanId', 'area', 'search',
            'countMenunggu', 'countBerjalan', 'countSelesai'
        ));
    }

    /**
     * Detail Rincian Konsultasi, Chat Room, Disposisi & Berita Acara
     */
    public function show(Konsultasi $konsultasi): View
    {
        $konsultasi->load([
            'pemohon.objekPenugasan',
            'objekPenugasan',
            'irban',
            'tim.user',
            'chats.sender',
        ]);

        $usersList = User::internal()->aktif()->get();
        $irbans = Irban::all();

        $selectedTim = [
            'penanggung_jawab'  => $konsultasi->tim->where('peran', 'penanggung_jawab')->pluck('user_id')->toArray(),
            'pengendali_teknis' => $konsultasi->tim->where('peran', 'pengendali_teknis')->pluck('user_id')->toArray(),
            'ketua_tim'         => $konsultasi->tim->where('peran', 'ketua_tim')->pluck('user_id')->toArray(),
            'anggota_tim'       => $konsultasi->tim->where('peran', 'anggota_tim')->pluck('user_id')->toArray(),
        ];

        return view('konsultasi.show', compact('konsultasi', 'usersList', 'irbans', 'selectedTim'));
    }

    /**
     * Disposisi Tingkat 1: Inspektur mendisposisikan konsultasi ke Irban tujuan & arahan pimpinan.
     */
    public function disposisiInspektur(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        $request->validate([
            'irban_id'                    => ['required', 'exists:irbans,id'],
            'catatan_disposisi_inspektur' => ['required', 'string', 'max:1000'],
        ], [
            'irban_id.required'                    => 'Irban tujuan disposisi wajib dipilih.',
            'catatan_disposisi_inspektur.required' => 'Catatan / arahan disposisi Inspektur wajib diisi.',
        ]);

        $sebelum = $konsultasi->toArray();

        $konsultasi->update([
            'irban_id'                    => $request->irban_id,
            'catatan_disposisi_inspektur' => trim($request->catatan_disposisi_inspektur),
            'disposisi_inspektur_oleh'    => auth()->id(),
            'disposisi_inspektur_pada'    => now(),
            'status'                      => 'menunggu_penugasan_tim',
        ]);

        ActivityLog::catat('konsultasi', $konsultasi->id, 'update', $sebelum, $konsultasi->toArray());

        $irban = Irban::find($request->irban_id);

        // Kirim Notifikasi (Web + WA + Email) ke Pejabat Irban tujuan
        $irbanUsers = User::where('irban_id', $irban->id)
            ->whereHas('roles', fn($r) => $r->whereIn('name', ['irban', 'admin_irban']))
            ->aktif()
            ->get();

        if ($irbanUsers->isEmpty()) {
            $irbanUsers = User::where('irban_id', $irban->id)->aktif()->get();
        }

        foreach ($irbanUsers as $uIrban) {
            \App\Models\Notifikasi::create([
                'user_id'      => $uIrban->id,
                'jenis'        => 'info_lain',
                'judul'        => 'Disposisi Konsultasi dari Inspektur',
                'pesan'        => "Konsultasi '{$konsultasi->topik}' didisposisikan kepada Irban Anda. Arahan: {$request->catatan_disposisi_inspektur}",
                'url_target'   => route('konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $uIrban->notify(new \App\Notifications\KonsultasiNotifikasiNotification(
                    $konsultasi,
                    'disposisi_inspektur',
                    auth()->user()->nama ?? 'Inspektur',
                    $request->catatan_disposisi_inspektur
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif disposisi inspektur ke {$uIrban->id}: " . $e->getMessage());
            }
        }

        // Notifikasi update ke pemohon OPD
        if ($konsultasi->pemohon) {
            \App\Models\Notifikasi::create([
                'user_id'      => $konsultasi->user_id,
                'jenis'        => 'info_lain',
                'judul'        => 'Konsultasi Didisposisikan oleh Inspektur',
                'pesan'        => "Permohonan konsultasi #{$konsultasi->nomor_tiket} telah didisposisikan oleh Inspektur kepada {$irban->nama_irban}.",
                'url_target'   => route('opd.konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);
        }

        return back()->with('status', "Konsultasi berhasil didisposisikan kepada {$irban->nama_irban}. Notifikasi telah dikirimkan ke Irban terkait.");
    }

    /**
     * Disposisi Tingkat 2: Irban Menunjuk Tim Konsultasi APIP & Persetujuan Metode (Online / Tatap Muka)
     */
    public function disposisi(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        $request->validate([
            'metode_disetujui'    => ['required', 'in:online,offline'],
            'tanggal_tatap_muka'  => ['nullable', 'required_if:metode_disetujui,offline', 'date'],
            'lokasi_tatap_muka'   => ['nullable', 'required_if:metode_disetujui,offline', 'string', 'max:150'],
            'tim_pj'              => ['required', 'array', 'min:1'],
            'tim_daltek'          => ['required', 'array', 'min:1'],
            'tim_ketua'           => ['required', 'array', 'min:1'],
            'tim_anggota'         => ['required', 'array', 'min:1'],
        ], [
            'metode_disetujui.required' => 'Metode konsultasi wajib disetujui.',
            'tim_pj.required'           => 'Penanggung Jawab wajib dipilih.',
            'tim_daltek.required'       => 'Pengendali Teknis wajib dipilih.',
            'tim_ketua.required'        => 'Ketua Tim wajib dipilih.',
            'tim_anggota.required'      => 'Anggota Tim wajib dipilih.',
        ]);

        $sebelum = $konsultasi->toArray();

        $konsultasi->update([
            'metode_disetujui'   => $request->metode_disetujui,
            'tanggal_tatap_muka' => $request->tanggal_tatap_muka,
            'lokasi_tatap_muka'  => $request->lokasi_tatap_muka,
            'status'             => 'berjalan',
        ]);

        // Re-sync Tim Konsultasi
        KonsultasiTim::where('konsultasi_id', $konsultasi->id)->delete();

        $roles = [
            'penanggung_jawab'  => $request->input('tim_pj', []),
            'pengendali_teknis' => $request->input('tim_daltek', []),
            'ketua_tim'         => $request->input('tim_ketua', []),
            'anggota_tim'       => $request->input('tim_anggota', []),
        ];

        foreach ($roles as $peran => $userIds) {
            foreach (array_unique($userIds) as $uId) {
                KonsultasiTim::create([
                    'konsultasi_id' => $konsultasi->id,
                    'user_id'       => $uId,
                    'peran'         => $peran,
                ]);
            }
        }

        ActivityLog::catat('konsultasi', $konsultasi->id, 'update', $sebelum, $konsultasi->toArray());

        // Kirim Notifikasi (Email + WA + In-App) ke Tim yang Didisposisikan
        $timUserIds = array_unique(array_merge(
            $request->input('tim_pj', []),
            $request->input('tim_daltek', []),
            $request->input('tim_ketua', []),
            $request->input('tim_anggota', [])
        ));

        $auditors = User::whereIn('id', $timUserIds)->aktif()->get();
        foreach ($auditors as $auditor) {
            \App\Models\Notifikasi::create([
                'user_id'      => $auditor->id,
                'jenis'        => 'info_lain',
                'judul'        => 'Penugasan Tim Konsultasi APIP',
                'pesan'        => "Konsultasi topik '{$konsultasi->topik}' ditugaskan kepada Anda.",
                'url_target'   => route('konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $auditor->notify(new \App\Notifications\KonsultasiNotifikasiNotification($konsultasi, 'disposisi_irban'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif penugasan tim ke {$auditor->id}: " . $e->getMessage());
            }
        }

        // Kirim Notifikasi ke Pemohon OPD bahwa Tim telah siap
        if ($konsultasi->pemohon) {
            \App\Models\Notifikasi::create([
                'user_id'      => $konsultasi->user_id,
                'jenis'        => 'info_lain',
                'judul'        => 'Tim Konsultasi APIP Telah Siap',
                'pesan'        => "Permohonan konsultasi #{$konsultasi->nomor_tiket} telah ditanggapi. Tim APIP telah siap melayani.",
                'url_target'   => route('opd.konsultasi.show', $konsultasi->id),
                'status'       => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            try {
                $konsultasi->pemohon->notify(new \App\Notifications\KonsultasiNotifikasiNotification($konsultasi, 'disposisi_irban'));
            } catch (\Throwable $e) {
                // Ignore error
            }
        }

        return back()->with('status', 'Disposisi Tim Konsultasi APIP & Metode Konsultasi berhasil ditetapkan.');
    }

    /**
     * Kirim Pesan / Balasan Chat Online dari APIP
     */
    public function sendChat(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        $request->validate([
            'pesan'         => ['required', 'string'],
            'lampiran_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip', 'max:10240'],
        ], [
            'pesan.required' => 'Pesan percakapan tidak boleh kosong.',
            'lampiran_file.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('lampiran_file')) {
            $file = $request->file('lampiran_file');
            $fileName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('berkas_konsultasi/' . date('Y/m'), $fileName);
        }

        $userId = auth()->id() ?? 1;

        KonsultasiChat::create([
            'konsultasi_id' => $konsultasi->id,
            'user_id'       => $userId,
            'tipe_pengirim' => 'apip',
            'pesan'         => $request->pesan,
            'lampiran_file' => $filePath,
        ]);

        if ($konsultasi->status === 'menunggu_disposisi') {
            $konsultasi->update(['status' => 'berjalan']);
        }

        // Notifikasi ke pemohon OPD
        if ($konsultasi->user) {
            try {
                $konsultasi->user->notify(new \App\Notifications\KonsultasiNotifikasiNotification(
                    $konsultasi,
                    'chat_baru',
                    auth()->user()->nama_display ?? 'Auditor APIP',
                    $request->pesan
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif chat ke OPD: " . $e->getMessage());
            }
        }

        return back()->with('status', 'Pesan balasan konsultasi berhasil dikirim.');
    }

    /**
     * Formulir Kesimpulan Advis & Terbitkan Berita Acara Konsultasi (PDF)
     */
    public function terbitkanBa(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        $request->validate([
            'kesimpulan_advis' => ['required', 'string'],
        ], [
            'kesimpulan_advis.required' => 'Kesimpulan & advis hasil konsultasi wajib diisi.',
        ]);

        $sebelum = $konsultasi->toArray();

        $konsultasi->update([
            'kesimpulan_advis' => $request->kesimpulan_advis,
            'status'           => 'selesai',
        ]);

        ActivityLog::catat('konsultasi', $konsultasi->id, 'update', $sebelum, $konsultasi->toArray());

        // Notifikasi ke pemohon OPD bahwa BA telah terbit
        if ($konsultasi->user) {
            try {
                $konsultasi->user->notify(new \App\Notifications\KonsultasiNotifikasiNotification(
                    $konsultasi,
                    'ba_terbit'
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[SIPANDA Notification] Gagal kirim notif BA ke OPD: " . $e->getMessage());
            }
        }

        return back()->with('status', 'Kesimpulan advis & Berita Acara Konsultasi resmi berhasil diterbitkan.');
    }

    /**
     * Toggle Publikasi Artikel FAQ
     */
    public function toggleFaq(Konsultasi $konsultasi): RedirectResponse
    {
        $konsultasi->update([
            'is_faq_public' => ! $konsultasi->is_faq_public,
        ]);

        $status = $konsultasi->is_faq_public ? 'dipublikasikan ke Bank FAQ Publik.' : 'ditarik dari Bank FAQ Publik.';

        return back()->with('status', 'Topik konsultasi berhasil ' . $status);
    }

    /**
     * Cetak Berita Acara Hasil Konsultasi (PDF/Print View)
     */
    public function cetakBa(Konsultasi $konsultasi): View
    {
        $konsultasi->load([
            'pemohon.objekPenugasan',
            'objekPenugasan',
            'irban',
            'tim.user',
            'chats.sender',
        ]);

        return view('konsultasi.cetak-ba', compact('konsultasi'));
    }

    /**
     * Bank QnA / FAQ Publik & Layanan Penasihat AI
     */
    public function faqIndex(Request $request): View
    {
        $search = $request->input('search');
        $area   = $request->input('area');

        // 1. Ambil FAQ resmi dari FaqArtikel
        $faqArtikelQuery = \App\Models\FaqArtikel::with('regulasi')->published()->search($search);
        if ($area) {
            $faqArtikelQuery->where(function ($q) use ($area) {
                $q->where('kategori', strtolower($area))
                  ->orWhere('kategori', 'like', "%{$area}%");
            });
        }
        $faqArtikels = $faqArtikelQuery->orderBy('urutan', 'asc')->orderBy('created_at', 'desc')->get();

        // 2. Ambil FAQ hasil publikasi e-Consulting
        $konsultasiQuery = Konsultasi::where('is_faq_public', true)->where('status', 'selesai');
        if ($area) {
            $konsultasiQuery->where('area_konsultasi', $area);
        }
        if ($search) {
            $konsultasiQuery->where(function ($q) use ($search) {
                $q->where('judul_permasalahan', 'like', "%{$search}%")
                  ->orWhere('uraian_permasalahan', 'like', "%{$search}%")
                  ->orWhere('kesimpulan_advis', 'like', "%{$search}%");
            });
        }
        $faqs = $konsultasiQuery->orderBy('updated_at', 'desc')->paginate(12)->withQueryString();

        // 3. Ambil Dokumen Regulasi Populer
        $regulasiPopuler = \App\Models\RegulasiHukum::publik()->orderBy('diunduh_count', 'desc')->take(6)->get();

        return view('konsultasi.faq', compact('faqs', 'faqArtikels', 'regulasiPopuler', 'search', 'area'));
    }
}
