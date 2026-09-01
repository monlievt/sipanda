<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ArsipDigital;
use App\Models\Penugasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArsipDigitalController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = $request->input('kategori');
        $search = $request->input('search');

        $query = ArsipDigital::with(['penugasan', 'pengunggah']);

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where('nama_file', 'like', "%{$search}%");
        }

        $listArsip = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $penugasanList = Penugasan::orderBy('no_spt')->get();

        return view('arsip.index', compact('listArsip', 'penugasanList', 'kategori', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file'         => ['required', 'file', 'mimes:pdf,docx,xlsx,jpg,png', 'max:10240'], // 10MB max
            'penugasan_id' => ['nullable', 'exists:penugasan,id'],
            'kategori'     => ['required', 'string'],
        ]);

        $file = $request->file('file');
        $path = $file->store('arsip', 'local');

        $arsip = ArsipDigital::create([
            'penugasan_id'  => $request->penugasan_id,
            'nama_file'     => $file->getClientOriginalName(),
            'path_file'     => $path,
            'ukuran_kb'     => round($file->getSize() / 1024) . ' KB',
            'mime_type'     => $file->getClientMimeType(),
            'kategori'      => $request->kategori,
            'diunggah_oleh' => auth()->id(),
        ]);

        ActivityLog::catat('arsip_digital', $arsip->id, 'create', null, $arsip->toArray());

        return back()->with('status', 'Berkas berhasil diunggah ke Arsip Digital.');
    }

    public function download(ArsipDigital $arsip): StreamedResponse|RedirectResponse
    {
        if (Storage::disk('public')->exists($arsip->path_file)) {
            return Storage::disk('public')->download($arsip->path_file, $arsip->nama_file);
        }

        if (Storage::disk('local')->exists($arsip->path_file)) {
            return Storage::disk('local')->download($arsip->path_file, $arsip->nama_file);
        }

        return back()->with('error', 'File fisik tidak ditemukan di server.');
    }

    public function destroy(ArsipDigital $arsip): RedirectResponse
    {
        $sebelum = $arsip->toArray();

        if (Storage::disk('local')->exists($arsip->path_file)) {
            Storage::disk('local')->delete($arsip->path_file);
        }

        $arsip->delete();

        ActivityLog::catat('arsip_digital', $arsip->id, 'delete', $sebelum, null);

        return back()->with('status', 'File arsip berhasil dihapus.');
    }
}
