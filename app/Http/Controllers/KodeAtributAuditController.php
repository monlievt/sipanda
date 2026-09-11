<?php

namespace App\Http\Controllers;

use App\Models\KodeAtributRekomendasi;
use App\Models\KodeAtributTemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KodeAtributAuditController extends Controller
{
    /**
     * Tampilkan antarmuka Kamus Kode Atribut Temuan & Rekomendasi PermenPAN-RB 42/2011.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $kelompok = $request->input('kelompok');
        $tab = $request->input('tab', 'temuan'); // 'temuan' atau 'rekomendasi'

        // Query Temuan
        $temuanQuery = KodeAtributTemuan::query();
        if ($kelompok) {
            $temuanQuery->where('kode_kelompok', $kelompok);
        }
        if ($search) {
            $temuanQuery->where(function ($q) use ($search) {
                $q->where('kode_lengkap', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('nama_sub_kelompok', 'like', "%{$search}%");
            });
        }
        $listTemuan = $temuanQuery->orderBy('kode_kelompok')
            ->orderBy('kode_sub_kelompok')
            ->orderBy('kode_jenis')
            ->get();

        // Grouping temuan per Kelompok & Sub-Kelompok untuk visualisasi rapi
        $groupedTemuan = $listTemuan->groupBy('nama_kelompok')->map(function ($items) {
            return $items->groupBy('nama_sub_kelompok');
        });

        // Query Rekomendasi
        $rekomendasiQuery = KodeAtributRekomendasi::query();
        if ($search && $tab === 'rekomendasi') {
            $rekomendasiQuery->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        $listRekomendasi = $rekomendasiQuery->orderBy('kode')->get();

        // Dictionary map Rekomendasi untuk referensi badge rekomendasi
        $mapRekomendasi = $listRekomendasi->keyBy('kode');

        return view('master.kamus-atribut.index', compact(
            'listTemuan', 'groupedTemuan', 'listRekomendasi', 'mapRekomendasi',
            'search', 'kelompok', 'tab'
        ));
    }

    /**
     * API JSON untuk kebutuhan form dinamis modal input temuan & rekomendasi.
     */
    public function apiOptions(): JsonResponse
    {
        $temuan = KodeAtributTemuan::orderBy('kode_kelompok')
            ->orderBy('kode_sub_kelompok')
            ->orderBy('kode_jenis')
            ->get();

        $rekomendasi = KodeAtributRekomendasi::orderBy('kode')->get();

        return response()->json([
            'success' => true,
            'temuan' => $temuan,
            'rekomendasi' => $rekomendasi,
        ]);
    }
}
