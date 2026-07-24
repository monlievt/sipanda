<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Konsultasi #{{ $konsultasi->nomor_tiket }} - Portal OPD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-200 flex flex-col min-h-screen">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('opd.dashboard') }}" class="font-extrabold text-lg tracking-tight hover:text-teal-200">
                        SIPANDA <span class="text-xs font-normal opacity-80">| Portal OPD</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Detail OPD -->
    <main class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center gap-1">
                &larr; Kembali ke Daftar Konsultasi
            </a>

            @if($konsultasi->status === 'selesai')
                <a href="{{ route('konsultasi.cetak_ba', $konsultasi->id) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-xs inline-flex items-center gap-1.5">
                    📄 Unduh Berita Acara Hasil Konsultasi (PDF)
                </a>
            @endif
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card Informas Tiket -->
        <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 block font-mono">No. Tiket: {{ $konsultasi->nomor_tiket }}</span>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ $konsultasi->judul_permasalahan }}</h2>
                </div>

                <span class="px-3 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider block w-max
                    {{ $konsultasi->status === 'menunggu_disposisi' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $konsultasi->status === 'berjalan' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $konsultasi->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                    Status: {{ $konsultasi->status_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Area / Topik</span>
                    <span class="font-bold text-teal-800 dark:text-teal-400 uppercase block">{{ $konsultasi->area_konsultasi }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Irban Pembina</span>
                    <span class="font-bold text-slate-900 dark:text-white block">{{ $konsultasi->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Metode Konsultasi</span>
                    <span class="font-bold text-slate-900 dark:text-white block">
                        @if($konsultasi->metode_disetujui)
                            {{ $konsultasi->metode_disetujui === 'online' ? '💬 Online Chat' : '🤝 Tatap Muka (' . ($konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('d/m/Y H:i') : '-') . ')' }}
                        @else
                            Preferensi Usulan: {{ strtoupper($konsultasi->preferensi_metode) }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1 text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300 block">Uraian Permasalahan:</span>
                <p class="text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $konsultasi->uraian_permasalahan }}</p>
                @if($konsultasi->berkas_pendukung)
                    <div class="pt-2">
                        <a href="{{ asset('storage/' . $konsultasi->berkas_pendukung) }}" target="_blank" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1">
                            📎 Unduh Berkas Pendukung
                        </a>
                    </div>
                @endif
            </div>

            <!-- Susunan Tim APIP Ditunjuk -->
            @if($konsultasi->tim->isNotEmpty())
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">Tim APIP Inspektorat Ditunjuk:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                        @foreach($konsultasi->tim as $tMember)
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">{{ $tMember->peran_label }}</span>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $tMember->user?->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Advis Resmi APIP jika Selesai -->
            @if($konsultasi->kesimpulan_advis)
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-900 space-y-1 text-xs">
                    <span class="font-bold text-emerald-900 dark:text-emerald-200 block">💡 Advis & Solusi Resmi Inspektorat:</span>
                    <p class="text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $konsultasi->kesimpulan_advis }}</p>
                </div>
            @endif
        </div>

        <!-- Ruang Percakapan Chat Online OPD -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-black text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    💬 Ruang Percakapan & Tanggapan APIP ({{ $konsultasi->chats->count() }} Pesan):
                </h3>
            </div>

            <!-- List Chat Bubble -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                @forelse($konsultasi->chats as $chat)
                    <div class="flex flex-col {{ $chat->tipe_pengirim === 'opd' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-2xl p-4 rounded-2xl text-xs space-y-1.5 shadow-xs
                            {{ $chat->tipe_pengirim === 'opd' ? 'bg-teal-700 text-white rounded-br-none' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-bl-none' }}">
                            <div class="flex items-center justify-between gap-4 font-bold text-[10px] opacity-80 border-b border-white/20 pb-1 mb-1">
                                <span>{{ $chat->tipe_pengirim === 'opd' ? 'Anda (OPD)' : ($chat->sender?->nama ?? 'Tim APIP Inspektorat') }}</span>
                                <span>{{ $chat->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="whitespace-pre-line leading-relaxed">{{ $chat->pesan }}</p>
                            @if($chat->lampiran_file)
                                <div class="pt-1">
                                    <a href="{{ asset('storage/' . $chat->lampiran_file) }}" target="_blank" class="font-bold underline text-[11px]">
                                        📎 Unduh Berkas Lampiran
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs font-medium">
                        Belum ada pesan percakapan. Tuliskan pesan di bawah ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Kirim Pesan OPD -->
            <form method="POST" action="{{ route('opd.konsultasi.chat', $konsultasi->id) }}" enctype="multipart/form-data" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                @csrf
                <div>
                    <textarea name="pesan" rows="3" required placeholder="Tuliskan pesan pertanyaan / penjelasan tambahan untuk Tim APIP..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-teal-500"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <input type="file" name="lampiran_file" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">

                    <button type="submit" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                        ✈️ Kirim Pesan OPD
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
