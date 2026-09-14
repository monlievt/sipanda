<x-app-layout>
    <x-slot name="header">
        Detail Surat Tugas (SPT) — {{ $penugasan->no_spt }}
    </x-slot>

    <!-- Navigation Header & Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('penugasan.index') }}" class="p-2 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Rincian Surat Tugas (SPT)</span>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span>No. SPT: {{ $penugasan->no_spt }}</span>
                    @if($penugasan->is_sesuai_pkppt)
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 text-xs font-bold rounded-lg border border-blue-200">✓ PKPPT</span>
                    @else
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 text-xs font-semibold rounded-lg">Non-PKPPT</span>
                    @endif
                </h2>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($penugasan->status_persetujuan === 'disetujui' || auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban']))
            <a href="{{ route('penugasan.export-docx', $penugasan->id) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Word (.docx)</span>
            </a>
            <a href="{{ route('penugasan.cetak', $penugasan->id) }}" target="_blank" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Web / PDF</span>
            </a>
            @else
            <button disabled class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-400 font-bold rounded-xl text-xs flex items-center gap-1.5 cursor-not-allowed opacity-70" title="SPT belum disetujui oleh Irban">
                <span>🔒 Dokumen SPT (Terkunci)</span>
            </button>
            @endif

            @can('penugasan.edit')
            <a href="{{ route('penugasan.edit', $penugasan->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Surat Tugas</span>
            </a>
            @endcan

            @can('penugasan.delete')
            <form method="POST" action="{{ route('penugasan.destroy', $penugasan->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat Tugas {{ $penugasan->no_spt }} ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs flex items-center gap-1 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 rounded-2xl border border-emerald-200 dark:border-emerald-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-lg">
                        ✓
                    </div>
                    <div>
                        <h4 class="font-black text-emerald-900 dark:text-emerald-200 text-sm">Surat Tugas Telah Disetujui Irban</h4>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400">
                            SPT ini sah dan resmi dapat dicetak serta dilaksanakan di lapangan. 
                            @if($penugasan->diverifikasi_pada)
                                Diverifikasi pada: {{ $penugasan->diverifikasi_pada->format('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-600 text-white font-black text-xs rounded-full self-start sm:self-auto">
                    Status: Disetujui
                </span>
            </div>
        @elseif($penugasan->status_persetujuan === 'ditolak')
            <div class="p-4 bg-rose-50 dark:bg-rose-950/50 rounded-2xl border border-rose-200 dark:border-rose-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold text-lg">
                            ✕
                        </div>
                        <div>
                            <h4 class="font-black text-rose-900 dark:text-rose-200 text-sm">Konsep Surat Tugas Ditolak / Perlu Revisi</h4>
                            <p class="text-xs text-rose-700 dark:text-rose-400">
                                Harap perbaiki data pengajuan SPT melalui tombol <strong>Edit Surat Tugas</strong> sesuai catatan verifikator di bawah.
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-rose-600 text-white font-black text-xs rounded-full self-start sm:self-auto">
                        Status: Ditolak
                    </span>
                </div>
                @if($penugasan->catatan_revisi)
                    <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-rose-300 dark:border-rose-700 text-xs text-slate-800 dark:text-slate-200">
                        <span class="font-bold text-rose-600 block uppercase text-[10px] mb-1">Catatan Revisi dari Irban:</span>
                        <p class="italic">"{{ $penugasan->catatan_revisi }}"</p>
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 bg-amber-50 dark:bg-amber-950/50 rounded-2xl border border-amber-200 dark:border-amber-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg">
                        ⏳
                    </div>
                    <div>
                        <h4 class="font-black text-amber-900 dark:text-amber-200 text-sm">Menunggu Verifikasi Irban</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Konsep Surat Tugas ini telah diajukan oleh {{ $penugasan->pembuatData?->nama ?? 'Staf' }} dan menunggu persetujuan Irban penanggung jawab.
                        </p>
                    </div>
                </div>

                @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban', 'admin_irban']))
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button type="button" onclick="document.getElementById('modalTolakSpt').classList.remove('hidden')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs">
                            ✕ Tolak (Revisi)
                        </button>
                        <form method="POST" action="{{ route('penugasan.verifikasi', $penugasan->id) }}" onsubmit="return confirm('Apakah Anda yakin menyetujui penerbitan Surat Tugas {{ $penugasan->no_spt }} ini?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status_persetujuan" value="disetujui">
                            <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1">
                                <span>✓ Setujui SPT</span>
                            </button>
                        </form>
                    </div>
                @else
                    <span class="px-3 py-1 bg-amber-500 text-white font-black text-xs rounded-full self-start sm:self-auto">
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
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                <h3 class="font-black text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>📋 Informasi Umum Penugasan</span>
                </h3>

                <div>
                    <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Uraian / Judul Penugasan:</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-white leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                        {{ $penugasan->uraian_penugasan }}
                    </p>
                </div>

                @if($penugasan->dasar_penugasan)
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Dasar Surat Perintah Tugas (Dasar Hukum):</span>
                    <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 whitespace-pre-line">
                        {{ $penugasan->dasar_penugasan }}
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">Jenis Penugasan:</span>
                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ $penugasan->jenisPenugasan?->nama ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">Sumber Penugasan:</span>
                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ $penugasan->sumberPenugasan?->nama ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">Irban Penanggung Jawab:</span>
                        <p class="font-bold text-emerald-600 dark:text-emerald-400">{{ $penugasan->irban_list_names }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">Dibuat Oleh:</span>
                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ $penugasan->pembuatData?->nama ?? 'Administrator' }}</p>
                    </div>
                </div>

                @if($penugasan->penugasan_induk_id)
                <div class="p-3.5 bg-purple-50 dark:bg-purple-950/40 rounded-2xl border border-purple-200 dark:border-purple-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-purple-600 uppercase block">Informasi ST Perpanjangan:</span>
                        <p class="font-bold text-purple-900 dark:text-purple-200">Merupakan ST Perpanjangan dari Induk: {{ $penugasan->penugasanInduk?->no_spt }}</p>
                    </div>
                    <a href="{{ route('penugasan.show', $penugasan->penugasan_induk_id) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs">
                        Lihat ST Induk
                    </a>
                </div>
                @endif
            </div>

            <!-- Objek OPD Target Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-3">
                <h3 class="font-black text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>🏢 Objek Pengawasan Target (OPD / Wilayah)</span>
                </h3>

                <div class="flex flex-wrap gap-2 pt-1">
                    @forelse($penugasan->objekPenugasan as $objek)
                        <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                            🏢 {{ $objek->nama }}
                        </span>
                    @empty
                        <p class="text-slate-400 italic">Belum ada Objek Pengawasan yang didaftarkan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Susunan Personil Tim Pengawasan Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                <h3 class="font-black text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <span>👥 Susunan Personil Tim Pengawasan</span>
                    <span class="text-xs font-bold text-slate-500">Total: {{ $penugasan->tim->count() }} Personil</span>
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

                <div class="divide-y divide-slate-200 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800/50">
                    @forelse($sortedTim as $pTim)
                        <div class="p-3.5 flex items-center justify-between hover:bg-white dark:hover:bg-slate-800 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold flex items-center justify-center text-xs">
                                    👤
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white text-xs">{{ $pTim->user?->nama ?? 'Personil Tim' }}</p>
                                    <span class="text-[10px] text-slate-400 font-mono">NIP: {{ $pTim->user?->nip ?? '-' }}</span>
                                </div>
                            </div>

                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border tracking-wider
                                {{ $pTim->peran === 'wakil_penanggung_jawab' || $pTim->peran === 'penanggung_jawab' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border-purple-200' : '' }}
                                {{ $pTim->peran === 'pengendali_teknis' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border-blue-200' : '' }}
                                {{ $pTim->peran === 'ketua_tim' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200' : '' }}
                                {{ $pTim->peran === 'anggota_tim' ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-300' : '' }}">
                                {{ $pTim->peran_label }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 italic">Belum ada susunan personil tim yang didaftarkan.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column (1/3 width): Status & Related LHP -->
        <div class="space-y-6">
            
            <!-- Status Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                <h3 class="font-black text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-800 pb-3">
                    Status Pelaksanaan
                </h3>

                <div class="text-center p-4 rounded-2xl border
                    {{ $penugasan->status === 'selesai' ? 'bg-emerald-50 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200' : '' }}
                    {{ $penugasan->status === 'berjalan' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200' : '' }}
                    {{ $penugasan->status === 'belum_berjalan' ? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border-slate-300' : '' }}">
                    <span class="text-[10px] font-bold uppercase tracking-wider block opacity-70">Status SPT Saat Ini:</span>
                    <p class="text-lg font-black mt-0.5 uppercase">{{ $penugasan->status_label }}</p>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase">🗓️ Tanggal Pelaksanaan:</span>
                    <p class="font-mono font-bold text-slate-800 dark:text-slate-200">
                        {{ $penugasan->tanggal_mulai->format('d/m/Y') }} s/d {{ $penugasan->tanggal_selesai->format('d/m/Y') }}
                    </p>
                    <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">
                        Durasi: {{ $penugasan->tanggal_mulai->diffInDays($penugasan->tanggal_selesai) + 1 }} Hari Kerja
                    </span>
                </div>
            </div>

            <!-- Dokumen LHP Hasil Pengawasan Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-3">
                <h3 class="font-black text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-800 pb-3">
                    📜 Laporan Hasil Pengawasan (LHP)
                </h3>

                @forelse($penugasan->tindakLanjut as $tl)
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/40 rounded-2xl border border-blue-200 dark:border-blue-800 space-y-2">
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400 text-xs block">
                            No. LHP: {{ $tl->no_lhp }}
                        </span>
                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ $tl->judul_lhp }}</p>
                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs w-full justify-center">
                            <span>📄 Buka Matriks LHP Lengkap</span>
                        </a>
                    </div>
                @empty
                    <p class="text-slate-400 italic text-center py-4">Belum ada dokumen LHP yang dikaitkan dengan SPT ini.</p>
                @endforelse
            </div>

    </div>

    <!-- Modal Tolak SPT (Catatan Revisi) -->
    <div id="modalTolakSpt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full space-y-4 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2 text-rose-600">
                    <span class="text-lg">✕</span>
                    <h3 class="font-black text-slate-900 dark:text-white text-sm">Kembalikan / Tolak Konsep SPT</h3>
                </div>
                <button type="button" onclick="document.getElementById('modalTolakSpt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('penugasan.verifikasi', $penugasan->id) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status_persetujuan" value="ditolak">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Catatan Revisi / Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="catatan_revisi" rows="4" required placeholder="Jelaskan hal-hal yang perlu diperbaiki oleh staf/tim pengusul SPT..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalTolakSpt').classList.add('hidden')" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-md">
                        Kirim Catatan & Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
