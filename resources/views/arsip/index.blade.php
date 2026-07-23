<x-app-layout>
    <x-slot name="header">
        Modul Arsip Digital Laporan & Dokumen
    </x-slot>

    <!-- Header Actions & Modal Upload -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Penyimpanan Berkas Digital Terpusat</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Dokumen Surat Tugas, Laporan Hasil, & Lampiran Tindak Lanjut.</p>
        </div>

        <button onclick="document.getElementById('modalUploadArsip').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>Unggah Berkas Baru</span>
        </button>
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
                        <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listArsip as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $item->nama_file }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $item->penugasan?->no_spt ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $item->pengunggah?->nama_display }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $item->ukuran_kb }}</td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('arsip.download', $item->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg dark:hover:bg-blue-950/50" title="Unduh">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('arsip.destroy', $item->id) }}" onsubmit="return confirm('Hapus berkas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg dark:hover:bg-rose-950/50" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada berkas tersimpan di Arsip Digital.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Upload Arsip -->
    <div id="modalUploadArsip" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Unggah Berkas Baru</h3>
                <button onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('arsip.store') }}" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">File Berkas (PDF, DOCX, XLSX, JPG, PNG — Max 10MB) <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs p-2">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Kategori Berkas <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="Surat Tugas">Surat Tugas (SPT)</option>
                        <option value="Laporan Hasil">Laporan Hasil Pengawasan</option>
                        <option value="Bukti Tindak Lanjut">Bukti Tindak Lanjut</option>
                        <option value="DokumenPendukung">Dokumen Pendukung Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Tautkan ke Penugasan (SPT) (Opsional)</label>
                    <select name="penugasan_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="">-- Tidak Ditautkan --</option>
                        @foreach($penugasanList as $p)
                            <option value="{{ $p->id }}">{{ $p->no_spt }} — {{ Str::limit($p->uraian_penugasan, 40) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalUploadArsip').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
