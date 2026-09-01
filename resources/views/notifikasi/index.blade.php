<x-app-layout>
    <x-slot name="header">
        Pusat Notifikasi
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Pusat Notifikasi & Pengingat</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Semua peringatan jadwal penugasan SPT, tindak lanjut, dan aktivitas pengawasan Anda.</p>
        </div>

        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifikasi.mark_all_read') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Tandai Semua Telah Dibaca</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex border-b border-slate-200 dark:border-slate-800 gap-6 text-xs font-bold">
        <a href="{{ route('notifikasi.index', ['filter' => 'semua']) }}" class="pb-3 border-b-2 transition-all flex items-center gap-2 {{ $filter === 'semua' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <span>Semua Notifikasi</span>
        </a>
        <a href="{{ route('notifikasi.index', ['filter' => 'belum_dibaca']) }}" class="pb-3 border-b-2 transition-all flex items-center gap-2 {{ $filter === 'belum_dibaca' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Notification Cards List -->
    <div class="space-y-3">
        @forelse($listNotifikasi as $item)
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border transition-all flex items-start justify-between gap-4 {{ $item->is_read ? 'border-slate-200 dark:border-slate-800 opacity-80' : 'border-emerald-300 dark:border-emerald-800 bg-emerald-50/20 dark:bg-emerald-950/10 shadow-sm' }}">
                <div class="flex items-start gap-3.5">
                    <!-- Notification Icon -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold
                        {{ str_contains($item->jenis, 'reminder') ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : '' }}
                        {{ str_contains($item->jenis, 'bukti') ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : '' }}
                        {{ !str_contains($item->jenis, 'reminder') && !str_contains($item->jenis, 'bukti') ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : '' }}">
                        @if(str_contains($item->jenis, 'h1'))
                            ⏰
                        @elseif(str_contains($item->jenis, 'h3'))
                            📅
                        @elseif(str_contains($item->jenis, 'bukti'))
                            📑
                        @else
                            🔔
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs">
                                {{ $item->judul ?: ucfirst(str_replace('_', ' ', $item->jenis)) }}
                            </h4>
                            @if(! $item->is_read)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white">
                                    BARU
                                </span>
                            @endif
                            <span class="text-[10px] text-slate-400">
                                • {{ $item->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ $item->pesan }}
                        </p>
                        @if($item->penugasan)
                            <div class="pt-1">
                                <a href="{{ route('notifikasi.read', $item->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    <span>Buka SPT: {{ $item->penugasan->no_spt }} &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    @if(! $item->is_read)
                        <a href="{{ route('notifikasi.read', $item->id) }}" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 rounded-lg text-xs" title="Tandai Sudah Dibaca">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('notifikasi.destroy', $item->id) }}" onsubmit="return confirm('Hapus notifikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg text-xs cursor-pointer" title="Hapus Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="font-bold text-slate-800 dark:text-white text-sm">Tidak ada notifikasi</p>
                <p class="text-xs text-slate-400 mt-1">Anda sudah melihat seluruh pemberitahuan sistem.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $listNotifikasi->links() }}
    </div>
</x-app-layout>
