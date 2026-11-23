<x-app-layout>
    <x-slot name="header">
        Detail Surat Tugas (SPT): {{ $penugasan->no_spt }}
    </x-slot>

    <!-- Navigation Header & Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('penugasan.index') }}" class="p-2 bg-surface text-ink-secondary hover:bg-surface-subtle rounded-[6px] border border-hairline-strong transition-colors" aria-label="Kembali ke daftar penugasan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <span class="text-xs font-semibold text-brand-ink uppercase tracking-wider block">Rincian Surat Tugas (SPT)</span>
                <h2 class="text-xl font-bold text-ink tracking-tight flex flex-wrap items-center gap-2">
                    <span>No. SPT: {{ $penugasan->no_spt }}</span>
                    @if($penugasan->isBantuan())
                        <span class="px-2 py-0.5 bg-cyan-50 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300 text-xs font-semibold rounded-[4px] border border-cyan-200 dark:border-cyan-800">ST Bantuan</span>
                    @elseif($penugasan->isPerpanjangan())
                        <span class="px-2 py-0.5 bg-purple-50 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 text-xs font-semibold rounded-[4px] border border-purple-200 dark:border-purple-800">ST Perpanjangan</span>
                    @endif
                    @if($penugasan->is_sesuai_pkppt)
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 text-xs font-semibold rounded-[4px] border border-blue-200 dark:border-blue-800">PKPPT</span>
                    @else
                        <span class="px-2 py-0.5 bg-surface-subtle text-ink-secondary border border-hairline text-xs font-semibold rounded-[4px]">Non-PKPPT</span>
                    @endif
                </h2>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($penugasan->status_persetujuan === 'disetujui' || auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban']))
            <a href="{{ route('penugasan.export-docx', $penugasan->id) }}" class="px-3.5 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold rounded-[6px] text-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Word (.docx)</span>
            </a>
            <a href="{{ route('penugasan.cetak', $penugasan->id) }}" target="_blank" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-[6px] text-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Web / PDF</span>
            </a>
            @else
            <button type="button" disabled class="px-4 py-2 bg-surface-subtle text-ink-muted border border-hairline font-semibold rounded-[6px] text-xs flex items-center gap-1.5 cursor-not-allowed opacity-70" title="SPT belum disetujui oleh Irban">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Dokumen SPT (Terkunci)</span>
            </button>
            @endif

            @can('penugasan.edit')
            <a href="{{ route('penugasan.edit', $penugasan->id) }}" class="ledger-btn-primary !min-h-0 !py-2 !text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Surat Tugas</span>
            </a>
            @endcan

            @can('penugasan.delete')
            <form method="POST" action="{{ route('penugasan.destroy', $penugasan->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat Tugas {{ $penugasan->no_spt }} ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-[6px] text-xs flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Approval Workflow Banner -->
    <div class="mb-6">
        @if($penugasan->status_persetujuan === 'disetujui')
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-[10px] border border-emerald-200 dark:border-emerald-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[6px] bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 dark:text-emerald-200 text-sm">Surat Tugas Telah Disetujui Irban</h4>
                        <p class="text-xs text-emerald-800 dark:text-emerald-300">
                            SPT ini sah dan resmi dapat dicetak serta dilaksanakan di lapangan. 
                            @if($penugasan->diverifikasi_pada)
                                Diverifikasi pada: {{ $penugasan->diverifikasi_pada->format('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-700 text-white font-semibold text-xs rounded-[4px] self-start sm:self-auto">
                    Status: Disetujui
                </span>
            </div>
        @elseif($penugasan->status_persetujuan === 'ditolak')
            <div class="p-4 bg-red-50 dark:bg-red-950/40 rounded-[10px] border border-red-200 dark:border-red-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[6px] bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-red-900 dark:text-red-200 text-sm">Konsep Surat Tugas Ditolak / Perlu Revisi</h4>
                            <p class="text-xs text-red-800 dark:text-red-300">
                                Harap perbaiki data pengajuan SPT melalui tombol <strong>Edit Surat Tugas</strong> sesuai catatan verifikator di bawah.
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-red-700 text-white font-semibold text-xs rounded-[4px] self-start sm:self-auto">
                        Status: Ditolak
                    </span>
                </div>
                @if($penugasan->catatan_revisi)
                    <div class="bg-surface p-3.5 rounded-[6px] border border-red-200 dark:border-red-800 text-xs text-ink">
                        <span class="font-semibold text-red-800 dark:text-red-300 block uppercase text-[10px] mb-1">Catatan Revisi dari Irban:</span>
                        <p class="italic">"{{ $penugasan->catatan_revisi }}"</p>
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 rounded-[10px] border border-amber-200 dark:border-amber-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[6px] bg-amber-100 dark:bg-amber-900/60 text-amber-900 dark:text-amber-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900 dark:text-amber-200 text-sm">Menunggu Verifikasi Irban</h4>
                        <p class="text-xs text-amber-900 dark:text-amber-300">
                            Konsep Surat Tugas ini telah diajukan oleh {{ $penugasan->pembuatData?->nama ?? 'Staf' }} dan menunggu persetujuan Irban penanggung jawab.
                        </p>
                    </div>
                </div>

                @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban', 'admin_irban']))
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button type="button" onclick="document.getElementById('modalTolakSpt').classList.remove('hidden')" class="px-3 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-semibold rounded-[6px] transition-colors">
                            Tolak (Revisi)
                        </button>
                        <form method="POST" action="{{ route('penugasan.verifikasi', $penugasan->id) }}" onsubmit="return confirm('Apakah Anda yakin menyetujui penerbitan Surat Tugas {{ $penugasan->no_spt }} ini?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status_persetujuan" value="disetujui">
                            <button type="submit" class="px-4 py-1.5 bg-brand hover:bg-brand-hover text-white text-xs font-semibold rounded-[6px] flex items-center gap-1 transition-colors">
                                <span>Setujui SPT</span>
                            </button>
                        </form>
                    </div>
                @else
                    <span class="px-3 py-1 bg-amber-700 text-white font-semibold text-xs rounded-[4px] self-start sm:self-auto">
                        Status: Diajukan
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
        
        <!-- Left Column (2/3 width): Detail Information -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Information Card -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3 flex items-center gap-2">
                    <span>Informasi Umum Penugasan</span>
                </h3>

                <div>
                    <span class="font-semibold text-ink-muted uppercase text-[10px] block mb-1">Uraian / Judul Penugasan:</span>
                    <p class="text-sm font-semibold text-ink leading-relaxed bg-surface-subtle p-3.5 rounded-[6px] border border-hairline">
                        {{ $penugasan->uraian_penugasan }}
                    </p>
                </div>

                @if($penugasan->dasar_penugasan)
                <div>
                    <span class="font-semibold text-ink-muted uppercase text-[10px] block mb-1">Dasar Surat Perintah Tugas (Dasar Hukum):</span>
                    <div class="text-xs text-ink-secondary leading-relaxed bg-surface-subtle p-3.5 rounded-[6px] border border-hairline whitespace-pre-line">
                        {{ $penugasan->dasar_penugasan }}
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                        <span class="text-[10px] font-semibold text-ink-muted block uppercase">Jenis Penugasan:</span>
                        <p class="font-semibold text-ink">{{ $penugasan->jenisPenugasan?->nama ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                        <span class="text-[10px] font-semibold text-ink-muted block uppercase">Sumber Penugasan:</span>
                        <p class="font-semibold text-ink">{{ $penugasan->sumberPenugasan?->nama ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-[6px] border border-indigo-200 dark:border-indigo-800 space-y-1 sm:col-span-2">
                        <span class="text-[10px] font-semibold text-indigo-800 dark:text-indigo-300 block uppercase">Kelompok / Kluster Pengawasan (Bahan ILHP):</span>
                        <p class="font-semibold text-indigo-900 dark:text-indigo-200">
                            {{ $penugasan->kelompokPengawasan?->nama_kelompok ?? ($penugasan->pkppt?->kelompokPengawasan?->nama_kelompok ?? 'Belum Ditentukan') }}
                        </p>
                        @if($penugasan->kelompokPengawasan?->deskripsi_singkat)
                            <p class="text-[11px] text-ink-secondary mt-0.5">
                                {{ $penugasan->kelompokPengawasan->deskripsi_singkat }}
                            </p>
                        @endif
                    </div>

                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                        <span class="text-[10px] font-semibold text-ink-muted block uppercase">Irban Penanggung Jawab:</span>
                        <p class="font-semibold text-brand-ink">{{ $penugasan->irban_list_names }}</p>
                    </div>

                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                        <span class="text-[10px] font-semibold text-ink-muted block uppercase">Tanggal Naskah SPT:</span>
                        <p class="font-semibold text-ink tabular-nums">{{ ($penugasan->tanggal_st ?? $penugasan->tanggal_mulai)->format('d F Y') }}</p>
                    </div>

                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                        <span class="text-[10px] font-semibold text-ink-muted block uppercase">Dibuat Oleh:</span>
                        <p class="font-semibold text-ink">{{ $penugasan->pembuatData?->nama ?? 'Administrator' }}</p>
                    </div>
                </div>

                @if($penugasan->isBantuan())
                <div class="p-3.5 bg-cyan-50 dark:bg-cyan-950/40 rounded-[6px] border border-cyan-200 dark:border-cyan-800 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-semibold text-cyan-800 dark:text-cyan-300 uppercase block">Informasi ST Bantuan:</span>
                        <p class="font-semibold text-cyan-900 dark:text-cyan-200">Merupakan ST Bantuan dari Tim Utama (Induk: {{ $penugasan->penugasanInduk?->no_spt }})</p>
                    </div>
                    <a href="{{ route('penugasan.show', $penugasan->penugasan_induk_id) }}" class="px-3 py-1.5 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold rounded-[6px] text-xs shrink-0 transition-colors">
                        Lihat ST Utama
                    </a>
                </div>
                @elseif($penugasan->isPerpanjangan())
                <div class="p-3.5 bg-purple-50 dark:bg-purple-950/40 rounded-[6px] border border-purple-200 dark:border-purple-800 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-semibold text-purple-800 dark:text-purple-300 uppercase block">Informasi ST Perpanjangan:</span>
                        <p class="font-semibold text-purple-900 dark:text-purple-200">Merupakan ST Perpanjangan dari Induk: {{ $penugasan->penugasanInduk?->no_spt }}</p>
                    </div>
                    <a href="{{ route('penugasan.show', $penugasan->penugasan_induk_id) }}" class="px-3 py-1.5 bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-[6px] text-xs shrink-0 transition-colors">
                        Lihat ST Induk
                    </a>
                </div>
                @endif
            </div>

            <!-- Objek OPD Target Card -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-3">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3 flex items-center gap-2">
                    <span>Objek Pengawasan Target (OPD / Wilayah)</span>
                </h3>

                <div class="flex flex-wrap gap-2 pt-1">
                    @forelse($penugasan->objekPenugasan as $objek)
                        <span class="px-3 py-1.5 bg-surface-subtle text-ink font-semibold rounded-[6px] border border-hairline text-xs">
                            {{ $objek->nama }}
                        </span>
                    @empty
                        <p class="text-ink-muted italic">Belum ada Objek Pengawasan yang didaftarkan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Susunan Personil Tim Pengawasan Card -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3 flex items-center justify-between">
                    <span>Susunan Personil Tim Pengawasan</span>
                    <span class="text-xs font-semibold text-ink-secondary tabular-nums">Total: {{ $penugasan->tim->count() }} Personil</span>
                </h3>

                @php
                    $sortedTim = $penugasan->tim->sortBy(function($t) {
                        return match($t->peran) {
                            'penanggung_jawab' => 1,
                            'wakil_penanggung_jawab' => 2,
                            'pengendali_teknis' => 3,
                            'ketua_tim' => 4,
                            'anggota_tim' => 5,
                            default => 6,
                        };
                    });
                @endphp

                <div class="divide-y divide-hairline border border-hairline rounded-[10px] overflow-hidden bg-surface-subtle">
                    @forelse($sortedTim as $pTim)
                        <div class="p-3.5 flex items-center justify-between hover:bg-surface transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-[6px] bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-ink text-xs">{{ $pTim->user?->nama ?? 'Personil Tim' }}</p>
                                    <span class="text-[10px] text-ink-muted font-mono tabular-nums">NIP: {{ $pTim->user?->nip ?? '-' }}</span>
                                </div>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-bold uppercase border tracking-wider
                                {{ $pTim->peran === 'wakil_penanggung_jawab' || $pTim->peran === 'penanggung_jawab' ? 'bg-purple-50 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800' : '' }}
                                {{ $pTim->peran === 'pengendali_teknis' ? 'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800' : '' }}
                                {{ $pTim->peran === 'ketua_tim' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}
                                {{ $pTim->peran === 'anggota_tim' ? 'bg-surface text-ink-secondary border-hairline' : '' }}">
                                {{ $pTim->peran_label }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-ink-muted italic">Belum ada susunan personil tim yang didaftarkan.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column (1/3 width): Status & Related LHP -->
        <div class="space-y-6">
            
            <!-- Status Card -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3">
                    Status Pelaksanaan
                </h3>

                <div class="text-center p-4 rounded-[6px] border
                    {{ $penugasan->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : '' }}
                    {{ $penugasan->status === 'berjalan' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}
                    {{ $penugasan->status === 'belum_berjalan' ? 'bg-surface-subtle text-ink border-hairline' : '' }}">
                    <span class="text-[10px] font-semibold uppercase tracking-wider block opacity-80">Status SPT Saat Ini:</span>
                    <p class="text-lg font-bold mt-0.5 uppercase">{{ $penugasan->status_label }}</p>
                </div>

                <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                    <span class="text-[10px] font-semibold text-ink-muted block uppercase">Tanggal Pelaksanaan:</span>
                    <p class="font-mono font-semibold text-ink tabular-nums">
                        {{ $penugasan->tanggal_mulai->format('d/m/Y') }} s/d {{ $penugasan->tanggal_selesai->format('d/m/Y') }}
                    </p>
                    <span class="text-[11px] text-brand-ink font-semibold block mt-0.5 tabular-nums">
                        Durasi: {{ $penugasan->tanggal_mulai->diffInDays($penugasan->tanggal_selesai) + 1 }} Hari Kerja
                    </span>
                </div>
            </div>

            <!-- Card Berkas ST Resmi Bertanda Tangan -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3 flex items-center justify-between">
                    <span>Berkas ST Resmi (TTD)</span>
                    @if($penugasan->file_st)
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-semibold rounded-[4px]">Tersedia</span>
                    @else
                        <span class="px-2 py-0.5 bg-surface-subtle text-ink-secondary border border-hairline text-[10px] font-semibold rounded-[4px]">Belum Ada</span>
                    @endif
                </h3>

                @if($penugasan->file_st)
                    <div class="p-3.5 bg-teal-50 dark:bg-teal-950/40 rounded-[6px] border border-teal-200 dark:border-teal-800 space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-[6px] bg-surface border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-teal-900 dark:text-teal-200 text-xs truncate">{{ $penugasan->file_st_nama ?? basename($penugasan->file_st) }}</p>
                                <span class="text-[10px] text-teal-800 dark:text-teal-300 block tabular-nums">
                                    Diunggah: {{ $penugasan->file_st_diunggah_pada?->format('d/m/Y H:i') ?? '-' }}
                                    @if($penugasan->fileStDiunggahOleh)
                                        oleh {{ $penugasan->fileStDiunggahOleh->nama }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('penugasan.berkas-st', $penugasan->id) }}" target="_blank" class="w-full py-2 bg-teal-700 hover:bg-teal-800 text-white font-semibold rounded-[6px] text-xs flex items-center justify-center gap-1.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh / Lihat Berkas ST (TTD)</span>
                        </a>
                    </div>
                @else
                    <p class="text-ink-secondary text-[11px] leading-relaxed">
                        Belum ada berkas fisik / digital ST bertanda tangan Inspektur yang diunggah untuk penugasan ini.
                    </p>
                @endif

                <!-- Form Upload / Revisi Berkas ST -->
                <form method="POST" action="{{ route('penugasan.upload-berkas', $penugasan->id) }}" enctype="multipart/form-data" class="space-y-3 pt-2 border-t border-hairline">
                    @csrf
                    <div>
                        <label class="block font-semibold text-ink mb-1 text-[11px]">
                            {{ $penugasan->file_st ? 'Unggah Berkas Revisi (Ganti File Lama)' : 'Unggah Berkas ST TTD Baru' }}
                        </label>
                        <input type="file" name="berkas_st" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-xs text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-[4px] file:border-0 file:text-[11px] file:font-semibold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 dark:file:bg-surface-subtle dark:file:text-teal-300 border border-hairline-strong rounded-[6px] p-1 bg-surface">
                        <p class="text-[11px] text-ink-muted mt-1">Maks. 20 MB (PDF/Gambar/Doc). Nomor ST tetap sama.</p>
                    </div>

                    <button type="submit" class="w-full ledger-btn-secondary !text-xs">
                        <span>{{ $penugasan->file_st ? 'Simpan Revisi Berkas ST' : 'Simpan Berkas ST' }}</span>
                    </button>
                </form>
            </div>

            <!-- Dokumen LHP Hasil Pengawasan Card -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-3">
                <h3 class="font-bold text-ink text-base border-b border-hairline pb-3">
                    Laporan Hasil Pengawasan (LHP)
                </h3>

                @forelse($penugasan->tindakLanjut as $tl)
                    <div class="p-4 bg-blue-50/70 dark:bg-blue-950/30 rounded-[10px] border border-blue-200 dark:border-blue-800 space-y-2">
                        <span class="font-mono font-semibold text-blue-800 dark:text-blue-300 text-xs block">
                            No. LHP: {{ $tl->no_lhp }}
                        </span>
                        <p class="font-semibold text-ink">{{ $tl->judul_lhp }}</p>
                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-[6px] text-xs w-full justify-center transition-colors">
                            <span>Buka Matriks LHP Lengkap</span>
                        </a>
                    </div>
                @empty
                    <p class="text-ink-muted italic text-center py-4">Belum ada dokumen LHP yang dikaitkan dengan SPT ini.</p>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Modal Tolak SPT (Catatan Revisi) -->
    <div id="modalTolakSpt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-md w-full space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <div class="flex items-center gap-2 text-red-800 dark:text-red-300">
                    <h3 class="font-bold text-ink text-sm">Kembalikan / Tolak Konsep SPT</h3>
                </div>
                <button type="button" onclick="document.getElementById('modalTolakSpt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('penugasan.verifikasi', $penugasan->id) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status_persetujuan" value="ditolak">

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Catatan Revisi / Alasan Penolakan <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <textarea name="catatan_revisi" rows="4" required placeholder="Jelaskan hal-hal yang perlu diperbaiki oleh staf/tim pengusul SPT..." class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-3 text-ink"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalTolakSpt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-[6px] text-xs transition-colors">
                        Kirim Catatan & Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
