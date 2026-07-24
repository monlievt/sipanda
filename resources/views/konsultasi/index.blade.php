<x-app-layout>
    <x-slot name="header">
        Layanan Konsultasi & Advisory APIP (E-Consulting QnA)
    </x-slot>

    <div class="space-y-6">
        <!-- Header Title & Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Daftar Tiket Konsultasi & QnA APIP</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola permohonan konsultasi online/tatap muka dari Perangkat Daerah (OPD), disposisi tim APIP, dan penerbitan Berita Acara.</p>
            </div>

            <a href="{{ route('faq.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs inline-flex items-center gap-2 transition-all">
                📚 Bank FAQ / QnA Publik &rarr;
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg">
                    ⏳
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $countMenunggu }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Menunggu Disposisi Irban</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-lg">
                    💬
                </div>
                <div>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $countBerjalan }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Sedang Berjalan / Tim Ditunjuk</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    ✅
                </div>
                <div>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $countSelesai }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Selesai (Berita Acara Terbit)</p>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <form method="GET" action="{{ route('konsultasi.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-3">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Tiket / Permasalahan..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                </div>

                <div class="sm:col-span-3">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                        <option value="">-- Semua Status --</option>
                        <option value="menunggu_disposisi" {{ $status === 'menunggu_disposisi' ? 'selected' : '' }}>Menunggu Disposisi Irban</option>
                        <option value="berjalan" {{ $status === 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai (BA Terbit)</option>
                    </select>
                </div>

                @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
                <div class="sm:col-span-3">
                    <select name="irban_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                        <option value="">-- Semua Irban --</option>
                        @foreach($irbans as $irb)
                            <option value="{{ $irb->id }}" {{ $irbanId == $irb->id ? 'selected' : '' }}>{{ $irb->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs">
                        Filter Tiket
                    </button>
                    @if($status || $irbanId || $search)
                        <a href="{{ route('konsultasi.index') }}" class="px-3 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 text-xs font-bold rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List Tiket Konsultasi -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">No. Tiket / Tanggal</th>
                            <th class="py-3.5 px-4">Pemohon & OPD Target</th>
                            <th class="py-3.5 px-4">Area & Permasalahan</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Status & Tim APIP</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                        @forelse($listKonsultasi as $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-blue-600 dark:text-blue-400 block">{{ $item->nomor_tiket }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $item->objekPenugasan?->nama ?? $item->pemohon?->nama }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $item->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 rounded font-bold text-[10px] uppercase block w-max mb-1">
                                        {{ $item->area_konsultasi }}
                                    </span>
                                    <p class="font-bold text-slate-900 dark:text-white line-clamp-1">{{ $item->judul_permasalahan }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->metode_disetujui)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $item->metode_disetujui === 'online' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                            {{ $item->metode_disetujui === 'online' ? '💬 Online Chat' : '🤝 Tatap Muka' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Usulan: {{ strtoupper($item->preferensi_metode) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider block w-max mb-1
                                        {{ $item->status === 'menunggu_disposisi' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}
                                        {{ $item->status === 'berjalan' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                                        {{ $item->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}">
                                        {{ $item->status_label }}
                                    </span>

                                    @if($item->timUsers->isNotEmpty())
                                        <span class="text-[10px] text-slate-500 font-semibold block">
                                            Tim APIP: {{ $item->timUsers->pluck('nama')->implode(', ') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('konsultasi.show', $item->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1">
                                        <span>Proses & Chat</span> &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                                    Belum ada tiket permohonan konsultasi yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listKonsultasi->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $listKonsultasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
