<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Bank Regulasi & Dasar Hukum APIP</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Repositori Dokumen PDF Regulasi Pengawasan, SBM, Perbup, dan Dasar Pengetahuan AI</p>
            </div>
            <div>
                <button onclick="openModalUploadRegulasi()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Unggah Dokumen Regulasi
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Pencarian -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.regulasi.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5 items-end text-sm">
            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Cari Judul / Nomor / Isi</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Kategori Pengawasan</label>
                <select name="kategori" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori --</option>
                    <option value="keuangan" {{ $kategori === 'keuangan' ? 'selected' : '' }}>Keuangan & Anggaran Daerah</option>
                    <option value="pbj" {{ $kategori === 'pbj' ? 'selected' : '' }}>Pengadaan Barang & Jasa (PBJ)</option>
                    <option value="desa" {{ $kategori === 'desa' ? 'selected' : '' }}>Pemerintahan & Keuangan Desa</option>
                    <option value="aset" {{ $kategori === 'aset' ? 'selected' : '' }}>Aset & Barang Milik Daerah (BMD)</option>
                    <option value="kepegawaian" {{ $kategori === 'kepegawaian' ? 'selected' : '' }}>Disiplin ASN & Etika</option>
                    <option value="investigasi" {{ $kategori === 'investigasi' ? 'selected' : '' }}>Audit Investigasi & Tipikor</option>
                    <option value="umum" {{ $kategori === 'umum' ? 'selected' : '' }}>Umum / Tata Kelola</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Jenis Dokumen</label>
                <select name="jenis" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Jenis --</option>
                    <option value="perbup" {{ $jenis === 'perbup' ? 'selected' : '' }}>Peraturan Bupati (Perbup)</option>
                    <option value="perda" {{ $jenis === 'perda' ? 'selected' : '' }}>Peraturan Daerah (Perda)</option>
                    <option value="perpres" {{ $jenis === 'perpres' ? 'selected' : '' }}>Peraturan Presiden (Perpres)</option>
                    <option value="permendagri" {{ $jenis === 'permendagri' ? 'selected' : '' }}>Permendagri</option>
                    <option value="surat_edaran" {{ $jenis === 'surat_edaran' ? 'selected' : '' }}>Surat Edaran (SE)</option>
                    <option value="juknis" {{ $jenis === 'juknis' ? 'selected' : '' }}>Petunjuk Teknis (Juknis)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition-all cursor-pointer">Filter</button>
                @if($search || $kategori || $jenis)
                    <a href="{{ route('master.regulasi.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Regulasi -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nomor & Tahun Regulasi</th>
                        <th class="py-3.5 px-4">Judul & Intisari Regulasi</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Berkas PDF</th>
                        <th class="py-3.5 px-4 text-center">Visibilitas</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($regulasiList as $index => $reg)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $regulasiList->firstItem() + $index }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $reg->nomor_regulasi }}</span>
                                <span class="text-[10px] text-slate-400">Tahun {{ $reg->tahun }} &bull; {{ strtoupper($reg->jenis_regulasi) }}</span>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $reg->judul }}</p>
                                @if($reg->ringkasan_eksekutif)
                                    <p class="text-[10px] text-slate-500 line-clamp-2 mt-1">{{ $reg->ringkasan_eksekutif }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    {{ ucfirst($reg->kategori) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($reg->file_path)
                                    <a href="{{ route('master.regulasi.download', $reg->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-bold rounded-lg text-[10px] transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        PDF ({{ $reg->ukuran_kb ?: 'Doc' }})
                                    </a>
                                    <span class="block text-[9px] text-slate-400 mt-0.5">{{ $reg->diunduh_count }}x diunduh</span>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">Tanpa File</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reg->visibilitas === 'publik' ? 'bg-emerald-100 text-emerald-800' : ($reg->visibilitas === 'opd' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                    {{ ucfirst($reg->visibilitas) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openModalEditRegulasi({{ json_encode($reg) }})" class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-lg transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('master.regulasi.destroy', $reg->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus regulasi {{ addslashes($reg->nomor_regulasi) }}?');" class="inline">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada dokumen regulasi yang diunggah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $regulasiList->links() }}
        </div>
    </div>

    <!-- Modal Upload Regulasi Baru -->
    <div id="modalUploadRegulasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Unggah Dokumen Regulasi / Juknis</h3>
                    <p class="text-[11px] text-slate-500">Dokumen PDF akan diindeks sebagai dasar hukum AI dan materi unduhan.</p>
                </div>
                <button onclick="document.getElementById('modalUploadRegulasi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.regulasi.store') }}" enctype="multipart/form-data" class="space-y-3.5 mt-4">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">Judul Peraturan / Regulasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: Standar Biaya Masukan (SBM) Pemerintah Kabupaten Trenggalek" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nomor Regulasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nomor_regulasi" required placeholder="Perbup No. 25 Tahun 2025" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun" required value="{{ date('Y') }}" min="2000" max="2035" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Regulasi <span class="text-rose-500">*</span></label>
                        <select name="jenis_regulasi" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="perbup">Peraturan Bupati (Perbup)</option>
                            <option value="perda">Peraturan Daerah (Perda)</option>
                            <option value="perpres">Peraturan Presiden (Perpres)</option>
                            <option value="permendagri">Permendagri</option>
                            <option value="surat_edaran">Surat Edaran (SE)</option>
                            <option value="keputusan_inspektur">Keputusan Inspektur</option>
                            <option value="juknis">Petunjuk Teknis (Juknis)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
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
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ringkasan Eksekutif / Poin-Poin Pasal Kunci</label>
                    <textarea name="ringkasan_eksekutif" rows="4" placeholder="Tuliskan pasal-pasal kunci, batas nominal, atau ketentuan penting yang sering dijadikan acuan audit (bagian ini akan dibaca secara cerdas oleh AI Assistant)..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Unggah Berkas PDF (Maks 25MB)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Visibilitas Akses <span class="text-rose-500">*</span></label>
                        <select name="visibilitas" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="publik">Publik & OPD (Bisa Diunduh Bebas)</option>
                            <option value="opd">Hanya Pengguna OPD Terdaftar</option>
                            <option value="internal">Internal APIP Inspektorat Saja</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalUploadRegulasi').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan & Indeks Regulasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Regulasi -->
    <div id="modalEditRegulasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Dokumen Regulasi</h3>
                </div>
                <button onclick="document.getElementById('modalEditRegulasi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditRegulasi" method="POST" action="" enctype="multipart/form-data" class="space-y-3.5 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1">Judul Peraturan <span class="text-rose-500">*</span></label>
                    <input type="text" id="editJudul" name="judul" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nomor Regulasi <span class="text-rose-500">*</span></label>
                        <input type="text" id="editNomor" name="nomor_regulasi" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" id="editTahun" name="tahun" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Regulasi <span class="text-rose-500">*</span></label>
                        <select id="editJenis" name="jenis_regulasi" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="perbup">Peraturan Bupati (Perbup)</option>
                            <option value="perda">Peraturan Daerah (Perda)</option>
                            <option value="perpres">Peraturan Presiden (Perpres)</option>
                            <option value="permendagri">Permendagri</option>
                            <option value="surat_edaran">Surat Edaran (SE)</option>
                            <option value="keputusan_inspektur">Keputusan Inspektur</option>
                            <option value="juknis">Petunjuk Teknis (Juknis)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Kategori Pengawasan <span class="text-rose-500">*</span></label>
                        <select id="editKategori" name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="keuangan">Keuangan & Anggaran</option>
                            <option value="pbj">Pengadaan Barang & Jasa</option>
                            <option value="desa">Pemerintahan & Keuangan Desa</option>
                            <option value="aset">Aset & BMD</option>
                            <option value="kepegawaian">Disiplin ASN & Etika</option>
                            <option value="investigasi">Investigasi & Tipikor</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ringkasan Eksekutif / Poin-Poin Pasal Kunci</label>
                    <textarea id="editRingkasan" name="ringkasan_eksekutif" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Ganti Berkas PDF (Opsional)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Visibilitas Akses <span class="text-rose-500">*</span></label>
                        <select id="editVisibilitas" name="visibilitas" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="publik">Publik & OPD (Bisa Diunduh Bebas)</option>
                            <option value="opd">Hanya Pengguna OPD Terdaftar</option>
                            <option value="internal">Internal APIP Inspektorat Saja</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditRegulasi').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalUploadRegulasi() {
            document.getElementById('modalUploadRegulasi').classList.remove('hidden');
        }

        function openModalEditRegulasi(reg) {
            document.getElementById('formEditRegulasi').action = '/master/regulasi/' + reg.id;
            document.getElementById('editJudul').value = reg.judul || '';
            document.getElementById('editNomor').value = reg.nomor_regulasi || '';
            document.getElementById('editTahun').value = reg.tahun || '';
            document.getElementById('editJenis').value = reg.jenis_regulasi || 'perbup';
            document.getElementById('editKategori').value = reg.kategori || 'umum';
            document.getElementById('editRingkasan').value = reg.ringkasan_eksekutif || '';
            document.getElementById('editVisibilitas').value = reg.visibilitas || 'publik';
            document.getElementById('modalEditRegulasi').classList.remove('hidden');
        }
    </script>
</x-app-layout>
