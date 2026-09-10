<x-app-layout>
    <x-slot name="header">
        Edit Dokumen Ikhtisar Laporan (ILHP)
    </x-slot>

    <div class="space-y-6">
        <div>
            <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                &larr; Kembali ke Tampilan Dokumen
            </a>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Edit Narasi & Parameter Dokumen</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sesuaikan judul, nomor surat dinas, dan catatan rumusan evaluasi pada BAB V.</p>
        </div>

        <form method="POST" action="{{ route('ikhtisar-laporan.update', $ikhtisarLaporan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-7">
                        <label class="block font-semibold mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $ikhtisarLaporan->judul) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold mb-1">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $ikhtisarLaporan->nomor_surat) }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500 font-mono">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold mb-1">Tanggal Dokumen <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">A. Simpulan Eksekutif</label>
                        <textarea name="simpulan" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('simpulan', $ikhtisarLaporan->simpulan) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">B. Hambatan & Kendala</label>
                        <textarea name="hambatan" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('hambatan', $ikhtisarLaporan->hambatan) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">C. Rekomendasi untuk Bupati</label>
                        <textarea name="rekomendasi" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('rekomendasi', $ikhtisarLaporan->rekomendasi) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Dokumen</label>
                        <select name="status" class="w-full sm:w-64 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                            <option value="draft" {{ $ikhtisarLaporan->status === 'draft' ? 'selected' : '' }}>📝 Draf Konsep</option>
                            <option value="final" {{ $ikhtisarLaporan->status === 'final' ? 'selected' : '' }}>✓ Final Resmi Siap Cetak</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 text-xs font-bold rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
