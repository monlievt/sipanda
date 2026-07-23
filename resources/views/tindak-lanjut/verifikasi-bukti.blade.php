<x-app-layout>
    <x-slot name="header">
        Verifikasi Bukti Tindak Lanjut Perangkat Daerah
    </x-slot>

    <!-- Header Actions & Navigation -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Persetujuan & Reviu Bukti OPD</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Peninjauan bukti tindak lanjut yang dikirimkan oleh Perangkat Daerah.</p>
        </div>

        <div>
            <a href="{{ route('tindak-lanjut.index') }}" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs inline-flex items-center gap-2 border border-slate-200 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Matriks LHP</span>
            </a>
        </div>
    </div>

    <!-- Table Bukti Tindak Lanjut -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 text-[11px]">
                    <tr>
                        <th class="py-3.5 px-3 w-8 text-center">No</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Pengirim (OPD)</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">No. SPT & Irban</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Rekomendasi Terkait</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Penjelasan OPD & Catatan Tim</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Lampiran Bukti</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Status Evaluasi</th>
                        <th class="py-3.5 px-4 text-center w-40 whitespace-nowrap">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listBukti as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-3 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            
                            <!-- Pengirim OPD -->
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $item->pengunggah?->name ?? 'Pengelola OPD' }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                            </td>

                            <!-- Clickable No. SPT -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="font-mono font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline block" title="Klik untuk membuka Halaman Detail LHP Lengkap">
                                    📄 {{ $item->tindakLanjut?->penugasan?->no_spt }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ $item->tindakLanjut?->penugasan?->irban?->nama_irban }}</span>
                            </td>

                            <!-- Clickable Rekomendasi Terkait -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="font-bold text-slate-800 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 line-clamp-2" title="Klik untuk melihat dialog LHP lengkap">
                                    {{ $item->tindakLanjut?->rekomendasi }}
                                </a>
                            </td>

                            <!-- Penjelasan OPD -->
                            <td class="py-3.5 px-4 space-y-1">
                                <p class="font-medium text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $item->catatan_opd ?? '-' }}</p>
                                @if($item->catatan_verifikasi)
                                    <div class="p-1.5 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 text-[10px] text-amber-900 dark:text-amber-300">
                                        <span class="font-bold block">Catatan Evaluasi Tim:</span>
                                        <span>{{ $item->catatan_verifikasi }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Lampiran Bukti File Download Link -->
                            <td class="py-3.5 px-4 text-center font-bold whitespace-nowrap">
                                @if($item->arsipDigital->count() > 0)
                                    <div class="flex flex-col items-center gap-1">
                                        @foreach($item->arsipDigital as $file)
                                            <a href="{{ route('arsip.download', $file->id) }}" target="_blank" class="px-2 py-1 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 hover:bg-blue-100 rounded-lg text-[10px] font-bold inline-flex items-center gap-1 border border-blue-200 dark:border-blue-800">
                                                📎 {{ Str::limit($file->nama_file, 15) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 font-normal italic">Tidak Ada File</span>
                                @endif
                            </td>

                            <!-- Status Evaluasi Badge -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border
                                    {{ $item->status_verifikasi === 'diterima' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200' : '' }}
                                    {{ $item->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border-rose-200' : '' }}
                                    {{ $item->status_verifikasi === 'tdt' ? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border-slate-300' : '' }}
                                    {{ $item->status_verifikasi === 'menunggu' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200' : '' }}">
                                    @if($item->status_verifikasi === 'diterima')
                                        Selesai (Diterima)
                                    @elseif($item->status_verifikasi === 'ditolak')
                                        Revisi OPD
                                    @elseif($item->status_verifikasi === 'tdt')
                                        TDT
                                    @else
                                        Belum Dievaluasi
                                    @endif
                                </span>
                            </td>

                            <!-- Action Buttons: Interactive Verification & Re-evaluation -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openModalEvaluasi({{ $item->id }}, '{{ $item->status_verifikasi }}', '{{ addslashes($item->catatan_verifikasi ?? '') }}')" class="px-2.5 py-1 bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300 hover:bg-amber-100 rounded-lg text-[10px] font-bold border border-amber-300 flex items-center gap-1 shadow-xs">
                                        <span>✍️ Evaluasi</span>
                                    </button>

                                    <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-bold shadow-xs flex items-center gap-1" title="Buka Halaman Detail LHP ini">
                                        <span>📄 Detail LHP</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada pengajuan bukti tindak lanjut dari Perangkat Daerah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Evaluasi & Reviu Bukti -->
    <div id="modalEvaluasiBukti" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Evaluasi & Verifikasi Bukti OPD</h3>
                <button onclick="document.getElementById('modalEvaluasiBukti').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEvaluasiBukti" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Hasil Evaluasi Tim Pemeriksa <span class="text-rose-500">*</span></label>
                    <select id="evalStatusVerifikasi" name="status_verifikasi" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold">
                        <option value="diterima">Selesai Evaluasi (Diterima oleh Tim)</option>
                        <option value="ditolak">Memerlukan Perbaikan (Minta Revisi OPD)</option>
                        <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI (TDT)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Keterangan / Catatan Kekurangan untuk OPD</label>
                    <textarea id="evalCatatanVerifikasi" name="catatan_verifikasi" rows="3" placeholder="Tuliskan catatan penjelasan evaluasi atau kekurangan yang harus diperbaiki OPD..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-amber-500 font-medium"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEvaluasiBukti').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-md">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalEvaluasi(id, currentStatus, currentCatatan) {
            const form = document.getElementById('formEvaluasiBukti');
            form.action = '/bukti-tindak-lanjut/' + id + '/verifikasi';
            document.getElementById('evalStatusVerifikasi').value = currentStatus === 'menunggu' ? 'diterima' : currentStatus;
            document.getElementById('evalCatatanVerifikasi').value = currentCatatan;
            document.getElementById('modalEvaluasiBukti').classList.remove('hidden');
        }
    </script>
</x-app-layout>
