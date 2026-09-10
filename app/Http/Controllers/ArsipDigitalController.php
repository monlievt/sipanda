<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\Irban;
use App\Models\Penugasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArsipDigitalController extends Controller
{
    /**
     * Halaman Utama Penyimpanan Berkas Digital Terpusat
     */
    public function index(Request $request): View
    {
        $user     = auth()->user();
        $kategori = $request->input('kategori');
        $search   = $request->input('search');
        $tahun    = $request->input('tahun');
        $irbanId  = $request->input('irban_id');

        // Query dibatasi berdasarkan hak akses pengguna
        $query = ArsipDigital::with(['penugasan.irbans', 'penugasan.irban', 'penugasan.objekPenugasan', 'pengunggah'])
            ->accessibleBy($user);

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($tahun) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun)
                  ->orWhereHas('penugasan', function ($pq) use ($tahun) {
                      $pq->whereYear('tanggal_mulai', $tahun);
                  });
            });
        }

        if ($irbanId) {
            $query->whereHas('penugasan', function ($pq) use ($irbanId) {
                $pq->where('irban_id', $irbanId)
                  ->orWhereHas('irbans', fn($iq) => $iq->where('irbans.id', $irbanId));
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%{$search}%")
                  ->orWhereHas('penugasan', function ($pq) use ($search) {
                      $pq->where('no_spt', 'like', "%{$search}%")
                        ->orWhere('uraian_penugasan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pengunggah', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $listArsip = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Hitung statistik berkas sesuai filter akses user
        $baseStats = ArsipDigital::accessibleBy($user);
        $totalBerkas   = (clone $baseStats)->count();
        $totalLhp      = (clone $baseStats)->where('kategori', 'like', '%Laporan Hasil%')->count();
        $totalBuktiTl  = (clone $baseStats)->where('kategori', 'like', '%Bukti Tindak Lanjut%')->count();
        $totalSptLain  = $totalBerkas - $totalLhp - $totalBuktiTl;

        // Pilihan SPT yang dapat ditautkan user
        $penugasanList = Penugasan::accessibleBy($user)->orderBy('no_spt', 'desc')->get();

        $irbans = Irban::all();
        $availableYears = range(date('Y') + 1, 2022);

        return view('arsip.index', compact(
            'listArsip', 'penugasanList', 'kategori', 'search', 'tahun', 'irbanId',
            'totalBerkas', 'totalLhp', 'totalBuktiTl', 'totalSptLain', 'irbans', 'availableYears'
        ));
    }

    /**
     * Unggah Berkas Baru ke Arsip Digital
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'file'         => ['required', 'file', 'mimes:pdf,docx,xlsx,jpg,jpeg,png', 'max:25600'], // 25MB max
            'penugasan_id' => ['nullable', 'exists:penugasan,id'],
            'kategori'     => ['required', 'string', 'max:100'],
        ], [
            'file.required' => 'Berkas file wajib diunggah.',
            'file.max'      => 'Ukuran file maksimal 25 MB.',
            'file.mimes'    => 'Format file yang didukung: PDF, DOCX, XLSX, JPG, PNG.',
        ]);

        // Cek otorisasi jika ditautkan ke penugasan
        if ($request->filled('penugasan_id')) {
            $penugasan = Penugasan::findOrFail($request->penugasan_id);
            if (! $penugasan->canAccess($user)) {
                return back()->with('error', 'Anda tidak memiliki hak akses untuk menautkan berkas ke Surat Tugas ini.');
            }
        }

        $file = $request->file('file');
        $path = $file->store('arsip/' . date('Y/m'), 'public');

        $arsip = ArsipDigital::create([
            'penugasan_id'  => $request->penugasan_id,
            'nama_file'     => $file->getClientOriginalName(),
            'path_file'     => $path,
            'ukuran_kb'     => round($file->getSize() / 1024) . ' KB',
            'mime_type'     => $file->getMimeType(),
            'kategori'      => $request->kategori,
            'diunggah_oleh' => $user->id,
        ]);

        ActivityLog::catat('arsip_digital', $arsip->id, 'create', null, $arsip->toArray());

        return back()->with('status', '✓ Berkas berhasil diunggah dan disimpan ke Arsip Digital Terpusat.');
    }

    /**
     * Preview Berkas Inline
     */
    public function preview(ArsipDigital $arsip): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        $user = auth()->user();

        if (! $arsip->canAccess($user)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat berkas penugasan ini.');
        }

        if (Storage::disk('public')->exists($arsip->path_file)) {
            $path = Storage::disk('public')->path($arsip->path_file);
            return response()->file($path, [
                'Content-Type' => $arsip->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $arsip->nama_file . '"'
            ]);
        }

        if (Storage::disk('local')->exists($arsip->path_file)) {
            $path = Storage::disk('local')->path($arsip->path_file);
            return response()->file($path, [
                'Content-Type' => $arsip->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $arsip->nama_file . '"'
            ]);
        }

        return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
    }

    /**
     * Unduh Berkas Arsip
     */
    public function download(ArsipDigital $arsip): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        $user = auth()->user();

        if (! $arsip->canAccess($user)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengunduh berkas penugasan ini.');
        }

        if (Storage::disk('public')->exists($arsip->path_file)) {
            return Storage::disk('public')->download($arsip->path_file, $arsip->nama_file);
        }

        if (Storage::disk('local')->exists($arsip->path_file)) {
            return Storage::disk('local')->download($arsip->path_file, $arsip->nama_file);
        }

        return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
    }

    /**
     * Hapus Berkas Arsip
     */
    public function destroy(ArsipDigital $arsip): RedirectResponse
    {
        $user = auth()->user();

        // Hanya pengunggah, Admin, Inspektur, Sekretaris, atau Irban penanggung jawab yang boleh menghapus
        $canDelete = $user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris'])
            || $arsip->diunggah_oleh == $user->id
            || ($user->hasRole(['irban', 'admin_irban']) && $arsip->penugasan && $arsip->penugasan->irban_id == $user->irban_id);

        if (! $canDelete) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus berkas arsip ini.');
        }

        $sebelum = $arsip->toArray();

        if (Storage::disk('public')->exists($arsip->path_file)) {
            Storage::disk('public')->delete($arsip->path_file);
        } elseif (Storage::disk('local')->exists($arsip->path_file)) {
            Storage::disk('local')->delete($arsip->path_file);
        }

        $arsip->delete();

        ActivityLog::catat('arsip_digital', $arsip->id, 'delete', $sebelum, null);

        return back()->with('status', 'Berkas arsip berhasil dihapus.');
    }
}
