<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-ink">Kotak Masukan, Saran & Laporan Bug UAT</h1>
                <p class="text-xs text-ink-secondary mt-0.5">Pusat Pengumpulan Feedback, Kendala Teknis, dan Usulan Perbaikan dari Pengguna Pengujian</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-xs font-bold rounded-[4px] inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-600 dark:bg-amber-400"></span>
                    Masa Pengujian (UAT Mode)
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ink-secondary uppercase tracking-wider">Total Masukan</span>
                <div class="w-8 h-8 rounded-[6px] bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-bold text-ink tabular-nums">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-ink-muted ml-1">laporan masuk</span>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ink-secondary uppercase tracking-wider">Perlu Ditelaah</span>
                <div class="w-8 h-8 rounded-[6px] bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-bold text-amber-800 dark:text-amber-300 tabular-nums">{{ $stats['baru'] }}</span>
                <span class="text-[11px] text-ink-muted ml-1">belum ditindaklanjuti</span>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ink-secondary uppercase tracking-wider">Bug & Kendala Kritis</span>
                <div class="w-8 h-8 rounded-[6px] bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-bold text-rose-700 dark:text-rose-300 tabular-nums">{{ $stats['bug_kritis'] }}</span>
                <span class="text-[11px] text-ink-muted ml-1">prioritas tinggi/kritis</span>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ink-secondary uppercase tracking-wider">Sudah Diperbaiki</span>
                <div class="w-8 h-8 rounded-[6px] bg-brand-soft text-brand-ink flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-bold text-brand-ink tabular-nums">{{ $stats['diperbaiki'] }}</span>
                <span class="text-[11px] text-ink-muted ml-1">telah diselesaikan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="mb-6 bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
        <form method="GET" action="{{ route('master.feedback.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3.5 items-end text-sm">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Cari Judul / Uraian / Nama Pelapor</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
            </div>

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Kategori</label>
                <select name="kategori" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                    <option value="">-- Semua Kategori --</option>
                    <option value="bug" {{ $kategori === 'bug' ? 'selected' : '' }}>Bug / Kendala</option>
                    <option value="saran" {{ $kategori === 'saran' ? 'selected' : '' }}>Ide & Saran</option>
                    <option value="pertanyaan" {{ $kategori === 'pertanyaan' ? 'selected' : '' }}>Pertanyaan Alur</option>
                    <option value="apresiasi" {{ $kategori === 'apresiasi' ? 'selected' : '' }}>Apresiasi / UX</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Status</label>
                <select name="status" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="baru" {{ $status === 'baru' ? 'selected' : '' }}>Baru Masuk</option>
                    <option value="sedang_ditelaah" {{ $status === 'sedang_ditelaah' ? 'selected' : '' }}>Sedang Ditelaah</option>
                    <option value="diperbaiki" {{ $status === 'diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki</option>
                    <option value="ditutup" {{ $status === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="ledger-btn-primary w-full !text-xs cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('master.feedback.index') }}" class="ledger-btn-secondary !text-xs">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Feedback -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-hairline bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Pelapor & Waktu</th>
                        <th class="py-3.5 px-4">Kategori & Urgensi</th>
                        <th class="py-3.5 px-4">Judul & Uraian Masukan</th>
                        <th class="py-3.5 px-4 text-center">Screenshot</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($feedbacks as $index => $item)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono text-ink-muted">
                                {{ $feedbacks->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-ink block">{{ $item->nama_pelapor }}</span>
                                <span class="text-[10px] text-ink-muted">{{ $item->role_pelapor }}</span>
                                <span class="text-[10px] text-ink-secondary font-mono block mt-0.5">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4 space-y-1">
                                <span class="inline-block px-2 py-0.5 rounded-[4px] text-[10px] font-bold {{ $item->kategori_badge['class'] }}">
                                    {{ $item->kategori_badge['label'] }}
                                </span>
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded-[4px] text-[10px] {{ $item->urgensi_badge['class'] }}">
                                        {{ $item->urgensi_badge['label'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="font-bold text-ink block text-xs line-clamp-1">{{ $item->judul }}</span>
                                <p class="text-ink-secondary text-[11px] line-clamp-2 mt-0.5">{{ $item->deskripsi }}</p>
                                @if($item->url_halaman)
                                    <span class="text-[10px] text-amber-800 dark:text-amber-300 font-mono line-clamp-1 mt-1">URL: {{ $item->url_halaman }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->screenshot_url)
                                    <button type="button" onclick="previewScreenshot('{{ $item->screenshot_url }}', '{{ addslashes($item->judul) }}')" class="group relative inline-block rounded-[6px] overflow-hidden border border-hairline-strong hover:border-brand transition-colors cursor-pointer" title="Perbesar tangkapan layar">
                                        <img src="{{ $item->screenshot_url }}" alt="Thumb" class="w-12 h-12 object-cover">
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                    </button>
                                @else
                                    <span class="text-ink-muted text-[10px] italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $item->status_badge['class'] }}">
                                    {{ $item->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openDetailModal({{ json_encode($item) }})" class="px-2.5 py-1 bg-surface-subtle hover:bg-hairline text-ink rounded-[6px] text-[10px] font-bold border border-hairline-strong cursor-pointer transition-colors" title="Tinjau & Tindak Lanjut">
                                        Tindak Lanjut
                                    </button>

                                    <form method="POST" action="{{ route('master.feedback.destroy', $item->id) }}" onsubmit="return confirm('Hapus feedback ini dari database?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 hover:bg-rose-100 rounded-[6px] text-[10px] border border-rose-200 dark:border-rose-800 transition-colors cursor-pointer" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-muted">
                                <div class="font-bold text-ink">Belum ada laporan kendala / masukan baru.</div>
                                <div class="text-[11px] text-ink-secondary mt-1">Seluruh modul berjalan lancar atau belum ada pengguna yang mengirimkan masukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-hairline">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail & Update Status Feedback -->
    <div id="detailFeedbackModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50">
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle bg-surface border border-hairline shadow-lg rounded-[10px] relative text-ink">
                <button type="button" onclick="closeDetailModal()" class="absolute top-5 right-5 text-ink-muted hover:text-ink p-1.5 rounded-[6px] hover:bg-surface-subtle transition-colors" aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <h3 class="text-base font-bold text-ink mb-4 flex items-center gap-2 pr-8">
                    <span>Tinjau & Tindak Lanjut Masukan UAT</span>
                    <span id="modalKategoriBadge" class="text-[10px] px-2 py-0.5 rounded-[4px] font-bold"></span>
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Info Pelapor -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-surface-subtle rounded-[10px] border border-hairline text-[11px]">
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase font-semibold">Pelapor</span>
                            <strong id="modalPelapor" class="text-ink"></strong>
                        </div>
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase font-semibold">Role/Instansi</span>
                            <strong id="modalRole" class="text-ink-secondary"></strong>
                        </div>
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase font-semibold">Urgensi</span>
                            <strong id="modalUrgensi" class="text-amber-800 dark:text-amber-300"></strong>
                        </div>
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase font-semibold">Waktu Lapor</span>
                            <span id="modalWaktu" class="text-ink-secondary font-mono"></span>
                        </div>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <div class="space-y-2">
                        <h4 id="modalJudul" class="font-bold text-sm text-ink"></h4>
                        <div class="p-3.5 bg-surface-subtle rounded-[10px] border border-hairline text-ink-secondary leading-relaxed whitespace-pre-wrap max-h-48 overflow-y-auto" id="modalDeskripsi"></div>
                    </div>

                    <!-- URL & Device Info -->
                    <div class="space-y-1 text-[10px] text-ink-secondary bg-surface-subtle p-3 rounded-[6px] border border-hairline">
                        <div><strong class="text-ink">Halaman:</strong> <span id="modalUrl" class="text-brand-ink font-mono"></span></div>
                        <div class="truncate"><strong class="text-ink">Info Browser/Layar:</strong> <span id="modalBrowser" class="font-mono text-ink-muted"></span></div>
                    </div>

                    <!-- Screenshot Preview -->
                    <div id="modalScreenshotBox" class="hidden">
                        <span class="font-bold text-ink-secondary block text-[10px] uppercase mb-1">Lampiran Tangkapan Layar:</span>
                        <a id="modalScreenshotLink" href="" target="_blank" class="inline-block rounded-[6px] overflow-hidden border border-hairline-strong">
                            <img id="modalScreenshotImg" src="" alt="Screenshot" class="max-h-56 rounded-[6px] object-contain">
                        </a>
                    </div>

                    <!-- Form Tindak Lanjut Admin -->
                    <form id="formUpdateStatus" method="POST" action="" class="pt-3 border-t border-hairline space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-ink-secondary uppercase tracking-wider text-[10px] mb-1">Ubah Status Tindak Lanjut</label>
                                <select name="status" id="modalStatusSelect" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                                    <option value="baru">Baru Masuk</option>
                                    <option value="sedang_ditelaah">Sedang Ditelaah</option>
                                    <option value="diperbaiki">Sudah Diperbaiki</option>
                                    <option value="ditutup">Ditutup / Arsip</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-ink-secondary uppercase tracking-wider text-[10px] mb-1">Catatan Respon / Solusi Admin</label>
                                <textarea name="catatan_admin" id="modalCatatanAdmin" rows="2" placeholder="Catatan perbaikan teknis atau konfirmasi solusi..." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2 resize-none"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" onclick="closeDetailModal()" class="ledger-btn-secondary !text-xs">
                                Tutup
                            </button>
                            <button type="submit" class="ledger-btn-primary !text-xs">
                                Simpan Tindak Lanjut
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal Screenshot Full-Size -->
    <div id="lightboxModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80" aria-hidden="true" onclick="closeLightbox()"></div>
        <div class="relative z-10 max-w-5xl max-h-screen text-center space-y-3">
            <div class="flex justify-end">
                <button type="button" onclick="closeLightbox()" class="px-3 py-1.5 bg-surface text-ink border border-hairline rounded-[6px] text-xs font-semibold cursor-pointer">
                    Tutup Pratinjau
                </button>
            </div>
            <img id="lightboxImg" src="" alt="Full Screenshot" class="max-h-[80vh] max-w-full rounded-[10px] shadow-lg border border-hairline mx-auto">
            <p id="lightboxCaption" class="text-xs text-white/90"></p>
        </div>
    </div>

    <script>
        function openDetailModal(item) {
            document.getElementById('modalPelapor').textContent = item.nama_pelapor;
            document.getElementById('modalRole').textContent = item.role_pelapor || '-';
            document.getElementById('modalUrgensi').textContent = item.urgensi.toUpperCase();
            document.getElementById('modalWaktu').textContent = new Date(item.created_at).toLocaleString('id-ID');
            document.getElementById('modalJudul').textContent = item.judul;
            document.getElementById('modalDeskripsi').textContent = item.deskripsi;
            document.getElementById('modalUrl').textContent = item.url_halaman || '-';
            document.getElementById('modalBrowser').textContent = item.browser_info || '-';
            document.getElementById('modalStatusSelect').value = item.status;
            document.getElementById('modalCatatanAdmin').value = item.catatan_admin || '';

            const screenshotBox = document.getElementById('modalScreenshotBox');
            if (item.screenshot_path) {
                const url = '/storage/' + item.screenshot_path;
                document.getElementById('modalScreenshotImg').src = url;
                document.getElementById('modalScreenshotLink').href = url;
                screenshotBox.classList.remove('hidden');
            } else {
                screenshotBox.classList.add('hidden');
            }

            document.getElementById('formUpdateStatus').action = '/master/feedback/' + item.id + '/status';
            document.getElementById('detailFeedbackModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailFeedbackModal').classList.add('hidden');
        }

        function previewScreenshot(url, caption) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxCaption').textContent = caption;
            document.getElementById('lightboxModal').classList.remove('hidden');
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
