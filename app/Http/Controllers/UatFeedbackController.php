<?php

namespace App\Http\Controllers;

use App\Models\UatFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UatFeedbackController extends Controller
{
    /**
     * Tampilkan Halaman Master Kotak Masukan & Bug UAT (Internal Admin).
     */
    public function index(Request $request): View
    {
        $status   = $request->input('status');
        $kategori = $request->input('kategori');
        $urgensi  = $request->input('urgensi');
        $search   = $request->input('search');

        $query = UatFeedback::with(['user', 'adminTindakLanjut'])->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($urgensi) {
            $query->where('urgensi', $urgensi);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$search}%")
                  ->orWhere('url_halaman', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->paginate(15)->withQueryString();

        // Ringkasan Statistik
        $stats = [
            'total'       => UatFeedback::count(),
            'baru'        => UatFeedback::where('status', 'baru')->count(),
            'bug_kritis'  => UatFeedback::where('kategori', 'bug')->whereIn('urgensi', ['kritis', 'tinggi'])->count(),
            'diperbaiki'  => UatFeedback::where('status', 'diperbaiki')->count(),
        ];

        return view('master.feedback.index', compact('feedbacks', 'stats', 'status', 'kategori', 'urgensi', 'search'));
    }

    /**
     * Endpoint API Pengiriman Feedback / Bug Report dari Floating Widget (AJAX).
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'judul'          => ['required', 'string', 'max:255'],
            'deskripsi'      => ['required', 'string', 'max:5000'],
            'kategori'       => ['required', 'in:bug,saran,pertanyaan,apresiasi'],
            'urgensi'        => ['required', 'in:rendah,sedang,tinggi,kritis'],
            'url_halaman'    => ['nullable', 'string', 'max:500'],
            'browser_info'   => ['nullable', 'string'],
            'screenshot'     => ['nullable', 'file', 'image', 'max:10240'], // File upload biasa
            'screenshot_b64' => ['nullable', 'string'], // Hasil Paste Clipboard Base64
        ]);

        $userId      = null;
        $guardType   = 'guest';
        $namaPelapor = $request->input('nama_pelapor', 'Pengguna Pengujian');
        $emailPelapor= $request->input('email_pelapor');
        $noHpPelapor = $request->input('no_hp_pelapor');
        $rolePelapor = $request->input('role_pelapor', 'Tester');

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userId       = $user->id;
            $guardType    = 'web';
            $namaPelapor  = $user->nama ?? $user->name;
            $emailPelapor = $user->email;
            $noHpPelapor  = $user->no_hp ?? $user->no_wa ?? null;
            $rolePelapor  = $user->getRoleNames()->first() ?? 'APIP Internal';
        } elseif (Auth::guard('opd')->check()) {
            $user = Auth::guard('opd')->user();
            $userId       = $user->id;
            $guardType    = 'opd';
            $namaPelapor  = $user->nama_pic ?? $user->nama ?? 'PIC Perangkat Daerah';
            $emailPelapor = $user->email;
            $noHpPelapor  = $user->no_hp ?? null;
            $rolePelapor  = 'Auditi OPD (' . ($user->objekPenugasan?->nama ?? 'Perangkat Daerah') . ')';
        }

        $screenshotPath = null;

        // 1. Tangani Upload File Normal
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $filename = 'feedback_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $screenshotPath = $file->storeAs('uat_feedback_screenshots', $filename, 'public');
        }
        // 2. Tangani Paste Screenshot Base64 dari Clipboard
        elseif ($request->filled('screenshot_b64') && str_starts_with($request->input('screenshot_b64'), 'data:image')) {
            $b64Data = $request->input('screenshot_b64');
            $parts = explode(',', $b64Data);
            if (count($parts) === 2) {
                $imageBinary = base64_decode($parts[1]);
                $filename = 'feedback_paste_' . date('Ymd_His') . '_' . Str::random(8) . '.png';
                Storage::disk('public')->put('uat_feedback_screenshots/' . $filename, $imageBinary);
                $screenshotPath = 'uat_feedback_screenshots/' . $filename;
            }
        }

        $feedback = UatFeedback::create([
            'user_id'         => $userId,
            'guard_type'      => $guardType,
            'nama_pelapor'    => $namaPelapor,
            'email_pelapor'   => $emailPelapor,
            'no_hp_pelapor'   => $noHpPelapor,
            'role_pelapor'    => $rolePelapor,
            'kategori'        => $request->input('kategori'),
            'urgensi'         => $request->input('urgensi'),
            'url_halaman'     => $request->input('url_halaman'),
            'judul'           => $request->input('judul'),
            'deskripsi'       => $request->input('deskripsi'),
            'screenshot_path' => $screenshotPath,
            'browser_info'    => $request->input('browser_info'),
            'status'          => 'baru',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Masukan/Laporan kendala Anda berhasil dikirim dan tersimpan di database tim pengembang.',
            'id'      => $feedback->id,
        ]);
    }

    /**
     * Update Status & Catatan Respon Tindak Lanjut Admin.
     */
    public function updateStatus(Request $request, UatFeedback $feedback): RedirectResponse
    {
        $request->validate([
            'status'        => ['required', 'in:baru,sedang_ditelaah,diperbaiki,ditutup'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback->update([
            'status'               => $request->input('status'),
            'catatan_admin'        => $request->input('catatan_admin'),
            'ditindaklanjuti_oleh' => Auth::id(),
            'ditindaklanjuti_pada' => now(),
        ]);

        return back()->with('status', "Status feedback #{$feedback->id} berhasil diperbarui menjadi " . strtoupper($feedback->status));
    }

    /**
     * Hapus Feedback UAT.
     */
    public function destroy(UatFeedback $feedback): RedirectResponse
    {
        if ($feedback->screenshot_path) {
            Storage::disk('public')->delete($feedback->screenshot_path);
        }

        $feedback->delete();

        return back()->with('status', "Laporan feedback berhasil dihapus dari database.");
    }
}
