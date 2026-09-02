<!-- Modal Universal Pratinjau Dokumen & PDF SIPANDA (Popup Preview Tanpa Download) -->
<div id="pdfPreviewModalWrapper" class="no-print font-sans relative" style="z-index: 99999999; display: none;">
    
    <!-- Backdrop Blur Gelap -->
    <div id="pdfPreviewBackdrop" 
         onclick="closePdfPreviewModal()"
         style="position: fixed; inset: 0; background: rgba(2, 6, 23, 0.82); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); transition: opacity 0.25s ease;" 
         class="cursor-pointer"></div>

    <!-- Modal Dialog Window -->
    <div style="position: fixed; inset: 0; pointer-events: none; display: flex; items-center: center; justify-content: center; padding: 16px; z-index: 100000000;" class="sm:p-6">
        <div id="pdfPreviewContainer" 
             style="pointer-events: auto; width: 100%; max-width: 1100px; height: 92vh; max-height: 96vh; background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.75);" 
             class="rounded-3xl flex flex-col overflow-hidden transition-all transform scale-100">
            
            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-slate-900/95 border-b border-slate-800 text-white flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="pdfModalIcon" class="w-9 h-9 rounded-2xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg font-bold border border-teal-500/30 shrink-0">
                        📄
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-teal-500/20 text-teal-300 tracking-wider">
                                Pratinjau Dokumen
                            </span>
                        </div>
                        <h3 id="pdfModalTitle" class="text-xs sm:text-sm font-bold text-slate-100 truncate mt-0.5" title="Pratinjau Dokumen">
                            Dokumen PDF
                        </h3>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Tombol Print -->
                    <button type="button" 
                            id="pdfBtnPrint" 
                            onclick="printPdfPreviewFrame()" 
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors cursor-pointer"
                            title="Cetak Dokumen Ini">
                        <span>🖨️</span>
                        <span>Cetak</span>
                    </button>

                    <!-- Tombol Buka di Tab Baru -->
                    <a id="pdfBtnNewTab" 
                       href="#" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       data-no-preview="true"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors"
                       title="Buka Dokumen di Tab Browser Baru">
                        <span>↗️</span>
                        <span class="hidden md:inline">Tab Baru</span>
                    </a>

                    <!-- Tombol Unduh Fisik Asli -->
                    <a id="pdfBtnDownload" 
                       href="#" 
                       download 
                       data-no-preview="true"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/30 transition-all cursor-pointer"
                       title="Unduh File Dokumen">
                        <span>📥</span>
                        <span>Unduh File</span>
                    </a>

                    <!-- Tombol Tutup (Silang) -->
                    <button type="button" 
                            onclick="closePdfPreviewModal()" 
                            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors ml-1 cursor-pointer" 
                            title="Tutup Pratinjau (ESC)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Iframe & Image Preview Container) -->
            <div class="flex-1 w-full h-full relative bg-slate-950 overflow-hidden flex items-center justify-center">
                
                <!-- Loading State Spinner -->
                <div id="pdfLoadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 text-slate-400 z-10 space-y-3 pointer-events-none">
                    <svg class="w-9 h-9 animate-spin text-teal-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-xs font-medium tracking-wide">Memuat pratinjau dokumen...</span>
                </div>

                <!-- PDF / Web Iframe Viewer -->
                <iframe id="pdfPreviewIframe" 
                        src="about:blank" 
                        onload="handlePdfIframeLoaded()"
                        class="w-full h-full border-0 bg-white" 
                        style="width: 100%; height: 100%;"></iframe>

                <!-- Image Preview Viewer (Untuk file foto/gambar) -->
                <div id="imagePreviewContainer" style="display: none;" class="w-full h-full overflow-auto p-4 flex items-center justify-center bg-slate-950">
                    <img id="pdfPreviewImg" src="" alt="Pratinjau Berkas" class="max-h-full max-w-full rounded-xl object-contain shadow-2xl border border-slate-800">
                </div>
            </div>

            <!-- Modal Footer Note -->
            <div class="px-4 py-2 bg-slate-900 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between shrink-0">
                <span>💡 Tips: Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 text-slate-200 rounded font-mono text-[10px]">ESC</kbd> untuk menutup pratinjau.</span>
                <span class="text-slate-500">SIPANDA Dokumen Viewer</span>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Buka Modal Pratinjau Dokumen Universal
     */
    function openPdfPreviewModal(url, title = 'Dokumen PDF') {
        if (!url) return;

        const wrapper = document.getElementById('pdfPreviewModalWrapper');
        const iframe = document.getElementById('pdfPreviewIframe');
        const imgContainer = document.getElementById('imagePreviewContainer');
        const img = document.getElementById('pdfPreviewImg');
        const loading = document.getElementById('pdfLoadingIndicator');
        const modalTitle = document.getElementById('pdfModalTitle');
        const btnNewTab = document.getElementById('pdfBtnNewTab');
        const btnDownload = document.getElementById('pdfBtnDownload');
        const modalIcon = document.getElementById('pdfModalIcon');
        const btnPrint = document.getElementById('pdfBtnPrint');

        // Normalisasi URL preview untuk rute download arsip & regulasi agar tidak trigger forced download
        let previewUrl = url;
        if (previewUrl.includes('/arsip/') && previewUrl.endsWith('/download')) {
            previewUrl = previewUrl.replace('/download', '/preview');
        } else if (previewUrl.includes('/regulasi/') && previewUrl.endsWith('/download')) {
            previewUrl = previewUrl.replace('/download', '/preview');
        }

        // Set Link New Tab & Download Asli
        btnNewTab.href = previewUrl;
        btnDownload.href = url;
        modalTitle.textContent = title || 'Dokumen PDF';
        modalTitle.title = title || 'Dokumen PDF';

        const isImage = /\.(jpg|jpeg|png|webp|gif)($|\?)/i.test(url);

        loading.style.display = 'flex';

        if (isImage) {
            iframe.style.display = 'none';
            imgContainer.style.display = 'flex';
            img.src = previewUrl;
            modalIcon.textContent = '🖼️';
            btnPrint.style.display = 'none';
            img.onload = () => { loading.style.display = 'none'; };
        } else {
            imgContainer.style.display = 'none';
            iframe.style.display = 'block';
            modalIcon.textContent = '📄';
            btnPrint.style.display = 'inline-flex';
            iframe.src = previewUrl;
        }

        wrapper.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    /**
     * Tutup Modal Pratinjau Dokumen
     */
    function closePdfPreviewModal() {
        const wrapper = document.getElementById('pdfPreviewModalWrapper');
        const iframe = document.getElementById('pdfPreviewIframe');
        const img = document.getElementById('pdfPreviewImg');

        wrapper.style.display = 'none';
        iframe.src = 'about:blank';
        img.src = '';
        document.body.style.overflow = '';
    }

    function handlePdfIframeLoaded() {
        const iframe = document.getElementById('pdfPreviewIframe');
        const loading = document.getElementById('pdfLoadingIndicator');
        if (iframe && iframe.src !== 'about:blank') {
            loading.style.display = 'none';
        }
    }

    function printPdfPreviewFrame() {
        const iframe = document.getElementById('pdfPreviewIframe');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                window.open(iframe.src, '_blank');
            }
        }
    }

    // Keyboard ESC Listener
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const wrapper = document.getElementById('pdfPreviewModalWrapper');
            if (wrapper && wrapper.style.display !== 'none') {
                closePdfPreviewModal();
            }
        }
    });

    // Universal Global Click Interceptor untuk seluruh Link PDF & Cetak Dokumen
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) return;

        // Skip jika ditandai eksplisit tidak ingin preview
        if (link.hasAttribute('data-no-preview')) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('javascript:')) return;

        const lowerHref = href.toLowerCase();
        const isPdf = lowerHref.includes('.pdf') || 
                      lowerHref.includes('/cetak') || 
                      lowerHref.includes('/preview') ||
                      (lowerHref.includes('/arsip/') && lowerHref.includes('/download')) ||
                      (lowerHref.includes('/regulasi/') && lowerHref.includes('/download')) ||
                      link.classList.contains('preview-pdf') || 
                      link.hasAttribute('data-preview-pdf');

        const isImage = /\.(jpg|jpeg|png|webp|gif)($|\?)/i.test(lowerHref);

        if (isPdf || isImage) {
            e.preventDefault();
            e.stopPropagation();

            let title = link.getAttribute('title') || 
                        link.getAttribute('data-title') || 
                        link.innerText.trim() || 
                        'Dokumen';
            
            // Bersihkan teks emoji atau tanda panah dari title
            title = title.replace(/[📥📄📎🔍→&rarr;]/g, '').trim();

            openPdfPreviewModal(href, title || 'Pratinjau Dokumen');
        }
    });
</script>
