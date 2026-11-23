<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Konsultasi Baru | Portal OPD SINGAWASI</title>

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
            </div>
        </nav>
    </header>

    <!-- Main Form Create -->
    <main id="main-content" class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        <div>
            <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink transition-colors">
                Kembali ke Daftar Konsultasi
            </a>
        </div>

        <section class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-5">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-ink tracking-tight">Form Pengajuan Konsultasi & Advisory APIP Baru</h1>
                <p class="text-xs sm:text-sm text-ink-secondary mt-1">Isikan rincian permasalahan atau pertanyaan pengawasan yang ingin dikonsultasikan kepada Inspektorat Kabupaten Trenggalek.</p>
            </div>

            <form method="POST" action="{{ route('opd.konsultasi.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="opd_pemohon" class="block font-semibold text-xs sm:text-sm text-ink mb-1">Perangkat Daerah (OPD) Pemohon</label>
                    <input id="opd_pemohon" type="text" value="{{ $objek?->nama ?? $opdUser->nama }}" disabled class="w-full font-semibold opacity-75">
                </div>

                <div>
                    <label for="area_konsultasi" class="block font-semibold text-xs sm:text-sm text-brand-ink mb-1">Pilih Area / Topik Konsultasi <span class="text-red-700 dark:text-red-400">*</span></label>
                    <select id="area_konsultasi" name="area_konsultasi" required class="w-full font-semibold">
                        <option value="">Pilih Area Konsultasi</option>
                        @foreach($areas as $ar)
                            <option value="{{ $ar }}">{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="judul_permasalahan" class="block font-semibold text-xs sm:text-sm text-ink mb-1">Judul Ringkas Permasalahan <span class="text-red-700 dark:text-red-400">*</span></label>
                    <input id="judul_permasalahan" type="text" name="judul_permasalahan" required placeholder="mis. Konsultasi Prosedur Pengadaan Barang/Jasa Dana Alokasi Khusus..." class="w-full">
                </div>

                <div>
                    <label for="uraian_permasalahan" class="block font-semibold text-xs sm:text-sm text-ink mb-1">Uraian Permasalahan / Pertanyaan Detail <span class="text-red-700 dark:text-red-400">*</span></label>
                    <textarea id="uraian_permasalahan" name="uraian_permasalahan" rows="6" required placeholder="Jelaskan kronologi, latar belakang masalah, dan poin pertanyaan yang ingin dikonsultasikan secara rinci..." class="w-full"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="preferensi_metode" class="block font-semibold text-xs sm:text-sm text-ink mb-1">Preferensi Metode Konsultasi <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select id="preferensi_metode" name="preferensi_metode" required class="w-full font-medium">
                            <option value="online">Online Chat (Percakapan Daring di Aplikasi)</option>
                            <option value="offline">Tatap Muka (Pertemuan di Kantor Inspektorat)</option>
                        </select>
                    </div>

                    <div>
                        <label for="berkas_pendukung" class="block font-semibold text-xs sm:text-sm text-ink mb-1">Berkas Lampiran Pendukung (Opsional)</label>
                        <input id="berkas_pendukung" type="file" name="berkas_pendukung" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs text-ink p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                        <p class="text-xs text-ink-muted mt-1">Format: PDF, Zip, Gambar, Word (Maks. 10 MB)</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-hairline flex justify-end gap-2">
                    <a href="{{ route('opd.konsultasi.index') }}" class="ledger-btn-secondary min-h-[42px] px-4">Batal</a>
                    <button type="submit" class="ledger-btn-primary min-h-[42px] px-6">
                        Kirim Permohonan Konsultasi
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
