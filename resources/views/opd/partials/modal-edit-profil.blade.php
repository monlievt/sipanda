@php
    $currentOpdUser = auth('opd')->user();
@endphp

<!-- Modal Edit Data & Profil OPD Mandiri -->
<div id="modalEditProfilOpd" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 border border-slate-200 dark:border-slate-800 text-xs transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Header Modal -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-teal-700 dark:text-teal-300 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Edit Data Mandiri OPD</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Perbarui kontak PIC, email resmi, dan kata sandi akun</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalEditProfilOpd').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Edit Data Mandiri -->
        <form method="POST" action="{{ route('opd.profil.update') }}" class="space-y-4 mt-5">
            @csrf
            @method('PUT')

            <!-- Instansi Objek Penugasan (Read Only Info) -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/60 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instansi Objek Penugasan</span>
                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-md bg-teal-100 dark:bg-teal-900/60 text-teal-800 dark:text-teal-200 border border-teal-200 dark:border-teal-700">
                        {{ strtoupper($currentOpdUser?->objekPenugasan?->kategori ?? 'OPD') }}
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 flex items-center gap-1.5">
                    <span>🏢</span>
                    <span>{{ $currentOpdUser?->objekPenugasan?->nama ?? 'Instansi OPD Terdaftar' }}</span>
                </p>
                <p class="text-[10px] text-slate-400">Instansi terdaftar resmi di Inspektorat dan tidak dapat diubah sendiri demi integritas data.</p>
            </div>

            <!-- Nama PIC OPD -->
            <div>
                <label class="block font-bold mb-1.5 text-slate-800 dark:text-slate-200">
                    Nama PIC OPD <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input type="text" name="nama" value="{{ old('nama', $currentOpdUser?->nama) }}" required 
                        placeholder="Contoh: PIC Inspektorat / Bpk. Fulan"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 pl-9 pr-3.5 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-xs">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Nama lengkap pejabat / staf penghubung tindak lanjut pengawasan.</p>
            </div>

            <!-- Username / Email Resmi Entitas -->
            <div>
                <label class="block font-bold mb-1.5 text-slate-800 dark:text-slate-200">
                    Username / Email Resmi OPD <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" name="email" value="{{ old('email', $currentOpdUser?->email) }}" required 
                        placeholder="dinas@trenggalekkab.go.id"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 pl-9 pr-3.5 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-xs">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk login ke Portal OPD serta menerima notifikasi & tautan reset kata sandi.</p>
            </div>

            <!-- Nomor WhatsApp / HP Entitas -->
            <div>
                <label class="block font-bold mb-1.5 text-slate-800 dark:text-slate-200">
                    Nomor WhatsApp / HP Entitas
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $currentOpdUser?->no_hp) }}" 
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 pl-9 pr-3.5 py-2.5 text-xs font-mono font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-xs">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk menerima notifikasi pengawasan & tindak lanjut LHP otomatis via WhatsApp.</p>
            </div>

            <!-- Ganti Kata Sandi (Opsional) -->
            <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 rounded-2xl border border-amber-200/80 dark:border-amber-900/60 space-y-3">
                <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Ubah Kata Sandi Baru (Opsional)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-amber-900 dark:text-amber-200 mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" minlength="6" placeholder="Minimal 6 karakter"
                            class="w-full rounded-xl border border-amber-300 dark:border-amber-800 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-mono focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-amber-900 dark:text-amber-200 mb-1">Ulangi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" minlength="6" placeholder="Ketik ulang sandi"
                            class="w-full rounded-xl border border-amber-300 dark:border-amber-800 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-mono focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <p class="text-[10px] text-amber-700 dark:text-amber-400">
                    💡 Kosongkan kedua kolom ini jika Anda tidak berniat mengganti kata sandi.
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modalEditProfilOpd').classList.add('hidden')" 
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold rounded-xl shadow-lg shadow-teal-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
