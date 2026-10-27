<!-- Modal Fitur Dalam Tahap Pengembangan (Under Development Modal) -->
<div id="modalUnderDevelopment" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeFeatureUnderDev()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-800 p-6 sm:p-7 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Icon & Badge Header -->
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl shadow-inner border border-amber-500/20">
                    🛠️
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Tahap Pengembangan
                </span>
            </div>

            <!-- Title & Description -->
            <div class="space-y-2">
                <h3 id="devModalTitle" class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    Fitur Sedang Disiapkan
                </h3>
                <p id="devModalDesc" class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Modul ini sedang dalam tahap perumusan alur kerja dan finalisasi integrasi pengawasan internal APIP. Fitur akan segera tersedia pada pembaruan rilis sistem berikutnya.
                </p>
            </div>

            <!-- Note Box -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                <span class="text-base">💡</span>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                    Fokus saat ini adalah pemantauan <strong>Data Penugasan</strong>, <strong>Tindak Lanjut LHP</strong>, dan pelaporan eksekutif <strong>Ikhtisar Laporan (ILHP)</strong>.
                </p>
            </div>

            <!-- Action Button -->
            <div class="pt-2 flex justify-end">
                <button type="button" 
                        onclick="closeFeatureUnderDev()" 
                        class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-900/20 transition-all cursor-pointer">
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
