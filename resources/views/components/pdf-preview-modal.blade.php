<!-- Modal Universal Pratinjau Dokumen & PDF SINGAWASI (Popup Preview Tanpa Download) -->
<div id="pdfPreviewModalWrapper" class="no-print font-sans relative" style="z-index: 99999999; display: none;">

    <!-- Solid Scrim Backdrop -->
    <div id="pdfPreviewBackdrop"
         onclick="closePdfPreviewModal()"
         style="position: fixed; inset: 0; transition: opacity 0.15s ease;"
         class="bg-black/50 cursor-pointer"></div>

    <!-- Modal Dialog Window -->
    <div style="position: fixed; inset: 0; pointer-events: none; display: flex; align-items: center; justify-content: center; padding: 16px; z-index: 100000000;" class="sm:p-6">
        <div id="pdfPreviewContainer"
             style="pointer-events: auto; width: 100%; max-width: 1100px; height: 92vh; max-height: 96vh;"
             class="rounded-[10px] bg-surface border border-hairline shadow-lg flex flex-col overflow-hidden transition-all">

            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-surface-subtle border-b border-hairline text-ink flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="pdfModalIcon" class="w-9 h-9 rounded-[6px] bg-brand-soft text-brand-ink flex items-center justify-center border border-brand-border shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-[4px] bg-brand-soft text-brand-ink border border-brand-border tracking-wider">
                                Pratinjau Dokumen
                            </span>
                        </div>
                        <h3 id="pdfModalTitle" class="text-xs sm:text-sm font-bold text-ink truncate mt-0.5" title="Pratinjau Dokumen">
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
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] bg-surface hover:bg-surface-subtle text-ink text-xs font-semibold border border-hairline-strong transition-colors cursor-pointer"
                            title="Cetak Dokumen Ini">
                        <svg class="w-4 h-4 text-ink-secondary" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak</span>
                    </button>

                    <!-- Tombol Buka di Tab Baru -->
                    <a id="pdfBtnNewTab"
                       href="#"
                       target="_blank"
                       rel="noopener noreferrer"
                       data-no-preview="true"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] bg-surface hover:bg-surface-subtle text-ink text-xs font-semibold border border-hairline-strong transition-colors"
                       title="Buka Dokumen di Tab Browser Baru">
                        <svg class="w-4 h-4 text-ink-secondary" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span class="hidden md:inline">Tab Baru</span>
                    </a>

                    <!-- Tombol Unduh Fisik Asli -->
                    <a id="pdfBtnDownload"
                       href="#"
                       download
                       data-no-preview="true"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-[6px] bg-brand hover:bg-brand-hover text-white text-xs font-semibold transition-colors cursor-pointer"
                       title="Unduh File Dokumen">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh File</span>
                    </a>

                    <!-- Tombol Tutup (Silang) -->
                    <button type="button"
                            onclick="closePdfPreviewModal()"
                            aria-label="Tutup pratinjau dokumen"
                            class="p-2 rounded-[6px] text-ink-muted hover:text-ink hover:bg-surface transition-colors ml-1 cursor-pointer"
                            title="Tutup Pratinjau (ESC)">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Iframe & Image Preview Container) -->
            <div class="flex-1 w-full h-full relative bg-canvas overflow-hidden flex items-center justify-center">

                <!-- Loading State Spinner -->
                <div id="pdfLoadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center bg-canvas text-ink-muted z-10 space-y-3 pointer-events-none">
                    <svg class="w-8 h-8 animate-spin text-brand-ink" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-xs font-medium tracking-wide">Memuat pratinjau dokumen...</span>
                </div>

                <!-- PDF / Web Iframe Viewer -->
                <iframe id="pdfPreviewIframe"
                        src="about:blank"
                        onload="handlePdfIframeLoaded()"
                        title="Pratinjau Dokumen PDF"
                        class="w-full h-full border-0 bg-surface"
                        style="width: 100%; height: 100%;"></iframe>

                <!-- Image Preview Viewer (Untuk file foto/gambar) -->
                <div id="imagePreviewContainer" style="display: none;" class="w-full h-full overflow-auto p-4 flex items-center justify-center bg-canvas">
                    <img id="pdfPreviewImg" src="" alt="Pratinjau Berkas" class="max-h-full max-w-full rounded-[10px] object-contain border border-hairline">
                </div>
            </div>

            <!-- Modal Footer Note -->
            <div class="px-4 py-2 bg-surface-subtle border-t border-hairline text-xs text-ink-muted flex items-center justify-between shrink-0">
                <span>Tips: Tekan <kbd class="px-1.5 py-0.5 bg-surface border border-hairline text-ink rounded-[4px] font-mono text-[10px]">ESC</kbd> untuk menutup pratinjau.</span>
                <span class="text-ink-secondary font-medium">SINGAWASI Dokumen Viewer</span>
            </div>
        </div>
    </div>
</div>

<script>
    const pdfIconSvg = '<svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>';
    const imageIconSvg = '<svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>';

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
        } else if (previewUrl.includes('/storage/')) {
            // Bypass Apache 403 Forbidden: Alihkan direct static storage link ke backend stream
            const pathPart = previewUrl.substring(previewUrl.indexOf('/storage/') + 9);
            previewUrl = '/dokumen/berkas/' + pathPart;
        }

        // Set Link New Tab & Download Asli
        btnNewTab.href = previewUrl;
        btnDownload.href = previewUrl.includes('/dokumen/berkas/')
            ? (previewUrl + (previewUrl.includes('?') ? '&download=1' : '?download=1'))
            : url;
        modalTitle.textContent = title || 'Dokumen PDF';
        modalTitle.title = title || 'Dokumen PDF';

        const isImage = /\.(jpg|jpeg|png|webp|gif)($|\?)/i.test(url);

        loading.style.display = 'flex';

        if (isImage) {
            iframe.style.display = 'none';
            imgContainer.style.display = 'flex';
            img.src = previewUrl;
            modalIcon.innerHTML = imageIconSvg;
            btnPrint.style.display = 'none';
            img.onload = () => { loading.style.display = 'none'; };
        } else {
            imgContainer.style.display = 'none';
            iframe.style.display = 'block';
            modalIcon.innerHTML = pdfIconSvg;
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
                      lowerHref.includes('/dokumen/berkas') ||
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

            // Bersihkan karakter simbol dekoratif dari title
            title = title.replace(/[\u2190-\u2199\u{1F300}-\u{1FAFF}]/gu, '').trim();

            openPdfPreviewModal(href, title || 'Pratinjau Dokumen');
        }
    });
</script>
