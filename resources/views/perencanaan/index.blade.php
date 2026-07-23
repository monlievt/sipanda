<x-app-layout>
    <x-slot name="header">
        Perencanaan PKPT Berbasis Risiko Sederhana (Siklus N-1)
    </x-slot>

    <!-- Header Actions & Hitung Risiko / Generate Button -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Matriks Penilaian Risiko & Kapasitas SDM</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penetapan prioritas pengawasan tahun depan berdasarkan skor risiko objektif.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('perencanaan.hitung_risiko') }}">
                @csrf
                <input type="hidden" name="tahun_perencanaan" value="{{ $tahunRencana }}">
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Hitung Skor Risiko {{ $tahunRencana }}</span>
                </button>
            </form>

            <form method="POST" action="{{ route('perencanaan.generate_draft') }}">
                @csrf
                <input type="hidden" name="tahun_perencanaan" value="{{ $tahunRencana }}">
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Generate Draf PKPT Otomatis</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Matriks Risiko Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mb-8">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">Peringkat Objek Pengawasan Berdasarkan Skor Risiko (Tahun {{ $tahunRencana }})</h3>
            <span class="text-xs font-semibold text-slate-500">Bobot: Aging 30%, Anggaran 25%, Temuan 20%, Mandek 15%, Pengaduan 10%</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">Rank</th>
                        <th class="py-3 px-4">Nama Objek Penugasan (OPD/Kec)</th>
                        <th class="py-3 px-4 text-center">Aging (30%)</th>
                        <th class="py-3 px-4 text-center">Anggaran (25%)</th>
                        <th class="py-3 px-4 text-center">Temuan (20%)</th>
                        <th class="py-3 px-4 text-center">TL Mandek (15%)</th>
                        <th class="py-3 px-4 text-center font-black text-emerald-600">Skor Total</th>
                        <th class="py-3 px-4 text-center">Tingkat Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($penilaianRisiko as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-extrabold text-center text-slate-700 dark:text-slate-300">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $item->objekPenugasan?->nama }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ $item->skor_aging }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ $item->skor_anggaran }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ $item->skor_temuan }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-600">{{ $item->skor_tindak_lanjut_mandek }}</td>
                            <td class="py-3 px-4 text-center font-black text-base text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">
                                {{ $item->skor_total }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->skor_total >= 4.0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">TINGGI (PRIORITAS 1)</span>
                                @elseif($item->skor_total >= 3.0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">SEDANG (PRIORITAS 2)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">RENDAH (CADANGAN)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada penilaian risiko untuk tahun {{ $tahunRencana }}. Klik tombol "Hitung Skor Risiko {{ $tahunRencana }}" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
