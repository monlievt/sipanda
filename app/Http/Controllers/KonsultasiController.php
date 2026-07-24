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
            $query->where('status', $status);
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

        $countMenunggu = Konsultasi::where('status', 'menunggu_disposisi')->count();
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
     * Disposisi Tim Konsultasi APIP & Persetujuan Metode (Online / Tatap Muka)
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
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('berkas_konsultasi', $fileName, 'public');
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
     * Bank QnA / FAQ Publik
     */
    public function faqIndex(Request $request): View
    {
        $search = $request->input('search');
        $area   = $request->input('area');

        $query = Konsultasi::where('is_faq_public', true)->where('status', 'selesai');

        if ($area) {
            $query->where('area_konsultasi', $area);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_permasalahan', 'like', "%{$search}%")
                  ->orWhere('uraian_permasalahan', 'like', "%{$search}%")
                  ->orWhere('kesimpulan_advis', 'like', "%{$search}%");
            });
        }

        $faqs = $query->orderBy('updated_at', 'desc')->paginate(12)->withQueryString();

        return view('konsultasi.faq', compact('faqs', 'search', 'area'));
    }
}
