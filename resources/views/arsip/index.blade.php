<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-ink tracking-tight">Penyimpanan Berkas Digital Terpusat</h1>
                <p class="text-xs text-ink-secondary mt-0.5">Repositori Dokumen Surat Tugas, Laporan Hasil Pengawasan (LHP), dan Berkas Bukti Tindak Lanjut.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalUploadArsip').classList.remove('hidden')" class="ledger-btn-primary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Unggah Berkas Baru</span>
            </button>
        </div>
    </x-slot>

    <!-- Ringkasan Statistik Berkas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface rounded-[10px] p-4 border border-hairline flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-[6px] bg-surface-subtle text-ink-secondary border border-hairline flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted">Total Berkas</span>
                <p class="text-xl font-bold text-ink tabular-nums mt-0.5">{{ number_format($totalBerkas) }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-4 border border-hairline flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-[6px] bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Laporan Hasil (LHP)</span>
                <p class="text-xl font-bold text-ink tabular-nums mt-0.5">{{ number_format($totalLhp) }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-4 border border-hairline flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-brand-ink">Bukti Tindak Lanjut</span>
                <p class="text-xl font-bold text-ink tabular-nums mt-0.5">{{ number_format($totalBuktiTl) }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-4 border border-hairline flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-[6px] bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">SPT & Dokumen Lain</span>
                <p class="text-xl font-bold text-ink tabular-nums mt-0.5">{{ number_format($totalSptLain) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Berkas -->
    <div class="mb-6 bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
        <form method="GET" action="{{ route('arsip.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end text-xs">
            <!-- Search Bar -->
            <div class="lg:col-span-2">
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Cari Nama File / No. SPT / Pengunggah</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-ink-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." style="padding-left: 2.25rem !important;" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs py-2.5">
                </div>
            </div>

            <!-- Kategori Filter -->
            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Kategori Berkas</label>
                <select name="kategori" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-medium px-3 py-2.5">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Laporan Hasil" {{ $kategori === 'Laporan Hasil' ? 'selected' : '' }}>Laporan Hasil Pengawasan (LHP)</option>
                    <option value="Bukti Tindak Lanjut" {{ $kategori === 'Bukti Tindak Lanjut' ? 'selected' : '' }}>Bukti Tindak Lanjut OPD</option>
                    <option value="Surat Tugas" {{ $kategori === 'Surat Tugas' ? 'selected' : '' }}>Surat Tugas (SPT)</option>
                    <option value="DokumenPendukung" {{ $kategori === 'DokumenPendukung' ? 'selected' : '' }}>Dokumen Pendukung Lainnya</option>
                </select>
            </div>

            <!-- Tahun Filter -->
            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Tahun Dokumen</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-medium px-3 py-2.5">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ (string)$tahun === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="ledger-btn-primary w-full text-xs">
                    Cari Berkas
                </button>
                @if($search || $kategori || $tahun || $irbanId)
                    <a href="{{ route('arsip.index') }}" class="ledger-btn-secondary text-xs shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Arsip Digital -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
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
                <tbody class="divide-y divide-hairline">
                    @forelse($listArsip as $index => $item)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <td class="py-3 px-4 font-medium text-center text-ink-muted tabular-nums">{{ $listArsip->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-semibold text-ink max-w-xs">
                                <div class="flex items-center gap-2">
                                    @if(str_contains(strtolower($item->nama_file), '.pdf'))
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50 shrink-0">PDF</span>
                                    @elseif(str_contains(strtolower($item->nama_file), '.doc'))
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50 shrink-0">DOC</span>
                                    @elseif(str_contains(strtolower($item->nama_file), '.xls'))
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50 shrink-0">XLS</span>
                                    @elseif(str_contains(strtolower($item->nama_file), '.jpg') || str_contains(strtolower($item->nama_file), '.png'))
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50 shrink-0">IMG</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-surface-subtle text-ink-secondary border border-hairline shrink-0">FILE</span>
                                    @endif
                                    <span class="truncate block font-semibold text-ink" title="{{ $item->nama_file }}">{{ $item->nama_file }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold border
                                    {{ str_contains($item->kategori, 'Laporan') ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50' : '' }}
                                    {{ str_contains($item->kategori, 'Bukti') ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50' : '' }}
                                    {{ str_contains($item->kategori, 'Surat') ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50' : '' }}
                                    {{ !str_contains($item->kategori, 'Laporan') && !str_contains($item->kategori, 'Bukti') && !str_contains($item->kategori, 'Surat') ? 'bg-surface-subtle text-ink-secondary border-hairline' : '' }}">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->penugasan)
                                    <a href="{{ route('penugasan.show', $item->penugasan->id) }}" class="font-semibold text-brand-ink hover:underline block">
                                        {{ $item->penugasan->no_spt }}
                                    </a>
                                    <span class="text-[11px] text-ink-muted truncate max-w-xs block">{{ Str::limit($item->penugasan->uraian_penugasan, 35) }}</span>
                                @else
                                    <span class="text-ink-muted italic">Umum / Tidak Ditautkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-ink-secondary whitespace-nowrap">
                                {{ $item->pengunggah?->nama ?? $item->pengunggah?->email ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4 text-ink-muted font-mono tabular-nums whitespace-nowrap">{{ $item->ukuran_kb }}</td>
                            <td class="py-3 px-4 whitespace-nowrap text-ink-muted tabular-nums">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('arsip.preview', $item->id) }}" target="_blank" class="p-1.5 bg-surface-subtle hover:bg-hairline text-ink-secondary border border-hairline rounded-[6px] transition-colors cursor-pointer" title="Buka / Preview Berkas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('arsip.download', $item->id) }}" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:hover:bg-blue-900/60 dark:text-blue-300 dark:border-blue-800/50 rounded-[6px] transition-colors cursor-pointer" title="Unduh Berkas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris']) || $item->diunggah_oleh == auth()->id())
                                        <form method="POST" action="{{ route('arsip.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas arsip {{ addslashes($item->nama_file) }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:text-rose-300 dark:border-rose-800/50 rounded-[6px] transition-colors cursor-pointer" title="Hapus Berkas">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-ink-muted">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-[6px] bg-surface-subtle border border-hairline text-ink-muted flex items-center justify-center mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-ink">Tidak ada berkas yang ditemukan.</p>
                                    <p class="text-[11px] text-ink-muted">Pastikan kata kunci pencarian sesuai atau Anda memiliki izin tugas pada penugasan terkait.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline">
            {{ $listArsip->links() }}
        </div>
    </div>

    <!-- Modal Upload Arsip -->
    <div id="modalUploadArsip" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-semibold text-ink text-[15px]">Unggah Berkas ke Arsip Digital</h3>
                    <p class="text-[11px] text-ink-muted mt-0.5">Simpan dokumen penting pengawasan secara terpusat dan aman.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer px-1">&times;</button>
            </div>

            <form method="POST" action="{{ route('arsip.store') }}" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-ink mb-1.5">Pilih File Dokumen <span class="text-rose-700 dark:text-rose-400">*</span></label>
                    <input type="file" name="file" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs p-2 file:mr-2 file:py-1.5 file:px-3 file:rounded-[6px] file:border file:border-brand-border file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                    <span class="text-[11px] text-ink-muted mt-1 block">Format: PDF, DOCX, XLSX, JPG, PNG (Maks 25 MB).</span>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1.5">Kategori Berkas <span class="text-rose-700 dark:text-rose-400">*</span></label>
                    <select name="kategori" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-medium px-3 py-2.5">
                        <option value="Laporan Hasil">Laporan Hasil Pengawasan (LHP)</option>
                        <option value="Bukti Tindak Lanjut">Bukti Tindak Lanjut OPD</option>
                        <option value="Surat Tugas">Surat Perintah Tugas (SPT)</option>
                        <option value="DokumenPendukung">Dokumen Pendukung Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1.5">Tautkan ke Penugasan (SPT) (Opsional)</label>
                    <select name="penugasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3 py-2.5">
                        <option value="">-- Tidak Ditautkan (Arsip Umum) --</option>
                        @foreach($penugasanList as $p)
                            <option value="{{ $p->id }}">{{ $p->no_spt }} | {{ Str::limit($p->uraian_penugasan, 45) }}</option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-ink-muted mt-1 block">Hanya menampilkan SPT yang berada dalam lingkup wewenang tugas Anda.</span>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="ledger-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="ledger-btn-primary text-xs">Unggah & Simpan Berkas</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
