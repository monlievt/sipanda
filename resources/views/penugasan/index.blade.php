<x-app-layout>
    <x-slot name="header">
        Data Penugasan (SPT): Hasil Input PKPPT & Non-PKPPT
    </x-slot>

    <!-- Header Actions & Search/Filters -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink tracking-tight">Daftar Penugasan Pengawasan</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Tabel terpadu penugasan PKPPT & Non-PKPPT.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('penugasan.export', ['tahun' => $tahun, 'irban_id' => $irbanId]) }}" class="ledger-btn-secondary !text-xs">
                    <svg class="w-4 h-4 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                @can('penugasan.create')
                <a href="{{ route('penugasan.create') }}" class="ledger-btn-primary !text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input Penugasan Baru</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Filter Box -->
        <form method="GET" action="{{ route('penugasan.index') }}" class="p-4 sm:p-5 bg-surface rounded-[10px] border border-hairline grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Cari SPT / Uraian</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="No. SPT / Uraian..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-sm text-ink px-3.5 py-2.5">
            </div>

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Tahun</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isPimpinanOrAdmin())
            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    <option value="">-- Semua --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="belum_berjalan" {{ $status === 'belum_berjalan' ? 'selected' : '' }}>Belum Berjalan</option>
                    <option value="berjalan" {{ $status === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Jenis Penugasan</label>
                <select name="jenis_penugasan_id" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j->id }}" {{ $jenisId == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full ledger-btn-primary">
                    Cari
                </button>
                @if($search || $status || $jenisId || $irbanId)
                <a href="{{ route('penugasan.index') }}" class="ledger-btn-secondary">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4">No. SPT</th>
                        <th class="py-3.5 px-4">Uraian & Objek Target</th>
                        <th class="py-3.5 px-4">Jenis & Sumber</th>
                        <th class="py-3.5 px-4">Irban Penanggung Jawab</th>
                        <th class="py-3.5 px-4">Tanggal Pelaksanaan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($listPenugasan as $item)
                        <tr class="hover:bg-surface-subtle/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-semibold whitespace-nowrap">
                                <a href="{{ route('penugasan.show', $item->id) }}" class="text-blue-800 dark:text-blue-300 hover:underline block" title="Klik untuk membuka Rincian Lengkap Surat Tugas ini">
                                    {{ $item->no_spt }}
                                </a>

                                <span class="block text-[10px] text-ink-muted font-sans mt-0.5 tabular-nums">
                                    Tgl ST: {{ ($item->tanggal_st ?? $item->created_at)->format('d/m/Y') }}
                                </span>

                                @if($item->isBantuan())
                                    <span class="inline-block text-[10px] font-sans font-semibold text-cyan-800 dark:text-cyan-300 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.5 rounded-[4px] border border-cyan-200 dark:border-cyan-800 mt-0.5">
                                        ST Bantuan (Induk: {{ $item->penugasanInduk?->no_spt }})
                                    </span>
                                @elseif($item->isPerpanjangan())
                                    <span class="inline-block text-[10px] font-sans font-semibold text-purple-800 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 rounded-[4px] border border-purple-200 dark:border-purple-800 mt-0.5">
                                        ST Perpanjangan (Induk: {{ $item->penugasanInduk?->no_spt }})
                                    </span>
                                @endif

                                @if($item->is_sesuai_pkppt)
                                    <span class="block text-[10px] font-sans font-semibold text-blue-800 dark:text-blue-300 mt-0.5">PKPPT</span>
                                @else
                                    <span class="block text-[10px] font-sans font-medium text-ink-muted mt-0.5">Non-PKPPT</span>
                                @endif

                                @if($item->file_st)
                                    <a href="{{ route('penugasan.berkas-st', $item->id) }}" target="_blank" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold font-sans bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200 dark:border-teal-800 mt-1 hover:underline" title="Buka Berkas SPT Resmi Bertanda Tangan Inspektur">
                                        Berkas TTD
                                    </a>
                                @endif

                                @if($item->status_persetujuan === 'disetujui')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold font-sans bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 mt-1">
                                        Disetujui
                                    </span>
                                @elseif($item->status_persetujuan === 'ditolak')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold font-sans bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800 mt-1" title="Catatan: {{ $item->catatan_revisi }}">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold font-sans bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 mt-1">
                                        Menunggu Verifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <a href="{{ route('penugasan.show', $item->id) }}" class="font-semibold text-ink hover:text-brand-ink line-clamp-2">
                                    {{ $item->uraian_penugasan }}
                                </a>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($item->objekPenugasan as $objek)
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium bg-surface-subtle border border-hairline text-ink-secondary rounded-[4px]">
                                            {{ $objek->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="block font-semibold text-ink">{{ $item->jenisPenugasan?->nama }}</span>
                                <span class="text-[11px] text-ink-muted block">Sumber: {{ $item->sumberPenugasan?->nama }}</span>
                                @if($item->kelompokPengawasan)
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold bg-indigo-50 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        {{ $item->kelompokPengawasan->nama_kelompok }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-brand-ink">
                                {{ $item->irban_list_names }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-ink-secondary tabular-nums">
                                {{ $item->tanggal_mulai->format('d/m/Y') }} s.d. {{ $item->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($item->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-[4px] text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai (100%)
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-bold uppercase tracking-wider inline-block
                                            {{ $item->status === 'berjalan' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-surface-subtle text-ink-secondary border border-hairline' }}">
                                            {{ $item->status_label }}
                                        </span>
                                        @if($item->canComplete(auth()->user()))
                                        <div>
                                            <button type="button" onclick="openModalSelesaikanPenugasan({
                                                 id: {{ $item->id }},
                                                 no_spt: '{{ addslashes($item->no_spt) }}',
                                                 uraian: '{{ addslashes($item->uraian_penugasan) }}'
                                             })" class="inline-flex items-center gap-1 px-2 py-0.5 bg-brand hover:bg-brand-hover text-white rounded-[6px] text-[10px] font-semibold transition-colors" title="Klaim Penugasan ini telah Selesai">
                                                 <span>Tandai Selesai</span>
                                             </button>
                                        </div>
                                         @endif
                                     </div>
                                 @endif
                             </td>
                             <td class="py-3 px-4 text-center whitespace-nowrap">
                                 <div class="flex items-center justify-center gap-1">
                                     <a href="{{ route('penugasan.show', $item->id) }}" class="px-2.5 py-1 bg-surface text-ink hover:bg-surface-subtle rounded-[6px] text-[10px] font-semibold border border-hairline-strong transition-colors" title="Lihat Rincian Isi Surat Tugas">
                                         Detail
                                     </a>
                                     @if($item->status_persetujuan === 'disetujui' || auth()->user()->hasRole(['admin','administrator','inspektur','sekretaris','irban']))
                                         <a href="{{ route('penugasan.cetak', $item->id) }}" target="_blank" class="px-2.5 py-1 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-[6px] text-[10px] font-semibold border border-blue-200 dark:border-blue-800 transition-colors" title="Cetak Naskah Dinas SPT Resmi">
                                             Cetak
                                         </a>
                                     @else
                                         <button type="button" disabled class="px-2.5 py-1 bg-surface-subtle text-ink-muted rounded-[6px] text-[10px] font-semibold border border-hairline cursor-not-allowed opacity-70" title="SPT belum disetujui Irban">
                                             Cetak
                                         </button>
                                     @endif
                                    <!-- Tombol Upload / Ganti Berkas ST Bertanda Tangan Inspektur -->
                                    <button type="button" onclick="openModalUploadBerkasSt({
                                        id: {{ $item->id }},
                                        no_spt: '{{ addslashes($item->no_spt) }}',
                                        uraian: '{{ addslashes($item->uraian_penugasan) }}',
                                        has_file: {{ $item->file_st ? 'true' : 'false' }},
                                        file_name: '{{ addslashes($item->file_st_nama ?? '') }}'
                                    })" class="px-2.5 py-1 {{ $item->file_st ? 'bg-cyan-50 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800' : 'bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 border-teal-200 dark:border-teal-800' }} hover:opacity-85 rounded-[6px] text-[10px] font-semibold border transition-colors flex items-center gap-1" title="{{ $item->file_st ? 'Unggah Ulang / Revisi Berkas ST TTD' : 'Unggah Berkas ST yang telah ditandatangani Inspektur' }}">
                                        <span>{{ $item->file_st ? 'Revisi ST' : 'Upload ST' }}</span>
                                    </button>

                                    @can('penugasan.edit')
                                    <a href="{{ route('penugasan.edit', $item->id) }}" class="px-2.5 py-1 bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-[6px] text-[10px] font-semibold border border-amber-200 dark:border-amber-800 transition-colors" title="Edit Surat Tugas">
                                        Edit
                                    </a>
                                    @endcan
                                    @can('penugasan.delete')
                                    <form method="POST" action="{{ route('penugasan.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Surat Tugas {{ addslashes($item->no_spt) }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-[6px] text-[10px] font-semibold border border-red-200 dark:border-red-800 transition-colors" title="Hapus Surat Tugas">
                                            Hapus
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-ink-muted">
                                Tidak ada data penugasan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listPenugasan->hasPages())
            <div class="p-4 border-t border-hairline">
                {{ $listPenugasan->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Cepat Selesaikan Penugasan -->
    <div id="modalSelesaikanPenugasan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-[6px] bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-ink text-base">Tandai Penugasan Selesai</h3>
                        <p class="text-[11px] text-ink-secondary">Klaim realisasi pengawasan lapangan telah rampung</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalSelesaikanPenugasan').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form id="formSelesaikanPenugasan" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="selesai">
                <input type="hidden" name="progres_persen" value="100">

                <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                    <span class="text-[10px] font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wider block">Nomor Surat Tugas (SPT)</span>
                    <p id="labelNoSpt" class="font-mono font-bold text-ink text-sm"></p>
                    <p id="labelUraian" class="text-ink-secondary text-xs mt-1"></p>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Keterangan Hasil / Catatan Ringkas (Opsional)</label>
                    <textarea name="keterangan_hasil" rows="3" placeholder="mis. Penugasan telah selesai dilaksanakan dan Laporan Hasil Pengawasan (LHP) telah diterbitkan." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                    <p class="text-[11px] text-ink-muted mt-1">Status akan otomatis menjadi <strong>SELESAI (100%)</strong> dan tercatat pada Monitoring Realisasi PKPPT.</p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalSelesaikanPenugasan').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Selesaikan Penugasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Unggah / Revisi Berkas ST Resmi Bertanda Tangan -->
    <div id="modalUploadBerkasSt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-[6px] bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200 dark:border-teal-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </span>
                    <div>
                        <h3 id="modalUploadTitle" class="font-bold text-ink text-base">Unggah Berkas ST Resmi</h3>
                        <p class="text-[11px] text-ink-secondary">Berkas Surat Tugas yang telah ditandatangani Inspektur</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalUploadBerkasSt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form id="formUploadBerkasSt" method="POST" action="" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf

                <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                    <span class="text-[10px] font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wider block">Nomor Surat Tugas (SPT)</span>
                    <p id="labelUploadNoSpt" class="font-mono font-bold text-ink text-sm"></p>
                    <p id="labelUploadUraian" class="text-ink-secondary text-xs mt-1"></p>
                </div>

                <div id="infoBerkasLama" class="hidden p-3 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 rounded-[6px] text-cyan-900 dark:text-cyan-200">
                    <span class="font-semibold text-[11px] block">Berkas Saat Ini:</span>
                    <p id="labelNamaBerkasLama" class="text-xs font-semibold truncate mt-0.5"></p>
                    <p class="text-[11px] text-cyan-800 dark:text-cyan-300 mt-1">Mengunggah file baru akan menggantikan berkas lama untuk nomor ST ini (bila terdapat revisi ST).</p>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Pilih Berkas ST (PDF, JPG, PNG, DOCX) <span class="text-red-700 dark:text-red-400">*</span></label>
                    <input type="file" name="berkas_st" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-xs text-ink-secondary file:mr-4 file:py-2 file:px-4 file:rounded-[4px] file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 dark:file:bg-surface-subtle dark:file:text-teal-300 border border-hairline-strong rounded-[6px] p-1 bg-surface">
                    <p class="text-[11px] text-ink-muted mt-1">Ukuran file maksimal 20 MB. Pastikan stempel dan tanda tangan elektronik/basah terlihat jelas.</p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalUploadBerkasSt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-teal-700 hover:bg-teal-800 text-white font-semibold rounded-[6px] text-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span id="btnSubmitUploadText">Simpan Berkas ST</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalSelesaikanPenugasan(data) {
            const form = document.getElementById('formSelesaikanPenugasan');
            form.action = '/penugasan/' + data.id + '/status';
            document.getElementById('labelNoSpt').innerText = data.no_spt;
            document.getElementById('labelUraian').innerText = data.uraian;
            document.getElementById('modalSelesaikanPenugasan').classList.remove('hidden');
        }

        function openModalUploadBerkasSt(data) {
            const form = document.getElementById('formUploadBerkasSt');
            form.action = '/penugasan/' + data.id + '/upload-berkas';
            document.getElementById('labelUploadNoSpt').innerText = data.no_spt;
            document.getElementById('labelUploadUraian').innerText = data.uraian;

            const infoLama = document.getElementById('infoBerkasLama');
            const title = document.getElementById('modalUploadTitle');
            const btnText = document.getElementById('btnSubmitUploadText');

            if (data.has_file) {
                infoLama.classList.remove('hidden');
                document.getElementById('labelNamaBerkasLama').innerText = data.file_name || 'Berkas Terlampir';
                title.innerText = 'Unggah Ulang / Revisi Berkas ST';
                btnText.innerText = 'Perbarui Berkas ST';
            } else {
                infoLama.classList.add('hidden');
                title.innerText = 'Unggah Berkas ST Resmi';
                btnText.innerText = 'Simpan Berkas ST';
            }

            document.getElementById('modalUploadBerkasSt').classList.remove('hidden');
        }
    </script>
</x-app-layout>
