@php
    $currentOpdUser = auth('opd')->user();
@endphp

<!-- Modal Edit Data Objek Pengawasan / Entitas -->
<div id="modalEditProfilOpd" role="dialog" aria-modal="true" aria-labelledby="modal-edit-profil-opd-title" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
    <div class="bg-surface rounded-[10px] max-w-lg w-full p-6 sm:p-7 border border-hairline text-xs">
        <!-- Header Modal -->
        <div class="flex items-start justify-between gap-3 pb-4 border-b border-hairline">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-[6px] bg-brand-soft border border-brand-border flex items-center justify-center text-brand-ink shrink-0" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 id="modal-edit-profil-opd-title" class="font-bold text-ink text-base leading-tight">Edit Data Objek Pengawasan / Entitas</h2>
                    <p class="text-xs text-ink-muted mt-0.5">Perbarui kontak PIC, email resmi, dan kata sandi akun</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalEditProfilOpd').classList.add('hidden')" aria-label="Tutup modal edit data OPD" class="inline-flex items-center justify-center min-h-[36px] min-w-[36px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Edit Data Mandiri -->
        <form method="POST" action="{{ route('opd.profil.update') }}" class="space-y-4 mt-5">
            @csrf
            @method('PUT')

            <!-- Instansi Objek Penugasan (Read Only Info) -->
            <div class="p-3.5 bg-surface-subtle rounded-[6px] border border-hairline space-y-1">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wide">Instansi Objek Penugasan</span>
                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase rounded-[4px] bg-brand-soft text-brand-ink border border-brand-border">
                        {{ strtoupper($currentOpdUser?->objekPenugasan?->kategori ?? 'OPD') }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm font-bold text-ink">
                    {{ $currentOpdUser?->objekPenugasan?->nama ?? 'Instansi OPD Terdaftar' }}
                </p>
                <p class="text-xs text-ink-muted">Instansi terdaftar resmi di Inspektorat dan tidak dapat diubah sendiri demi integritas data.</p>
            </div>

            <!-- Nama PIC OPD -->
            <div>
                <label for="opd_profil_nama" class="block text-xs font-semibold mb-1 text-ink">
                    Nama PIC OPD <span class="text-red-700 dark:text-red-400">*</span>
                </label>
                <input id="opd_profil_nama" type="text" name="nama" value="{{ old('nama', $currentOpdUser?->nama) }}" required
                    placeholder="Nama lengkap PIC Perangkat Daerah"
                    class="w-full font-medium">
                <p class="text-xs text-ink-muted mt-1">Nama lengkap pejabat / staf penghubung tindak lanjut pengawasan.</p>
            </div>

            <!-- Email Objek Pengawasan / Entitas -->
            <div>
                <label for="opd_profil_email" class="block text-xs font-semibold mb-1 text-ink">
                    Email Objek Pengawasan / Entitas <span class="text-red-700 dark:text-red-400">*</span>
                </label>
                <input id="opd_profil_email" type="email" name="email" value="{{ old('email', $currentOpdUser?->email) }}" required
                    placeholder="dinas@trenggalekkab.go.id"
                    class="w-full font-medium">
                <p class="text-xs text-ink-muted mt-1">Digunakan untuk login ke Portal OPD serta menerima notifikasi & tautan reset kata sandi.</p>
            </div>

            <!-- Nomor WhatsApp / HP Entitas -->
            <div>
                <label for="opd_profil_no_hp" class="block text-xs font-semibold mb-1 text-ink">
                    Nomor WhatsApp / HP Entitas
                </label>
                <input id="opd_profil_no_hp" type="text" name="no_hp" value="{{ old('no_hp', $currentOpdUser?->no_hp) }}"
                    placeholder="Nomor WhatsApp / HP aktif"
                    class="w-full font-mono font-medium">
                <p class="text-xs text-ink-muted mt-1">Digunakan untuk menerima notifikasi pengawasan & tindak lanjut LHP otomatis via WhatsApp.</p>
            </div>

            <!-- Ganti Kata Sandi (Opsional) -->
            <div class="p-4 bg-amber-50/70 dark:bg-amber-950/30 rounded-[6px] border border-amber-200 dark:border-amber-800/60 space-y-3">
                <div class="flex items-center gap-2 text-amber-900 dark:text-amber-200 font-semibold text-xs">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Ubah Kata Sandi Baru (Opsional)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="opd_profil_password" class="block text-xs font-semibold text-amber-900 dark:text-amber-200 mb-1">Kata Sandi Baru</label>
                        <input id="opd_profil_password" type="password" name="password" minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter"
                            class="w-full font-mono">
                    </div>
                    <div>
                        <label for="opd_profil_password_confirmation" class="block text-xs font-semibold text-amber-900 dark:text-amber-200 mb-1">Ulangi Kata Sandi Baru</label>
                        <input id="opd_profil_password_confirmation" type="password" name="password_confirmation" minlength="6" autocomplete="new-password" placeholder="Ketik ulang sandi"
                            class="w-full font-mono">
                    </div>
                </div>
                <p class="text-xs text-amber-900 dark:text-amber-300">
                    Kosongkan kedua kolom ini jika Anda tidak berniat mengganti kata sandi.
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-hairline">
                <button type="button" onclick="document.getElementById('modalEditProfilOpd').classList.add('hidden')"
                    class="ledger-btn-secondary min-h-[40px] px-4">
                    Batal
                </button>
                <button type="submit"
                    class="ledger-btn-primary min-h-[40px] px-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
