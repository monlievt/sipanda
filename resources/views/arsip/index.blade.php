<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Penyimpanan Berkas Digital Terpusat</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Repositori Dokumen Surat Tugas, Laporan Hasil Pengawasan (LHP), dan Berkas Bukti Tindak Lanjut.</p>
            </div>
            <button onclick="document.getElementById('modalUploadArsip').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Unggah Berkas Baru</span>
            </button>
        </div>
    </x-slot>

    <!-- Ringkasan Statistik Berkas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 flex items-center justify-center font-bold text-lg">
                📁
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Berkas</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalBerkas) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 flex items-center justify-center font-bold text-lg">
                📜
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500">Laporan Hasil (LHP)</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalLhp) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center font-bold text-lg">
                📑
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500">Bukti Tindak Lanjut</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalBuktiTl) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 flex items-center justify-center font-bold text-lg">
                🗂️
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-500">SPT & Dokumen Lain</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalSptLain) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Berkas -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('arsip.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end text-xs">
            <!-- Search Bar -->
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-500 uppercase mb-1">Cari Nama File / No. SPT / Pengunggah</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="w-full pl-9 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Kategori Filter -->
            <div>
                <label class="block font-bold text-slate-500 uppercase mb-1">Kategori Berkas</label>
                <select name="kategori" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Laporan Hasil" {{ $kategori === 'Laporan Hasil' ? 'selected' : '' }}>Laporan Hasil Pengawasan (LHP)</option>
                    <option value="Bukti Tindak Lanjut" {{ $kategori === 'Bukti Tindak Lanjut' ? 'selected' : '' }}>Bukti Tindak Lanjut OPD</option>
                    <option value="Surat Tugas" {{ $kategori === 'Surat Tugas' ? 'selected' : '' }}>Surat Tugas (SPT)</option>
                    <option value="DokumenPendukung" {{ $kategori === 'DokumenPendukung' ? 'selected' : '' }}>Dokumen Pendukung Lainnya</option>
                </select>
            </div>

            <!-- Tahun Filter -->
            <div>
                <label class="block font-bold text-slate-500 uppercase mb-1">Tahun Dokumen</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ (string)$tahun === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all cursor-pointer">
                    Cari Berkas
                </button>
                @if($search || $kategori || $tahun || $irbanId)
                    <a href="{{ route('arsip.index') }}" class="py-2.5 px-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Arsip Digital -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama File Berkas</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Penugasan Terkait</th>
                        <th class="py-3.5 px-4">Pengunggah</th>
                        <th class="py-3.5 px-4">Ukuran</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Tanggal Unggah</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listArsip as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $listArsip->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white max-w-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">
                                        @if(str_contains(strtolower($item->nama_file), '.pdf'))
                                            📕
                                        @elseif(str_contains(strtolower($item->nama_file), '.doc'))
                                            📘
                                        @elseif(str_contains(strtolower($item->nama_file), '.xls'))
                                            📗
                                        @elseif(str_contains(strtolower($item->nama_file), '.jpg') || str_contains(strtolower($item->nama_file), '.png'))
                                            🖼️
                                        @else
                                            📄
                                        @endif
                                    </span>
                                    <span class="truncate block font-semibold" title="{{ $item->nama_file }}">{{ $item->nama_file }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ str_contains($item->kategori, 'Laporan') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                                    {{ str_contains($item->kategori, 'Bukti') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                    {{ str_contains($item->kategori, 'Surat') ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : '' }}
                                    {{ !str_contains($item->kategori, 'Laporan') && !str_contains($item->kategori, 'Bukti') && !str_contains($item->kategori, 'Surat') ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : '' }}">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->penugasan)
                                    <a href="{{ route('penugasan.show', $item->penugasan->id) }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline block">
                                        {{ $item->penugasan->no_spt }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 truncate max-w-xs block">{{ Str::limit($item->penugasan->uraian_penugasan, 35) }}</span>
                                @else
                                    <span class="text-slate-400 italic">Umum / Tidak Ditautkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ $item->pengunggah?->nama ?? $item->pengunggah?->email ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono whitespace-nowrap">{{ $item->ukuran_kb }}</td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('arsip.preview', $item->id) }}" target="_blank" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-lg transition-colors cursor-pointer" title="Buka / Preview Berkas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('arsip.download', $item->id) }}" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors cursor-pointer" title="Unduh Berkas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris']) || $item->diunggah_oleh == auth()->id())
                                        <form method="POST" action="{{ route('arsip.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas arsip {{ addslashes($item->nama_file) }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors cursor-pointer" title="Hapus Berkas">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <span class="text-3xl block">📭</span>
                                    <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada berkas yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-400">Pastikan kata kunci pencarian sesuai atau Anda memiliki izin tugas pada penugasan terkait.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $listArsip->links() }}
        </div>
    </div>

    <!-- Modal Upload Arsip -->
    <div id="modalUploadArsip" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Unggah Berkas ke Arsip Digital</h3>
                    <p class="text-[11px] text-slate-500">Simpan dokumen penting pengawasan secara terpusat dan aman.</p>
                </div>
                <button onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('arsip.store') }}" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">Pilih File Dokumen <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, DOCX, XLSX, JPG, PNG (Maks 25 MB).</span>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Kategori Berkas <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="Laporan Hasil">Laporan Hasil Pengawasan (LHP)</option>
                        <option value="Bukti Tindak Lanjut">Bukti Tindak Lanjut OPD</option>
                        <option value="Surat Tugas">Surat Perintah Tugas (SPT)</option>
                        <option value="DokumenPendukung">Dokumen Pendukung Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Tautkan ke Penugasan (SPT) (Opsional)</label>
                    <select name="penugasan_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Tidak Ditautkan (Arsip Umum) --</option>
                        @foreach($penugasanList as $p)
                            <option value="{{ $p->id }}">{{ $p->no_spt }} — {{ Str::limit($p->uraian_penugasan, 45) }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-400 mt-1 block">Hanya menampilkan SPT yang berada dalam lingkup wewenang tugas Anda.</span>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Unggah & Simpan Berkas</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
