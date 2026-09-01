<!-- Global Floating Action Button & Chat Drawer: Kotak Saran & Bug UAT (Pojok Kanan Bawah) -->
<div id="uatFeedbackWidgetWrapper" class="no-print font-sans">

    <!-- 1. Floating Toggle Button (Selalu di Pojok Kanan Bawah, Sangat Kontras & Jelas) -->
    <button type="button" 
            id="uatFeedbackToggleBtn" 
            onclick="toggleUatFeedbackPopup()" 
            style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 9999998 !important; background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important; box-shadow: 0 12px 28px -4px rgba(234, 88, 12, 0.65), 0 6px 12px -2px rgba(245, 158, 11, 0.4) !important; border: 2px solid #fef3c7 !important;" 
            class="group flex items-center gap-2.5 px-4 py-3 text-white font-extrabold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 active:scale-95 cursor-pointer">
        
        <!-- Blinking Pulse Beacon -->
        <span class="relative flex h-3.5 w-3.5 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-85"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-white shadow-xs"></span>
        </span>
        
        <!-- Icon -->
        <span id="uatToggleIcon" class="text-base flex items-center">
            💬
        </span>
        
        <span class="text-xs font-black tracking-wide text-white drop-shadow-sm uppercase">
            Kotak Saran & Bug UAT
        </span>
    </button>

    <!-- 2. Chatbox Popup Window Drawer (TERBUKA KE ATAS / DI ATAS TOMBOL, 100% SOLID OPAQUE) -->
    <div id="uatFeedbackPopupDrawer" 
         style="position: fixed !important; bottom: 85px !important; right: 24px !important; width: 400px !important; max-width: calc(100vw - 32px) !important; max-height: calc(100vh - 105px) !important; z-index: 9999999 !important; display: none; background-color: #0f172a !important; background: #0f172a !important; opacity: 1 !important;" 
         class="border-2 border-amber-500/50 rounded-3xl shadow-2xl shadow-black flex-col overflow-hidden transition-all duration-200">
        
        <!-- Header Chat Drawer (Solid Slate-950) -->
        <div style="background-color: #020617 !important;" class="p-4 border-b border-slate-700 flex items-center justify-between text-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg font-bold border border-amber-500/30">
                    🛠️
                </div>
                <div>
                    <h3 class="text-sm font-black text-white tracking-tight flex items-center gap-1.5">
                        <span>Kotak Saran & Bug UAT</span>
                        <span class="px-1.5 py-0.5 bg-amber-500/20 text-amber-300 text-[9px] font-bold rounded-md uppercase">UAT Mode</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Kirim kendala, saran & screenshot saat pengujian</p>
                </div>
            </div>

            <!-- Minimize / Close Button -->
            <button type="button" onclick="toggleUatFeedbackPopup()" class="text-slate-400 hover:text-white p-1.5 rounded-xl hover:bg-slate-800 transition-colors" title="Tutup Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Chat Body / Scrollable Form (Solid Slate-900) -->
        <div style="background-color: #0f172a !important;" class="p-4 overflow-y-auto space-y-3.5 text-xs text-slate-200 custom-scrollbar flex-1">
            
            <!-- Intro Chat Bubble from Support -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-bold shrink-0 border border-amber-500/30">
                    🤖
                </div>
                <div style="background-color: #1e293b !important;" class="p-3 rounded-2xl rounded-tl-sm border border-slate-700 text-[11px] text-slate-200 leading-relaxed shadow-sm">
                    Halo! Tim pengembang siap menampung laporan kendala, pertanyaan alur, atau ide saran perbaikan Anda saat mencoba SIPANDA.
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
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Jenis Masukan <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="uatKategori" required class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2 focus:ring-2 focus:ring-amber-500">
                        <option value="bug">🐞 Bug / Kendala Error</option>
                        <option value="saran">💡 Ide & Saran Perbaikan</option>
                        <option value="pertanyaan">❓ Pertanyaan Alur / Bingung</option>
                        <option value="apresiasi">⭐ Ulasan / Catatan UX</option>
                    </select>
                </div>

                <!-- Tingkat Urgensi -->
                <div>
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Tingkat Urgensi <span class="text-rose-500">*</span></label>
                    <select name="urgensi" id="uatUrgensi" required class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2 focus:ring-2 focus:ring-amber-500">
                        <option value="sedang">🟡 Sedang (Normal)</option>
                        <option value="rendah">🟢 Rendah (Saran Minor)</option>
                        <option value="tinggi">⚠️ Tinggi (Fitur Tidak Berfungsi)</option>
                        <option value="kritis">🔥 Kritis (Error 500 / Blocker)</option>
                    </select>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Judul Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="uatJudul" required placeholder="Contoh: Tombol cetak tidak merespon di HP" class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2 focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Uraian / Kronologi <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi" id="uatDeskripsi" rows="3" required placeholder="Jelaskan apa yang dicoba, apa yang diharapkan, dan apa yang terjadi..." class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2 focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>

                <!-- Screenshot Box with Paste (Ctrl+V) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px]">Tangkapan Layar (Screenshot)</label>
                        <span class="text-[9px] text-amber-400 font-semibold">Bisa Ctrl+V Paste!</span>
                    </div>

                    <div id="uatPasteZone" class="border border-dashed border-slate-700 hover:border-amber-500/60 rounded-2xl p-2.5 text-center bg-slate-800/60 transition-colors relative cursor-pointer" onclick="document.getElementById('uatFileInput').click()">
                        <input type="file" id="uatFileInput" name="screenshot" accept="image/*" class="hidden" onchange="handleUatFileSelect(this)">
                        
                        <div id="uatUploadPrompt" class="space-y-0.5 py-1">
                            <div class="text-slate-300 text-xs font-semibold">📷 Klik pilih file atau tekan <kbd class="px-1 py-0.5 bg-slate-700 rounded text-slate-200 font-mono text-[9px]">Ctrl+V</kbd></div>
                            <div class="text-[9px] text-slate-500">JPG, PNG, WEBP (Maks. 10MB)</div>
                        </div>

                        <!-- Preview -->
                        <div id="uatPreviewContainer" class="hidden relative inline-block mt-1">
                            <img id="uatImagePreview" src="" alt="Pratinjau Screenshot" class="max-h-28 rounded-xl border border-slate-700 shadow-md mx-auto">
                            <button type="button" onclick="clearUatScreenshot(event)" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full p-1 shadow-md transition-colors" title="Hapus Gambar">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Halaman Terekam -->
                <div class="p-2 bg-slate-950 rounded-xl border border-slate-800 text-[9px] flex items-center justify-between text-slate-400">
                    <span class="truncate">📍 <strong id="uatUrlBadge" class="text-slate-300 font-mono"></strong></span>
                    <span class="shrink-0 text-emerald-400 font-semibold ml-1">Auto</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button type="submit" id="uatSubmitBtn" class="w-full py-2.5 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span id="uatBtnText">Kirim Masukan Sekarang 🚀</span>
                        <svg id="uatBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
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
            document.getElementById('uatToggleIcon').innerHTML = '✕';
        } else {
            popup.style.display = 'none';
            document.getElementById('uatToggleIcon').innerHTML = '💬';
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
        document.getElementById('uatImagePreview').src = src;
        document.getElementById('uatUploadPrompt').classList.add('hidden');
        document.getElementById('uatPreviewContainer').classList.remove('hidden');
    }

    function clearUatScreenshot(e) {
        if (e) e.stopPropagation();
        document.getElementById('uatFileInput').value = '';
        document.getElementById('uatFeedbackScreenshotB64').value = '';
        document.getElementById('uatImagePreview').src = '';
        document.getElementById('uatUploadPrompt').classList.remove('hidden');
        document.getElementById('uatPreviewContainer').classList.add('hidden');
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
                alert('✅ ' + result.message);
                form.reset();
                clearUatScreenshot(null);
                toggleUatFeedbackPopup();
            } else {
                alert('⚠️ Gagal mengirim: ' + (result.message || 'Silakan lengkapi kolom yang wajib diisi.'));
            }
        } catch (error) {
            console.error('Error feedback submit:', error);
            alert('❌ Terjadi kendala saat mengirim masukan. Pastikan server aktif.');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Kirim Masukan Sekarang 🚀';
            btnSpinner.classList.add('hidden');
        }
    }
</script>
