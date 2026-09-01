<!-- Floating Action Button: UAT Feedback & Bug Report -->
<div id="uatFeedbackFloatingContainer" class="fixed bottom-6 left-6 z-50 no-print">
    <button type="button" onclick="openUatFeedbackModal()" class="group flex items-center gap-2.5 px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-2xl shadow-xl shadow-amber-500/25 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 cursor-pointer border border-amber-400/30">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
        </span>
        <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        <span class="text-xs tracking-wide">Kritik, Saran & Bug UAT</span>
    </button>
</div>

<!-- Modal Popup Form UAT Feedback -->
<div id="uatFeedbackModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300">
    <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
        <div class="inline-block w-full max-w-xl p-6 sm:p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-slate-900 border border-slate-800 shadow-2xl rounded-3xl relative text-white">
            
            <!-- Close Button -->
            <button type="button" onclick="closeUatFeedbackModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Header -->
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-500/20">
                    🛠️
                </div>
                <div>
                    <h3 class="text-lg font-black text-white tracking-tight">Pusat Masukan & Laporan Pengujian (UAT)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Sampaikan kendala, ide saran, atau pertanyaan saat mencoba fitur SIPANDA</p>
                </div>
            </div>

            <!-- Form -->
            <form id="uatFeedbackForm" onsubmit="submitUatFeedback(event)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="url_halaman" id="uatFeedbackUrl" value="">
                <input type="hidden" name="browser_info" id="uatFeedbackBrowser" value="">
                <input type="hidden" name="screenshot_b64" id="uatFeedbackScreenshotB64" value="">

                <!-- Kategori & Urgensi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1.5">Jenis Masukan <span class="text-rose-500">*</span></label>
                        <select name="kategori" id="uatKategori" required class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500">
                            <option value="bug">🐞 Bug / Kendala Error</option>
                            <option value="saran">💡 Ide & Saran Perbaikan</option>
                            <option value="pertanyaan">❓ Pertanyaan Alur / Bingung</option>
                            <option value="apresiasi">⭐ Ulasan / Catatan Kenyamanan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1.5">Tingkat Urgensi <span class="text-rose-500">*</span></label>
                        <select name="urgensi" id="uatUrgensi" required class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500">
                            <option value="sedang">🟡 Sedang (Normal)</option>
                            <option value="rendah">🟢 Rendah (Saran Minor)</option>
                            <option value="tinggi">⚠️ Tinggi (Fitur Tidak Jalan)</option>
                            <option value="kritis">🔥 Kritis (Aplikasi Terhenti/Error 500)</option>
                        </select>
                    </div>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1.5">Judul Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="uatJudul" required placeholder="Contoh: Tombol cetak SPT tidak merespon di browser HP" class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Deskripsi & Langkah Kronologi -->
                <div>
                    <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1.5">Uraian Detail / Kronologi Masalah <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi" id="uatDeskripsi" rows="3" required placeholder="Jelaskan apa yang sedang Anda lakukan, apa yang Anda harapkan, dan apa yang sebenarnya terjadi..." class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>

                <!-- Screenshot Upload & Clipboard Paste -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px]">Tangkapan Layar / Screenshot (Opsional)</label>
                        <span class="text-[10px] text-amber-400 font-semibold">💡 Bisa Paste (Ctrl+V) langsung!</span>
                    </div>

                    <!-- Dropzone / Paste Area -->
                    <div id="uatPasteZone" class="border-2 border-dashed border-slate-700 hover:border-amber-500/60 rounded-2xl p-3 text-center bg-slate-800/50 transition-colors relative cursor-pointer" onclick="document.getElementById('uatFileInput').click()">
                        <input type="file" id="uatFileInput" name="screenshot" accept="image/*" class="hidden" onchange="handleUatFileSelect(this)">
                        
                        <div id="uatUploadPrompt" class="space-y-1 py-2">
                            <div class="text-slate-400 text-sm">📷 Klik untuk pilih file gambar atau tekan <kbd class="px-1.5 py-0.5 bg-slate-700 rounded text-slate-200 font-mono text-[10px]">Ctrl+V</kbd> untuk Paste</div>
                            <div class="text-[10px] text-slate-500">Mendukung JPG, PNG, WEBP (Maks. 10MB)</div>
                        </div>

                        <!-- Live Preview Box -->
                        <div id="uatPreviewContainer" class="hidden relative inline-block">
                            <img id="uatImagePreview" src="" alt="Pratinjau Screenshot" class="max-h-36 rounded-xl border border-slate-700 shadow-md mx-auto">
                            <button type="button" onclick="clearUatScreenshot(event)" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full p-1 shadow-md transition-colors" title="Hapus Gambar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Halaman Saat Ini (Auto) -->
                <div class="p-2.5 bg-slate-950/80 rounded-xl border border-slate-800 text-[10px] flex items-center justify-between text-slate-400">
                    <span class="truncate">📍 Halaman: <strong id="uatUrlBadge" class="text-slate-300 font-mono"></strong></span>
                    <span class="shrink-0 text-emerald-400 font-semibold">Terekam Otomatis</span>
                </div>

                <!-- Submit Button & Loader -->
                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeUatFeedbackModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" id="uatSubmitBtn" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2 cursor-pointer">
                        <span id="uatBtnText">Kirim Masukan</span>
                        <svg id="uatBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUatFeedbackModal() {
        document.getElementById('uatFeedbackUrl').value = window.location.href;
        document.getElementById('uatUrlBadge').textContent = window.location.pathname + window.location.search;
        document.getElementById('uatFeedbackBrowser').value = navigator.userAgent + ' | Screen: ' + window.innerWidth + 'x' + window.innerHeight;
        
        document.getElementById('uatFeedbackModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeUatFeedbackModal() {
        document.getElementById('uatFeedbackModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Global Paste Listener (Ctrl+V / Cmd+V)
    window.addEventListener('paste', function(e) {
        const modal = document.getElementById('uatFeedbackModal');
        if (modal.classList.contains('hidden')) return;

        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                const reader = new FileReader();
                reader.onload = function(event) {
                    showUatImagePreview(event.target.result);
                    document.getElementById('uatFeedbackScreenshotB64').value = event.target.result;
                    document.getElementById('uatFileInput').value = ''; // Reset file input jika menggunakan paste
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
                document.getElementById('uatFeedbackScreenshotB64').value = ''; // Menggunakan file asli
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
        e.stopPropagation();
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
                clearUatScreenshot(e);
                closeUatFeedbackModal();
            } else {
                alert('⚠️ Gagal mengirim: ' + (result.message || 'Silakan lengkapi kolom yang wajib diisi.'));
            }
        } catch (error) {
            console.error('Error feedback submit:', error);
            alert('❌ Terjadi kendala saat mengirim masukan. Pastikan koneksi server aktif.');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Kirim Masukan';
            btnSpinner.classList.add('hidden');
        }
    }
</script>
