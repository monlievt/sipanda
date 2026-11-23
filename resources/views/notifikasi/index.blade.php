<x-app-layout>
    <x-slot name="header">
        Pusat Notifikasi
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Pusat Notifikasi & Pengingat</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Semua peringatan jadwal penugasan SPT, tindak lanjut, dan aktivitas pengawasan Anda.</p>
        </div>

        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifikasi.mark_all_read') }}">
                    @csrf
                    <button type="submit" class="ledger-btn-secondary text-xs">
                        <svg class="w-4 h-4 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Tandai Semua Telah Dibaca</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex border-b border-hairline gap-6 text-xs font-semibold">
        <a href="{{ route('notifikasi.index', ['filter' => 'semua']) }}" class="pb-3 border-b-2 transition-colors flex items-center gap-2 {{ $filter === 'semua' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
            <span>Semua Notifikasi</span>
        </a>
        <a href="{{ route('notifikasi.index', ['filter' => 'belum_dibaca']) }}" class="pb-3 border-b-2 transition-colors flex items-center gap-2 {{ $filter === 'belum_dibaca' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-700 text-white tabular-nums">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Notification Cards List -->
    <div class="space-y-3">
        @forelse($listNotifikasi as $item)
            <div class="p-4 rounded-[10px] border transition-colors flex items-start justify-between gap-4 {{ $item->is_read ? 'bg-surface border-hairline' : 'bg-brand-soft/40 border-brand-border' }}">
                <div class="flex items-start gap-3.5">
                    <!-- Notification Icon -->
                    <div class="w-10 h-10 rounded-[6px] border flex items-center justify-center shrink-0
                        {{ str_contains($item->jenis, 'reminder') ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50' : '' }}
                        {{ str_contains($item->jenis, 'bukti') ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50' : '' }}
                        {{ !str_contains($item->jenis, 'reminder') && !str_contains($item->jenis, 'bukti') ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50' : '' }}">
                        @if(str_contains($item->jenis, 'h1'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif(str_contains($item->jenis, 'h3'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @elseif(str_contains($item->jenis, 'bukti'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-semibold text-ink text-xs">
                                {{ $item->judul ?: ucfirst(str_replace('_', ' ', $item->jenis)) }}
                            </h4>
                            @if(! $item->is_read)
                                <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-red-700 text-white">
                                    BARU
                                </span>
                            @endif
                            <span class="text-[11px] text-ink-muted tabular-nums">
                                &bull; {{ $item->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-ink-secondary leading-relaxed">
                            {{ $item->pesan }}
                        </p>
                        @if($item->penugasan)
                            <div class="pt-1">
                                <a href="{{ route('notifikasi.read', $item->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-ink hover:underline">
                                    <span>Buka SPT: {{ $item->penugasan->no_spt }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1.5 shrink-0">
                    @if(! $item->is_read)
                        <a href="{{ route('notifikasi.read', $item->id) }}" class="p-1.5 bg-surface hover:bg-surface-subtle border border-hairline text-brand-ink rounded-[6px] text-xs transition-colors" title="Tandai Sudah Dibaca">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('notifikasi.destroy', $item->id) }}" onsubmit="return confirm('Hapus notifikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 bg-surface hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-hairline text-ink-muted hover:text-rose-700 dark:hover:text-rose-300 rounded-[6px] text-xs transition-colors cursor-pointer" title="Hapus Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-surface rounded-[10px] border border-hairline">
                <div class="w-12 h-12 bg-surface-subtle border border-hairline text-ink-muted rounded-[6px] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="font-semibold text-ink text-sm">Tidak ada notifikasi</p>
                <p class="text-xs text-ink-muted mt-1">Anda sudah melihat seluruh pemberitahuan sistem.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $listNotifikasi->links() }}
    </div>
</x-app-layout>
