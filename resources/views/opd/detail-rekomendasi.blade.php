<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Rekomendasi OPD | SINGAWASI</title>

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
        <nav aria-label="Navigasi Portal OPD" class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('opd.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="font-bold text-sm text-ink hidden sm:inline">SINGAWASI <span class="text-xs font-normal text-ink-muted">| Portal OPD</span></span>
                </a>
                <a href="{{ route('opd.dashboard') }}" class="ledger-btn-secondary text-xs min-h-[38px] px-3 py-1.5">
                    Kembali ke Dashboard OPD
                </a>
            </div>

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
        </nav>
    </header>

    <main id="main-content" class="max-w-4xl mx-auto py-8 px-4 sm:px-6 space-y-6 flex-1 w-full">
        <div>
            <p class="text-xs font-semibold text-brand-ink uppercase tracking-wide">Portal Perangkat Daerah</p>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight mt-0.5">Detail Rekomendasi Tindak Lanjut</h1>
        </div>

        @if (session('status'))
            <div role="status" class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-900 dark:text-emerald-200 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card Detail Rekomendasi -->
        <section class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-hairline pb-3">
                <span class="font-mono font-bold text-brand-ink text-sm">No. SPT: {{ $tindakLanjut->penugasan?->no_spt }}</span>
                <span class="px-2.5 py-0.5 rounded-[4px] font-semibold uppercase text-[11px] bg-surface-subtle text-ink border border-hairline">
                    Status: {{ $tindakLanjut->status_label }}
                </span>
            </div>

            <div>
                <span class="block text-xs font-semibold text-ink-muted uppercase">Uraian Temuan Inspektorat</span>
                <p class="text-sm font-medium text-ink mt-1 leading-relaxed">{{ $tindakLanjut->uraian_temuan }}</p>
            </div>

            <div>
                <span class="block text-xs font-semibold text-ink-muted uppercase">Rekomendasi Wajib Ditindaklanjuti</span>
                <p class="text-sm font-bold text-ink mt-1 leading-relaxed">{{ $tindakLanjut->rekomendasi }}</p>
            </div>
        </section>

        <!-- Form Unggah Bukti Baru -->
        @if($tindakLanjut->status_tindak_lanjut !== 'selesai')
        <section class="bg-surface rounded-[10px] p-6 border border-hairline">
            <h2 class="font-bold text-ink text-base mb-4">Kirim / Unggah Bukti Tindak Lanjut</h2>

            <form method="POST" action="{{ route('opd.tindak-lanjut.bukti.store', $tindakLanjut->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="catatan_opd" class="block text-xs font-semibold text-ink mb-1">Penjelasan / Uraian Tindak Lanjut OPD <span class="text-red-700 dark:text-red-400">*</span></label>
                    <textarea id="catatan_opd" name="catatan_opd" rows="3" required placeholder="Jelaskan langkah perbaikan / tindak lanjut yang telah dilaksanakan oleh OPD..." class="w-full"></textarea>
                </div>

                <!-- Input Setoran Finansial (Opsional / Kondisional) -->
                <div class="p-4 bg-surface-subtle rounded-[6px] border border-hairline space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="font-semibold text-ink text-xs">Penyetoran Kas Daerah (Jika Ada Unsur Finansial)</span>
                        <span class="text-xs text-ink-muted">Kosongkan jika bukan rekomendasi finansial</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label for="nilai_setor_rp" class="block font-semibold mb-1 text-xs text-ink-secondary">Nominal Setor (Rp)</label>
                            <input id="nilai_setor_rp" type="text" name="nilai_setor_rp" oninput="formatRupiahInput(this)" placeholder="mis. 5.000.000" class="w-full font-semibold rupiah-input tabular-nums">
                        </div>
                        <div>
                            <label for="nama_bank" class="block font-semibold mb-1 text-xs text-ink-secondary">Nama Bank / Kasda</label>
                            <input id="nama_bank" type="text" name="nama_bank" placeholder="mis. Bank Jatim / Kasda" class="w-full">
                        </div>
                        <div>
                            <label for="no_referensi_ntpn" class="block font-semibold mb-1 text-xs text-ink-secondary">No. STS / Bukti Bank / NTPN</label>
                            <input id="no_referensi_ntpn" type="text" name="no_referensi_ntpn" placeholder="mis. STS-2026/08/01" class="w-full">
                        </div>
                        <div>
                            <label for="tgl_setor" class="block font-semibold mb-1 text-xs text-ink-secondary">Tanggal Penyetoran</label>
                            <input id="tgl_setor" type="date" name="tgl_setor" value="{{ date('Y-m-d') }}" class="w-full">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="file" class="block text-xs font-semibold text-ink mb-1">Lampiran Berkas Bukti (PDF, DOCX, XLSX, JPG, Maks. 10MB)</label>
                    <input id="file" type="file" name="file" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs text-ink p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                </div>

                <button type="submit" class="ledger-btn-primary min-h-[42px] px-6">
                    Kirim Bukti ke Inspektorat
                </button>
            </form>
        </section>
        @endif

        <!-- Rincian Penyetoran Kas Daerah (Jika Ada) -->
        @if($tindakLanjut->rincianPenyetoran->count() > 0)
        <section class="bg-surface rounded-[10px] p-6 border border-hairline space-y-3">
            <h2 class="font-bold text-ink text-base">Rincian Penyetoran Kas Daerah (NTPN)</h2>
            <div class="overflow-x-auto rounded-[6px] border border-hairline">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase border-b border-hairline">
                        <tr>
                            <th class="p-3">No. Referensi / NTPN</th>
                            <th class="p-3">Bank</th>
                            <th class="p-3">Nilai Setoran</th>
                            <th class="p-3 text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline font-mono">
                        @foreach($tindakLanjut->rincianPenyetoran as $s)
                            <tr class="hover:bg-surface-subtle/60">
                                <td class="p-3 font-semibold text-ink">{{ $s->no_referensi_ntpn ?: '-' }}</td>
                                <td class="p-3 text-ink-secondary font-sans">{{ $s->nama_bank ?: 'Bank Jatim / Kas Daerah' }}</td>
                                <td class="p-3 font-bold text-brand-ink tabular-nums">{{ $s->formatted_nilai_setor }}</td>
                                <td class="p-3 text-center text-ink-muted font-sans tabular-nums">{{ $s->tgl_setor ? $s->tgl_setor->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <!-- Riwayat Pengajuan Bukti -->
        <section class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4">
            <h2 class="font-bold text-ink text-base">Riwayat Pengajuan Bukti & Catatan Verifikasi</h2>

            <div class="space-y-3 text-xs">
                @forelse($tindakLanjut->buktiTindakLanjut as $b)
                    <div class="p-4 rounded-[6px] border {{ $b->status_verifikasi === 'diterima' ? 'border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/20' : ($b->status_verifikasi === 'ditolak' ? 'border-red-200 dark:border-red-800/60 bg-red-50/40 dark:bg-red-950/20' : 'border-hairline bg-surface-subtle') }}">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-medium text-ink-muted tabular-nums">{{ $b->created_at->format('d/m/Y H:i') }}</span>
                            <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-semibold uppercase border
                                {{ $b->status_verifikasi === 'diterima' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60' : '' }}
                                {{ $b->status_verifikasi === 'ditolak' ? 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800/60' : '' }}
                                {{ $b->status_verifikasi === 'menunggu' ? 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : '' }}">
                                {{ $b->status_verifikasi }}
                            </span>
                        </div>
                        <p class="font-medium text-ink">{{ $b->catatan_opd }}</p>

                        @if($b->arsipDigital && $b->arsipDigital->count() > 0)
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
                            <div class="mt-2 pt-2 border-t border-hairline text-red-700 dark:text-red-300 font-semibold">
                                Catatan Verifikator: {{ $b->catatan_verifikasi }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-ink-muted text-center py-4">Belum ada bukti yang diunggah.</p>
                @endforelse
            </div>
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
</body>
</html>
