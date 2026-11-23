<x-app-layout>
    <x-slot name="header">
        Perencanaan PKPT Berbasis Risiko Sederhana (Siklus N-1)
    </x-slot>

    <!-- Header Actions & Hitung Risiko / Generate Button -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Matriks Penilaian Risiko & Kapasitas SDM</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Penetapan prioritas pengawasan tahun depan berdasarkan skor risiko objektif.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form method="POST" action="{{ route('perencanaan.hitung_risiko') }}">
                @csrf
                <input type="hidden" name="tahun_perencanaan" value="{{ $tahunRencana }}">
                <button type="submit" class="ledger-btn-secondary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Hitung Skor Risiko {{ $tahunRencana }}</span>
                </button>
            </form>

            <form method="POST" action="{{ route('perencanaan.generate_draft') }}">
                @csrf
                <input type="hidden" name="tahun_perencanaan" value="{{ $tahunRencana }}">
                <button type="submit" class="ledger-btn-primary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Generate Draf PKPT Otomatis</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Matriks Risiko Table -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden mb-8">
        <div class="p-4 border-b border-hairline bg-surface-subtle/60 flex flex-col lg:flex-row lg:items-center justify-between gap-2">
            <h3 class="font-semibold text-ink text-[15px]">Peringkat Objek Pengawasan Berdasarkan Skor Risiko (Tahun {{ $tahunRencana }})</h3>
            <span class="text-xs font-medium text-ink-muted">Bobot: Aging 30%, Anggaran 25%, Temuan 20%, Mandek 15%, Pengaduan 10%</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">Rank</th>
                        <th class="py-3.5 px-4">Nama Objek Penugasan (OPD/Kec)</th>
                        <th class="py-3.5 px-4 text-center">Aging (30%)</th>
                        <th class="py-3.5 px-4 text-center">Anggaran (25%)</th>
                        <th class="py-3.5 px-4 text-center">Temuan (20%)</th>
                        <th class="py-3.5 px-4 text-center">TL Mandek (15%)</th>
                        <th class="py-3.5 px-4 text-center font-bold text-brand-ink">Skor Total</th>
                        <th class="py-3.5 px-4 text-center">Tingkat Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($penilaianRisiko as $index => $item)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-center text-ink-secondary tabular-nums">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-semibold text-ink">{{ $item->objekPenugasan?->nama }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-ink-secondary tabular-nums">{{ $item->skor_aging }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-ink-secondary tabular-nums">{{ $item->skor_anggaran }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-ink-secondary tabular-nums">{{ $item->skor_temuan }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-ink-secondary tabular-nums">{{ $item->skor_tindak_lanjut_mandek }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-sm text-brand-ink bg-brand-soft/40 tabular-nums">
                                {{ $item->skor_total }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($item->skor_total >= 4.0)
                                    <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50">TINGGI (PRIORITAS 1)</span>
                                @elseif($item->skor_total >= 3.0)
                                    <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50">SEDANG (PRIORITAS 2)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50">RENDAH (CADANGAN)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-ink-muted">
                                Belum ada penilaian risiko untuk tahun {{ $tahunRencana }}. Klik tombol "Hitung Skor Risiko {{ $tahunRencana }}" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
