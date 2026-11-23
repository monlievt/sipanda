<x-app-layout>
    <x-slot name="header">
        Verifikasi Bukti Tindak Lanjut Perangkat Daerah
    </x-slot>

    <!-- Header Actions & Navigation -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Persetujuan & Reviu Bukti OPD</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Peninjauan bukti tindak lanjut yang dikirimkan oleh Perangkat Daerah.</p>
        </div>

        <div>
            <a href="{{ route('tindak-lanjut.index') }}" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Matriks LHP</span>
            </a>
        </div>
    </div>

    <!-- Table Bukti Tindak Lanjut -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline text-[11px]">
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
                <tbody class="divide-y divide-hairline">
                    @forelse($listBukti as $index => $item)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <td class="py-3.5 px-3 font-semibold text-center text-ink-muted tabular-nums">{{ $index + 1 }}</td>

                            <!-- Pengirim OPD -->
                            <td class="py-3.5 px-4 font-semibold text-ink">
                                {{ $item->pengunggah?->name ?? 'Pengelola OPD' }}
                                <span class="block text-[11px] text-ink-muted font-normal tabular-nums">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                            </td>

                            <!-- Clickable No. SPT -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="font-mono font-semibold text-blue-800 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200 hover:underline block" title="Klik untuk membuka Halaman Detail LHP Lengkap">
                                    {{ $item->tindakLanjut?->penugasan?->no_spt }}
                                </a>
                                <span class="text-[11px] text-ink-muted font-medium block mt-0.5">{{ $item->tindakLanjut?->penugasan?->irban?->nama_irban }}</span>
                            </td>

                            <!-- Clickable Rekomendasi Terkait -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="font-semibold text-ink hover:text-brand-ink line-clamp-2" title="Klik untuk melihat dialog LHP lengkap">
                                    {{ $item->tindakLanjut?->rekomendasi }}
                                </a>
                            </td>

                            <!-- Penjelasan OPD -->
                            <td class="py-3.5 px-4 space-y-1.5">
                                <p class="font-normal text-ink leading-relaxed whitespace-pre-line">{{ $item->catatan_opd ?? '-' }}</p>
                                @if($item->catatan_verifikasi)
                                    <div class="p-2 bg-amber-50 dark:bg-amber-950/40 rounded-[6px] border border-amber-200 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-300">
                                        <span class="font-semibold block">Catatan Evaluasi Tim:</span>
                                        <span>{{ $item->catatan_verifikasi }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Lampiran Bukti File Download Link -->
                            <td class="py-3.5 px-4 text-center font-semibold whitespace-nowrap">
                                @if($item->arsipDigital->count() > 0)
                                    <div class="flex flex-col items-center gap-1">
                                        @foreach($item->arsipDigital as $file)
                                            <a href="{{ route('arsip.download', $file->id) }}" target="_blank" class="px-2 py-1 bg-blue-50 dark:bg-blue-950/50 text-blue-800 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/60 rounded-[4px] text-[11px] font-semibold inline-flex items-center gap-1 border border-blue-200 dark:border-blue-800">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span>{{ Str::limit($file->nama_file, 15) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-ink-muted font-normal italic">Tidak Ada File</span>
                                @endif
                            </td>

                            <!-- Status Evaluasi Badge -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase border
                                    {{ $item->status_verifikasi === 'diterima' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : '' }}
                                    {{ $item->status_verifikasi === 'ditolak' ? 'bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800' : '' }}
                                    {{ $item->status_verifikasi === 'tdt' ? 'bg-surface-subtle text-ink-secondary border-hairline-strong' : '' }}
                                    {{ $item->status_verifikasi === 'menunggu' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}">
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
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openModalEvaluasi({{ $item->id }}, '{{ $item->status_verifikasi }}', '{{ addslashes($item->catatan_verifikasi ?? '') }}')" class="px-2.5 py-1.5 bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/60 rounded-[6px] text-[11px] font-semibold border border-amber-300 dark:border-amber-800 inline-flex items-center gap-1 transition-colors">
                                        <span>Evaluasi</span>
                                    </button>

                                    <a href="{{ route('tindak-lanjut.show', $item->tindak_lanjut_id) }}" class="px-2.5 py-1.5 bg-brand hover:bg-brand-hover text-white rounded-[6px] text-[11px] font-semibold inline-flex items-center gap-1 transition-colors" title="Buka Halaman Detail LHP ini">
                                        <span>Detail LHP</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-ink-muted">Belum ada pengajuan bukti tindak lanjut dari Perangkat Daerah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Evaluasi & Reviu Bukti -->
    <div id="modalEvaluasiBukti" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-semibold text-ink text-base">Evaluasi & Verifikasi Bukti OPD</h3>
                <button type="button" onclick="document.getElementById('modalEvaluasiBukti').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEvaluasiBukti" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf

                <div>
                    <label for="evalStatusVerifikasi" class="block font-semibold mb-1 text-ink">Hasil Evaluasi Tim Pemeriksa <span class="text-red-700 dark:text-red-400">*</span></label>
                    <select id="evalStatusVerifikasi" name="status_verifikasi" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="diterima">Selesai Evaluasi (Diterima oleh Tim)</option>
                        <option value="ditolak">Memerlukan Perbaikan (Minta Revisi OPD)</option>
                        <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI (TDT)</option>
                    </select>
                </div>

                <div>
                    <label for="evalCatatanVerifikasi" class="block font-semibold mb-1 text-ink">Keterangan / Catatan Kekurangan untuk OPD</label>
                    <textarea id="evalCatatanVerifikasi" name="catatan_verifikasi" rows="3" placeholder="Tuliskan catatan penjelasan evaluasi atau kekurangan yang harus diperbaiki OPD..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-normal"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEvaluasiBukti').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-4 !min-h-0">Simpan Evaluasi</button>
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
