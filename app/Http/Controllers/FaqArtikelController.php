<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FaqArtikel;
use App\Models\RegulasiHukum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqArtikelController extends Controller
{
    /**
     * Master Data: Kelola Bank Artikel FAQ APIP (Internal)
     */
    public function index(Request $request): View
    {
        $search   = $request->input('search');
        $kategori = $request->input('kategori');

        $query = FaqArtikel::with(['regulasi', 'pembuat'])->search($search)->kategori($kategori);

        $faqList = $query->orderBy('urutan', 'asc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $regulasiList = RegulasiHukum::orderBy('nomor_regulasi')->get(['id', 'nomor_regulasi', 'judul']);

        return view('master.faq.index', compact('faqList', 'regulasiList', 'search', 'kategori'));
    }

    /**
     * Simpan Artikel FAQ Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan'           => ['required', 'string', 'max:255'],
            'jawaban'              => ['required', 'string'],
            'kategori'             => ['required', 'string', 'max:50'],
            'regulasi_hukum_id'    => ['nullable', 'exists:regulasi_hukum,id'],
            'dasar_hukum_rujukan'  => ['nullable', 'string', 'max:255'],
            'is_published'         => ['required', 'boolean'],
            'urutan'               => ['nullable', 'integer'],
        ], [
            'pertanyaan.required' => 'Pertanyaan FAQ wajib diisi.',
            'jawaban.required'    => 'Jawaban / advis wajib diisi.',
        ]);

        $faq = FaqArtikel::create([
            'pertanyaan'          => $validated['pertanyaan'],
            'jawaban'             => $validated['jawaban'],
            'kategori'            => $validated['kategori'],
            'regulasi_hukum_id'   => $validated['regulasi_hukum_id'],
            'dasar_hukum_rujukan' => $validated['dasar_hukum_rujukan'],
            'is_published'        => (bool) $validated['is_published'],
            'urutan'              => (int) ($validated['urutan'] ?? 0),
            'dibuat_oleh'         => auth()->id(),
        ]);

        ActivityLog::catat('faq_artikel', $faq->id, 'create', null, $faq->toArray());

        return back()->with('status', "✓ Artikel FAQ berhasil dipublikasikan!");
    }

    /**
     * Update Artikel FAQ
     */
    public function update(Request $request, FaqArtikel $faq): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan'           => ['required', 'string', 'max:255'],
            'jawaban'              => ['required', 'string'],
            'kategori'             => ['required', 'string', 'max:50'],
            'regulasi_hukum_id'    => ['nullable', 'exists:regulasi_hukum,id'],
            'dasar_hukum_rujukan'  => ['nullable', 'string', 'max:255'],
            'is_published'         => ['required', 'boolean'],
            'urutan'               => ['nullable', 'integer'],
        ]);

        $sebelum = $faq->toArray();

        $faq->update([
            'pertanyaan'          => $validated['pertanyaan'],
            'jawaban'             => $validated['jawaban'],
            'kategori'            => $validated['kategori'],
            'regulasi_hukum_id'   => $validated['regulasi_hukum_id'],
            'dasar_hukum_rujukan' => $validated['dasar_hukum_rujukan'],
            'is_published'        => (bool) $validated['is_published'],
            'urutan'              => (int) ($validated['urutan'] ?? 0),
        ]);

        ActivityLog::catat('faq_artikel', $faq->id, 'update', $sebelum, $faq->toArray());

        return back()->with('status', "Artikel FAQ berhasil diperbarui.");
    }

    /**
     * Hapus Artikel FAQ
     */
    public function destroy(FaqArtikel $faq): RedirectResponse
    {
        $sebelum = $faq->toArray();
        $faq->delete();

        ActivityLog::catat('faq_artikel', $faq->id, 'delete', $sebelum, null);

        return back()->with('status', "Artikel FAQ berhasil dihapus.");
    }
}
