<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kotak Masukan, Saran & Laporan Bug UAT</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pusat Pengumpulan Feedback, Kendala Teknis, dan Usulan Perbaikan dari Pengguna Pengujian</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-bold rounded-xl flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Masa Pengujian (UAT Mode)
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Masukan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-xs">💬</div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-slate-500 ml-1">laporan masuk</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Ditelaah</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-xs">🆕</div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $stats['baru'] }}</span>
                <span class="text-[11px] text-amber-600/80 ml-1">belum ditindaklanjuti</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bug & Kendala Kritis</span>
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-xs">🔥</div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $stats['bug_kritis'] }}</span>
                <span class="text-[11px] text-rose-600/80 ml-1">prioritas tinggi/kritis</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Diperbaiki</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-xs">✅</div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['diperbaiki'] }}</span>
                <span class="text-[11px] text-emerald-600/80 ml-1">telah diselesaikan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.feedback.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3.5 items-end text-sm">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Cari Judul / Uraian / Nama Pelapor</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Kategori</label>
                <select name="kategori" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5">
                    <option value="">-- Semua Kategori --</option>
                    <option value="bug" {{ $kategori === 'bug' ? 'selected' : '' }}>🐞 Bug / Kendala</option>
                    <option value="saran" {{ $kategori === 'saran' ? 'selected' : '' }}>💡 Ide & Saran</option>
                    <option value="pertanyaan" {{ $kategori === 'pertanyaan' ? 'selected' : '' }}>❓ Pertanyaan Alur</option>
                    <option value="apresiasi" {{ $kategori === 'apresiasi' ? 'selected' : '' }}>⭐ Apresiasi / UX</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="baru" {{ $status === 'baru' ? 'selected' : '' }}>🆕 Baru Masuk</option>
                    <option value="sedang_ditelaah" {{ $status === 'sedang_ditelaah' ? 'selected' : '' }}>🔍 Sedang Ditelaah</option>
                    <option value="diperbaiki" {{ $status === 'diperbaiki' ? 'selected' : '' }}>✅ Sudah Diperbaiki</option>
                    <option value="ditutup" {{ $status === 'ditutup' ? 'selected' : '' }}>📁 Ditutup</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('master.feedback.index') }}" class="px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Feedback -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Pelapor & Waktu</th>
                        <th class="py-3.5 px-4">Kategori & Urgensi</th>
                        <th class="py-3.5 px-4">Judul & Uraian Masukan</th>
                        <th class="py-3.5 px-4 text-center">Screenshot</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($feedbacks as $index => $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $feedbacks->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $item->nama_pelapor }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item->role_pelapor }}</span>
                                <span class="text-[10px] text-slate-500 font-mono block mt-0.5">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4 space-y-1">
                                <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-bold {{ $item->kategori_badge['class'] }}">
                                    {{ $item->kategori_badge['label'] }}
                                </span>
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] {{ $item->urgensi_badge['class'] }}">
                                        {{ $item->urgensi_badge['label'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs line-clamp-1">{{ $item->judul }}</span>
                                <p class="text-slate-500 dark:text-slate-400 text-[11px] line-clamp-2 mt-0.5">{{ $item->deskripsi }}</p>
                                @if($item->url_halaman)
                                    <span class="text-[10px] text-amber-500 font-mono line-clamp-1 mt-1">📍 {{ $item->url_halaman }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->screenshot_url)
                                    <button type="button" onclick="previewScreenshot('{{ $item->screenshot_url }}', '{{ addslashes($item->judul) }}')" class="group relative inline-block rounded-xl overflow-hidden border border-slate-700 shadow-xs hover:border-amber-500 transition-all cursor-pointer">
                                        <img src="{{ $item->screenshot_url }}" alt="Thumb" class="w-12 h-12 object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                                            🔍
                                        </div>
                                    </button>
                                @else
                                    <span class="text-slate-500 text-[10px] italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] {{ $item->status_badge['class'] }}">
                                    {{ $item->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openDetailModal({{ json_encode($item) }})" class="px-2.5 py-1 bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300 hover:bg-amber-100 rounded-lg text-[10px] font-bold border border-amber-300 shadow-xs cursor-pointer" title="Tinjau & Tindak Lanjut">
                                        ⚙️ Tindak Lanjut
                                    </button>

                                    <form method="POST" action="{{ route('master.feedback.destroy', $item->id) }}" onsubmit="return confirm('Hapus feedback ini dari database?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-400 hover:bg-rose-100 rounded-lg text-[10px] border border-rose-300 shadow-xs" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🎉</div>
                                <div class="font-bold text-slate-300">Belum ada laporan kendala / masukan baru.</div>
                                <div class="text-[11px] text-slate-500">Seluruh modul berjalan lancar atau belum ada pengguna yang mengirimkan masukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail & Update Status Feedback -->
    <div id="detailFeedbackModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-sm">
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle bg-slate-900 border border-slate-800 shadow-2xl rounded-3xl relative text-white">
                <button type="button" onclick="closeDetailModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <h3 class="text-base font-black text-white mb-4 flex items-center gap-2">
                    <span>Tinjau & Tindak Lanjut Masukan UAT</span>
                    <span id="modalKategoriBadge" class="text-[10px] px-2 py-0.5 rounded font-bold"></span>
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Info Pelapor -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-3 bg-slate-950 rounded-2xl border border-slate-800 text-[11px]">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Pelapor</span>
                            <strong id="modalPelapor" class="text-white"></strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Role/Instansi</span>
                            <strong id="modalRole" class="text-slate-300"></strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Urgensi</span>
                            <strong id="modalUrgensi" class="text-amber-400"></strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Waktu Lapor</span>
                            <span id="modalWaktu" class="text-slate-400 font-mono"></span>
                        </div>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <div class="space-y-2">
                        <h4 id="modalJudul" class="font-bold text-sm text-white"></h4>
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-slate-300 leading-relaxed whitespace-pre-wrap max-h-48 overflow-y-auto" id="modalDeskripsi"></div>
                    </div>

                    <!-- URL & Device Info -->
                    <div class="space-y-1 text-[10px] text-slate-400 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                        <div>📍 <strong>Halaman:</strong> <span id="modalUrl" class="text-amber-400 font-mono"></span></div>
                        <div class="truncate">💻 <strong>Info Browser/Layar:</strong> <span id="modalBrowser" class="font-mono text-slate-500"></span></div>
                    </div>

                    <!-- Screenshot Preview -->
                    <div id="modalScreenshotBox" class="hidden">
                        <span class="font-bold text-slate-400 block text-[10px] uppercase mb-1">Lampiran Tangkapan Layar:</span>
                        <a id="modalScreenshotLink" href="" target="_blank" class="inline-block rounded-xl overflow-hidden border border-slate-700 shadow-md">
                            <img id="modalScreenshotImg" src="" alt="Screenshot" class="max-h-56 rounded-xl object-contain">
                        </a>
                    </div>

                    <!-- Form Tindak Lanjut Admin -->
                    <form id="formUpdateStatus" method="POST" action="" class="pt-3 border-t border-slate-800 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Ubah Status Tindak Lanjut</label>
                                <select name="status" id="modalStatusSelect" required class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2">
                                    <option value="baru">🆕 Baru Masuk</option>
                                    <option value="sedang_ditelaah">🔍 Sedang Ditelaah</option>
                                    <option value="diperbaiki">✅ Sudah Diperbaiki</option>
                                    <option value="ditutup">📁 Ditutup / Arsip</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-1">Catatan Respon / Solusi Admin</label>
                                <textarea name="catatan_admin" id="modalCatatanAdmin" rows="2" placeholder="Catatan perbaikan teknis atau konfirmasi solusi..." class="w-full rounded-xl border border-slate-700 bg-slate-800 text-white text-xs px-3 py-2 resize-none"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs">
                                Tutup
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md">
                                Simpan Tindak Lanjut
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal Screenshot Full-Size -->
    <div id="lightboxModal" class="fixed inset-0 z-50 hidden bg-black/95 flex items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
        <div class="max-w-5xl max-h-screen text-center space-y-2">
            <img id="lightboxImg" src="" alt="Full Screenshot" class="max-h-[85vh] max-w-full rounded-2xl shadow-2xl border border-slate-800 mx-auto">
            <p id="lightboxCaption" class="text-xs text-slate-400"></p>
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
