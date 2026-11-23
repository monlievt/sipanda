<x-app-layout>
    <x-slot name="header">
        Layanan Konsultasi & Advisory APIP (E-Consulting QnA)
    </x-slot>

    <div class="space-y-6">
        <!-- Header Title & Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink tracking-tight">Daftar Tiket Konsultasi & QnA APIP</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Kelola permohonan konsultasi online/tatap muka dari Perangkat Daerah (OPD), disposisi tim APIP, dan penerbitan Berita Acara.</p>
            </div>

            <a href="{{ route('faq.index') }}" class="ledger-btn-secondary text-xs shrink-0">
                <svg class="w-4 h-4 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Bank FAQ / QnA Publik</span>
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 bg-brand-soft border border-brand-border rounded-[10px] text-brand-ink text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-[6px] bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-800 dark:text-amber-300 tabular-nums">{{ $countMenunggu }}</p>
                    <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider">Menunggu Disposisi Irban</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-[6px] bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 tabular-nums">{{ $countBerjalan }}</p>
                    <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider">Sedang Berjalan / Tim Ditunjuk</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-[6px] bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-brand-ink tabular-nums">{{ $countSelesai }}</p>
                    <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider">Selesai (Berita Acara Terbit)</p>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-surface p-4 sm:p-5 rounded-[10px] border border-hairline">
            <form method="GET" action="{{ route('konsultasi.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-center">
                <div class="sm:col-span-3">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Tiket / Permasalahan..." class="w-full text-sm rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                </div>

                <div class="sm:col-span-3">
                    <select name="status" onchange="this.form.submit()" class="w-full text-sm rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                        <option value="">-- Semua Status --</option>
                        <option value="menunggu_disposisi" {{ $status === 'menunggu_disposisi' ? 'selected' : '' }}>Menunggu Disposisi Irban</option>
                        <option value="berjalan" {{ $status === 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai (BA Terbit)</option>
                    </select>
                </div>

                @if(auth()->user()->isPimpinanOrAdmin())
                <div class="sm:col-span-3">
                    <select name="irban_id" onchange="this.form.submit()" class="w-full text-sm rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                        <option value="">-- Semua Irban --</option>
                        @foreach($irbans as $irb)
                            <option value="{{ $irb->id }}" {{ $irbanId == $irb->id ? 'selected' : '' }}>{{ $irb->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="ledger-btn-primary w-full text-sm">
                        Filter Tiket
                    </button>
                    @if($status || $irbanId || $search)
                        <a href="{{ route('konsultasi.index') }}" class="ledger-btn-secondary text-sm shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List Tiket Konsultasi -->
        <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-subtle border-b border-hairline text-ink-secondary font-semibold uppercase tracking-wider">
                            <th class="py-3.5 px-4">No. Tiket / Tanggal</th>
                            <th class="py-3.5 px-4">Pemohon & OPD Target</th>
                            <th class="py-3.5 px-4">Area & Permasalahan</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Status & Tim APIP</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline text-ink font-medium">
                        @forelse($listKonsultasi as $item)
                            <tr class="hover:bg-surface-subtle/60 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-blue-700 dark:text-blue-400 block">{{ $item->nomor_tiket }}</span>
                                    <span class="text-[11px] text-ink-muted tabular-nums block">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-ink block">{{ $item->objekPenugasan?->nama ?? $item->pemohon?->nama }}</span>
                                    <span class="text-[11px] text-ink-muted block">{{ $item->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50 rounded-[4px] font-semibold text-[10px] uppercase block w-max mb-1">
                                        {{ $item->area_konsultasi }}
                                    </span>
                                    <p class="font-semibold text-ink line-clamp-1">{{ $item->judul_permasalahan }}</p>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($item->metode_disetujui)
                                        <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold border {{ $item->metode_disetujui === 'online' ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50' }}">
                                            {{ $item->metode_disetujui === 'online' ? 'Online Chat' : 'Tatap Muka' }}
                                        </span>
                                    @else
                                        <span class="text-ink-muted text-[11px]">Usulan: {{ strtoupper($item->preferensi_metode) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold border block w-max mb-1
                                        {{ $item->status === 'menunggu_disposisi' ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50' : '' }}
                                        {{ $item->status === 'berjalan' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50' : '' }}
                                        {{ $item->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50' : '' }}">
                                        {{ $item->status_label }}
                                    </span>

                                    @if($item->timUsers->isNotEmpty())
                                        <span class="text-[11px] text-ink-muted font-medium block">
                                            Tim APIP: {{ $item->timUsers->pluck('nama')->implode(', ') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('konsultasi.show', $item->id) }}" class="px-3 py-1.5 bg-brand hover:bg-brand-hover text-white font-semibold text-xs rounded-[6px] inline-flex items-center gap-1 transition-colors">
                                        <span>Proses & Chat</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-ink-muted font-medium">
                                    Belum ada tiket permohonan konsultasi yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listKonsultasi->hasPages())
                <div class="p-4 border-t border-hairline">
                    {{ $listKonsultasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
