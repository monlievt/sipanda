<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Tindak Lanjut LHP | Portal OPD SINGAWASI</title>

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
        <nav aria-label="Navigasi Portal OPD" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-3">
                <div class="flex items-center gap-4 min-w-0">
                    <a href="{{ route('opd.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-sm sm:text-base text-ink tracking-tight block leading-tight">SINGAWASI</span>
                            <span class="text-[11px] text-ink-muted font-medium block truncate">Portal OPD</span>
                        </div>
                    </a>

                    <button type="button"
                            onclick="showFeatureUnderDev('Layanan E-Consulting APIP', 'Layanan permohonan konsultasi online Perangkat Daerah sedang dalam tahap perumusan standar operasional prosedur (SOP) dan finalisasi alur advis APIP.')"
                            class="hidden sm:inline-flex items-center gap-2 min-h-[38px] px-3 text-xs font-semibold rounded-[6px] bg-surface-subtle hover:bg-brand-soft text-ink-secondary hover:text-brand-ink border border-hairline transition-colors">
                        <span>E-Consulting</span>
                        <span class="text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-1.5 py-0.5 rounded-[4px] uppercase">Dev</span>
                    </button>
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

    <!-- Main Body OPD -->
    <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
            <div>
                <p class="text-xs font-semibold text-brand-ink uppercase tracking-wide">Portal Perangkat Daerah</p>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight mt-0.5">Dashboard Tindak Lanjut LHP</h1>
            </div>
            <p class="text-xs text-ink-muted">PIC Aktif: <strong class="text-ink">{{ $user->nama }}</strong></p>
        </div>

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
        @if ($errors->any())
            <div role="alert" class="p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[10px] text-red-800 dark:text-red-200 text-xs space-y-1">
                <p class="font-bold">Terjadi kesalahan pada isian form:</p>
                <ul class="list-disc pl-5 font-normal space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stat Cards OPD (Matriks Standar PTL Pengawasan) -->
        <section aria-label="Ringkasan status tindak lanjut" class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
            <div class="bg-surface p-4 rounded-[10px] border border-hairline">
                <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wide">Total LHP Diawasi</p>
                <p class="text-2xl font-bold text-ink mt-1 tabular-nums">{{ $rekap['total_lhp'] }} <span class="text-xs font-medium text-ink-muted">Dokumen</span></p>
                <p class="text-xs text-ink-secondary mt-0.5 tabular-nums">Total {{ $rekap['total_rekomendasi'] }} Rekomendasi</p>
            </div>
            <div class="bg-surface p-4 rounded-[10px] border border-emerald-200 dark:border-emerald-800/60">
                <p class="text-[11px] font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wide">Sesuai (SS)</p>
                <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1 tabular-nums">{{ $rekap['sesuai'] }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></p>
                <p class="text-xs text-ink-secondary mt-0.5">Tuntas & Selesai Diverifikasi</p>
            </div>
            <div class="bg-surface p-4 rounded-[10px] border border-blue-200 dark:border-blue-800/60">
                <p class="text-[11px] font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wide">Belum Sesuai (BS)</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1 tabular-nums">{{ $rekap['belum_sesuai'] }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></p>
                <p class="text-xs text-ink-secondary mt-0.5">Dalam Proses / Verifikasi</p>
            </div>
            <div class="bg-surface p-4 rounded-[10px] border border-amber-200 dark:border-amber-800/60">
                <p class="text-[11px] font-semibold text-amber-900 dark:text-amber-300 uppercase tracking-wide">Belum di-TL (BTL)</p>
                <p class="text-2xl font-bold text-amber-800 dark:text-amber-300 mt-1 tabular-nums">{{ $rekap['belum'] }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></p>
                <p class="text-xs text-ink-secondary mt-0.5">Perlu Tindak Lanjut Cepat</p>
            </div>
            <div class="bg-surface p-4 rounded-[10px] border border-hairline col-span-2 lg:col-span-1">
                <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wide">Tidak Dapat di-TL</p>
                <p class="text-2xl font-bold text-ink mt-1 tabular-nums">{{ $rekap['tdt'] }} <span class="text-xs font-medium text-ink-muted">Rekomendasi</span></p>
                <p class="text-xs text-ink-secondary mt-0.5">Kategori TDT / TDL BPKP</p>
            </div>
        </section>

        @if($rekap['total_target_rp'] > 0 || $rekap['total_setor_rp'] > 0)
        <!-- Financial Summary Banner -->
        <section aria-label="Ringkasan kewajiban finansial" class="bg-surface rounded-[10px] p-5 border border-hairline flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2.5">
                <p class="text-[11px] font-semibold tracking-wide text-brand-ink uppercase">Ringkasan Kewajiban Finansial / Pengembalian Kas Daerah</p>
                <div class="flex items-center gap-6 flex-wrap text-xs">
                    <div>
                        <span class="text-ink-muted block">Total Kewajiban Setor</span>
                        <span class="text-lg font-bold text-ink tabular-nums">Rp {{ number_format($rekap['total_target_rp'], 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-ink-muted block">Telah Disetor</span>
                        <span class="text-lg font-bold text-brand-ink tabular-nums">Rp {{ number_format($rekap['total_setor_rp'], 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-ink-muted block">Sisa Kurang Setor</span>
                        <span class="text-lg font-bold text-red-700 dark:text-red-400 tabular-nums">Rp {{ number_format($rekap['sisa_setor_rp'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="md:text-right border-t md:border-t-0 md:border-l border-hairline pt-3 md:pt-0 md:pl-6 shrink-0">
                <span class="text-[11px] text-ink-muted block uppercase font-semibold">Progres Penyelesaian LHP</span>
                <span class="text-2xl font-bold text-brand-ink tabular-nums">{{ $rekap['persen_selesai'] }}%</span>
            </div>
        </section>
        @endif

        <!-- Filter Box -->
        <section class="bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
            <form method="GET" action="{{ route('opd.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label for="filter-tahun" class="block text-xs font-semibold text-ink mb-1">Tahun Pengawasan</label>
                    <select id="filter-tahun" name="tahun" onchange="this.form.submit()" class="w-full font-medium">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-search" class="block text-xs font-semibold text-ink mb-1">Cari No. LHP / Judul</label>
                    <input id="filter-search" type="text" name="search" value="{{ $search }}" placeholder="Ketik No. LHP atau Judul..." class="w-full">
                </div>
                <div>
                    <button type="submit" class="ledger-btn-primary w-full min-h-[42px]">
                        Tampilkan Data
                    </button>
                </div>
            </form>
        </section>

        <!-- Table Rekapitulasi LHP OPD -->
        <section class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-hairline bg-surface-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="font-bold text-ink text-sm sm:text-base">Daftar Laporan Hasil Pengawasan (LHP) & Status Tindak Lanjut</h2>
                    <p class="text-xs text-ink-muted mt-0.5">Menampilkan seluruh LHP yang ditujukan untuk instansi ini</p>
                </div>
                <span class="text-xs font-semibold text-brand-ink bg-brand-soft px-2.5 py-1 rounded-[4px] border border-brand-border tabular-nums w-max">
                    Total: {{ $groupedLhp->count() }} LHP
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase border-b border-hairline">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4">Nomor & Tanggal LHP</th>
                            <th class="py-3 px-4">Judul / Uraian LHP</th>
                            <th class="py-3 px-4 text-center">Matriks Rekomendasi</th>
                            <th class="py-3 px-4 text-center">Kewajiban Setor (Rp)</th>
                            <th class="py-3 px-4 text-center">Progres</th>
                            <th class="py-3 px-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline">
                        @forelse($groupedLhp as $index => $lhp)
                            <tr class="hover:bg-surface-subtle/70 transition-colors">
                                <td class="py-3.5 px-4 text-center font-semibold text-ink-muted tabular-nums">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-brand-ink block text-xs">{{ $lhp->no_lhp }}</span>
                                    <span class="text-xs text-ink-muted tabular-nums">{{ \Carbon\Carbon::parse($lhp->tgl_lhp)->format('d/m/Y') }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-sm">
                                    <span class="font-semibold text-ink block line-clamp-2 leading-relaxed">{{ $lhp->judul_lhp }}</span>
                                    @if($lhp->penugasan?->irban)
                                        <span class="text-[11px] text-ink-muted">Pengawas: {{ $lhp->penugasan->irban->nama_irban }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 font-semibold rounded-[4px] text-[11px] tabular-nums" title="Sesuai">
                                            {{ $lhp->count_sesuai }} SS
                                        </span>
                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/50 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 font-semibold rounded-[4px] text-[11px] tabular-nums" title="Belum Sesuai">
                                            {{ $lhp->count_belum_sesuai }} BS
                                        </span>
                                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/50 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 font-semibold rounded-[4px] text-[11px] tabular-nums" title="Belum di-TL">
                                            {{ $lhp->count_belum }} BTL
                                        </span>
                                        @if($lhp->count_tdt > 0)
                                        <span class="px-2 py-0.5 bg-surface-subtle text-ink-secondary border border-hairline font-semibold rounded-[4px] text-[11px] tabular-nums" title="Tidak Dapat di-TL">
                                            {{ $lhp->count_tdt }} TDT
                                        </span>
                                        @endif
                                    </div>
                                    <span class="block text-[11px] text-ink-muted mt-1 tabular-nums">Total {{ $lhp->total_rekomendasi }} Rekomendasi</span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap tabular-nums">
                                    @if($lhp->total_nilai_target > 0)
                                        <span class="font-bold text-ink block text-xs">{{ $lhp->formatted_nilai_target }}</span>
                                        <span class="text-[11px] text-brand-ink block">Disetor: {{ $lhp->formatted_total_setor }}</span>
                                        @if($lhp->sisa_setor_rp > 0)
                                            <span class="text-[11px] text-red-700 dark:text-red-400 font-semibold block">Sisa: {{ $lhp->formatted_sisa_setor }}</span>
                                        @else
                                            <span class="inline-block mt-0.5 text-[10px] px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 rounded-[4px] font-semibold">LUNAS</span>
                                        @endif
                                    @else
                                        <span class="text-ink-muted text-xs">Non Finansial</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="w-16 mx-auto">
                                        <div class="text-xs font-semibold text-ink mb-1 tabular-nums">{{ $lhp->persen_selesai }}%</div>
                                        <div class="w-full bg-surface-subtle border border-hairline rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-brand h-1.5 rounded-full" style="width: {{ $lhp->persen_selesai }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('opd.lhp.show', $lhp->first_id) }}" class="ledger-btn-primary text-xs min-h-[36px] px-3 py-1.5">
                                        Rincian LHP
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center text-ink-muted">
                                    <p class="text-sm font-semibold text-ink">Belum ada dokumen LHP / rekomendasi pengawasan yang ditujukan untuk instansi ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

    <!-- Global PDF & Document Preview Modal -->
    <x-pdf-preview-modal />

    <!-- Global Under Development Modal -->
    <x-under-development-modal />

    <!-- Modal Edit Data & Profil OPD Mandiri -->
    @include('opd.partials.modal-edit-profil')
</body>
</html>
