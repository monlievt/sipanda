<x-app-layout>
    <x-slot name="header">
        Pusat Cadangan (Backup) Database & Pemeliharaan Sistem
    </x-slot>

    <div class="space-y-6" x-data="{ showModalPurge: false, showModalGdriveGuide: false }">
        <!-- Header Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Pusat Cadangan Data & Pemeliharaan Database</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola salinan cadangan (*backup*) otomatis multi-channel (Unduh Langsung, Email Admin, Google Drive) dan pembersihan data percobaan.</p>
            </div>
            @if($settings['last_run'])
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block font-semibold">Terakhir Dicadangkan:</span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ $settings['last_run'] }}</span>
                </div>
            @endif
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- TIGA OPSI / FITUR CADANGAN (UNDUH LANGSUNG, EMAIL, GOOGLE DRIVE) --}}
        {{-- ================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- OPSI 1: UNDUH CADANGAN MANUAL (ON-DEMAND) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-2xl">
                        📥
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-base">1. Unduh Cadangan Instan</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Buat salinan database terkini dalam format SQL dump dan unduh langsung ke komputer Anda.
                        </p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                        <div class="flex justify-between">
                            <span>Format Berkas:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">.sql murni</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kompatibilitas:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">MySQL & SQLite</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('backup.download_current') }}" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Database Sekarang (.sql)</span>
                    </a>
                </div>
            </div>

            <!-- OPSI 2: BACKUP OTOMATIS KE EMAIL ADMIN -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-2xl">
                        📧
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">2. Kirim Otomatis ke Email</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $settings['auto_email_enabled'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' }}">
                                {{ $settings['auto_email_enabled'] ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Sistem secara rutin mengirimkan berkas lampiran database ke kotak masuk email Super Admin.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('backup.update_settings') }}" id="formSettingsEmail" class="space-y-3 pt-2 text-xs">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="auto_email_enabled" value="1" {{ $settings['auto_email_enabled'] ? 'checked' : '' }} onchange="document.getElementById('formSettingsEmail').submit()" class="rounded-lg text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Aktifkan Pengiriman Otomatis</span>
                        </label>

                        <div>
                            <label class="block font-semibold text-slate-500 text-[10px] uppercase mb-1">Email Tujuan</label>
                            <input type="email" name="email_destination" value="{{ $settings['email_destination'] }}" placeholder="admin@trenggalek.go.id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-1.5 font-semibold">
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <button type="submit" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] rounded-xl transition-all">
                                Simpan Setting
                            </button>
                        </div>
                    </form>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <form method="POST" action="{{ route('backup.send_email') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $settings['email_destination'] }}">
                        <button type="submit" class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>⚡ Tes Kirim ke Email Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- OPSI 3: SINKRONISASI CLOUD GOOGLE DRIVE -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-black text-2xl">
                        ☁️
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">3. Sinkron Google Drive</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $settings['gdrive_enabled'] ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : 'bg-slate-200 text-slate-700' }}">
                                {{ $settings['gdrive_enabled'] ? 'SIAP SINKRON' : 'OPSIONAL' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Mencadangkan database dan seluruh arsip berkas bukti PDF audit ke folder Google Drive resmi Inspektorat.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('backup.update_settings') }}" id="formSettingsGdrive" class="space-y-3 pt-2 text-xs">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="gdrive_enabled" value="1" {{ $settings['gdrive_enabled'] ? 'checked' : '' }} onchange="document.getElementById('formSettingsGdrive').submit()" class="rounded-lg text-purple-600 focus:ring-purple-500 w-4 h-4 border-slate-300">
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Aktifkan Sinkronisasi Cloud</span>
                        </label>

                        <div>
                            <label class="block font-semibold text-slate-500 text-[10px] uppercase mb-1">Nama Folder Target di Google Drive</label>
                            <input type="text" name="gdrive_folder" value="{{ $settings['gdrive_folder'] }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-1.5 font-semibold">
                        </div>

                        <div>
                            <button type="submit" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] rounded-xl transition-all">
                                Simpan Nama Folder
                            </button>
                        </div>
                    </form>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showModalGdriveGuide = true" class="w-full py-2.5 px-3 bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>📖 Panduan Koneksi Google Drive (VPS)</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- DAFTAR BERKAS CADANGAN DI SERVER LOKAL                           --}}
        {{-- ================================================================= --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">🗄️ Riwayat Berkas Cadangan di Server</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ditemukan <strong>{{ count($backupsList) }} berkas cadangan</strong> tersimpan di folder server lokal (otomatis dibersihkan jika berumur > 7 hari).</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Berkas Cadangan</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4">Waktu Pembuatan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                        @forelse($backupsList as $b)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="text-blue-600">📄</span>
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $b['size'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    <span class="block font-medium">{{ $b['created_at'] }} WIB</span>
                                    <span class="text-[10px] text-slate-400">({{ $b['age'] }})</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('backup.download_file', $b['filename']) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 font-bold text-xs rounded-xl flex items-center gap-1 transition-all">
                                            <span>📥 Unduh</span>
                                        </a>
                                        <form method="POST" action="{{ route('backup.delete_file', $b['filename']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas cadangan {{ $b['filename'] }} dari server?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 font-bold text-xs rounded-xl transition-all cursor-pointer">
                                                <span>🗑️ Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-medium">
                                    Belum ada berkas cadangan di server. Klik "Unduh Database Sekarang" atau aktifkan jadwal backup otomatis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- SECTION KHUSUS SUPER ADMIN: PEMBERSIHAN DATA PERCOBAAN / TESTING  --}}
        {{-- ================================================================= --}}
        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <div class="p-6 bg-amber-50/60 dark:bg-amber-950/30 rounded-3xl border border-amber-200 dark:border-amber-900 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xl shrink-0">
                            🛠️
                        </div>
                        <div>
                            <h3 class="font-black text-amber-950 dark:text-amber-200 text-base">Pusat Pembersihan Data Percobaan (Purge Dummy Data)</h3>
                            <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                                Gunakan fitur ini untuk membersihkan data uji coba / simulasi sebelum aplikasi resmi digunakan (*Go-Live*).
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="showModalPurge = true" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition-all shrink-0 flex items-center gap-1.5 cursor-pointer">
                        <span>🧹 Bersihkan Data Percobaan...</span>
                    </button>
                </div>

                <!-- Statistik Data Operasional Saat Ini -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Penugasan (SPT)</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">{{ $purgeStats['penugasan'] }} data</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Rekomendasi LHP</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">{{ $purgeStats['tindak_lanjut'] }} data</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Berkas Bukti OPD</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">{{ $purgeStats['bukti_tl'] }} berkas</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">E-Consulting (QnA)</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">{{ $purgeStats['konsultasi'] }} tiket</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200 dark:border-amber-900 col-span-2 sm:col-span-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Notifikasi & Log</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">{{ $purgeStats['notifikasi'] + $purgeStats['activity_logs'] }} log</span>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-amber-100 dark:border-amber-950 text-[11px] text-slate-600 dark:text-slate-400 flex items-center gap-2">
                    <span class="text-emerald-600 font-bold">🔒 Proteksi Data Inti:</span>
                    <span>Seluruh Akun Pegawai (Inspektur, Irban, Auditor, OPD), Struktur Irban, dan Bank Regulasi <strong>DIJAMIN UTUH 100%</strong> dan tidak akan tersentuh penghapusan.</span>
                </div>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- MODAL KONFIRMASI PEMBERSIHAN DATA PERCOBAAN                       --}}
        {{-- ================================================================= --}}
        <div x-show="showModalPurge" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl p-6 space-y-5 shadow-2xl border border-rose-200 dark:border-rose-900">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 text-rose-600">
                        <span class="text-xl">⚠️</span>
                        <h3 class="font-black text-slate-900 dark:text-white text-base">Konfirmasi Pembersihan Data</h3>
                    </div>
                    <button type="button" @click="showModalPurge = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('backup.purge_dummy') }}" class="space-y-4 text-xs">
                    @csrf

                    <div class="p-3 bg-rose-50 dark:bg-rose-950/50 rounded-xl border border-rose-200 dark:border-rose-900 text-rose-900 dark:text-rose-200 text-[11px] leading-relaxed">
                        Tindakan ini akan menghapus data operasional yang dipilih secara permanen. Pastikan Anda telah mengunduh cadangan database terlebih dahulu.
                    </div>

                    <!-- Checklist Modul yang Dihapus -->
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200 block mb-2">Pilih Modul Data yang Ingin Dibersihkan:</span>
                        <div class="space-y-2 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-2xl">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="modules[]" value="tindak_lanjut" checked class="rounded text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Matriks Tindak Lanjut & LHP</span>
                                    <span class="text-[10px] text-slate-500">Termasuk {{ $purgeStats['tindak_lanjut'] }} butir rekomendasi dan {{ $purgeStats['bukti_tl'] }} berkas bukti upload OPD.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-slate-200 dark:border-slate-700">
                                <input type="checkbox" name="modules[]" value="penugasan" checked class="rounded text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Penugasan & SPT Pengawasan</span>
                                    <span class="text-[10px] text-slate-500">Termasuk {{ $purgeStats['penugasan'] }} data SPT dan susunan tim auditor penugasan.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-slate-200 dark:border-slate-700">
                                <input type="checkbox" name="modules[]" value="konsultasi" checked class="rounded text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">E-Consulting & QnA APIP</span>
                                    <span class="text-[10px] text-slate-500">Termasuk {{ $purgeStats['konsultasi'] }} tiket permohonan konsultasi dan ruang percakapan.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-slate-200 dark:border-slate-700">
                                <input type="checkbox" name="modules[]" value="logs" checked class="rounded text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">Riwayat Log Aktivitas & Notifikasi</span>
                                    <span class="text-[10px] text-slate-500">Termasuk {{ $purgeStats['notifikasi'] + $purgeStats['activity_logs'] }} riwayat notifikasi masa testing.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Ketik Konfirmasi -->
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">
                            Ketik Frasa Persetujuan: <span class="font-mono text-rose-600 uppercase">BERSIHKAN DATA PERCOBAAN</span> *
                        </label>
                        <input type="text" name="konfirmasi_kata" required placeholder="Ketik persis: BERSIHKAN DATA PERCOBAAN" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    </div>

                    <!-- Password Super Admin -->
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">
                            Masukkan Password Akun Anda Saat Ini *
                        </label>
                        <input type="password" name="password_konfirmasi" required placeholder="Kata sandi akun Anda" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" @click="showModalPurge = false" class="px-4 py-2.5 bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold rounded-xl text-xs shadow-md cursor-pointer">
                            Eksekusi Pembersihan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL PANDUAN KONEKSI GOOGLE DRIVE VIA RCLONE                     --}}
        {{-- ================================================================= --}}
        <div x-show="showModalGdriveGuide" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-800 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <span>☁️ Panduan Sinkronisasi Google Drive Otomatis (VPS)</span>
                    </h3>
                    <button type="button" @click="showModalGdriveGuide = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-slate-700 dark:text-slate-300 leading-relaxed">
                    <p>
                        Untuk mencadangkan seluruh berkas database dan dokumen PDF ke Google Drive Inspektorat Trenggalek tanpa batas ukuran, gunakan utilitas resmi <strong>Rclone</strong> di server Linux VPS:
                    </p>

                    <div class="space-y-2 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl text-[11px]">
                        <p class="font-bold text-slate-900 dark:text-white">Langkah 1: Hubungkan Akun Google Drive di Server</p>
                        <code class="block font-mono bg-slate-900 text-emerald-400 p-2.5 rounded-xl">rclone config</code>
                        <span class="text-slate-400 block mt-1">Pilih Google Drive, beri nama remote: <code class="text-blue-500 font-bold">gdrive</code>.</span>

                        <p class="font-bold text-slate-900 dark:text-white pt-2">Langkah 2: Pasang Jadwal Cron Job Otomatis di VPS</p>
                        <span class="text-slate-400 block mb-1">Buka crontab server via <code class="text-blue-500">crontab -e</code> lalu tambahkan:</span>
                        <code class="block font-mono bg-slate-900 text-emerald-400 p-2.5 rounded-xl text-[10px]">
                            0 2 * * * rclone sync /home/inspektorat/domains/sipanda.inspektorat.trenggalekkab.go.id/public_html/storage/app/backups gdrive:SIPANDA_TRENGGALEK_BACKUP
                        </code>
                        <span class="text-slate-400 block mt-1">Setiap pukul 02.00 dini hari, seluruh berkas cadangan otomatis tersinkron ke Google Drive.</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="button" @click="showModalGdriveGuide = false" class="px-5 py-2 bg-slate-900 text-white font-bold rounded-xl text-xs">
                        Tutup Panduan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
