<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\KapasitasSdm;
use App\Models\ObjekPenugasan;
use App\Models\PenilaianRisiko;
use App\Models\Penugasan;
use App\Models\Pkppt;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerencanaanPkptController extends Controller
{
    /**
     * Tampilkan Dashboard Perencanaan PKPT (Penilaian Risiko & Draf PKPT).
     */
    public function index(Request $request): View
    {
        $tahunRencana = $request->input('tahun', date('Y') + 1);

        $penilaianRisiko = PenilaianRisiko::with('objekPenugasan')
            ->where('tahun_perencanaan', $tahunRencana)
            ->orderBy('skor_total', 'desc')
            ->get();

        $kapasitasSdm = KapasitasSdm::with('irban')
            ->where('tahun_perencanaan', $tahunRencana)
            ->get();

        $irbans = Irban::where('nama_irban', '!=', 'Sekretariat')->get();
        $tahunList = range(date('Y') + 2, 2024);

        return view('perencanaan.index', compact(
            'penilaianRisiko', 'kapasitasSdm', 'irbans', 'tahunRencana', 'tahunList'
        ));
    }

    /**
     * Jalankan algoritma penghitungan skor risiko otomatis untuk seluruh objek.
     */
    public function hitungRisiko(Request $request): RedirectResponse
    {
        $tahunRencana = $request->input('tahun_perencanaan', date('Y') + 1);
        $objekList = ObjekPenugasan::aktif()->get();

        foreach ($objekList as $objek) {
            // 1. Skor Aging (Makin lama tidak diperiksa, skor makin tinggi 1.0 - 5.0)
            $lastPenugasan = Penugasan::whereHas('objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $objek->id))
                ->orderBy('tanggal_selesai', 'desc')
                ->first();

            $skorAging = 3.0; // default medium
            if ($lastPenugasan) {
                $selisihBulan = now()->diffInMonths($lastPenugasan->tanggal_selesai);
                if ($selisihBulan >= 24) $skorAging = 5.0;
                elseif ($selisihBulan >= 12) $skorAging = 4.0;
                elseif ($selisihBulan >= 6)  $skorAging = 2.5;
                else                         $skorAging = 1.0;
            } else {
                $skorAging = 5.0; // Belum pernah diperiksa
            }

            // 2. Skor Temuan (Berdasarkan jumlah tindak lanjut)
            $totalTL = \App\Models\TindakLanjut::whereHas('penugasan.objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $objek->id))->count();
            $skorTemuan = min(5.0, 1.0 + ($totalTL * 0.8));

            // 3. Skor Tindak Lanjut Mandek (% belum selesai)
            $tlBelum = \App\Models\TindakLanjut::whereHas('penugasan.objekPenugasan', fn($q) => $q->where('objek_penugasan.id', $objek->id))
                ->whereIn('status_tindak_lanjut', ['belum', 'proses', 'dikembalikan'])->count();
            $skorMandek = $totalTL > 0 ? min(5.0, ($tlBelum / $totalTL) * 5.0) : 1.0;

            // 4. Skor Anggaran (Preserve input manual sebelumnya jika ada)
            $existing = PenilaianRisiko::where('objek_penugasan_id', $objek->id)
                ->where('tahun_perencanaan', $tahunRencana)->first();
            $skorAnggaran = $existing ? $existing->skor_anggaran : 3.0;
            $skorPengaduan = $existing ? $existing->skor_pengaduan_khusus : 1.0;

            // Agregat berbobot (Aging 30%, Anggaran 25%, Temuan 20%, Mandek 15%, Pengaduan 10%)
            $skorTotal = round(
                ($skorAging * 0.30) +
                ($skorAnggaran * 0.25) +
                ($skorTemuan * 0.20) +
                ($skorMandek * 0.15) +
                ($skorPengaduan * 0.10),
                2
            );

            PenilaianRisiko::updateOrCreate(
                [
                    'objek_penugasan_id' => $objek->id,
                    'tahun_perencanaan'  => $tahunRencana,
                ],
                [
                    'skor_aging'                 => $skorAging,
                    'skor_anggaran'              => $skorAnggaran,
                    'skor_temuan'                => $skorTemuan,
                    'skor_tindak_lanjut_mandek'  => $skorMandek,
                    'skor_pengaduan_khusus'      => $skorPengaduan,
                    'skor_total'                 => $skorTotal,
                    'dihitung_pada'              => now(),
                ]
            );
        }

        return back()->with('status', "Penghitungan skor risiko untuk tahun {$tahunRencana} selesai dihitung secara otomatis.");
    }

    /**
     * Simpan alokasi Kapasitas SDM per Irban.
     */
    public function storeKapasitasSdm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_perencanaan'    => ['required', 'integer'],
            'irban_id'             => ['required', 'exists:irbans,id'],
            'jumlah_hari_tersedia' => ['required', 'integer', 'min:1'],
            'catatan'              => ['nullable', 'string'],
        ]);

        KapasitasSdm::updateOrCreate(
            [
                'irban_id'          => $validated['irban_id'],
                'tahun_perencanaan' => $validated['tahun_perencanaan'],
            ],
            [
                'jumlah_hari_tersedia' => $validated['jumlah_hari_tersedia'],
                'catatan'              => $validated['catatan'],
            ]
        );

        return back()->with('status', 'Kapasitas SDM berhasil diperbarui.');
    }

    /**
     * Generate Draf PKPT otomatis berdasarkan objek skor risiko tertinggi & alokasi SDM.
     */
    public function generateDraft(Request $request): RedirectResponse
    {
        $tahunRencana = $request->input('tahun_perencanaan', date('Y') + 1);

        $risikoList = PenilaianRisiko::with('objekPenugasan')
            ->where('tahun_perencanaan', $tahunRencana)
            ->orderBy('skor_total', 'desc')
            ->get();

        if ($risikoList->isEmpty()) {
            return back()->with('error', 'Silakan jalankan "Hitung Skor Risiko" terlebih dahulu.');
        }

        $irbans = Irban::where('nama_irban', '!=', 'Sekretariat')->get();
        if ($irbans->isEmpty()) {
            return back()->with('error', 'Data Irban belum tersedia.');
        }

        $countGenerated = 0;
        $irbanIndex = 0;

        foreach ($risikoList as $risiko) {
            $irbanTarget = $irbans[$irbanIndex % $irbans->count()];

            // Buat draf PKPT jika belum ada
            $exists = Pkppt::where('tahun', $tahunRencana)
                ->where('area_pengawasan', 'like', "%{$risiko->objekPenugasan->nama}%")
                ->exists();

            if (! $exists) {
                Pkppt::create([
                    'tahun'                  => $tahunRencana,
                    'area_pengawasan'        => "Audit Kinerja Pengawasan pada " . $risiko->objekPenugasan->nama,
                    'jenis_pengawasan'       => 'Audit',
                    'sasaran'                => $risiko->objekPenugasan->nama,
                    'rencana_mulai'          => "{$tahunRencana}-03-01",
                    'rencana_selesai_laporan'=> "{$tahunRencana}-04-15",
                    'jumlah_laporan_rencana' => 1,
                    'irban_id'               => $irbanTarget->id,
                    'status'                 => 'draft',
                    'skor_risiko_acuan'      => $risiko->skor_total,
                    'dibuat_oleh'            => auth()->id(),
                ]);
                $countGenerated++;
                $irbanIndex++;
            }
        }

        return redirect()->route('pkppt.index', ['tahun' => $tahunRencana])
            ->with('status', "Draf PKPT otomatis berhasil dibuat ({$countGenerated} area pengawasan prioritas tinggi dialokasikan).");
    }

    /** Alur persetujuan: Irban mengusulkan PKPPT */
    public function usulkan(Pkppt $pkppt): RedirectResponse
    {
        $pkppt->update(['status' => 'diusulkan']);
        ActivityLog::catat('pkppt', $pkppt->id, 'update', null, ['status' => 'diusulkan']);
        return back()->with('status', "PKPPT '{$pkppt->area_pengawasan}' berhasil diusulkan.");
    }

    /** Alur persetujuan: Inspektur menetapkan PKPPT */
    public function tetapkan(Pkppt $pkppt): RedirectResponse
    {
        $pkppt->update([
            'status'             => 'ditetapkan',
            'ditetapkan_oleh'    => auth()->id(),
            'tanggal_ditetapkan' => now(),
        ]);
        ActivityLog::catat('pkppt', $pkppt->id, 'update', null, ['status' => 'ditetapkan']);
        return back()->with('status', "PKPPT '{$pkppt->area_pengawasan}' resmi DITETAPKAN oleh Inspektur.");
    }
}
