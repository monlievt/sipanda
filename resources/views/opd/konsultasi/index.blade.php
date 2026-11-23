<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Consulting QnA APIP | Portal OPD SINGAWASI</title>

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
<body class="h-full font-sans antialiased bg-canvas text-ink flex flex-col min-h-screen">
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

                    <a href="{{ route('opd.konsultasi.index') }}" aria-current="page" class="hidden sm:inline-flex items-center min-h-[38px] px-3 text-xs font-semibold rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border">
                        E-Consulting (QnA APIP)
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
                        <strong class="text-ink underline underline-offset-2">{{ $opdUser->nama }}</strong>
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

    <!-- Main Container OPD -->
    <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div role="status" class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-900 dark:text-emerald-200 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Header Title & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Layanan E-Consulting & QnA APIP</h1>
                <p class="text-xs sm:text-sm text-ink-secondary mt-0.5">Ajukan konsultasi online/tatap muka mengenai pengadaan, keuangan APBD/APBDes, aset, atau regulasi ke Inspektorat.</p>
            </div>

            <a href="{{ route('opd.konsultasi.create') }}" class="ledger-btn-primary min-h-[42px] px-4 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Konsultasi Baru</span>
            </a>
        </div>

        <!-- Table Tiket Konsultasi OPD -->
        <section class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-subtle border-b border-hairline text-ink-secondary font-semibold uppercase tracking-wide">
                            <th class="py-3.5 px-4">No. Tiket / Tanggal</th>
                            <th class="py-3.5 px-4">Area & Permasalahan</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Irban & Status Tiket</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline text-ink font-medium">
                        @forelse($listKonsultasi as $item)
                            <tr class="hover:bg-surface-subtle/70 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-brand-ink block">{{ $item->nomor_tiket }}</span>
                                    <span class="text-xs text-ink-muted block tabular-nums">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="px-2 py-0.5 bg-brand-soft text-brand-ink border border-brand-border rounded-[4px] font-semibold text-[11px] uppercase inline-block mb-1">
                                        {{ $item->area_konsultasi }}
                                    </span>
                                    <p class="font-semibold text-ink line-clamp-1">{{ $item->judul_permasalahan }}</p>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($item->metode_disetujui)
                                        <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase border {{ $item->metode_disetujui === 'online' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60' : 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' }}">
                                            {{ $item->metode_disetujui === 'online' ? 'Online Chat' : 'Tatap Muka' }}
                                        </span>
                                    @else
                                        <span class="text-ink-muted text-xs">Preferensi: {{ strtoupper($item->preferensi_metode) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase tracking-wide inline-block mb-1 border
                                        {{ $item->status === 'menunggu_disposisi' ? 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : '' }}
                                        {{ $item->status === 'berjalan' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60' : '' }}
                                        {{ $item->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60' : '' }}">
                                        {{ $item->status_label }}
                                    </span>
                                    <span class="text-xs text-ink-muted block">{{ $item->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('opd.konsultasi.show', $item->id) }}" class="ledger-btn-primary text-xs min-h-[36px] px-3 py-1.5">
                                        Buka Chat & Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 px-4 text-center text-ink-muted font-medium">
                                    Belum ada konsultasi yang diajukan. Klik tombol di atas untuk membuat konsultasi baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listKonsultasi->hasPages())
                <div class="p-4 border-t border-hairline">
                    {{ $listKonsultasi->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Footer OPD -->
    <footer class="mt-auto bg-surface border-t border-hairline py-4 text-center text-xs text-ink-muted">
        &copy; {{ date('Y') }} Inspektorat Kabupaten Trenggalek | E-Consulting & QnA APIP.
    </footer>

    <!-- Modal Edit Data & Profil OPD Mandiri -->
    @include('opd.partials.modal-edit-profil')
</body>
</html>
