<!-- Global Floating Action Button & Chat Drawer: Kotak Saran & Bug UAT (Pojok Kanan Bawah) -->
<div id="uatFeedbackWidgetWrapper" class="no-print font-sans">

    <!-- 1. Floating Toggle Button -->
    <button type="button"
            id="uatFeedbackToggleBtn"
            onclick="toggleUatFeedbackPopup()"
            aria-label="Kotak Saran dan Lapor Bug UAT"
            title="Kotak Saran & Lapor Bug UAT"
            style="position: fixed; bottom: 24px; right: 24px; width: 48px; height: 48px; z-index: 9999998;"
            class="group flex items-center justify-center rounded-full bg-brand hover:bg-brand-hover text-white border border-brand-border shadow-lg transition-colors duration-150 cursor-pointer">

        <!-- Static Notification Indicator -->
        <span style="position: absolute; top: -2px; right: -2px;" class="inline-flex rounded-full h-3 w-3 bg-amber-500 border-2 border-surface"></span>

        <!-- Icon Chat Bubble / Close -->
        <span id="uatToggleIcon" class="flex items-center justify-center text-white">
            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.03 2 11c0 2.87 1.5 5.43 3.82 7.02l-.82 3.02 3.25-1.42C9.26 19.83 10.6 20 12 20c5.52 0 10-4.03 10-9s-4.48-9-10-9zm0 16c-1.25 0-2.43-.22-3.49-.62l-.25-.1-1.92.84.48-1.78-.18-.26C5.64 14.88 5 13.01 5 11c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
        </span>
    </button>

    <!-- 2. Chatbox Popup Window Drawer -->
    <div id="uatFeedbackPopupDrawer"
         style="position: fixed; bottom: 84px; right: 24px; width: 400px; max-width: calc(100vw - 32px); max-height: calc(100vh - 105px); z-index: 9999999; display: none;"
         class="bg-surface border border-hairline rounded-[10px] shadow-lg flex-col overflow-hidden transition-all duration-150">

        <!-- Header Chat Drawer -->
        <div class="p-4 bg-surface-subtle border-b border-hairline flex items-center justify-between text-ink shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-brand-soft text-brand-ink flex items-center justify-center border border-brand-border shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-ink tracking-tight flex items-center gap-1.5">
                        <span>Kotak Saran & Bug UAT</span>
                        <span class="px-1.5 py-0.5 bg-brand-soft text-brand-ink border border-brand-border text-[10px] font-semibold rounded-[4px] uppercase">UAT Mode</span>
                    </h3>
                    <p class="text-xs text-ink-muted">Kirim kendala, saran & screenshot saat pengujian</p>
                </div>
            </div>

            <!-- Minimize / Close Button -->
            <button type="button" onclick="toggleUatFeedbackPopup()" aria-label="Tutup kotak saran" class="text-ink-muted hover:text-ink p-1.5 rounded-[6px] hover:bg-surface transition-colors" title="Tutup Chat">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Chat Body / Scrollable Form -->
        <div class="p-4 bg-surface overflow-y-auto space-y-3.5 text-xs text-ink custom-scrollbar flex-1">

            <!-- Intro Chat Bubble from Support -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-[6px] bg-brand-soft text-brand-ink flex items-center justify-center shrink-0 border border-brand-border">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="p-3 bg-surface-subtle rounded-[10px] border border-hairline text-xs text-ink-secondary leading-relaxed">
                    Halo! Tim pengembang siap menampung laporan kendala, pertanyaan alur, atau ide saran perbaikan Anda saat mencoba SINGAWASI.
                </div>
            </div>

            <!-- Form -->
            <form id="uatFeedbackForm" onsubmit="submitUatFeedback(event)" class="space-y-3 pt-1">
                @csrf
                <input type="hidden" name="url_halaman" id="uatFeedbackUrl" value="">
                <input type="hidden" name="browser_info" id="uatFeedbackBrowser" value="">
                <input type="hidden" name="screenshot_b64" id="uatFeedbackScreenshotB64" value="">

                <!-- Kategori Pilihan -->
                <div>
                    <label for="uatKategori" class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1">Jenis Masukan <span class="text-rose-600 dark:text-rose-400">*</span></label>
                    <select name="kategori" id="uatKategori" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                        <option value="bug">Bug / Kendala Error</option>
                        <option value="saran">Ide & Saran Perbaikan</option>
                        <option value="pertanyaan">Pertanyaan Alur / Bingung</option>
                        <option value="apresiasi">Ulasan / Catatan UX</option>
                    </select>
                </div>

                <!-- Tingkat Urgensi -->
                <div>
                    <label for="uatUrgensi" class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1">Tingkat Urgensi <span class="text-rose-600 dark:text-rose-400">*</span></label>
                    <select name="urgensi" id="uatUrgensi" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                        <option value="sedang">Sedang (Normal)</option>
                        <option value="rendah">Rendah (Saran Minor)</option>
                        <option value="tinggi">Tinggi (Fitur Tidak Berfungsi)</option>
                        <option value="kritis">Kritis (Error 500 / Blocker)</option>
                    </select>
                </div>

                <!-- Judul -->
                <div>
                    <label for="uatJudul" class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1">Judul Singkat <span class="text-rose-600 dark:text-rose-400">*</span></label>
                    <input type="text" name="judul" id="uatJudul" required placeholder="Contoh: Tombol cetak tidak merespon di HP" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="uatDeskripsi" class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1">Uraian / Kronologi <span class="text-rose-600 dark:text-rose-400">*</span></label>
                    <textarea name="deskripsi" id="uatDeskripsi" rows="3" required placeholder="Jelaskan apa yang dicoba, apa yang diharapkan, dan apa yang terjadi..." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2 resize-none"></textarea>
                </div>

                <!-- Screenshot Box with Paste (Ctrl+V) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="uatFileInput" class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px]">Tangkapan Layar (Screenshot)</label>
                        <span class="text-[11px] text-brand-ink font-semibold">Mendukung Ctrl+V</span>
                    </div>

                    <div class="border border-dashed border-hairline-strong hover:border-brand rounded-[10px] p-2.5 text-center bg-surface-subtle transition-colors relative">
                        <input type="file" id="uatFileInput" name="screenshot" accept="image/*" class="hidden" onchange="handleUatFileSelect(this)">

                        <!-- Upload Prompt Button -->
                        <button type="button"
                                id="uatPasteZone"
                                onclick="document.getElementById('uatFileInput').click()"
                                class="block w-full text-center rounded-[6px] cursor-pointer">
                            <span id="uatUploadPrompt" class="block space-y-1 py-1">
                                <span class="block text-ink text-xs font-semibold">Klik pilih file atau tekan <kbd class="px-1.5 py-0.5 bg-surface border border-hairline rounded-[4px] text-ink-secondary font-mono text-[10px]">Ctrl+V</kbd></span>
                                <span class="block text-[11px] text-ink-muted">JPG, PNG, WEBP (Maks. 10MB)</span>
                            </span>
                        </button>

                        <!-- Preview Box (Default: Hidden via display: none) -->
                        <div id="uatPreviewContainer" style="display: none;" class="relative inline-block mt-1">
                            <img id="uatImagePreview" alt="Pratinjau Screenshot" class="max-h-28 rounded-[6px] border border-hairline mx-auto object-contain">
                            <button type="button"
                                    onclick="clearUatScreenshot(event)"
                                    aria-label="Hapus gambar tangkapan layar"
                                    style="position: absolute; top: -8px; right: -8px; z-index: 20;"
                                    class="bg-rose-600 hover:bg-rose-700 text-white rounded-full p-1 transition-colors cursor-pointer"
                                    title="Hapus Gambar">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Halaman Terekam -->
                <div class="p-2 bg-surface-subtle rounded-[6px] border border-hairline text-[11px] flex items-center justify-between gap-2 text-ink-muted">
                    <span class="truncate inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0 text-ink-muted" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <strong id="uatUrlBadge" class="text-ink font-mono truncate"></strong>
                    </span>
                    <span class="shrink-0 text-brand-ink font-semibold">Auto</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button type="submit" id="uatSubmitBtn" class="ledger-btn-primary w-full text-xs cursor-pointer">
                        <span id="uatBtnText">Kirim Masukan Sekarang</span>
                        <svg id="uatBtnSpinner" aria-hidden="true" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleUatFeedbackPopup() {
        const popup = document.getElementById('uatFeedbackPopupDrawer');
        const isHidden = popup.style.display === 'none' || popup.style.display === '';

        if (isHidden) {
            document.getElementById('uatFeedbackUrl').value = window.location.href;
            document.getElementById('uatUrlBadge').textContent = window.location.pathname + window.location.search;
            document.getElementById('uatFeedbackBrowser').value = navigator.userAgent + ' | Screen: ' + window.innerWidth + 'x' + window.innerHeight;

            popup.style.display = 'flex';
            document.getElementById('uatToggleIcon').innerHTML = '<svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
        } else {
            popup.style.display = 'none';
            document.getElementById('uatToggleIcon').innerHTML = '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.03 2 11c0 2.87 1.5 5.43 3.82 7.02l-.82 3.02 3.25-1.42C9.26 19.83 10.6 20 12 20c5.52 0 10-4.03 10-9s-4.48-9-10-9zm0 16c-1.25 0-2.43-.22-3.49-.62l-.25-.1-1.92.84.48-1.78-.18-.26C5.64 14.88 5 13.01 5 11c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7z"/></svg>';
        }
    }

    // Listener Paste (Ctrl+V / Cmd+V)
    window.addEventListener('paste', function(e) {
        const popup = document.getElementById('uatFeedbackPopupDrawer');
        if (!popup || popup.style.display === 'none') return;

        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                const reader = new FileReader();
                reader.onload = function(event) {
                    showUatImagePreview(event.target.result);
                    document.getElementById('uatFeedbackScreenshotB64').value = event.target.result;
                    document.getElementById('uatFileInput').value = '';
                };
                reader.readAsDataURL(blob);
                break;
            }
        }
    });

    function handleUatFileSelect(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                showUatImagePreview(e.target.result);
                document.getElementById('uatFeedbackScreenshotB64').value = '';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showUatImagePreview(src) {
        const img = document.getElementById('uatImagePreview');
        img.src = src;
        document.getElementById('uatUploadPrompt').style.display = 'none';
        document.getElementById('uatPreviewContainer').style.display = 'inline-block';
    }

    function clearUatScreenshot(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        document.getElementById('uatFileInput').value = '';
        document.getElementById('uatFeedbackScreenshotB64').value = '';
        const img = document.getElementById('uatImagePreview');
        img.removeAttribute('src');
        document.getElementById('uatUploadPrompt').style.display = 'block';
        document.getElementById('uatPreviewContainer').style.display = 'none';
    }

    async function submitUatFeedback(e) {
        e.preventDefault();

        const form = document.getElementById('uatFeedbackForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('uatSubmitBtn');
        const btnText = document.getElementById('uatBtnText');
        const btnSpinner = document.getElementById('uatBtnSpinner');

        submitBtn.disabled = true;
        btnText.textContent = 'Mengirim...';
        btnSpinner.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("feedback.submit") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alert(result.message);
                form.reset();
                clearUatScreenshot(null);
                toggleUatFeedbackPopup();
            } else {
                alert('Gagal mengirim: ' + (result.message || 'Silakan lengkapi kolom yang wajib diisi.'));
            }
        } catch (error) {
            console.error('Error feedback submit:', error);
            alert('Terjadi kendala saat mengirim masukan. Pastikan server aktif.');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Kirim Masukan Sekarang';
            btnSpinner.classList.add('hidden');
        }
    }
</script>
