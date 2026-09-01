<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Notifikasi Pengguna.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $filter = $request->input('filter', 'semua');

        $query = Notifikasi::with('penugasan')
            ->where('user_id', $userId)
            ->terbaru();

        if ($filter === 'belum_dibaca') {
            $query->belumDibaca();
        }

        $listNotifikasi = $query->paginate(15)->withQueryString();
        $unreadCount = Notifikasi::where('user_id', $userId)->belumDibaca()->count();

        return view('notifikasi.index', compact('listNotifikasi', 'filter', 'unreadCount'));
    }

    /**
     * API JSON: Ambil notifikasi terbaru & jumlah belum dibaca untuk Dropdown Navbar.
     */
    public function getUnreadList(): JsonResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['unread_count' => 0, 'items' => []]);
        }

        $unreadCount = Notifikasi::where('user_id', $userId)->belumDibaca()->count();
        $items = Notifikasi::where('user_id', $userId)
            ->terbaru()
            ->limit(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id'          => $n->id,
                    'jenis'       => $n->jenis,
                    'judul'       => $n->judul ?: ucfirst(str_replace('_', ' ', $n->jenis)),
                    'pesan'       => $n->pesan,
                    'is_read'     => $n->is_read,
                    'waktu'       => $n->created_at->diffForHumans(),
                    'url'         => route('notifikasi.read', $n->id),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'items'        => $items,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca dan alihkan ke target url jika ada.
     */
    public function markAsRead(Request $request, Notifikasi $notifikasi): RedirectResponse|JsonResponse
    {
        // Pastikan notifikasi milik pengguna yang sedang login
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $notifikasi->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notifikasi->url_target) {
            return redirect($notifikasi->url_target);
        }

        if ($notifikasi->penugasan_id) {
            return redirect()->route('penugasan.show', $notifikasi->penugasan_id);
        }

        return back()->with('status', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Tandai SEMUA notifikasi pengguna sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $userId = auth()->id();

        Notifikasi::where('user_id', $userId)
            ->belumDibaca()
            ->update(['dibaca_pada' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('status', 'Semua notifikasi berhasil ditandai telah dibaca.');
    }

    /**
     * Hapus notifikasi.
     */
    public function destroy(Notifikasi $notifikasi): RedirectResponse
    {
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $notifikasi->delete();

        return back()->with('status', 'Notifikasi berhasil dihapus.');
    }
}
