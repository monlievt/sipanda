<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rincian LHP | Portal OPD SINGAWASI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleThemeMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>
<body class="bg-canvas text-ink antialiased font-sans min-h-screen flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand focus:text-white focus:rounded-[6px] focus:text-sm focus:font-semibold">
        Lewati ke konten utama
    </a>

    <!-- Navbar OPD -->
    <header class="bg-surface border-b border-hairline text-ink sticky top-0 z-30">
        <nav aria-label="Navigasi Rincian LHP" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('opd.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                        <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="font-bold text-sm text-ink hidden md:inline">SINGAWASI <span class="text-xs font-normal text-ink-muted">| Rincian LHP</span></span>
                    </a>

                    <a href="{{ route('opd.dashboard') }}" class="ledger-btn-secondary text-xs min-h-[38px] px-3 py-1.5">
                        Kembali ke Daftar LHP
                    </a>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button"
                            onclick="toggleThemeMode()"
                            aria-label="Ganti mode terang atau gelap"
                            title="Ganti Mode Terang / Gelap"
                            class="inline-flex items-center justify-center min-h-[38px] min-w-[38px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle border border-hairline transition-colors">
                        <svg class="w-[18px] h-[18px] block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg class="w-[18px] h-[18px] hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <button type="button"
                            onclick="document.getElementById('modalEditProfilOpd').classList.remove('hidden')"
                            title="Klik untuk ubah profil OPD"
                            class="hidden md:inline-flex items-center gap-1 min-h-[38px] px-3 text-xs text-ink-secondary hover:text-ink bg-surface-subtle hover:bg-surface rounded-[6px] border border-hairline transition-colors">
                        <span>PIC:</span>
                        <strong class="text-ink underline underline-offset-2">{{ $user->nama }}</strong>
                    </button>

                    <button type="button"
                            onclick="document.getElementById('modalEditProfilOpd').classList.remove('hidden')"
                            class="ledger-btn-primary text-xs min-h-[38px] px-3 py-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span class="hidden sm:inline">Edit Data OPD</span>
                        <span class="sm:hidden">Profil</span>
                    </button>

                    <form method="POST" action="{{ route('opd.logout') }}">
                        @csrf
                        <button type="submit" class="ledger-btn-secondary text-xs min-h-[38px] px-3 py-1.5">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div role="status" class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-900 dark:text-emerald-200 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[10px] text-red-800 dark:text-red-200 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Card Header LHP -->
        <section class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-hairline pb-5">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="px-2.5 py-0.5 bg-brand-soft text-brand-ink font-mono font-semibold text-xs rounded-[4px] border border-brand-border">
                            No. LHP: {{ $lhpSummary->no_lhp }}
                        </span>
                        <span class="text-xs text-ink-muted tabular-nums">Tanggal LHP: <strong class="text-ink">{{ \Carbon\Carbon::parse($lhpSummary->tgl_lhp)->format('d F Y') }}</strong></span>
                        @if($lhpSummary->penugasan?->no_spt)
                            <span class="text-xs text-ink-muted">| No. SPT: <span class="font-mono text-ink">{{ $lhpSummary->penugasan->no_spt }}</span></span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-ink leading-tight tracking-tight">
                        {{ $lhpSummary->judul_lhp }}
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('opd.lhp.berita_acara', $tindakLanjut->id ?? $lhpSummary->id) }}" target="_blank" class="ledger-btn-secondary text-xs min-h-[40px] px-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Berita Acara (PDF)</span>
                    </a>

                    @if($lhpSummary->berkas_dasar_lhp)
                        <a href="{{ route('dokumen.stream.path', $lhpSummary->berkas_dasar_lhp) }}" target="_blank" class="ledger-btn-primary text-xs min-h-[40px] px-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Dokumen LHP (PDF)</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Matriks Status & Finansial LHP -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3.5 bg-emerald-50/70 dark:bg-emerald-950/40 rounded-[6px] border border-emerald-200 dark:border-emerald-800/60">
                    <span class="font-semibold text-emerald-800 dark:text-emerald-300 block uppercase text-[11px]">Sesuai (SS)</span>
                    <span class="text-xl font-bold text-emerald-800 dark:text-emerald-300 mt-0.5 block tabular-nums">{{ $lhpSummary->count_sesuai }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-[6px] border border-blue-200 dark:border-blue-800/60">
                    <span class="font-semibold text-blue-800 dark:text-blue-300 block uppercase text-[11px]">Belum Sesuai (BS)</span>
                    <span class="text-xl font-bold text-blue-800 dark:text-blue-300 mt-0.5 block tabular-nums">{{ $lhpSummary->count_belum_sesuai }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-amber-50/70 dark:bg-amber-950/40 rounded-[6px] border border-amber-200 dark:border-amber-800/60">
                    <span class="font-semibold text-amber-900 dark:text-amber-300 block uppercase text-[11px]">Belum di-TL (BTL)</span>
                    <span class="text-xl font-bold text-amber-900 dark:text-amber-300 mt-0.5 block tabular-nums">{{ $lhpSummary->count_belum }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-surface-subtle rounded-[6px] border border-hairline">
                    <span class="font-semibold text-ink-secondary block uppercase text-[11px]">Tidak Dapat di-TL (TDT)</span>
                    <span class="text-xl font-bold text-ink mt-0.5 block tabular-nums">{{ $lhpSummary->count_tdt }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></span>
                </div>
            </div>

            @if($lhpSummary->total_target_rp > 0 || $lhpSummary->total_setor_rp > 0)
            <!-- Financial Tracking Bar -->
            <div class="p-4 bg-surface-subtle rounded-[6px] border border-hairline grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <span class="text-ink-muted font-semibold block text-[11px] uppercase">Kewajiban Setor Kas Daerah</span>
                    <span class="text-base font-bold text-ink tabular-nums">{{ $lhpSummary->formatted_total_target }}</span>
                </div>
                <div>
                    <span class="text-brand-ink font-semibold block text-[11px] uppercase">Telah Disetor ke Kasda</span>
                    <span class="text-base font-bold text-brand-ink tabular-nums">{{ $lhpSummary->formatted_total_setor }}</span>
                </div>
                <div>
                    <span class="text-red-700 dark:text-red-400 font-semibold block text-[11px] uppercase">Sisa Kurang Setor</span>
                    <span class="text-base font-bold text-red-700 dark:text-red-400 tabular-nums">{{ $lhpSummary->formatted_sisa_setor }}</span>
                </div>
            </div>
            @endif
        </section>

        <!-- Daftar Rincian Temuan & Rekomendasi -->
        <section class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h2 class="text-base sm:text-lg font-bold text-ink">
                    Daftar Rekomendasi / Saran ({{ $items->count() }} Item)
                </h2>
                <span class="text-xs text-ink-muted">Silakan berikan respon dan unggah bukti tindak lanjut di bawah</span>
            </div>

            @foreach($items as $idx => $item)
                <article class="bg-surface rounded-[10px] p-5 sm:p-6 border border-hairline space-y-5" id="rekomendasi-{{ $item->id }}">
                    <!-- Header Baris Rekomendasi -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-hairline pb-3.5">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="w-7 h-7 bg-brand text-white rounded-[6px] flex items-center justify-center font-bold text-xs shrink-0 tabular-nums">
                                #{{ $idx + 1 }}
                            </span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-semibold uppercase text-ink-muted">Status Rekomendasi:</span>
                                <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase border
                                    {{ $item->status_tindak_lanjut === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60' : '' }}
                                    {{ in_array($item->status_telaah, ['diajukan_irban', 'diajukan_inspektur']) || $item->status_tindak_lanjut === 'menunggu_verifikasi' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60' : '' }}
                                    {{ in_array($item->status_telaah, ['revisi_irban', 'revisi_inspektur']) ? 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800/60' : '' }}
                                    {{ in_array($item->status_tindak_lanjut, ['belum', 'proses']) && !in_array($item->status_telaah, ['diajukan_irban', 'diajukan_inspektur']) ? 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : '' }}
                                    {{ $item->status_tindak_lanjut === 'tdt' ? 'bg-surface-subtle text-ink-secondary border-hairline' : '' }}">
                                    @if(in_array($item->status_telaah, ['diajukan_irban', 'diajukan_inspektur']))
                                        Sedang Diverifikasi APIP (Telaah Berjenjang)
                                    @else
                                        {{ $item->status_label }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($item->tanggal_target)
                            <span class="text-xs text-ink-muted tabular-nums">
                                Target Penyelesaian: <strong class="text-ink">{{ $item->tanggal_target->format('d/m/Y') }}</strong>
                            </span>
                        @endif
                    </div>

                    <!-- Uraian Temuan & Rekomendasi -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="p-4 bg-surface-subtle rounded-[6px] border border-hairline space-y-1.5">
                            <span class="font-semibold text-ink-muted block uppercase text-[11px]">Uraian Temuan Pemeriksaan</span>
                            <p class="font-normal text-ink leading-relaxed">{{ $item->uraian_temuan }}</p>
                        </div>

                        <div class="p-4 bg-brand-soft rounded-[6px] border border-brand-border space-y-1.5">
                            <span class="font-semibold text-brand-ink block uppercase text-[11px]">Rekomendasi / Saran Wajib</span>
                            <p class="font-semibold text-ink leading-relaxed">{{ $item->rekomendasi }}</p>
                            @if($item->nilai_rekomendasi_rp > 0)
                                <div class="mt-2 pt-2 border-t border-brand-border flex items-center justify-between gap-2 flex-wrap text-xs tabular-nums">
                                    <span class="text-ink-secondary">Kewajiban Setor: <strong class="text-red-700 dark:text-red-400">{{ $item->formatted_nilai_rp }}</strong></span>
                                    <span class="text-ink-secondary">Telah Disetor: <strong class="text-brand-ink">{{ $item->formatted_total_setor }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Riwayat Setoran Finansial (Jika Ada) -->
                    @if($item->rincianPenyetoran->count() > 0)
                    <div class="p-4 bg-surface-subtle rounded-[6px] border border-hairline space-y-2.5 text-xs">
                        <span class="font-bold text-ink block uppercase text-[11px]">Rincian Penyetoran Kas Daerah (NTPN)</span>
                        <div class="overflow-x-auto bg-surface rounded-[6px] border border-hairline">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase border-b border-hairline">
                                    <tr>
                                        <th class="p-2.5">No. Referensi / NTPN</th>
                                        <th class="p-2.5">Bank</th>
                                        <th class="p-2.5">Nilai Setoran</th>
                                        <th class="p-2.5 text-center">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-hairline font-mono">
                                    @foreach($item->rincianPenyetoran as $s)
                                        <tr class="hover:bg-surface-subtle/60">
                                            <td class="p-2.5 font-semibold text-ink">{{ $s->no_referensi_ntpn ?: '-' }}</td>
                                            <td class="p-2.5 text-ink-secondary font-sans">{{ $s->nama_bank ?: 'Bank Jatim / Kas Daerah' }}</td>
                                            <td class="p-2.5 font-bold text-brand-ink tabular-nums">{{ $s->formatted_nilai_setor }}</td>
                                            <td class="p-2.5 text-center text-ink-muted font-sans tabular-nums">{{ $s->tgl_setor ? $s->tgl_setor->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Riwayat Respon & Catatan Verifikasi Sebelumnya -->
                    @if($item->buktiTindakLanjut->count() > 0)
                    <div class="space-y-2.5 text-xs">
                        <span class="font-semibold text-ink-muted block uppercase text-[11px]">Riwayat Pengajuan Bukti & Verifikasi APIP</span>
                        <div class="space-y-2">
                            @foreach($item->buktiTindakLanjut as $b)
                                <div class="p-3.5 rounded-[6px] border {{ $b->status_verifikasi === 'diterima' ? 'border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/20' : ($b->status_verifikasi === 'ditolak' ? 'border-red-200 dark:border-red-800/60 bg-red-50/40 dark:bg-red-950/20' : 'border-hairline bg-surface-subtle') }}">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <span class="text-xs font-medium text-ink-muted tabular-nums">{{ $b->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-semibold uppercase border
                                            {{ $b->status_verifikasi === 'diterima' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60' : '' }}
                                            {{ $b->status_verifikasi === 'ditolak' ? 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800/60' : '' }}
                                            {{ $b->status_verifikasi === 'menunggu' ? 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : '' }}">
                                            {{ $b->status_verifikasi }}
                                        </span>
                                    </div>
                                    <p class="font-medium text-ink">{{ $b->catatan_opd }}</p>

                                    @if($b->arsipDigital->count() > 0)
                                        <div class="mt-2 flex items-center gap-2 flex-wrap">
                                            @foreach($b->arsipDigital as $file)
                                                <a href="{{ route('dokumen.stream.path', $file->path_file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface border border-hairline rounded-[4px] text-xs font-semibold text-brand-ink hover:underline">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    <span>{{ Str::limit($file->nama_file, 24) }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($b->catatan_verifikasi)
                                        <div class="mt-2 pt-2 border-t border-hairline text-xs text-red-700 dark:text-red-300 font-semibold">
                                            Catatan APIP (Verifikator): {{ $b->catatan_verifikasi }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Form Respon & Input Setoran Baru -->
                    @if($item->status_tindak_lanjut !== 'selesai' && $item->status_tindak_lanjut !== 'tdt')
                    <div class="p-5 bg-surface-subtle rounded-[10px] border border-hairline space-y-4">
                        <div class="text-ink font-bold text-sm">
                            Kirim Tindak Lanjut & Setoran Baru
                        </div>

                        <form method="POST" action="{{ route('opd.tindak-lanjut.bukti.store', $item->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label for="catatan_opd_{{ $item->id }}" class="block text-xs font-semibold mb-1 text-ink">Uraian Langkah Tindak Lanjut OPD <span class="text-red-700 dark:text-red-400">*</span></label>
                                <textarea id="catatan_opd_{{ $item->id }}" name="catatan_opd" rows="3" required placeholder="Jelaskan secara rinci tindakan perbaikan, surat edaran, SOP, atau langkah penyelesaian yang telah dilakukan..." class="w-full"></textarea>
                            </div>

                            <!-- Input Setoran Finansial (Opsional / Kondisional) -->
                            <div class="p-4 bg-surface rounded-[6px] border border-hairline space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <span class="font-semibold text-ink text-xs">Penyetoran Kas Daerah (Jika Ada Unsur Finansial)</span>
                                    <span class="text-xs text-ink-muted">Kosongkan jika bukan rekomendasi finansial</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <label for="nilai_setor_rp_{{ $item->id }}" class="block font-semibold mb-1 text-xs text-ink-secondary">Nominal Setor (Rp)</label>
                                        <input id="nilai_setor_rp_{{ $item->id }}" type="text" name="nilai_setor_rp" oninput="formatRupiahInput(this)" placeholder="mis. 5.000.000" class="w-full font-semibold rupiah-input tabular-nums">
                                    </div>
                                    <div>
                                        <label for="nama_bank_{{ $item->id }}" class="block font-semibold mb-1 text-xs text-ink-secondary">Nama Bank / Kasda</label>
                                        <input id="nama_bank_{{ $item->id }}" type="text" name="nama_bank" placeholder="mis. Bank Jatim / Kasda" class="w-full">
                                    </div>
                                    <div>
                                        <label for="no_referensi_ntpn_{{ $item->id }}" class="block font-semibold mb-1 text-xs text-ink-secondary">No. STS / Bukti Bank / NTPN</label>
                                        <input id="no_referensi_ntpn_{{ $item->id }}" type="text" name="no_referensi_ntpn" placeholder="mis. STS-2026/08/01" class="w-full">
                                    </div>
                                    <div>
                                        <label for="tgl_setor_{{ $item->id }}" class="block font-semibold mb-1 text-xs text-ink-secondary">Tanggal Penyetoran</label>
                                        <input id="tgl_setor_{{ $item->id }}" type="date" name="tgl_setor" value="{{ date('Y-m-d') }}" class="w-full">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="file_{{ $item->id }}" class="block text-xs font-semibold mb-1 text-ink">Lampiran Berkas Bukti (PDF, DOCX, XLSX, JPG, Maks. 10MB)</label>
                                <input id="file_{{ $item->id }}" type="file" name="file" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs text-ink p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                                <p class="text-xs text-ink-muted mt-1">Unggah berkas bukti pendukung seperti scan STS, slip setoran bank, SK, SOP, atau foto dokumentasi perbaikan.</p>
                            </div>

                            <button type="submit" class="ledger-btn-primary min-h-[42px] px-5">
                                Kirim Respon Tindak Lanjut & Bukti
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 rounded-[6px] border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-semibold">
                        Rekomendasi ini telah dinyatakan <strong>{{ $item->status_label }}</strong> oleh Inspektorat.
                    </div>
                    @endif
                </article>
            @endforeach
        </section>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

    <!-- Global PDF & Document Preview Modal -->
    <x-pdf-preview-modal />

    <script>
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
    </script>

    <!-- Modal Edit Data & Profil OPD Mandiri -->
    @include('opd.partials.modal-edit-profil')
</body>
</html>
