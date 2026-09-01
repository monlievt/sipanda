<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Bank Artikel FAQ APIP</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Basis Pengetahuan Tanya-Jawab Resmi untuk Publik, OPD, dan Penasihat Virtual AI</p>
            </div>
            <div>
                <button onclick="openModalCreateFaq()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Artikel FAQ
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Pencarian -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.faq.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end text-sm">
            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Cari Pertanyaan / Jawaban / Pasal</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Kategori Pengawasan</label>
                <select name="kategori" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Kategori --</option>
                    <option value="keuangan" {{ $kategori === 'keuangan' ? 'selected' : '' }}>Keuangan & Anggaran</option>
                    <option value="pbj" {{ $kategori === 'pbj' ? 'selected' : '' }}>Pengadaan Barang & Jasa (PBJ)</option>
                    <option value="desa" {{ $kategori === 'desa' ? 'selected' : '' }}>Pemerintahan & Keuangan Desa</option>
                    <option value="aset" {{ $kategori === 'aset' ? 'selected' : '' }}>Aset & BMD</option>
                    <option value="kepegawaian" {{ $kategori === 'kepegawaian' ? 'selected' : '' }}>Disiplin ASN & Etika</option>
                    <option value="investigasi" {{ $kategori === 'investigasi' ? 'selected' : '' }}>Investigasi & Tipikor</option>
                    <option value="umum" {{ $kategori === 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-xl text-xs shadow-xs transition-all cursor-pointer">Filter</button>
                @if($search || $kategori)
                    <a href="{{ route('master.faq.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel FAQ -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Pertanyaan / Kasus</th>
                        <th class="py-3.5 px-4">Jawaban / Advis Resmi APIP</th>
                        <th class="py-3.5 px-4">Kategori & Dasar Hukum</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($faqList as $index => $faq)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $faqList->firstItem() + $index }}</td>
                            <td class="py-3 px-4 max-w-xs font-bold text-slate-900 dark:text-white">
                                {{ $faq->pertanyaan }}
                            </td>
                            <td class="py-3 px-4 max-w-md text-slate-600 dark:text-slate-300">
                                <p class="line-clamp-2">{{ $faq->jawaban }}</p>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 block w-fit mb-1">
                                    {{ ucfirst($faq->kategori) }}
                                </span>
                                @if($faq->dasar_hukum_rujukan)
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-medium block">
                                        ⚖️ {{ $faq->dasar_hukum_rujukan }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $faq->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $faq->is_published ? 'Terbit (Publik)' : 'Draf' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openModalEditFaq({{ json_encode($faq) }})" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-lg transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('master.faq.destroy', $faq->id) }}" onsubmit="return confirm('Hapus artikel FAQ ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada artikel FAQ yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $faqList->links() }}
        </div>
    </div>

    <!-- Modal Tambah FAQ Baru -->
    <div id="modalCreateFaq" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Artikel FAQ / QnA Resmi</h3>
                    <p class="text-[11px] text-slate-500">Artikel ini akan tampil di halaman publik dan dijadikan dasar acuan Asisten AI.</p>
                </div>
                <button onclick="document.getElementById('modalCreateFaq').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.faq.store') }}" class="space-y-3.5 mt-4">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">Pertanyaan / Topik Kasus <span class="text-rose-500">*</span></label>
                    <input type="text" name="pertanyaan" required placeholder="Contoh: Bagaimana ketentuan pembayaran honor narasumber non-PNS?" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Kategori Pengawasan <span class="text-rose-500">*</span></label>
                        <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="keuangan">Keuangan & Anggaran</option>
                            <option value="pbj">Pengadaan Barang & Jasa</option>
                            <option value="desa">Pemerintahan & Keuangan Desa</option>
                            <option value="aset">Aset & BMD</option>
                            <option value="kepegawaian">Disiplin ASN & Etika</option>
                            <option value="investigasi">Investigasi & Tipikor</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tautkan ke Regulasi Terkait</label>
                        <select name="regulasi_hukum_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="">-- Pilih Dokumen Regulasi (Opsional) --</option>
                            @foreach($regulasiList as $r)
                                <option value="{{ $r->id }}">{{ $r->nomor_regulasi }} - {{ Str::limit($r->judul, 40) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Dasar Hukum & Pasal Rujukan</label>
                    <input type="text" name="dasar_hukum_rujukan" placeholder="Contoh: Perbup No. 25/2025 Pasal 8 ayat (2) & Permendagri No. 77/2020" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jawaban & Pertimbangan Advis APIP <span class="text-rose-500">*</span></label>
                    <textarea name="jawaban" rows="5" required placeholder="Tuliskan uraian advis resmi, batasan aturan, dan solusi yang disarankan Inspektorat..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select name="is_published" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="1">Terbitkan (Tampil di Publik & AI)</option>
                            <option value="0">Draf (Disimpan Sementara)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Urutan Prioritas</label>
                        <input type="number" name="urutan" value="0" min="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalCreateFaq').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Artikel FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit FAQ -->
    <div id="modalEditFaq" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Artikel FAQ</h3>
                </div>
                <button onclick="document.getElementById('modalEditFaq').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditFaq" method="POST" action="" class="space-y-3.5 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1">Pertanyaan / Topik Kasus <span class="text-rose-500">*</span></label>
                    <input type="text" id="editPertanyaan" name="pertanyaan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Kategori Pengawasan <span class="text-rose-500">*</span></label>
                        <select id="editKategoriFaq" name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="keuangan">Keuangan & Anggaran</option>
                            <option value="pbj">Pengadaan Barang & Jasa</option>
                            <option value="desa">Pemerintahan & Keuangan Desa</option>
                            <option value="aset">Aset & BMD</option>
                            <option value="kepegawaian">Disiplin ASN & Etika</option>
                            <option value="investigasi">Investigasi & Tipikor</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tautkan ke Regulasi Terkait</label>
                        <select id="editRegulasiId" name="regulasi_hukum_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="">-- Pilih Dokumen Regulasi (Opsional) --</option>
                            @foreach($regulasiList as $r)
                                <option value="{{ $r->id }}">{{ $r->nomor_regulasi }} - {{ Str::limit($r->judul, 40) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Dasar Hukum & Pasal Rujukan</label>
                    <input type="text" id="editDasarHukum" name="dasar_hukum_rujukan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Jawaban & Pertimbangan Advis APIP <span class="text-rose-500">*</span></label>
                    <textarea id="editJawaban" name="jawaban" rows="5" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select id="editIsPublished" name="is_published" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="1">Terbitkan (Tampil di Publik & AI)</option>
                            <option value="0">Draf (Disimpan Sementara)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Urutan Prioritas</label>
                        <input type="number" id="editUrutan" name="urutan" min="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditFaq').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalCreateFaq() {
            document.getElementById('modalCreateFaq').classList.remove('hidden');
        }

        function openModalEditFaq(faq) {
            document.getElementById('formEditFaq').action = '/master/faq/' + faq.id;
            document.getElementById('editPertanyaan').value = faq.pertanyaan || '';
            document.getElementById('editJawaban').value = faq.jawaban || '';
            document.getElementById('editKategoriFaq').value = faq.kategori || 'umum';
            document.getElementById('editRegulasiId').value = faq.regulasi_hukum_id || '';
            document.getElementById('editDasarHukum').value = faq.dasar_hukum_rujukan || '';
            document.getElementById('editIsPublished').value = faq.is_published ? '1' : '0';
            document.getElementById('editUrutan').value = faq.urutan || 0;
            document.getElementById('modalEditFaq').classList.remove('hidden');
        }
    </script>
</x-app-layout>
