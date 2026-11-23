<!-- Modal Fitur Dalam Tahap Pengembangan (Under Development Modal) -->
<div id="modalUnderDevelopment" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="devModalTitle" role="dialog" aria-modal="true">
    <!-- Solid Scrim Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeFeatureUnderDev()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-[10px] bg-surface text-left shadow-lg transition-all sm:my-8 sm:w-full sm:max-w-md border border-hairline p-6 sm:p-7 space-y-5">

            <!-- Icon & Badge Header -->
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-[6px] bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[4px] text-[11px] font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-600 dark:bg-amber-400"></span>
                    Tahap Pengembangan
                </span>
            </div>

            <!-- Title & Description -->
            <div class="space-y-2">
                <h3 id="devModalTitle" class="text-lg font-bold text-ink tracking-tight">
                    Fitur Sedang Disiapkan
                </h3>
                <p id="devModalDesc" class="text-xs text-ink-secondary leading-relaxed">
                    Modul ini sedang dalam tahap perumusan alur kerja dan finalisasi integrasi pengawasan internal APIP. Fitur akan segera tersedia pada pembaruan rilis sistem berikutnya.
                </p>
            </div>

            <!-- Note Box -->
            <div class="p-3.5 bg-surface-subtle rounded-[10px] border border-hairline flex items-start gap-3">
                <svg class="w-4 h-4 text-brand-ink shrink-0 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-ink-secondary leading-normal">
                    Fokus saat ini adalah pemantauan <strong class="text-ink">Data Penugasan</strong>, <strong class="text-ink">Tindak Lanjut LHP</strong>, dan pelaporan eksekutif <strong class="text-ink">Ikhtisar Laporan (ILHP)</strong>.
                </p>
            </div>

            <!-- Action Button -->
            <div class="pt-2 flex justify-end">
                <button type="button"
                        onclick="closeFeatureUnderDev()"
                        class="ledger-btn-primary w-full sm:w-auto text-xs cursor-pointer">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showFeatureUnderDev(title, customDesc = null) {
        const modal = document.getElementById('modalUnderDevelopment');
        if (!modal) return;

        const titleEl = document.getElementById('devModalTitle');
        const descEl = document.getElementById('devModalDesc');

        if (title && titleEl) {
            titleEl.textContent = title;
        }
        if (customDesc && descEl) {
            descEl.textContent = customDesc;
        } else if (descEl) {
            descEl.textContent = 'Modul ' + (title || 'ini') + ' sedang dalam tahap perumusan alur kerja dan finalisasi integrasi pengawasan internal APIP. Fitur akan segera tersedia pada pembaruan rilis sistem berikutnya.';
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeFeatureUnderDev() {
        const modal = document.getElementById('modalUnderDevelopment');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Support tombol ESC
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFeatureUnderDev();
        }
    });
</script>
