import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * 💰 Format input angka ke mata uang Rupiah secara otomatis (ribuan dipisah titik)
 */
function formatRupiahInput(el) {
    if (!el) return;
    let cursorPos = el.selectionStart || 0;
    let originalLen = el.value.length;
    
    // Ambil hanya digit angka
    let rawVal = el.value.replace(/\D/g, '');
    if (!rawVal) {
        el.value = '';
        return;
    }
    
    // Format ribuan Indonesia
    let formatted = new Intl.NumberFormat('id-ID').format(rawVal);
    el.value = formatted;
    
    // Pertahankan posisi kursor
    let newLen = formatted.length;
    cursorPos = cursorPos + (newLen - originalLen);
    if (cursorPos > 0 && el.setSelectionRange) {
        el.setSelectionRange(cursorPos, cursorPos);
    }
}

window.formatRupiahInput = formatRupiahInput;
window.formatRupiah = formatRupiahInput;

// Auto-format listener untuk seluruh input rupiah di seluruh aplikasi
document.addEventListener('input', function (e) {
    const target = e.target;
    if (target && target.matches && (
        target.matches('input[name*="nilai_"]') ||
        target.matches('input[name*="_rp"]') ||
        target.matches('input[name*="nominal"]') ||
        target.matches('.rupiah-input') ||
        target.getAttribute('data-type') === 'currency'
    )) {
        formatRupiahInput(target);
    }
});

