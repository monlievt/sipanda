<x-app-layout>
    <x-slot name="header">
        Pusat Cadangan (Backup) Database & Pemeliharaan Sistem
    </x-slot>

    <div class="space-y-6" x-data="{ showModalPurge: false, showModalGdriveGuide: false }">
        <!-- Header Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink tracking-tight">Pusat Cadangan Data & Pemeliharaan Database</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Kelola salinan cadangan (backup) otomatis multi-channel (Unduh Langsung, Email Admin, Google Drive) dan pembersihan data percobaan.</p>
            </div>
            @if($settings['last_run'])
                <div class="text-right">
                    <span class="text-[10px] text-ink-muted block font-semibold">Terakhir Dicadangkan:</span>
                    <span class="text-xs font-bold text-brand-ink font-mono">{{ $settings['last_run'] }}</span>
                </div>
            @endif
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-[10px] text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- TIGA OPSI / FITUR CADANGAN (UNDUH LANGSUNG, EMAIL, GOOGLE DRIVE) --}}
        {{-- ================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- OPSI 1: UNDUH CADANGAN MANUAL (ON-DEMAND) -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-[6px] bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-ink text-[15px]">1. Unduh Cadangan Instan</h3>
                        <p class="text-[11px] text-ink-secondary mt-1 leading-relaxed">
                            Buat salinan database terkini dalam format SQL dump dan unduh langsung ke komputer Anda.
                        </p>
                    </div>

                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline text-[11px] text-ink-secondary space-y-1">
                        <div class="flex justify-between">
                            <span>Format Berkas:</span>
                            <span class="font-bold text-ink font-mono">.sql murni</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kompatibilitas:</span>
                            <span class="font-bold text-ink">MySQL & SQLite</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('backup.download_current') }}" class="ledger-btn-primary w-full !text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Database Sekarang (.sql)</span>
                    </a>
                </div>
            </div>

            <!-- OPSI 2: BACKUP OTOMATIS KE EMAIL ADMIN -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-bold text-ink text-[15px]">2. Kirim Otomatis ke Email</h3>
                            <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold {{ $settings['auto_email_enabled'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : 'bg-surface-subtle text-ink-secondary border border-hairline' }}">
                                {{ $settings['auto_email_enabled'] ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-ink-secondary mt-1 leading-relaxed">
                            Sistem secara rutin mengirimkan berkas lampiran database ke kotak masuk email Super Admin.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('backup.update_settings') }}" id="formSettingsEmail" class="space-y-3 pt-2 text-xs">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="auto_email_enabled" value="1" {{ $settings['auto_email_enabled'] ? 'checked' : '' }} onchange="document.getElementById('formSettingsEmail').submit()" class="rounded-[4px] text-brand focus:ring-brand w-4 h-4 border-hairline-strong">
                            <span class="font-bold text-ink text-[11px]">Aktifkan Pengiriman Otomatis</span>
                        </label>

                        <div>
                            <label class="block font-semibold text-ink-secondary text-[10px] uppercase mb-1">Email Tujuan</label>
                            <input type="email" name="email_destination" value="{{ $settings['email_destination'] }}" placeholder="admin@trenggalekkab.go.id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3 py-1.5 font-semibold">
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <button type="submit" class="ledger-btn-secondary !min-h-0 !py-1.5 !px-3 !text-[11px]">
                                Simpan Setting
                            </button>
                        </div>
                    </form>
                </div>

                <div class="pt-3 border-t border-hairline">
                    <form method="POST" action="{{ route('backup.send_email') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $settings['email_destination'] }}">
                        <button type="submit" class="ledger-btn-primary w-full !text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Tes Kirim ke Email Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- OPSI 3: SINKRONISASI CLOUD GOOGLE DRIVE -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-[6px] bg-surface-subtle text-ink border border-hairline-strong flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-bold text-ink text-[15px]">3. Sinkron Google Drive</h3>
                            <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold {{ $settings['gdrive_enabled'] ? 'bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800' : 'bg-surface-subtle text-ink-secondary border border-hairline' }}">
                                {{ $settings['gdrive_enabled'] ? 'SIAP SINKRON' : 'OPSIONAL' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-ink-secondary mt-1 leading-relaxed">
                            Mencadangkan database dan seluruh arsip berkas bukti PDF audit ke folder Google Drive resmi Inspektorat.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('backup.update_settings') }}" id="formSettingsGdrive" class="space-y-3 pt-2 text-xs">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="gdrive_enabled" value="1" {{ $settings['gdrive_enabled'] ? 'checked' : '' }} onchange="document.getElementById('formSettingsGdrive').submit()" class="rounded-[4px] text-brand focus:ring-brand w-4 h-4 border-hairline-strong">
                            <span class="font-bold text-ink text-[11px]">Aktifkan Sinkronisasi Cloud</span>
                        </label>

                        <div>
                            <label class="block font-semibold text-ink-secondary text-[10px] uppercase mb-1">Nama Folder Target di Google Drive</label>
                            <input type="text" name="gdrive_folder" value="{{ $settings['gdrive_folder'] }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3 py-1.5 font-semibold">
                        </div>

                        <div>
                            <button type="submit" class="ledger-btn-secondary !min-h-0 !py-1.5 !px-3 !text-[11px]">
                                Simpan Nama Folder
                            </button>
                        </div>
                    </form>
                </div>

                <div class="pt-3 border-t border-hairline">
                    <button type="button" @click="showModalGdriveGuide = true" class="ledger-btn-secondary w-full !text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Panduan Koneksi Google Drive (VPS)</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- DAFTAR BERKAS CADANGAN DI SERVER LOKAL                           --}}
        {{-- ================================================================= --}}
        <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="p-5 border-b border-hairline flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-ink text-[15px]">Riwayat Berkas Cadangan di Server</h3>
                    <p class="text-xs text-ink-secondary mt-0.5">Ditemukan <strong>{{ count($backupsList) }} berkas cadangan</strong> tersimpan di folder server lokal (otomatis dibersihkan jika berumur > 7 hari).</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-subtle border-b border-hairline text-ink-secondary font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Berkas Cadangan</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4">Waktu Pembuatan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline text-ink">
                        @forelse($backupsList as $b)
                            <tr class="hover:bg-surface-subtle transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-ink flex items-center gap-2">
                                    <svg class="w-4 h-4 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-ink-secondary">
                                    {{ $b['size'] }}
                                </td>
                                <td class="py-3.5 px-4 text-ink-secondary text-[11px]">
                                    <span class="block font-medium">{{ $b['created_at'] }} WIB</span>
                                    <span class="text-[10px] text-ink-muted">({{ $b['age'] }})</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('backup.download_file', $b['filename']) }}" class="px-3 py-1.5 bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800 font-bold text-xs rounded-[6px] inline-flex items-center gap-1.5 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Unduh</span>
                                        </a>
                                        <form method="POST" action="{{ route('backup.delete_file', $b['filename']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas cadangan {{ $b['filename'] }} dari server?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800 font-bold text-xs rounded-[6px] inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-ink-muted font-medium">
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
            <div class="p-6 bg-amber-50 dark:bg-amber-950/30 rounded-[10px] border border-amber-200 dark:border-amber-900 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[6px] bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-200 border border-amber-300 dark:border-amber-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-amber-950 dark:text-amber-200 text-[15px]">Pusat Pembersihan Data Percobaan (Purge Dummy Data)</h3>
                            <p class="text-xs text-amber-900 dark:text-amber-300 mt-0.5 leading-relaxed">
                                Gunakan fitur ini untuk membersihkan data uji coba / simulasi sebelum aplikasi resmi digunakan (Go-Live).
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="showModalPurge = true" class="px-4 py-2.5 bg-rose-700 hover:bg-rose-800 text-white font-semibold text-xs rounded-[6px] transition-colors shrink-0 inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Bersihkan Data Percobaan...</span>
                    </button>
                </div>

                <!-- Statistik Data Operasional Saat Ini -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
                    <div class="p-3 bg-surface rounded-[10px] border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-ink-muted font-bold uppercase block">Penugasan (SPT)</span>
                        <span class="text-lg font-bold text-ink tabular-nums">{{ $purgeStats['penugasan'] }} data</span>
                    </div>
                    <div class="p-3 bg-surface rounded-[10px] border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-ink-muted font-bold uppercase block">Rekomendasi LHP</span>
                        <span class="text-lg font-bold text-ink tabular-nums">{{ $purgeStats['tindak_lanjut'] }} data</span>
                    </div>
                    <div class="p-3 bg-surface rounded-[10px] border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-ink-muted font-bold uppercase block">Berkas Bukti OPD</span>
                        <span class="text-lg font-bold text-ink tabular-nums">{{ $purgeStats['bukti_tl'] }} berkas</span>
                    </div>
                    <div class="p-3 bg-surface rounded-[10px] border border-amber-200 dark:border-amber-900">
                        <span class="text-[10px] text-ink-muted font-bold uppercase block">E-Consulting (QnA)</span>
                        <span class="text-lg font-bold text-ink tabular-nums">{{ $purgeStats['konsultasi'] }} tiket</span>
                    </div>
                    <div class="p-3 bg-surface rounded-[10px] border border-amber-200 dark:border-amber-900 col-span-2 sm:col-span-1">
                        <span class="text-[10px] text-ink-muted font-bold uppercase block">Notifikasi & Log</span>
                        <span class="text-lg font-bold text-ink tabular-nums">{{ $purgeStats['notifikasi'] + $purgeStats['activity_logs'] }} log</span>
                    </div>
                </div>

                <div class="p-3.5 bg-surface rounded-[10px] border border-hairline text-[11px] text-ink-secondary flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span><strong class="text-brand-ink">Proteksi Data Inti:</strong> Seluruh Akun Pegawai (Inspektur, Irban, Auditor, OPD), Struktur Irban, dan Bank Regulasi <strong class="text-ink">DIJAMIN UTUH 100%</strong> dan tidak akan tersentuh penghapusan.</span>
                </div>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- MODAL KONFIRMASI PEMBERSIHAN DATA PERCOBAAN                       --}}
        {{-- ================================================================= --}}
        <div x-show="showModalPurge" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface w-full max-w-lg rounded-[10px] p-6 space-y-5 shadow-lg border border-hairline">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <h3 class="font-bold text-ink text-base">Konfirmasi Pembersihan Data</h3>
                    </div>
                    <button type="button" @click="showModalPurge = false" class="text-ink-muted hover:text-ink text-lg font-bold" aria-label="Tutup">&times;</button>
                </div>

                <form method="POST" action="{{ route('backup.purge_dummy') }}" class="space-y-4 text-xs">
                    @csrf

                    <div class="p-3 bg-rose-50 dark:bg-rose-950/50 rounded-[6px] border border-rose-200 dark:border-rose-900 text-rose-900 dark:text-rose-200 text-[11px] leading-relaxed">
                        Tindakan ini akan menghapus data operasional yang dipilih secara permanen. Pastikan Anda telah mengunduh cadangan database terlebih dahulu.
                    </div>

                    <!-- Checklist Modul yang Dihapus -->
                    <div>
                        <span class="font-bold text-ink block mb-2">Pilih Modul Data yang Ingin Dibersihkan:</span>
                        <div class="space-y-2 bg-surface-subtle p-3 rounded-[10px] border border-hairline">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="modules[]" value="tindak_lanjut" checked class="rounded-[4px] text-rose-700 focus:ring-rose-600">
                                <div>
                                    <span class="font-bold text-ink block">Matriks Tindak Lanjut & LHP</span>
                                    <span class="text-[10px] text-ink-secondary">Termasuk {{ $purgeStats['tindak_lanjut'] }} butir rekomendasi dan {{ $purgeStats['bukti_tl'] }} berkas bukti upload OPD.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-hairline">
                                <input type="checkbox" name="modules[]" value="penugasan" checked class="rounded-[4px] text-rose-700 focus:ring-rose-600">
                                <div>
                                    <span class="font-bold text-ink block">Penugasan & SPT Pengawasan</span>
                                    <span class="text-[10px] text-ink-secondary">Termasuk {{ $purgeStats['penugasan'] }} data SPT dan susunan tim auditor penugasan.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-hairline">
                                <input type="checkbox" name="modules[]" value="konsultasi" checked class="rounded-[4px] text-rose-700 focus:ring-rose-600">
                                <div>
                                    <span class="font-bold text-ink block">E-Consulting & QnA APIP</span>
                                    <span class="text-[10px] text-ink-secondary">Termasuk {{ $purgeStats['konsultasi'] }} tiket permohonan konsultasi dan ruang percakapan.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-2 border-t border-hairline">
                                <input type="checkbox" name="modules[]" value="logs" checked class="rounded-[4px] text-rose-700 focus:ring-rose-600">
                                <div>
                                    <span class="font-bold text-ink block">Riwayat Log Aktivitas & Notifikasi</span>
                                    <span class="text-[10px] text-ink-secondary">Termasuk {{ $purgeStats['notifikasi'] + $purgeStats['activity_logs'] }} riwayat notifikasi masa testing.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Ketik Konfirmasi -->
                    <div>
                        <label class="block font-bold text-ink mb-1">
                            Ketik Frasa Persetujuan: <span class="font-mono text-rose-700 dark:text-rose-400 uppercase">BERSIHKAN DATA PERCOBAAN</span> *
                        </label>
                        <input type="text" name="konfirmasi_kata" required placeholder="Ketik persis: BERSIHKAN DATA PERCOBAAN" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-mono">
                    </div>

                    <!-- Password Super Admin -->
                    <div>
                        <label class="block font-bold text-ink mb-1">
                            Masukkan Password Akun Anda Saat Ini *
                        </label>
                        <input type="password" name="password_konfirmasi" required placeholder="Kata sandi akun Anda" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>

                    <div class="pt-3 border-t border-hairline flex justify-end gap-2">
                        <button type="button" @click="showModalPurge = false" class="ledger-btn-secondary !text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-700 hover:bg-rose-800 text-white font-semibold rounded-[6px] text-xs transition-colors cursor-pointer">
                            Eksekusi Pembersihan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- MODAL PANDUAN KONEKSI GOOGLE DRIVE VIA RCLONE                     --}}
        {{-- ================================================================= --}}
        <div x-show="showModalGdriveGuide" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface w-full max-w-xl rounded-[10px] p-6 space-y-4 shadow-lg border border-hairline text-xs">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <h3 class="font-bold text-ink text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-ink-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        <span>Panduan Sinkronisasi Google Drive Otomatis (VPS)</span>
                    </h3>
                    <button type="button" @click="showModalGdriveGuide = false" class="text-ink-muted hover:text-ink text-lg font-bold" aria-label="Tutup">&times;</button>
                </div>

                <div class="space-y-3 text-ink-secondary leading-relaxed">
                    <p>
                        Untuk mencadangkan seluruh berkas database dan dokumen PDF ke Google Drive Inspektorat Trenggalek tanpa batas ukuran, gunakan utilitas resmi <strong class="text-ink">Rclone</strong> di server Linux VPS:
                    </p>

                    <div class="space-y-2 bg-surface-subtle p-4 rounded-[10px] border border-hairline text-[11px]">
                        <p class="font-bold text-ink">Langkah 1: Hubungkan Akun Google Drive di Server</p>
                        <code class="block font-mono bg-slate-900 text-emerald-300 p-2.5 rounded-[6px]">rclone config</code>
                        <span class="text-ink-muted block mt-1">Pilih Google Drive, beri nama remote: <code class="text-blue-700 dark:text-blue-300 font-bold">gdrive</code>.</span>

                        <p class="font-bold text-ink pt-2">Langkah 2: Pasang Jadwal Cron Job Otomatis di VPS</p>
                        <span class="text-ink-muted block mb-1">Buka crontab server via <code class="text-blue-700 dark:text-blue-300 font-bold">crontab -e</code> lalu tambahkan:</span>
                        <code class="block font-mono bg-slate-900 text-emerald-300 p-2.5 rounded-[6px] text-[10px] break-all">
                            0 2 * * * rclone sync /home/inspektorat/domains/singawasi.inspektorat.trenggalekkab.go.id/public_html/storage/app/backups gdrive:SINGAWASI_TRENGGALEK_BACKUP
                        </code>
                        <span class="text-ink-muted block mt-1">Setiap pukul 02.00 dini hari, seluruh berkas cadangan otomatis tersinkron ke Google Drive.</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-hairline flex justify-end">
                    <button type="button" @click="showModalGdriveGuide = false" class="ledger-btn-secondary !text-xs">
                        Tutup Panduan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
