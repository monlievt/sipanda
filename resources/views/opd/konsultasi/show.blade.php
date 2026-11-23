<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Konsultasi #{{ $konsultasi->nomor_tiket }} | Portal OPD SINGAWASI</title>

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

    <!-- Main Detail OPD -->
    <main id="main-content" class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink transition-colors">
                Kembali ke Daftar Konsultasi
            </a>

            @if($konsultasi->status === 'selesai')
                <a href="{{ route('konsultasi.cetak_ba', $konsultasi->id) }}" target="_blank" class="ledger-btn-primary text-xs min-h-[40px] px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Berita Acara Hasil Konsultasi (PDF)</span>
                </a>
            @endif
        </div>

        @if (session('status'))
            <div role="status" class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-900 dark:text-emerald-200 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card Informasi Tiket -->
        <section class="p-6 bg-surface rounded-[10px] border border-hairline space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-hairline">
                <div>
                    <span class="text-xs font-bold text-brand-ink block font-mono">No. Tiket: {{ $konsultasi->nomor_tiket }}</span>
                    <h1 class="text-lg sm:text-xl font-bold text-ink leading-tight mt-1">{{ $konsultasi->judul_permasalahan }}</h1>
                </div>

                <span class="px-2.5 py-1 rounded-[4px] text-xs font-semibold uppercase tracking-wide block w-max border
                    {{ $konsultasi->status === 'menunggu_disposisi' ? 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : '' }}
                    {{ $konsultasi->status === 'berjalan' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60' : '' }}
                    {{ $konsultasi->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60' : '' }}">
                    Status: {{ $konsultasi->status_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="block text-ink-muted font-semibold uppercase text-[11px]">Area / Topik</span>
                    <span class="font-bold text-brand-ink uppercase block mt-0.5">{{ $konsultasi->area_konsultasi }}</span>
                </div>
                <div>
                    <span class="block text-ink-muted font-semibold uppercase text-[11px]">Irban Pembina</span>
                    <span class="font-semibold text-ink block mt-0.5">{{ $konsultasi->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                </div>
                <div>
                    <span class="block text-ink-muted font-semibold uppercase text-[11px]">Metode Konsultasi</span>
                    <span class="font-semibold text-ink block mt-0.5 tabular-nums">
                        @if($konsultasi->metode_disetujui)
                            {{ $konsultasi->metode_disetujui === 'online' ? 'Online Chat' : 'Tatap Muka (' . ($konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('d/m/Y H:i') : '-') . ')' }}
                        @else
                            Preferensi Usulan: {{ strtoupper($konsultasi->preferensi_metode) }}
                        @endif
                    </span>
                </div>
            </div>

            @if($konsultasi->disposisi_inspektur_pada)
                <div class="p-3.5 bg-blue-50/70 dark:bg-blue-950/40 rounded-[6px] border border-blue-200 dark:border-blue-800/60 text-xs space-y-1">
                    <span class="font-bold text-blue-900 dark:text-blue-200 block text-[11px] uppercase tracking-wide">
                        Status Disposisi Pimpinan Inspektorat
                    </span>
                    <p class="text-ink-secondary leading-relaxed tabular-nums">
                        Permohonan telah didisposisikan oleh <strong class="text-ink">{{ $konsultasi->inspekturPemberiDisposisi?->nama ?? 'Inspektur Daerah' }}</strong> kepada <strong class="text-ink">{{ $konsultasi->irban?->nama_irban }}</strong> pada {{ $konsultasi->disposisi_inspektur_pada->format('d/m/Y H:i') }} WIB untuk ditindaklanjuti.
                    </p>
                </div>
            @endif

            <div class="p-4 bg-surface-subtle rounded-[6px] border border-hairline space-y-1.5 text-xs sm:text-sm">
                <span class="font-semibold text-ink block text-xs uppercase">Uraian Permasalahan</span>
                <p class="text-ink-secondary leading-relaxed whitespace-pre-line">{{ $konsultasi->uraian_permasalahan }}</p>
                @if($konsultasi->berkas_pendukung)
                    <div class="pt-2">
                        <a href="{{ asset('storage/' . $konsultasi->berkas_pendukung) }}" target="_blank" class="text-xs font-semibold text-brand-ink hover:underline inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span>Unduh Berkas Pendukung</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Susunan Tim APIP Ditunjuk -->
            @if($konsultasi->tim->isNotEmpty())
                <div class="pt-3 border-t border-hairline">
                    <span class="text-xs font-semibold text-ink block mb-2">Tim APIP Inspektorat Ditunjuk</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 text-xs">
                        @foreach($konsultasi->tim as $tMember)
                            <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline">
                                <span class="text-[11px] text-ink-muted font-semibold uppercase block">{{ $tMember->peran_label }}</span>
                                <span class="font-semibold text-ink block mt-0.5">{{ $tMember->user?->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Advis Resmi APIP jika Selesai -->
            @if($konsultasi->kesimpulan_advis)
                <div class="p-4 bg-brand-soft rounded-[6px] border border-brand-border space-y-1.5 text-xs sm:text-sm">
                    <span class="font-bold text-brand-ink block text-xs uppercase">Advis & Solusi Resmi Inspektorat</span>
                    <p class="text-ink leading-relaxed whitespace-pre-line">{{ $konsultasi->kesimpulan_advis }}</p>
                </div>
            @endif
        </section>

        <!-- Ruang Percakapan Chat Online OPD -->
        <section class="bg-surface rounded-[10px] border border-hairline p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h2 class="font-bold text-ink text-sm sm:text-base tabular-nums">
                    Ruang Percakapan & Tanggapan APIP ({{ $konsultasi->chats->count() }} Pesan)
                </h2>
            </div>

            <!-- List Chat Bubble -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse($konsultasi->chats as $chat)
                    <div class="flex flex-col {{ $chat->tipe_pengirim === 'opd' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-2xl p-4 rounded-[10px] text-xs space-y-1.5 border
                            {{ $chat->tipe_pengirim === 'opd' ? 'bg-brand text-white border-brand' : 'bg-surface-subtle text-ink border-hairline' }}">
                            <div class="flex items-center justify-between gap-4 font-semibold text-[11px] {{ $chat->tipe_pengirim === 'opd' ? 'text-emerald-100 border-b border-white/20' : 'text-ink-muted border-b border-hairline' }} pb-1 mb-1">
                                <span>{{ $chat->tipe_pengirim === 'opd' ? 'Anda (OPD)' : ($chat->sender?->nama ?? 'Tim APIP Inspektorat') }}</span>
                                <span class="tabular-nums">{{ $chat->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="whitespace-pre-line leading-relaxed">{{ $chat->pesan }}</p>
                            @if($chat->lampiran_file)
                                <div class="pt-1">
                                    <a href="{{ asset('storage/' . $chat->lampiran_file) }}" target="_blank" class="font-semibold underline text-xs inline-flex items-center gap-1">
                                        <span>Unduh Berkas Lampiran</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-ink-muted text-xs font-medium">
                        Belum ada pesan percakapan. Tuliskan pesan di bawah ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Kirim Pesan OPD -->
            <form method="POST" action="{{ route('opd.konsultasi.chat', $konsultasi->id) }}" enctype="multipart/form-data" class="pt-4 border-t border-hairline space-y-3">
                @csrf
                <div>
                    <label for="chat_pesan" class="block text-xs font-semibold text-ink mb-1">Tulis Pesan untuk Tim APIP <span class="text-red-700 dark:text-red-400">*</span></label>
                    <textarea id="chat_pesan" name="pesan" rows="3" required placeholder="Tuliskan pesan pertanyaan / penjelasan tambahan untuk Tim APIP..." class="w-full"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div class="flex-1">
                        <label for="chat_lampiran_file" class="block text-xs font-semibold text-ink mb-1">Lampiran Berkas (Opsional)</label>
                        <input id="chat_lampiran_file" type="file" name="lampiran_file" class="w-full sm:w-auto rounded-[6px] border border-hairline-strong bg-surface text-xs text-ink p-1.5 file:mr-3 file:py-1 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                    </div>

                    <button type="submit" class="ledger-btn-primary min-h-[42px] px-5 shrink-0">
                        Kirim Pesan OPD
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
