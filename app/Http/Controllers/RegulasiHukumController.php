<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RegulasiHukum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RegulasiHukumController extends Controller
{
    /**
     * Tampilan Master Bank Regulasi & Dasar Hukum (Internal)
     */
    public function index(Request $request): View
    {
        $search   = $request->input('search');
        $kategori = $request->input('kategori');
        $jenis    = $request->input('jenis');

        $query = RegulasiHukum::with('pengunggah')->search($search)->kategori($kategori);

        if ($jenis) {
            $query->where('jenis_regulasi', $jenis);
        }

        $regulasiList = $query->orderBy('tahun', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('master.regulasi.index', compact('regulasiList', 'search', 'kategori', 'jenis'));
    }

    /**
     * Simpan Unggahan Dokumen Regulasi Baru (PDF)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul'               => ['required', 'string', 'max:255'],
            'nomor_regulasi'      => ['required', 'string', 'max:100'],
            'tahun'               => ['required', 'integer', 'min:2000', 'max:2035'],
            'jenis_regulasi'      => ['required', 'string', 'max:50'],
            'kategori'            => ['required', 'string', 'max:50'],
            'ringkasan_eksekutif' => ['nullable', 'string'],
            'visibilitas'         => ['required', 'in:publik,opd,internal'],
            'file_pdf'            => ['nullable', 'file', 'mimes:pdf', 'max:25600'], // Maksimal 25 MB
        ], [
            'judul.required'          => 'Judul regulasi wajib diisi.',
            'nomor_regulasi.required' => 'Nomor regulasi wajib diisi.',
            'file_pdf.mimes'          => 'Berkas harus berformat PDF.',
            'file_pdf.max'            => 'Ukuran berkas PDF maksimal 25 MB.',
        ]);

        $filePath = null;
        $namaFileAsli = null;
        $ukuranKb = null;

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $namaFileAsli = $file->getClientOriginalName();
            $ukuranKb = round($file->getSize() / 1024) . ' KB';
            $uniqueName = Str::uuid() . '.' . $file->extension();
            $filePath = $file->storeAs('regulasi/' . $validated['tahun'], $uniqueName);
        }

        $regulasi = RegulasiHukum::create([
            'judul'               => $validated['judul'],
            'nomor_regulasi'      => $validated['nomor_regulasi'],
            'tahun'               => $validated['tahun'],
            'jenis_regulasi'      => $validated['jenis_regulasi'],
            'kategori'            => $validated['kategori'],
            'ringkasan_eksekutif' => $validated['ringkasan_eksekutif'],
            'file_path'           => $filePath,
            'nama_file_asli'      => $namaFileAsli,
            'ukuran_kb'           => $ukuranKb,
            'teks_konten'         => $validated['ringkasan_eksekutif'], // teks untuk pencarian AI
            'visibilitas'         => $validated['visibilitas'],
            'diunggah_oleh'       => auth()->id(),
        ]);

        ActivityLog::catat('regulasi_hukum', $regulasi->id, 'create', null, $regulasi->toArray());

        return back()->with('status', "✓ Dokumen regulasi '{$regulasi->nomor_regulasi}' berhasil diunggah!");
    }

    /**
     * Update Data Regulasi
     */
    public function update(Request $request, RegulasiHukum $regulasi): RedirectResponse
    {
        $validated = $request->validate([
            'judul'               => ['required', 'string', 'max:255'],
            'nomor_regulasi'      => ['required', 'string', 'max:100'],
            'tahun'               => ['required', 'integer', 'min:2000', 'max:2035'],
            'jenis_regulasi'      => ['required', 'string', 'max:50'],
            'kategori'            => ['required', 'string', 'max:50'],
            'ringkasan_eksekutif' => ['nullable', 'string'],
            'visibilitas'         => ['required', 'in:publik,opd,internal'],
            'file_pdf'            => ['nullable', 'file', 'mimes:pdf', 'max:25600'],
        ]);

        $sebelum = $regulasi->toArray();

        if ($request->hasFile('file_pdf')) {
            // Hapus file lama jika ada
            if ($regulasi->file_path && Storage::exists($regulasi->file_path)) {
                Storage::delete($regulasi->file_path);
            }

            $file = $request->file('file_pdf');
            $regulasi->nama_file_asli = $file->getClientOriginalName();
            $regulasi->ukuran_kb = round($file->getSize() / 1024) . ' KB';
            $uniqueName = Str::uuid() . '.' . $file->extension();
            $regulasi->file_path = $file->storeAs('regulasi/' . $validated['tahun'], $uniqueName);
        }

        $regulasi->update([
            'judul'               => $validated['judul'],
            'nomor_regulasi'      => $validated['nomor_regulasi'],
            'tahun'               => $validated['tahun'],
            'jenis_regulasi'      => $validated['jenis_regulasi'],
            'kategori'            => $validated['kategori'],
            'ringkasan_eksekutif' => $validated['ringkasan_eksekutif'],
            'teks_konten'         => $validated['ringkasan_eksekutif'],
            'visibilitas'         => $validated['visibilitas'],
        ]);

        ActivityLog::catat('regulasi_hukum', $regulasi->id, 'update', $sebelum, $regulasi->toArray());

        return back()->with('status', "Data regulasi '{$regulasi->nomor_regulasi}' berhasil diperbarui.");
    }

    /**
     * Hapus Dokumen Regulasi
     */
    public function destroy(RegulasiHukum $regulasi): RedirectResponse
    {
        if ($regulasi->file_path && Storage::exists($regulasi->file_path)) {
            Storage::delete($regulasi->file_path);
        }

        $sebelum = $regulasi->toArray();
        $regulasi->delete();

        ActivityLog::catat('regulasi_hukum', $regulasi->id, 'delete', $sebelum, null);

        return back()->with('status', "Dokumen regulasi berhasil dihapus.");
    }

    /**
     * Unduh File Regulasi (Internal / Authed)
     */
    public function download(RegulasiHukum $regulasi): BinaryFileResponse|RedirectResponse
    {
        if (! $regulasi->file_path || ! Storage::exists($regulasi->file_path)) {
            return back()->with('error', 'Berkas PDF regulasi tidak ditemukan di server.');
        }

        $regulasi->increment('diunduh_count');

        return response()->download(Storage::path($regulasi->file_path), $regulasi->nama_file_asli ?: "{$regulasi->nomor_regulasi}.pdf");
    }

    /**
     * Halaman Publik: Pusat Unduhan Regulasi Pengawasan Daerah (Tanpa Login)
     */
    public function publicIndex(Request $request): View
    {
        $search   = $request->input('search');
        $kategori = $request->input('kategori');
        $jenis    = $request->input('jenis');

        $query = RegulasiHukum::publik()->search($search)->kategori($kategori);

        if ($jenis) {
            $query->where('jenis_regulasi', $jenis);
        }

        $regulasiList = $query->orderBy('tahun', 'desc')->paginate(12)->withQueryString();

        return view('regulasi.public-index', compact('regulasiList', 'search', 'kategori', 'jenis'));
    }

    /**
     * Unduh File Regulasi Publik (Tanpa Login)
     */
    public function publicDownload(RegulasiHukum $regulasi): BinaryFileResponse|RedirectResponse
    {
        if ($regulasi->visibilitas !== 'publik') {
            abort(403, 'Akses ke dokumen ini dibatasi.');
        }

        if (! $regulasi->file_path || ! Storage::exists($regulasi->file_path)) {
            return back()->with('error', 'Berkas PDF regulasi tidak ditemukan.');
        }

        $regulasi->increment('diunduh_count');

        return response()->download(Storage::path($regulasi->file_path), $regulasi->nama_file_asli ?: "{$regulasi->nomor_regulasi}.pdf");
    }
}
