<x-app-layout>
    <x-slot name="header">
        Detail Bahan Tindak Lanjut (LHP)
    </x-slot>

    <!-- Header Navigation & Action -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('tindak-lanjut.index') }}" class="p-2 bg-surface text-ink-secondary hover:bg-surface-subtle hover:text-ink rounded-[6px] border border-hairline-strong transition-colors" aria-label="Kembali ke daftar LHP">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <span class="text-xs font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Detail Dokumen LHP</span>
                <h2 class="text-lg font-bold text-ink leading-tight">
                    {{ $tindakLanjut->no_lhp ? 'No. LHP: ' . $tindakLanjut->no_lhp : 'No. SPT: ' . $tindakLanjut->penugasan?->no_spt }}
                </h2>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tindak-lanjut.export_lhp', $tindakLanjut->id) }}" class="ledger-btn-primary !text-xs !py-2 !px-3.5 !min-h-0">
                <span>Ekspor Excel</span>
            </a>

            <a href="{{ route('tindak-lanjut.berita_acara', $tindakLanjut->id) }}" target="_blank" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                <span>Berita Acara (PDF)</span>
            </a>

            <!-- Naskah Dinas Matriks TL Resmi -->
            @if($tindakLanjut->status_telaah === 'disetujui_inspektur' || auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban']))
                <a href="{{ route('tindak-lanjut.cetak_matriks', $tindakLanjut->id) }}" target="_blank" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0 !text-blue-800 dark:!text-blue-300" title="Cetak Naskah Matriks Tindak Lanjut Hasil Pengawasan Resmi">
                    <span>Cetak Matriks TL</span>
                </a>
                <a href="{{ route('tindak-lanjut.cetak_surat_pengantar', $tindakLanjut->id) }}" target="_blank" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0" title="Cetak Naskah Surat Pengantar Matriks ke OPD/Desa">
                    <span>Cetak Surat Pengantar</span>
                </a>
            @else
                <button type="button" disabled class="px-3.5 py-2 bg-surface-subtle text-ink-muted font-semibold text-xs rounded-[6px] border border-hairline inline-flex items-center gap-1.5 cursor-not-allowed opacity-70" title="Matriks & Surat Pengantar baru dapat digenerate setelah Telaah disetujui final oleh Inspektur">
                    <span>Matriks & Surat Pengantar (Terkunci)</span>
                </button>
            @endif

            <button type="button" onclick="document.getElementById('modalSuratPengantar').classList.remove('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-3 !min-h-0" title="Atur Nomor, Tanggal, dan Tujuan Instansi Surat Pengantar">
                <span>Atur Surat</span>
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="space-y-6 text-xs">

        <!-- WORKFLOW PERSETUJUAN & TELAAH TINDAK LANJUT HASIL PENGAWASAN (TLHP) -->
        <div class="p-6 bg-surface rounded-[10px] border border-hairline space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-hairline pb-4">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-800 dark:text-blue-300 block">Proses Bisnis Pengawasan Internal</span>
                    <h3 class="text-base font-bold text-ink">
                        Alur Workflow Telaah & Persetujuan Matriks Tindak Lanjut
                    </h3>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-[4px] text-xs font-semibold uppercase tracking-wider border
                        {{ $tindakLanjut->status_telaah === 'disetujui_inspektur' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : '' }}
                        {{ $tindakLanjut->status_telaah === 'diajukan_inspektur' || $tindakLanjut->status_telaah === 'diajukan_irban' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}
                        {{ $tindakLanjut->status_telaah === 'ditolak_irban' || $tindakLanjut->status_telaah === 'ditolak_inspektur' ? 'bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800' : '' }}
                        {{ $tindakLanjut->status_telaah === 'draft' ? 'bg-surface-subtle text-ink-secondary border-hairline-strong' : '' }}">
                        {{ $tindakLanjut->status_telaah_label }}
                    </span>
                </div>
            </div>

            <!-- Stepper Timeline 4 Tahap -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                <!-- Tahap 1: ST Pemantauan & Input OPD -->
                <div class="p-4 rounded-[10px] border relative transition-colors {{ $tindakLanjut->st_pemantauan_id ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : 'bg-surface-subtle border-hairline' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-[6px] {{ $tindakLanjut->st_pemantauan_id ? 'bg-brand text-white' : 'bg-hairline-strong text-ink' }} font-bold text-xs flex items-center justify-center">1</span>
                        <h5 class="font-semibold text-ink">ST Pemantauan TL</h5>
                    </div>
                    <p class="text-[11px] text-ink-secondary mb-2">Surat Tugas dasar pemantauan atas pengawasan assurance.</p>
                    @if($tindakLanjut->stPemantauan)
                        <div class="space-y-1.5">
                            <span class="inline-block font-mono font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-[4px] text-[10px] border border-emerald-200 dark:border-emerald-800">
                                {{ $tindakLanjut->stPemantauan->no_spt }}
                            </span>
                            @if($tindakLanjut->stPemantauan->tim->isNotEmpty())
                                <div class="text-[10px] text-ink-secondary bg-surface p-2 rounded-[6px] border border-hairline">
                                    <span class="font-semibold text-brand-ink block mb-0.5">Tim Pemantau TL (Rolling):</span>
                                    <div class="space-y-0.5">
                                        @foreach($tindakLanjut->stPemantauan->tim as $member)
                                            <div class="flex items-center justify-between text-[10px]">
                                                <span class="font-medium text-ink">{{ $member->user?->name ?? 'Personil' }}</span>
                                                <span class="text-ink-muted">{{ $member->peran_label }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="inline-block text-ink-muted italic text-[11px]">Belum dikaitkan ST Pemantauan</span>
                    @endif

                    <div class="mt-3 pt-2 border-t border-hairline">
                        <button type="button" onclick="document.getElementById('modalKaitkanStPemantauan').classList.remove('hidden')" class="w-full py-1.5 px-2 bg-brand hover:bg-brand-hover text-white rounded-[6px] text-[11px] font-semibold flex items-center justify-center gap-1 transition-colors">
                            <span>{{ $tindakLanjut->st_pemantauan_id ? 'Ubah / Kaitkan ST Pemantauan' : 'Kaitkan ST Pemantauan' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Tahap 2: Telaah Tim Pemantauan -->
                <div class="p-4 rounded-[10px] border relative transition-colors {{ $tindakLanjut->ditelaah_oleh ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : 'bg-surface-subtle border-hairline' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-[6px] {{ $tindakLanjut->ditelaah_oleh ? 'bg-brand text-white' : 'bg-hairline-strong text-ink' }} font-bold text-xs flex items-center justify-center">2</span>
                        <h5 class="font-semibold text-ink">Telaah Tim TL</h5>
                    </div>
                    <p class="text-[11px] text-ink-secondary mb-2">Pemeriksaan bukti respon tindak lanjut oleh tim.</p>
                    @if($tindakLanjut->ditelaah_oleh)
                        <div class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold space-y-0.5">
                            <div>Ditelaah: {{ $tindakLanjut->penelaah?->nama }}</div>
                            @if($tindakLanjut->status_rekomendasi_usulan)
                                <div class="text-[10px] font-semibold text-blue-800 dark:text-blue-300">
                                    Usulan: {{ strtoupper($tindakLanjut->status_rekomendasi_usulan) }}
                                </div>
                            @endif
                            <span class="block text-[10px] font-normal text-ink-muted tabular-nums">{{ $tindakLanjut->ditelaah_pada?->format('d/m/Y H:i') }}</span>
                        </div>
                    @else
                        <span class="inline-block text-ink-muted italic text-[11px]">Menunggu telaah tim</span>
                    @endif

                    <div class="mt-3 pt-2 border-t border-hairline">
                        <button type="button" onclick="document.getElementById('modalTelaahTim').classList.remove('hidden')" class="w-full py-1.5 px-2 bg-surface hover:bg-surface-subtle text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-800 rounded-[6px] text-[11px] font-semibold flex items-center justify-center gap-1 transition-colors">
                            <span>{{ $tindakLanjut->ditelaah_oleh ? 'Ubah Hasil Telaah' : 'Input Telaah Tim' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Tahap 3: Verifikasi Irban -->
                <div class="p-4 rounded-[10px] border relative transition-colors {{ in_array($tindakLanjut->status_telaah, ['diajukan_inspektur', 'disetujui_inspektur']) ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : (in_array($tindakLanjut->status_telaah, ['revisi_irban', 'ditolak_irban']) ? 'bg-red-50 dark:bg-red-950/30 border-red-300 dark:border-red-800' : 'bg-surface-subtle border-hairline') }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-[6px] {{ in_array($tindakLanjut->status_telaah, ['diajukan_inspektur', 'disetujui_inspektur']) ? 'bg-brand text-white' : 'bg-hairline-strong text-ink' }} font-bold text-xs flex items-center justify-center">3</span>
                        <h5 class="font-semibold text-ink">Verifikasi Irban</h5>
                    </div>
                    <p class="text-[11px] text-ink-secondary mb-2">Validasi hasil telaah tim oleh Inspektur Pembantu.</p>
                    @if(in_array($tindakLanjut->status_telaah, ['diajukan_inspektur', 'disetujui_inspektur']))
                        <div class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold">
                            Diverifikasi: {{ $tindakLanjut->irbanPenyetuju?->nama }}
                            <span class="block text-[10px] font-normal text-ink-muted tabular-nums">{{ $tindakLanjut->diverifikasi_irban_pada?->format('d/m/Y H:i') }}</span>
                        </div>
                    @elseif(in_array($tindakLanjut->status_telaah, ['revisi_irban', 'ditolak_irban']))
                        <span class="text-[11px] font-semibold text-red-800 dark:text-red-300 block">Dikembalikan Irban (Perlu Revisi)</span>
                    @else
                        <span class="inline-block text-ink-muted italic text-[11px]">Menunggu verifikasi Irban</span>
                    @endif

                    @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'irban', 'admin_irban']))
                        <div class="mt-3 pt-2 border-t border-hairline">
                            <button type="button" onclick="document.getElementById('modalVerifikasiIrban').classList.remove('hidden')" class="w-full py-1.5 px-2 bg-surface hover:bg-surface-subtle text-ink border border-hairline-strong rounded-[6px] text-[11px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                <span>Aksi Irban</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Tahap 4: Persetujuan Akhir Inspektur -->
                <div class="p-4 rounded-[10px] border relative transition-colors {{ $tindakLanjut->status_telaah === 'disetujui_inspektur' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : (in_array($tindakLanjut->status_telaah, ['revisi_inspektur', 'ditolak_inspektur']) ? 'bg-red-50 dark:bg-red-950/30 border-red-300 dark:border-red-800' : 'bg-surface-subtle border-hairline') }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-[6px] {{ $tindakLanjut->status_telaah === 'disetujui_inspektur' ? 'bg-brand text-white' : 'bg-hairline-strong text-ink' }} font-bold text-xs flex items-center justify-center">4</span>
                        <h5 class="font-semibold text-ink">Persetujuan Inspektur</h5>
                    </div>
                    <p class="text-[11px] text-ink-secondary mb-2">Persetujuan final & publikasi status resmi ke Portal OPD.</p>
                    @if($tindakLanjut->status_telaah === 'disetujui_inspektur')
                        <div class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold">
                            Disetujui Final: {{ $tindakLanjut->inspekturPenyetuju?->nama ?? 'Inspektur' }}
                            <span class="block text-[10px] text-emerald-800 dark:text-emerald-300 font-semibold">Status Resmi Aktif di Portal OPD</span>
                            <span class="block text-[10px] font-normal text-ink-muted tabular-nums">{{ $tindakLanjut->inspektur_disetujui_pada?->format('d/m/Y H:i') }}</span>
                        </div>
                    @elseif(in_array($tindakLanjut->status_telaah, ['revisi_inspektur', 'ditolak_inspektur']))
                        <span class="text-[11px] font-semibold text-red-800 dark:text-red-300 block">Dikembalikan Inspektur (Revisi)</span>
                    @else
                        <span class="inline-block text-ink-muted italic text-[11px]">Menunggu persetujuan final</span>
                    @endif

                    @if(auth()->user()->hasRole(['admin', 'administrator', 'inspektur']))
                        <div class="mt-3 pt-2 border-t border-hairline">
                            <button type="button" onclick="document.getElementById('modalPersetujuanInspektur').classList.remove('hidden')" class="w-full py-1.5 px-2 bg-brand hover:bg-brand-hover text-white rounded-[6px] text-[11px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                <span>Aksi Inspektur</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Catatan-catatan Verifikasi / Revisi (Jika Ada) -->
            @if($tindakLanjut->catatan_telaah_tim || $tindakLanjut->catatan_verifikasi_irban || $tindakLanjut->catatan_persetujuan_inspektur)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-hairline">
                    @if($tindakLanjut->catatan_telaah_tim)
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-[6px] border border-blue-200 dark:border-blue-800 text-xs">
                            <span class="font-semibold text-blue-800 dark:text-blue-300 block uppercase text-[10px] mb-1">Catatan Telaah Tim:</span>
                            <p class="text-ink italic">"{{ $tindakLanjut->catatan_telaah_tim }}"</p>
                        </div>
                    @endif

                    @if($tindakLanjut->catatan_verifikasi_irban)
                        <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline text-xs">
                            <span class="font-semibold text-ink-secondary block uppercase text-[10px] mb-1">Catatan Verifikasi Irban:</span>
                            <p class="text-ink italic">"{{ $tindakLanjut->catatan_verifikasi_irban }}"</p>
                        </div>
                    @endif

                    @if($tindakLanjut->catatan_persetujuan_inspektur)
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-[6px] border border-emerald-200 dark:border-emerald-800 text-xs">
                            <span class="font-semibold text-emerald-800 dark:text-emerald-300 block uppercase text-[10px] mb-1">Catatan Persetujuan Inspektur:</span>
                            <p class="text-ink italic">"{{ $tindakLanjut->catatan_persetujuan_inspektur }}"</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Info Surat Pengantar Yang Terkonfigurasi -->
            <div class="p-3.5 bg-surface-subtle rounded-[10px] border border-hairline flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-0.5">
                    <span class="text-[11px] font-semibold text-ink-muted uppercase">Informasi Surat Pengantar Matriks TL:</span>
                    <p class="font-semibold text-ink text-xs">
                        No. Surat: <span class="font-mono text-blue-800 dark:text-blue-300">{{ $tindakLanjut->no_surat_pengantar ?? '(Belum Diatur)' }}</span>
                        | Tgl: <span class="font-mono tabular-nums">{{ $tindakLanjut->tgl_surat_pengantar ? $tindakLanjut->tgl_surat_pengantar->format('d/m/Y') : '-' }}</span>
                        | Tujuan: <span class="text-brand-ink font-semibold">{{ $tindakLanjut->tujuanSuratObjek?->nama ?? $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->first() ?? '(Pilih dari Master OPD)' }}</span>
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('modalSuratPengantar').classList.remove('hidden')" class="ledger-btn-secondary !text-xs !py-1.5 !px-3 !min-h-0 self-start sm:self-auto">
                    Edit Data Surat Pengantar
                </button>
            </div>
        </div>

        <!-- 4 Banner Metric Status Rekapitulasi LHP Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Sesuai -->
            <div class="p-4 bg-surface rounded-[10px] border border-hairline">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Sesuai</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-700 dark:bg-blue-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-blue-800 dark:text-blue-300 tabular-nums">{{ $countSesuai }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $lhpItems->count() > 0 ? round(($countSesuai / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-700 dark:bg-blue-400 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countSesuai / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 2. Belum Sesuai -->
            <div class="p-4 bg-surface rounded-[10px] border border-hairline">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Belum Sesuai</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-700 dark:bg-emerald-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $countBelumSesuai }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $lhpItems->count() > 0 ? round(($countBelumSesuai / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-700 dark:bg-emerald-400 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countBelumSesuai / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 3. Belum Ditindaklanjuti -->
            <div class="p-4 bg-surface rounded-[10px] border border-hairline">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-600 dark:bg-amber-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-amber-900 dark:text-amber-300 tabular-nums">{{ $countBelum }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $lhpItems->count() > 0 ? round(($countBelum / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-amber-600 dark:bg-amber-400 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countBelum / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 4. Tidak Dapat Ditindaklanjuti (TDT) -->
            <div class="p-4 bg-surface rounded-[10px] border border-hairline">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Tidak Dapat Ditindaklanjuti</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-red-700 dark:bg-red-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-red-800 dark:text-red-300 tabular-nums">{{ $countTdt }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $lhpItems->count() > 0 ? round(($countTdt / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-red-700 dark:bg-red-400 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countTdt / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Rekapitulasi Ringkasan Pengembalian Finansial LHP Ini -->
        <div class="p-4 bg-emerald-950 text-white rounded-[10px] border border-emerald-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-900 rounded-[6px]">
                    <svg class="w-6 h-6 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-sm text-emerald-100">Realisasi Pengembalian Finansial Dokumen LHP Ini</h4>
                    <p class="text-emerald-200 text-xs">Total pengembalian uang daerah yang disetorkan ke Kas Daerah (NTPN).</p>
                </div>
            </div>

            <div class="flex items-center gap-6 font-mono tabular-nums">
                <div>
                    <span class="block text-[11px] text-emerald-200 font-sans">Target Rekomendasi Rp</span>
                    <span class="text-base font-bold text-white">Rp {{ number_format($totalNilaiTarget, 0, ',', '.') }}</span>
                </div>
                <div class="h-8 w-px bg-emerald-800"></div>
                <div>
                    <span class="block text-[11px] text-emerald-200 font-sans">Telah Disetor (NTPN)</span>
                    <span class="text-base font-bold text-emerald-200">Rp {{ number_format($totalSetorRp, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 1. Header LHP Information -->
        <div class="p-6 bg-surface rounded-[10px] border border-hairline space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-4 border-b border-hairline">
                <div>
                    <span class="block font-semibold text-ink-muted text-[11px]">Nomor LHP</span>
                    <span class="font-mono font-semibold text-sm text-blue-800 dark:text-blue-300">{{ $tindakLanjut->no_lhp ?? '-' }}</span>
                </div>
                <div>
                    <span class="block font-semibold text-ink-muted text-[11px]">Nomor SPT & Irban</span>
                    <span class="font-mono font-semibold text-sm text-brand-ink">
                        {{ $tindakLanjut->penugasan?->no_spt }} ({{ $tindakLanjut->penugasan?->irban?->nama_irban }})
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-ink-muted text-[11px]">Nilai Yang Diawasi (APBD/APBDes)</span>
                    <span class="font-mono font-semibold text-sm text-blue-800 dark:text-blue-300 tabular-nums">
                        {{ $tindakLanjut->formatted_nilai_diawasi }}
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-ink-muted text-[11px]">Objek OPD Target</span>
                    <span class="font-semibold text-ink">
                        {{ $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-' }}
                    </span>
                </div>
            </div>

            <div>
                <span class="block font-semibold text-ink-muted text-[11px] mb-0.5">Judul LHP</span>
                <p class="text-sm font-semibold text-ink leading-relaxed">{{ $tindakLanjut->judul_lhp ?? 'Laporan Hasil Pengawasan Terkait SPT ' . $tindakLanjut->penugasan?->no_spt }}</p>
                @if($tindakLanjut->tgl_lhp)
                    <span class="text-[11px] text-ink-secondary font-medium mt-1 block tabular-nums">Tanggal Terbit LHP: {{ $tindakLanjut->tgl_lhp->format('d F Y') }}</span>
                @endif
            </div>

            @if($tindakLanjut->berkas_dasar_lhp)
                <div class="pt-3 border-t border-hairline flex items-center justify-between">
                    <span class="text-xs text-ink-secondary font-semibold">Lampiran Dokumen PDF LHP Resmi:</span>
                    <a href="{{ route('dokumen.stream.path', $tindakLanjut->berkas_dasar_lhp) }}" target="_blank" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/60 dark:hover:bg-red-900/60 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800 font-semibold text-xs rounded-[6px] inline-flex items-center gap-1.5 transition-colors">
                        Buka File PDF LHP
                    </a>
                </div>
            @endif
        </div>

        <!-- 2. Loop Seluruh Item Rekomendasi dalam LHP Ini (+ Dialog Interaktif OPD & Tim Pemeriksa) -->
        <div class="space-y-6">
            <h3 class="font-bold text-ink text-sm uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-brand"></span>
                Daftar Rincian Temuan & Rekomendasi ({{ $lhpItems->count() }} Item):
            </h3>

            @foreach($lhpItems as $idx => $item)
                <div class="p-6 bg-surface rounded-[10px] border border-hairline space-y-4" x-data="{ showFormInput: false }">
                    <!-- Item Header, Status Badge & Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-hairline">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border font-bold flex items-center justify-center text-xs tabular-nums">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <span class="font-bold text-ink text-sm">Item Rekomendasi #{{ $idx + 1 }}</span>
                                @if($item->nama_objek_sasaran)
                                    <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-[4px] text-[11px] font-semibold bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        {{ $item->nama_objek_sasaran }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-ink-muted tabular-nums">Target: {{ $item->tanggal_target ? $item->tanggal_target->format('d/m/Y') : '-' }}</span>

                            <span class="px-2.5 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase tracking-wider border
                                {{ $item->status_tindak_lanjut === 'selesai' ? 'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800' : '' }}
                                {{ $item->status_tindak_lanjut === 'proses' || $item->status_tindak_lanjut === 'menunggu_verifikasi' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : '' }}
                                {{ $item->status_tindak_lanjut === 'belum' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}
                                {{ $item->status_tindak_lanjut === 'tdt' ? 'bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800' : '' }}">
                                Status: {{ $item->status_label }}
                            </span>

                            @if($item->status_telaah !== 'disetujui_inspektur' && $item->status_rekomendasi_usulan)
                                <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-semibold uppercase bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                    {{ $item->status_telaah_label }} (Usulan: {{ strtoupper($item->status_rekomendasi_usulan) }})
                                </span>
                            @endif

                            @can('tindak_lanjut.edit')
                            <!-- Tombol Edit Item Rekomendasi -->
                            <button type="button" onclick="openModalEditTl({
                                id: {{ $item->id }},
                                objek_penugasan_id: '{{ $item->objek_penugasan_id ?? '' }}',
                                no_lhp: '{{ addslashes($item->no_lhp ?? '') }}',
                                judul_lhp: '{{ addslashes($item->judul_lhp ?? '') }}',
                                tgl_lhp: '{{ $item->tgl_lhp ? $item->tgl_lhp->format('Y-m-d') : '' }}',
                                kode_atribut_temuan_id: '{{ $item->kode_atribut_temuan_id ?? '' }}',
                                kode_atribut_rekomendasi_id: '{{ $item->kode_atribut_rekomendasi_id ?? '' }}',
                                kode_temuan_lengkap: '{{ addslashes($item->kode_temuan_lengkap ?? '') }}',
                                kode_rekomendasi: '{{ addslashes($item->kode_rekomendasi ?? '') }}',
                                uraian_temuan: '{{ addslashes($item->uraian_temuan) }}',
                                rekomendasi: '{{ addslashes($item->rekomendasi) }}',
                                nilai_diawasi_rp: {{ $item->nilai_diawasi_rp ?? 0 }},
                                nilai_rekomendasi_rp: {{ $item->nilai_rekomendasi_rp ?? 0 }},
                                tanggal_target: '{{ $item->tanggal_target ? $item->tanggal_target->format('Y-m-d') : '' }}',
                                status_tindak_lanjut: '{{ $item->status_tindak_lanjut }}'
                            })" class="px-3 py-1.5 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/60 rounded-[6px] text-xs font-semibold inline-flex items-center gap-1.5 border border-blue-200 dark:border-blue-800 transition-colors" title="Edit Rincian Temuan & Rekomendasi ini">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit Item Rekomendasi</span>
                            </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Temuan vs Rekomendasi Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-amber-50/50 dark:bg-amber-950/20 rounded-[10px] border border-amber-200 dark:border-amber-900 space-y-1.5">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <span class="font-semibold text-amber-900 dark:text-amber-300 text-xs block">Uraian Temuan:</span>
                                @if($item->kode_temuan_lengkap)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-[4px] text-[10px] font-semibold font-mono bg-amber-100 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700">
                                        Kode: {{ $item->kode_temuan_lengkap }}
                                    </span>
                                @endif
                            </div>
                            @if($item->kodeAtributTemuan)
                                <span class="text-[11px] text-amber-900 dark:text-amber-300 font-semibold block">
                                    {{ $item->kodeAtributTemuan->nama_sub_kelompok }}
                                </span>
                            @endif
                            <p class="text-ink font-normal leading-relaxed whitespace-pre-line">{{ $item->uraian_temuan }}</p>
                        </div>

                        <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-[10px] border border-emerald-200 dark:border-emerald-900 space-y-2">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <span class="font-semibold text-emerald-900 dark:text-emerald-200 text-xs block">Rekomendasi Wajib:</span>
                                @if($item->kode_rekomendasi)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-[4px] text-[10px] font-semibold font-mono bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700">
                                        Kode: {{ $item->kode_rekomendasi }}
                                    </span>
                                @endif
                            </div>
                            @if($item->kodeAtributRekomendasi)
                                <span class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold block">
                                    {{ $item->kodeAtributRekomendasi->deskripsi }}
                                </span>
                            @endif
                            <p class="text-ink font-normal leading-relaxed whitespace-pre-line">{{ $item->rekomendasi }}</p>
                            <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800 flex justify-between font-mono font-semibold text-xs tabular-nums">
                                <span class="text-ink-secondary">Target Rp: <span class="text-emerald-800 dark:text-emerald-300">{{ $item->formatted_nilai_rp }}</span></span>
                                <span class="text-ink-secondary">Telah Disetor: <span class="text-blue-800 dark:text-blue-300">{{ $item->formatted_total_setor }}</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Dialog Interaktif: Uraian Tindak Lanjut OPD & Catatan Evaluasi Pemeriksa -->
                    <div class="p-4 bg-surface-subtle rounded-[10px] border border-hairline space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-semibold text-ink text-xs block">
                                Dialog Tindak Lanjut (Jawaban OPD & Evaluasi Pemeriksa):
                            </span>

                            <!-- Tombol Toggle Form Input Tindak Lanjut (OPD / Admin) -->
                            <button type="button" @click="showFormInput = !showFormInput" class="ledger-btn-primary !text-xs !py-1.5 !px-3 !min-h-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Input Jawaban / Perbaikan Tindak Lanjut OPD</span>
                            </button>
                        </div>

                        <!-- Form Input Uraian Tindak Lanjut, Setoran Kasda, & Upload Berkas Bukti -->
                        <div x-show="showFormInput" x-transition class="p-4 bg-surface rounded-[10px] border border-brand space-y-4">
                            <h4 class="font-semibold text-ink text-xs border-b border-hairline pb-2">
                                Form Input Jawaban / Perbaikan Tindak Lanjut OPD
                            </h4>

                            <form method="POST" action="{{ route('tindak-lanjut.store_respon', $item->id) }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf

                                <!-- Uraian Perbaikan OPD -->
                                <div>
                                    <label class="block font-semibold mb-1 text-ink">Uraian / Jawaban Tindak Lanjut OPD <span class="text-red-700 dark:text-red-400">*</span></label>
                                    <textarea name="catatan_opd" required rows="3" placeholder="Tuliskan penjelasan perbaikan / tindak lanjut yang telah dilaksanakan OPD di sini..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-normal"></textarea>
                                </div>

                                <!-- Input Rincian Penyetoran Kas Daerah (Pengembalian Finansial) -->
                                <div class="p-3.5 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-[6px] border border-emerald-200 dark:border-emerald-900 space-y-3">
                                    <span class="font-semibold text-emerald-900 dark:text-emerald-300 text-xs block">
                                        Input Setoran ke Kas Daerah (Optional jika ada pengembalian finansial):
                                    </span>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-emerald-800 dark:text-emerald-300 mb-0.5">Nominal Setoran (Rp)</label>
                                            <input type="text" oninput="formatRupiahInput(this)" name="nilai_setor_rp" placeholder="0" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-brand-ink tabular-nums">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-ink-secondary mb-0.5">No. Referensi / NTPN</label>
                                            <input type="text" name="no_referensi_ntpn" placeholder="mis. NTPN 87123XX" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-ink-secondary mb-0.5">Nama Bank / Kas</label>
                                            <input type="text" name="nama_bank" placeholder="mis. Bank Jatim / Kasda" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-ink-secondary mb-0.5">Tanggal Penyetoran</label>
                                            <input type="date" name="tgl_setor" value="{{ date('Y-m-d') }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload File Bukti & Status Update -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold mb-1 text-ink">Unggah Berkas Bukti (PDF / Gambar / Zip)</label>
                                        <input type="file" name="berkas_bukti" accept=".pdf,.jpg,.jpeg,.png,.zip" class="w-full text-xs text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border file:border-blue-200 dark:file:border-blue-800 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-950/60 file:text-blue-800 dark:file:text-blue-300 hover:file:bg-blue-100">
                                    </div>

                                    <div>
                                        <label class="block font-semibold mb-1 text-ink">Update Status TL (Optional)</label>
                                        <select name="status_tindak_lanjut" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                                            <option value="">-- Biarkan Status Saat Ini --</option>
                                            <option value="proses">BELUM SESUAI</option>
                                            <option value="selesai">SESUAI</option>
                                            <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="pt-2 flex items-center justify-end gap-2">
                                    <button type="button" @click="showFormInput = false" class="ledger-btn-secondary !text-xs !py-1.5 !px-3.5 !min-h-0">Batal</button>
                                    <button type="submit" class="ledger-btn-primary !text-xs !py-1.5 !px-4 !min-h-0">Simpan Jawaban Tindak Lanjut</button>
                                </div>
                            </form>
                        </div>

                        <!-- Daftar Riwayat Tanggapan OPD & Evaluasi Pemeriksa -->
                        @forelse($item->buktiTindakLanjut as $bIdx => $bukti)
                            <div class="p-4 bg-surface rounded-[10px] border border-hairline space-y-3" x-data="{ showFormEvaluasi: false }">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-hairline pb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-ink text-xs">Respon OPD #{{ $bIdx + 1 }}: {{ $bukti->pengunggah?->name ?? 'Pengelola OPD / Pemeriksa' }}</span>
                                        <span class="text-[11px] text-ink-muted tabular-nums">({{ $bukti->created_at->format('d/m/Y H:i') }})</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 font-semibold rounded-[4px] text-[10px] border
                                            {{ $bukti->status_verifikasi === 'diterima' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : '' }}
                                            {{ $bukti->status_verifikasi === 'ditolak' ? 'bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800' : '' }}
                                            {{ $bukti->status_verifikasi === 'tdt' ? 'bg-surface-subtle text-ink-secondary border-hairline-strong' : '' }}
                                            {{ $bukti->status_verifikasi === 'menunggu' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : '' }}">
                                            Status Evaluasi: 
                                            @if($bukti->status_verifikasi === 'diterima')
                                                Selesai Evaluasi (Diterima oleh Tim)
                                            @elseif($bukti->status_verifikasi === 'ditolak')
                                                Memerlukan Perbaikan (Catatan Evaluasi Tim)
                                            @elseif($bukti->status_verifikasi === 'tdt')
                                                TIDAK DAPAT DITINDAKLANJUTI (TDT)
                                            @else
                                                Belum Dievaluasi oleh Tim
                                            @endif
                                        </span>

                                        <!-- Tombol Beri Evaluasi Kekurangan bagi Admin/Pemeriksa -->
                                        @if(auth()->user() && !auth()->user()->hasRole(['opd']))
                                            <button type="button" @click="showFormEvaluasi = !showFormEvaluasi" class="px-2.5 py-1 bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/60 font-semibold rounded-[6px] text-[11px] border border-amber-300 dark:border-amber-800 inline-flex items-center gap-1 transition-colors">
                                                <span>Evaluasi / Catat Kekurangan</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-semibold text-ink-muted uppercase tracking-wider mb-0.5">Uraian / Jawaban Tindak Lanjut OPD:</span>
                                    <p class="text-ink font-normal leading-relaxed whitespace-pre-line">{{ $bukti->catatan_opd ?? '-' }}</p>
                                </div>

                                @if($bukti->arsipDigital->count() > 0)
                                    <div class="pt-1.5 border-t border-hairline flex flex-wrap items-center gap-2">
                                        <span class="text-[11px] text-ink-muted font-semibold">Berkas Bukti Terlampir:</span>
                                        @foreach($bukti->arsipDigital as $fileBukti)
                                            <a href="{{ route('arsip.preview', $fileBukti->id) }}" target="_blank" class="px-2.5 py-1 bg-surface-subtle hover:bg-blue-50 dark:hover:bg-blue-950/60 text-ink hover:text-blue-800 dark:hover:text-blue-300 font-semibold rounded-[4px] text-[11px] inline-flex items-center gap-1 border border-hairline transition-colors">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span>{{ $fileBukti->nama_file }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Highlight Keterangan Catatan Evaluasi Kekurangan dari Tim Pemeriksa -->
                                @if($bukti->catatan_verifikasi)
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-[6px] border border-amber-300 dark:border-amber-800 space-y-1">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-semibold text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-amber-800 dark:text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span>Keterangan Evaluasi / Catatan Kekurangan dari Tim Pemeriksa:</span>
                                            </span>
                                            @if($bukti->diverifikasi_pada)
                                                <span class="text-[11px] text-ink-muted font-medium tabular-nums">{{ $bukti->diverifikasi_pada->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-ink font-medium leading-relaxed whitespace-pre-line text-xs pl-5.5">{{ $bukti->catatan_verifikasi }}</p>
                                    </div>
                                @endif

                                <!-- Form Evaluasi / Catat Kekurangan oleh Tim Pemeriksa (Termasuk opsi TDT) -->
                                <div x-show="showFormEvaluasi" x-transition class="p-3.5 bg-amber-50/70 dark:bg-amber-950/40 rounded-[6px] border border-amber-300 dark:border-amber-800 text-xs space-y-3 mt-2">
                                    <h5 class="font-semibold text-amber-900 dark:text-amber-200 text-xs">Evaluasi & Beri Catatan Kekurangan untuk OPD</h5>
                                    
                                    <form method="POST" action="{{ route('tindak-lanjut.bukti.verifikasi', $bukti->id) }}" class="space-y-3">
                                        @csrf
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-semibold mb-1 text-ink">Hasil Evaluasi Pemeriksa</label>
                                                <select name="status_verifikasi" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                                                    <option value="ditolak" {{ $bukti->status_verifikasi === 'ditolak' ? 'selected' : '' }}>Memerlukan Perbaikan (Catatan Evaluasi Tim)</option>
                                                    <option value="diterima" {{ $bukti->status_verifikasi === 'diterima' ? 'selected' : '' }}>Selesai Evaluasi (Diterima oleh Tim)</option>
                                                    <option value="tdt" {{ $bukti->status_verifikasi === 'tdt' ? 'selected' : '' }}>TIDAK DAPAT DITINDAKLANJUTI (TDT)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block font-semibold mb-1 text-ink">Keterangan Kekurangan untuk OPD <span class="text-red-700 dark:text-red-400">*</span></label>
                                                <textarea name="catatan_verifikasi" required rows="2" placeholder="Tuliskan secara jelas titik kekurangan OPD yang harus ditindaklanjuti ulang di sini..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-normal">{{ $bukti->catatan_verifikasi }}</textarea>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button type="button" @click="showFormEvaluasi = false" class="ledger-btn-secondary !text-xs !py-1.5 !px-3 !min-h-0">Batal</button>
                                            <button type="submit" class="ledger-btn-primary !text-xs !py-1.5 !px-4 !min-h-0">Simpan Catatan Evaluasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-ink-muted text-xs italic">Belum ada uraian tindak lanjut atau tanggapan yang diisi oleh OPD untuk rekomendasi ini.</p>
                        @endforelse
                    </div>

                    <!-- Rincian Penyetoran Kas Daerah (NTPN) untuk Item Ini -->
                    @if($item->rincianPenyetoran->count() > 0)
                        <div class="p-3 bg-surface-subtle rounded-[10px] border border-hairline space-y-2">
                            <span class="font-semibold text-ink text-xs block">Rincian Penyetoran Kas Daerah (NTPN):</span>
                            <div class="overflow-x-auto rounded-[6px] border border-hairline bg-surface">
                                <table class="w-full text-left text-[11px]">
                                    <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase border-b border-hairline">
                                        <tr>
                                            <th class="p-2">No. Referensi / NTPN</th>
                                            <th class="p-2">Bank</th>
                                            <th class="p-2">Nilai Setoran</th>
                                            <th class="p-2 text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-hairline font-mono tabular-nums">
                                        @foreach($item->rincianPenyetoran as $setor)
                                            <tr>
                                                <td class="p-2 font-semibold text-ink">{{ $setor->no_referensi_ntpn ?? '-' }}</td>
                                                <td class="p-2 text-ink-secondary">{{ $setor->nama_bank ?? '-' }}</td>
                                                <td class="p-2 font-semibold text-emerald-800 dark:text-emerald-300">{{ $setor->formatted_nilai_setor }}</td>
                                                <td class="p-2 text-center text-ink-muted">{{ $setor->tgl_setor->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Edit Temuan & Rekomendasi (Wider max-w-4xl) -->
    <div id="modalEditTindakLanjut" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-4xl w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-semibold text-ink text-base">Edit Item Temuan, Rekomendasi & LHP</h3>
                    <p class="text-xs text-ink-secondary mt-0.5">Perbarui rincian LHP, uraian temuan, rekomendasi wajib, dan berkas dasar.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalEditTindakLanjut').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditTindakLanjut" method="POST" action="" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf
                @method('PUT')

                <div class="p-4 bg-surface-subtle rounded-[10px] border border-hairline space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-4">
                            <label class="block font-semibold mb-1 text-blue-800 dark:text-blue-300">Nomor LHP</label>
                            <input type="text" id="editNoLhp" name="no_lhp" placeholder="mis. 700/85/LHP/406.008/2026" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block font-semibold mb-1 text-ink">Judul LHP</label>
                            <input type="text" id="editJudulLhp" name="judul_lhp" placeholder="mis. LHP atas Pengelolaan Keuangan OPD..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block font-semibold mb-1 text-ink">Tanggal LHP</label>
                            <input type="date" id="editTglLhp" name="tgl_lhp" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                        </div>
                    </div>

                    @if($tindakLanjut->penugasan && $tindakLanjut->penugasan->objekPenugasan->count() > 0)
                        <div class="pt-2 border-t border-hairline">
                            <label class="block font-semibold mb-1 text-ink">Objek / Instansi Sasaran Khusus Temuan Ini</label>
                            <select id="editObjekPenugasanId" name="objek_penugasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-ink">
                                @foreach($tindakLanjut->penugasan->objekPenugasan as $obj)
                                    <option value="{{ $obj->id }}">{{ $obj->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Klasifikasi Kode Atribut PermenPAN-RB -->
                <div class="p-3.5 bg-emerald-50/60 dark:bg-emerald-950/30 rounded-[10px] border border-emerald-200 dark:border-emerald-800 space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-emerald-900 dark:text-emerald-200 text-[11px] mb-1">
                                Kode Atribut Temuan (PermenPAN-RB 42/2011)
                            </label>
                            <select id="editKodeAtributTemuanId" name="kode_atribut_temuan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs text-ink">
                                <option value="">-- Tanpa Kode Temuan Baku --</option>
                                <optgroup label="1. TEMUAN KETIDAKPATUHAN TERHADAP PERATURAN">
                                    @foreach($kodeAtributTemuanList->where('kode_kelompok', '1') as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="2. KELEMAHAN SISTEM PENGENDALIAN INTERN (SPI)">
                                    @foreach($kodeAtributTemuanList->where('kode_kelompok', '2') as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="3. 3E (KETIDAKEFEKTIFAN, KETIDAKEFISIENAN, KETIDAKHEMATAN)">
                                    @foreach($kodeAtributTemuanList->where('kode_kelompok', '3') as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-emerald-900 dark:text-emerald-200 text-[11px] mb-1">
                                Kode Rekomendasi (PermenPAN-RB 42/2011)
                            </label>
                            <select id="editKodeAtributRekomendasiId" name="kode_atribut_rekomendasi_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs text-ink">
                                <option value="">-- Tanpa Kode Rekomendasi Baku --</option>
                                @foreach($kodeAtributRekomendasiList as $kr)
                                    <option value="{{ $kr->id }}">{{ $kr->kode }} - {{ $kr->deskripsi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1 text-ink">Uraian Temuan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <textarea id="editUraianTemuan" name="uraian_temuan" required rows="3" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-ink">Rekomendasi Wajib <span class="text-red-700 dark:text-red-400">*</span></label>
                        <textarea id="editRekomendasi" name="rekomendasi" required rows="3" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-blue-800 dark:text-blue-300">Nilai Yang Diawasi (Rp)</label>
                        <input type="text" oninput="formatRupiahInput(this)" id="editNilaiDiawasiRp" name="nilai_diawasi_rp" placeholder="0" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-blue-800 dark:text-blue-300 tabular-nums">
                        <p class="text-[10px] text-ink-muted mt-0.5">(Jika Tidak Ada Berikan Input 0)</p>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-brand-ink">Nilai Rekomendasi (Rp)</label>
                        <input type="text" oninput="formatRupiahInput(this)" id="editNilaiRp" name="nilai_rekomendasi_rp" placeholder="0" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-brand-ink tabular-nums">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-ink">Target Waktu</label>
                        <input type="date" id="editTanggalTarget" name="tanggal_target" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-ink">Status TL</label>
                        <select id="editStatusTl" name="status_tindak_lanjut" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="selesai">SESUAI</option>
                            <option value="proses">BELUM SESUAI</option>
                            <option value="belum">BELUM DITINDAKLANJUTI</option>
                            <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Ganti Berkas Dasar LHP (File PDF Optional)</label>
                    <input type="file" name="berkas_dasar_lhp" accept=".pdf" class="w-full text-xs text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border file:border-red-200 dark:file:border-red-800 file:text-xs file:font-semibold file:bg-red-50 dark:file:bg-red-950/60 file:text-red-800 dark:file:text-red-300 hover:file:bg-red-100">
                </div>

                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditTindakLanjut').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 0. Modal Pengaitan ST Pemantauan TL -->
    <div id="modalKaitkanStPemantauan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-lg w-full space-y-4"
            x-data="{
                search: '',
                selectedId: '{{ $tindakLanjut->st_pemantauan_id ?? '' }}',
                options: {{ Js::from($stPemantauanOptions ?? []) }},
                get filtered() {
                    if (!this.search.trim()) return this.options;
                    const q = this.search.toLowerCase().trim();
                    return this.options.filter(o => 
                        o.no_spt.toLowerCase().includes(q) || 
                        o.uraian.toLowerCase().includes(q) || 
                        o.objek.toLowerCase().includes(q) || 
                        o.irban.toLowerCase().includes(q)
                    );
                },
                select(id) {
                    this.selectedId = id;
                }
            }">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-sm">Kaitkan ST Pemantauan Tindak Lanjut</h3>
                <button type="button" onclick="document.getElementById('modalKaitkanStPemantauan').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.kaitkan_st_pemantauan', $tindakLanjut->id) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="st_pemantauan_id" :value="selectedId">

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">
                        Cari & Pilih Surat Perintah Tugas (SPT) Pemantauan Terkait
                    </label>

                    <!-- Search Input -->
                    <div class="relative mb-2">
                        <input type="text" x-model="search" placeholder="Ketik No. SPT, Uraian, atau Objek Sasaran..." 
                            class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface px-3.5 py-2.5 text-ink font-medium">
                        <button type="button" x-show="search.length > 0" @click="search = ''" class="absolute right-3 top-2.5 text-ink-muted hover:text-ink text-xs font-bold" aria-label="Hapus pencarian">
                            &times;
                        </button>
                    </div>

                    <!-- Opsi Lepas / Kosongkan ST -->
                    <div class="mb-2">
                        <button type="button" @click="select('')" 
                            class="w-full text-left p-2.5 rounded-[6px] border transition-colors flex items-center justify-between text-xs"
                            :class="!selectedId ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-semibold' : 'border-dashed border-hairline-strong hover:bg-surface-subtle text-ink-secondary'">
                            <span>-- Jangan Kaitkan / Lepaskan ST Pemantauan --</span>
                            <span x-show="!selectedId" class="text-brand-ink font-semibold text-xs">Dipilih</span>
                        </button>
                    </div>

                    <!-- Scrollable Filtered List -->
                    <div class="max-h-56 overflow-y-auto space-y-1.5 pr-1 divide-y divide-hairline border border-hairline rounded-[10px] p-1.5 bg-surface">
                        <template x-for="opt in filtered" :key="opt.id">
                            <button type="button" @click="select(opt.id)" 
                                class="w-full text-left p-2.5 rounded-[6px] transition-colors text-xs space-y-1"
                                :class="selectedId == opt.id ? 'bg-brand-soft border border-brand-border text-ink' : 'hover:bg-surface-subtle border border-transparent text-ink-secondary'">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-ink" x-text="opt.no_spt"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-[4px] font-semibold bg-surface-subtle text-ink-secondary border border-hairline shrink-0" x-text="opt.irban"></span>
                                </div>
                                <p class="text-[11px] text-ink-secondary line-clamp-2" x-text="opt.uraian"></p>
                                <div class="flex items-center justify-between text-[10px] text-ink-muted pt-0.5">
                                    <span>Objek: <span x-text="opt.objek"></span></span>
                                    <span class="tabular-nums" x-text="opt.tanggal"></span>
                                </div>
                            </button>
                        </template>

                        <div x-show="filtered.length === 0" class="p-4 text-center text-ink-muted text-xs">
                            Tidak ditemukan SPT dengan kata kunci "<span x-text="search"></span>"
                        </div>
                    </div>

                    <p class="text-[11px] text-ink-secondary mt-2 leading-relaxed">
                        Surat Tugas ini akan menjadi payung hukum dan dasar penugasan tim dalam pemantauan & penyusunan berita acara atas rekomendasi LHP ini.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalKaitkanStPemantauan').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        Simpan & Kaitkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 1. Modal Telaah Tim Pemantauan TL -->
    <div id="modalTelaahTim" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-xl w-full space-y-4"
            x-data="{
                search: '',
                selectedId: '{{ $tindakLanjut->st_pemantauan_id ?? '' }}',
                options: {{ Js::from($stPemantauanOptions ?? []) }},
                get filtered() {
                    if (!this.search.trim()) return this.options;
                    const q = this.search.toLowerCase().trim();
                    return this.options.filter(o => 
                        o.no_spt.toLowerCase().includes(q) || 
                        o.uraian.toLowerCase().includes(q) || 
                        o.objek.toLowerCase().includes(q) || 
                        o.irban.toLowerCase().includes(q)
                    );
                },
                select(id) {
                    this.selectedId = id;
                }
            }">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-sm">Form Hasil Telaah Tim Pemantauan TL</h3>
                <button type="button" onclick="document.getElementById('modalTelaahTim').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.ajukan_telaah', $tindakLanjut->id) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="st_pemantauan_id" :value="selectedId">

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Surat Perintah Tugas (SPT) Pemantauan Terkait
                    </label>
                    
                    <!-- Search Input -->
                    <div class="relative mb-2">
                        <input type="text" x-model="search" placeholder="Ketik No. SPT, Uraian, atau Objek Sasaran..." 
                            class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface px-3.5 py-2 text-ink font-medium">
                        <button type="button" x-show="search.length > 0" @click="search = ''" class="absolute right-3 top-2 text-ink-muted hover:text-ink text-xs font-bold" aria-label="Hapus pencarian">
                            &times;
                        </button>
                    </div>

                    <!-- Scrollable Filtered List -->
                    <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1 divide-y divide-hairline border border-hairline rounded-[10px] p-1.5 bg-surface">
                        <button type="button" @click="select('')" 
                            class="w-full text-left p-2 rounded-[6px] transition-colors flex items-center justify-between text-xs"
                            :class="!selectedId ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-semibold border border-amber-300 dark:border-amber-800' : 'hover:bg-surface-subtle text-ink-secondary'">
                            <span>-- Tanpa SPT Pemantauan (Belum Ada) --</span>
                            <span x-show="!selectedId" class="text-blue-800 dark:text-blue-300 text-[10px] font-semibold">Dipilih</span>
                        </button>
                        <template x-for="opt in filtered" :key="opt.id">
                            <button type="button" @click="select(opt.id)" 
                                class="w-full text-left p-2 rounded-[6px] transition-colors text-xs space-y-0.5"
                                :class="selectedId == opt.id ? 'bg-blue-50 dark:bg-blue-950/60 border border-blue-300 dark:border-blue-800 text-ink' : 'hover:bg-surface-subtle text-ink-secondary'">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-ink" x-text="opt.no_spt"></span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-[4px] font-semibold bg-surface-subtle text-ink-secondary border border-hairline" x-text="opt.irban"></span>
                                </div>
                                <p class="text-[11px] text-ink-secondary truncate" x-text="opt.uraian"></p>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Usulan Kesimpulan Status Rekomendasi <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <p class="text-[11px] text-ink-secondary mb-1.5">Pilih status usulan tim. Status resmi akan diaktifkan setelah melalui Verifikasi Irban dan Persetujuan Final Inspektur.</p>
                    <select name="status_rekomendasi" required class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-semibold">
                        <option value="selesai" {{ ($tindakLanjut->status_rekomendasi_usulan ?? $tindakLanjut->status_tindak_lanjut) === 'selesai' ? 'selected' : '' }}>SESUAI (SELESAI)</option>
                        <option value="proses" {{ in_array(($tindakLanjut->status_rekomendasi_usulan ?? $tindakLanjut->status_tindak_lanjut), ['proses', 'menunggu_verifikasi']) ? 'selected' : '' }}>BELUM SESUAI (PROSES / REVISI OPD)</option>
                        <option value="belum" {{ ($tindakLanjut->status_rekomendasi_usulan ?? $tindakLanjut->status_tindak_lanjut) === 'belum' ? 'selected' : '' }}>BELUM DITINDAKLANJUTI</option>
                        <option value="tdt" {{ ($tindakLanjut->status_rekomendasi_usulan ?? $tindakLanjut->status_tindak_lanjut) === 'tdt' ? 'selected' : '' }}>TIDAK DAPAT DITINDAKLANJUTI (TDT)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Catatan & Pertimbangan Hasil Telaah Tim <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <textarea name="catatan_telaah_tim" rows="4" required placeholder="Tuliskan analisis kelayakan bukti, kesesuaian dokumen, dan kesimpulan telaah tim..." class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-3 text-ink">{{ $tindakLanjut->hasil_telaah_tim ?? $tindakLanjut->catatan_telaah_tim }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalTelaahTim').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        Simpan & Ajukan ke Irban
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Modal Verifikasi Irban -->
    <div id="modalVerifikasiIrban" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-lg w-full space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-sm">Verifikasi Hasil Telaah oleh Irban</h3>
                <button type="button" onclick="document.getElementById('modalVerifikasiIrban').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.verifikasi_irban', $tindakLanjut->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Keputusan Verifikasi Irban <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <select name="aksi" required class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-semibold">
                        <option value="setujui">SETUJUI & Usulkan kepada Inspektur</option>
                        <option value="tolak">TOLAK & Kembalikan ke Tim Pemantauan (Revisi)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Catatan Verifikasi / Catatan Perbaikan Irban
                    </label>
                    <textarea name="catatan_verifikasi_irban" rows="3" placeholder="Tuliskan arahan atau catatan verifikasi untuk Inspektur atau Tim..." class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-3 text-ink">{{ $tindakLanjut->catatan_verifikasi_irban }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalVerifikasiIrban').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        Kirim Keputusan Irban
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Modal Persetujuan Akhir Inspektur -->
    <div id="modalPersetujuanInspektur" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-lg w-full space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-sm">Persetujuan Final Matriks TL oleh Inspektur</h3>
                <button type="button" onclick="document.getElementById('modalPersetujuanInspektur').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.persetujuan_inspektur', $tindakLanjut->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Keputusan Akhir Inspektur <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <select name="aksi" required class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-semibold">
                        <option value="setujui">SETUJUI FINAL (Matriks TL & Surat Pengantar Siap Diterbitkan)</option>
                        <option value="tolak">TOLAK (Kembalikan ke Irban & Tim untuk Disesuaikan)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Catatan / Arahan Khusus Inspektur
                    </label>
                    <textarea name="catatan_persetujuan_inspektur" rows="3" placeholder="Tuliskan arahan tindak lanjut pimpinan atau catatan penyesuaian..." class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-3 text-ink">{{ $tindakLanjut->catatan_persetujuan_inspektur }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalPersetujuanInspektur').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        Simpan Persetujuan Inspektur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. Modal Pengaturan Surat Pengantar Matriks TL -->
    <div id="modalSuratPengantar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-xl w-full space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-sm">Pengaturan Surat Pengantar Matriks TL</h3>
                <button type="button" onclick="document.getElementById('modalSuratPengantar').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.simpan_surat_pengantar', $tindakLanjut->id) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">
                            Nomor Surat Pengantar <span class="text-red-700 dark:text-red-400">*</span>
                        </label>
                        <input type="text" name="no_surat_pengantar" required value="{{ $tindakLanjut->no_surat_pengantar ?? '700/   /406.008/' . date('Y') }}" placeholder="mis. 700/123/406.008/2026" class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-mono font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">
                            Tanggal Surat Pengantar <span class="text-red-700 dark:text-red-400">*</span>
                        </label>
                        <input type="date" name="tgl_surat_pengantar" required value="{{ $tindakLanjut->tgl_surat_pengantar ? $tindakLanjut->tgl_surat_pengantar->format('Y-m-d') : date('Y-m-d') }}" class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">
                        Pilih Perangkat Daerah / Desa / Instansi Tujuan <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <select name="tujuan_surat_objek_id" required class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink font-semibold">
                        <option value="">-- Pilih Instansi / OPD Tujuan Surat dari Master --</option>
                        @foreach($objekList as $obj)
                            <option value="{{ $obj->id }}" {{ ($tindakLanjut->tujuan_surat_objek_id == $obj->id || ($tindakLanjut->penugasan && $tindakLanjut->penugasan->objekPenugasan->pluck('id')->contains($obj->id))) ? 'selected' : '' }}>
                                {{ $obj->nama }} ({{ ucfirst($obj->kategori ?? 'OPD') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-ink-muted mt-1">Daftar tujuan surat diambil otomatis dari data master Objek Penugasan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">
                            Sifat Surat
                        </label>
                        <input type="text" name="sifat_surat" value="{{ $tindakLanjut->sifat_surat ?? 'Biasa / Rahasia' }}" class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">
                            Hal Surat
                        </label>
                        <input type="text" name="hal_surat" value="{{ $tindakLanjut->hal_surat ?? 'Penyampaian Matriks Tindak Lanjut Hasil Pengawasan' }}" class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalSuratPengantar').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        Simpan Data Surat Pengantar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dynamic Live Currency Format Input (Ribuan Titik Koma)
        function formatRupiahInput(el) {
            if (!el) return;
            let cursorPos = el.selectionStart || 0;
            let originalLen = el.value.length;
            let rawVal = el.value.replace(/\D/g, '');
            if (!rawVal) {
                el.value = '';
                return;
            }
            let formatted = new Intl.NumberFormat('id-ID').format(rawVal);
            el.value = formatted;
            let newLen = formatted.length;
            cursorPos = cursorPos + (newLen - originalLen);
            if (cursorPos > 0 && el.setSelectionRange) {
                el.setSelectionRange(cursorPos, cursorPos);
            }
        }

        function openModalEditTl(data) {
            const form = document.getElementById('formEditTindakLanjut');
            form.action = '/tindak-lanjut/' + data.id;
            document.getElementById('editNoLhp').value = data.no_lhp || '';
            document.getElementById('editJudulLhp').value = data.judul_lhp || '';
            document.getElementById('editTglLhp').value = data.tgl_lhp || '';
            const objSelect = document.getElementById('editObjekPenugasanId');
            if (objSelect && data.objek_penugasan_id) {
                objSelect.value = data.objek_penugasan_id;
            }
            document.getElementById('editUraianTemuan').value = data.uraian_temuan;
            document.getElementById('editRekomendasi').value = data.rekomendasi;
            const ktSelect = document.getElementById('editKodeAtributTemuanId');
            if (ktSelect) {
                ktSelect.value = data.kode_atribut_temuan_id || '';
            }
            const krSelect = document.getElementById('editKodeAtributRekomendasiId');
            if (krSelect) {
                krSelect.value = data.kode_atribut_rekomendasi_id || '';
            }
            if (data.nilai_diawasi_rp) {
                document.getElementById('editNilaiDiawasiRp').value = new Intl.NumberFormat('id-ID').format(data.nilai_diawasi_rp);
            } else {
                document.getElementById('editNilaiDiawasiRp').value = '0';
            }
            if (data.nilai_rekomendasi_rp) {
                document.getElementById('editNilaiRp').value = new Intl.NumberFormat('id-ID').format(data.nilai_rekomendasi_rp);
            } else {
                document.getElementById('editNilaiRp').value = '';
            }
            document.getElementById('editTanggalTarget').value = data.tanggal_target;
            document.getElementById('editStatusTl').value = data.status_tindak_lanjut === 'dikembalikan' ? 'proses' : data.status_tindak_lanjut;
            document.getElementById('modalEditTindakLanjut').classList.remove('hidden');
        }
    </script>
</x-app-layout>
