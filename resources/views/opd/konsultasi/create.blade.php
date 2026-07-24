<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Konsultasi Baru - Portal OPD</title>
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

    <!-- Main Form Create -->
    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center gap-1">
            &larr; Kembali ke Daftar Konsultasi
        </a>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Form Pengajuan Konsultasi & Advisory APIP Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Isikan rincian permasalahan atau pertanyaan pengawasan yang ingin dikonsultasikan kepada Inspektorat Kabupaten Trenggalek.</p>
            </div>

            <form method="POST" action="{{ route('opd.konsultasi.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Perangkat Daerah (OPD) Pemohon</label>
                    <input type="text" value="{{ $objek?->nama ?? $opdUser->nama }}" disabled class="w-full rounded-xl border-slate-200 bg-slate-100 dark:bg-slate-800 font-bold text-slate-600">
                </div>

                <div>
                    <label class="block font-bold text-teal-700 dark:text-teal-400 mb-1">Pilih Area / Topik Konsultasi *</label>
                    <select name="area_konsultasi" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold">
                        <option value="">-- Pilih Area Konsultasi --</option>
                        @foreach($areas as $ar)
                            <option value="{{ $ar }}">{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Judul Ringkas Permasalahan *</label>
                    <input type="text" name="judul_permasalahan" required placeholder="mis. Konsultasi Prosedur Pengadaan Barang/Jasa Dana Alokasi Khusus..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Uraian Permasalahan / Pertanyaan Detail *</label>
                    <textarea name="uraian_permasalahan" rows="6" required placeholder="Jelaskan kronologi, latar belakang masalah, dan poin pertanyaan yang ingin dikonsultasikan secara rinci..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-teal-500"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Preferensi Metode Konsultasi *</label>
                        <select name="preferensi_metode" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                            <option value="online">💬 Online Chat (Percakapan Daring di Aplikasi)</option>
                            <option value="offline">🤝 Tatap Muka (Pertemuan Pertemuan di Kantor Inspektorat)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Berkas Lampiran Pendukung (Opsional)</label>
                        <input type="file" name="berkas_pendukung" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        <p class="text-[10px] text-slate-400 mt-1">Format: PDF, Zip, Gambar, Word (Maks. 10 MB)</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <a href="{{ route('opd.konsultasi.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs rounded-xl shadow-xs">
                        Kirim Permohonan Konsultasi
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
