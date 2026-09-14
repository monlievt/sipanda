<x-app-layout>
    <x-slot name="header">
        Ikhtisar Laporan Hasil Pengawasan (ILHP) — Laporan Berkala Bupati
    </x-slot>

    <div class="space-y-6">
        <!-- Header Actions & Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Daftar Dokumen Ikhtisar Laporan (ILHP)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Generator otomatis laporan eksekutif Triwulanan, Semesteran, dan Tahunan untuk Bupati Trenggalek.</p>
            </div>

            @hasanyrole('admin|sekretariat|superadmin')
            <div class="flex items-center gap-2">
                <a href="{{ route('ikhtisar-laporan.create', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Generate Ikhtisar Baru</span>
                </a>
            </div>
            @endhasanyrole
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Filter Form -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <form method="GET" action="{{ route('ikhtisar-laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-center">
                <div class="sm:col-span-4">
                    <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-emerald-500">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Periode Laporan</label>
                    <select name="periode" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-emerald-500">
                        <option value="">-- Semua Periode --</option>
                        <option value="triwulan_1" {{ $periode === 'triwulan_1' ? 'selected' : '' }}>Triwulan I (Jan – Mar)</option>
                        <option value="triwulan_2" {{ $periode === 'triwulan_2' ? 'selected' : '' }}>Triwulan II (Apr – Jun)</option>
                        <option value="triwulan_3" {{ $periode === 'triwulan_3' ? 'selected' : '' }}>Triwulan III (Jul – Sep)</option>
                        <option value="triwulan_4" {{ $periode === 'triwulan_4' ? 'selected' : '' }}>Triwulan IV (Okt – Des)</option>
                        <option value="semester_1" {{ $periode === 'semester_1' ? 'selected' : '' }}>Semester I (Jan – Jun)</option>
                        <option value="semester_2" {{ $periode === 'semester_2' ? 'selected' : '' }}>Semester II (Jul – Des)</option>
                        <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan (Jan – Des)</option>
                    </select>
                </div>

                <div class="sm:col-span-4 flex items-end gap-2 pt-5 sm:pt-0">
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        Terapkan Filter
                    </button>
                    @if($periode)
                        <a href="{{ route('ikhtisar-laporan.index', ['tahun' => $tahun]) }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List Dokumen -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5">Dokumen Ikhtisar</th>
                            <th class="px-4 py-3.5">Periode & Tahun</th>
                            <th class="px-4 py-3.5">Tanggal Ditetapkan</th>
                            <th class="px-4 py-3.5">Penyusun / Kasubag</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($listLaporan as $lap)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">
                                        <a href="{{ route('ikhtisar-laporan.show', $lap) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">
                                            {{ $lap->judul }}
                                        </a>
                                    </div>
                                    @if($lap->nomor_surat)
                                        <p class="text-[11px] text-slate-400 mt-0.5 font-mono">No: {{ $lap->nomor_surat }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 rounded font-semibold text-[11px]">
                                        {{ $lap->periode_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    {{ $lap->tanggal_laporan ? $lap->tanggal_laporan->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    {{ $lap->pembuat?->nama_display ?? 'Administrator' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($lap->status === 'final')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 rounded-full font-bold text-[10px]">
                                            ✓ FINAL RESMI
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 rounded-full font-bold text-[10px]">
                                            📝 DRAF KONSEP
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                    <a href="{{ route('ikhtisar-laporan.show', $lap) }}" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs">
                                        Lihat
                                    </a>
                                    <a href="{{ route('ikhtisar-laporan.cetak', $lap) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-xs">
                                        🖨️ Cetak / PDF
                                    </a>
                                    @hasanyrole('admin|sekretariat|superadmin')
                                    <a href="{{ route('ikhtisar-laporan.edit', $lap) }}" class="px-2 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold">
                                        Edit
                                    </a>
                                    @endhasanyrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto mb-2 text-slate-300 dark:text-slate-700">📑</div>
                                    <p class="font-bold text-slate-600 dark:text-slate-300">Belum ada dokumen Ikhtisar Hasil Pengawasan (ILHP) tersimpan untuk tahun {{ $tahun }}.</p>
                                    <p class="text-[11px] mt-1">Klik tombol <span class="font-bold text-emerald-600">"Generate Ikhtisar Baru"</span> di atas untuk mengompilasi laporan berkala secara instan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listLaporan->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $listLaporan->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
