<x-app-layout>
    <x-slot name="header">
        Bank FAQ / QnA Publik & Pusat Informasi Pengawasan APIP
    </x-slot>

    <div class="space-y-6">
        <!-- Banner Header -->
        <div class="p-6 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl text-white shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="px-3 py-1 bg-white/20 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider">Pusat Informasi & Edukasi APIP</span>
                <h2 class="text-xl font-black tracking-tight">Bank FAQ & Tanya Jawab Pengawasan</h2>
                <p class="text-xs text-blue-100 max-w-2xl">Kumpulan artikel tanya jawab, solusi permasalahan keuangan/pengawasan, dan advis resmi Inspektorat yang telah dipublikasikan untuk dipelajari seluruh OPD.</p>
            </div>

            <a href="{{ route('konsultasi.index') }}" class="px-4 py-2.5 bg-white text-blue-800 hover:bg-blue-50 text-xs font-bold rounded-xl shadow-xs shrink-0 text-center">
                💬 Ajukan Konsultasi APIP &rarr;
            </a>
        </div>

        <!-- Filter & Search FAQ -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <form method="GET" action="{{ route('faq.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari topik kata kunci (mis. Pengadaan, APBD, Dana Desa, Asset)..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                </div>

                <div class="sm:col-span-4">
                    <select name="area" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                        <option value="">-- Semua Area Konsultasi --</option>
                        <option value="Pengadaan Barang & Jasa" {{ $area === 'Pengadaan Barang & Jasa' ? 'selected' : '' }}>Pengadaan Barang & Jasa</option>
                        <option value="Keuangan APBD/APBDes" {{ $area === 'Keuangan APBD/APBDes' ? 'selected' : '' }}>Keuangan APBD/APBDes</option>
                        <option value="Tata Kelola Desa & Alokasi Dana" {{ $area === 'Tata Kelola Desa & Alokasi Dana' ? 'selected' : '' }}>Tata Kelola Desa</option>
                        <option value="Aset & Barang Milik Daerah (BMD)" {{ $area === 'Aset & Barang Milik Daerah (BMD)' ? 'selected' : '' }}>Aset & BMD</option>
                        <option value="Akuntabilitas Kinerja (SAKIP/LAKIP)" {{ $area === 'Akuntabilitas Kinerja (SAKIP/LAKIP)' ? 'selected' : '' }}>Akuntabilitas Kinerja</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Cari Artikel
                    </button>
                </div>
            </form>
        </div>

        <!-- Grid Cards FAQ Artikel -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($faqs as $item)
                <div class="p-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3 flex flex-col justify-between" x-data="{ open: false }">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-extrabold text-[10px] rounded-lg uppercase">
                                {{ $item->area_konsultasi }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">Dipublikasikan: {{ $item->updated_at->format('d/m/Y') }}</span>
                        </div>

                        <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-snug">{{ $item->judul_permasalahan }}</h3>

                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl text-xs space-y-1">
                            <span class="font-bold text-slate-500 text-[11px] block">Tanya / Permasalahan:</span>
                            <p class="text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">{{ $item->uraian_permasalahan }}</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="open = !open" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center justify-between w-full">
                            <span x-text="open ? 'Sembunyikan Advis APIP ▲' : 'Lihat Advis & Solusi APIP ▼'"></span>
                        </button>

                        <div x-show="open" x-cloak class="mt-3 p-4 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-900 text-xs space-y-1">
                            <span class="font-bold text-emerald-900 dark:text-emerald-200 block">💡 Jawaban & Advis Resmi APIP:</span>
                            <p class="text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $item->kesimpulan_advis }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-center space-y-2">
                    <p class="text-slate-400 text-xs font-semibold">Belum ada artikel QnA / FAQ publik yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if($faqs->hasPages())
            <div class="pt-4">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
