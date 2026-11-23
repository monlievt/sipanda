<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-ink">Bank Regulasi & Dasar Hukum APIP</h1>
                <p class="text-xs text-ink-secondary mt-0.5">Repositori Dokumen PDF Regulasi Pengawasan, SBM, Perbup, dan Dasar Pengetahuan AI</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('regulasi.public.index') }}" target="_blank" class="ledger-btn-secondary !text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Lihat Tampilan Publik
                </a>
                <button type="button" onclick="openModalUploadRegulasi()" class="ledger-btn-primary !text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Unggah Dokumen Regulasi
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Pencarian -->
    <div class="mb-6 bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
        <form method="GET" action="{{ route('master.regulasi.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5 items-end text-sm">
            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Cari Judul / Nomor / Isi</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
            </div>

            <div>
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Kategori Pengawasan</label>
                <select name="kategori" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
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
                <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Jenis Dokumen</label>
                <select name="jenis" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
                    <option value="">-- Semua Jenis --</option>
                    @foreach(\App\Models\RegulasiHukum::JENIS_REGULASI as $val => $label)
                        <option value="{{ $val }}" {{ $jenis === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="ledger-btn-primary w-full !text-xs cursor-pointer">Filter</button>
                @if($search || $kategori || $jenis)
                    <a href="{{ route('master.regulasi.index') }}" class="ledger-btn-secondary !text-xs text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Regulasi -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
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
                <tbody class="divide-y divide-hairline">
                    @forelse($regulasiList as $index => $reg)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3 px-4 font-semibold text-center text-ink-muted">{{ $regulasiList->firstItem() + $index }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-ink block">{{ $reg->nomor_regulasi }}</span>
                                <span class="text-[10px] text-ink-muted">Tahun {{ $reg->tahun }} &bull; {{ $reg->jenis_regulasi_label }}</span>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <p class="font-semibold text-ink">{{ $reg->judul }}</p>
                                @if($reg->ringkasan_eksekutif)
                                    <p class="text-[10px] text-ink-secondary line-clamp-2 mt-1">{{ $reg->ringkasan_eksekutif }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold uppercase bg-brand-soft text-brand-ink border border-brand-border">
                                    {{ ucfirst($reg->kategori) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($reg->file_path)
                                    <a href="{{ route('master.regulasi.download', $reg->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800 font-bold rounded-[6px] text-[10px] transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        PDF ({{ $reg->ukuran_kb ?: 'Doc' }})
                                    </a>
                                    <span class="block text-[9px] text-ink-muted mt-0.5 tabular-nums">{{ $reg->diunduh_count }}x diunduh</span>
                                @else
                                    <span class="text-ink-muted text-[10px] italic">Tanpa File</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold {{ $reg->visibilitas === 'publik' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : ($reg->visibilitas === 'opd' ? 'bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800' : 'bg-surface-subtle text-ink-secondary border border-hairline') }}">
                                    {{ ucfirst($reg->visibilitas) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openModalEditRegulasi({{ json_encode($reg) }})" class="p-1.5 bg-surface-subtle hover:bg-hairline text-ink border border-hairline rounded-[6px] transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('master.regulasi.destroy', $reg->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus regulasi {{ addslashes($reg->nomor_regulasi) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-[6px] transition-colors cursor-pointer" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-ink-muted">Belum ada dokumen regulasi yang diunggah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline">
            {{ $regulasiList->links() }}
        </div>
    </div>

    <!-- Modal Upload Regulasi Baru -->
    <div id="modalUploadRegulasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-bold text-ink text-base">Unggah Dokumen Regulasi / Juknis</h3>
                    <p class="text-[11px] text-ink-secondary">Dokumen PDF akan diindeks sebagai dasar hukum AI dan materi unduhan.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalUploadRegulasi').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.regulasi.store') }}" enctype="multipart/form-data" class="space-y-3.5 mt-4">
                @csrf

                <div>
                    <label class="block font-semibold text-ink mb-1">Judul Peraturan / Regulasi <span class="text-rose-600">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: Standar Biaya Masukan (SBM) Pemerintah Kabupaten Trenggalek" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Nomor Regulasi <span class="text-rose-600">*</span></label>
                        <input type="text" name="nomor_regulasi" required placeholder="Contoh: Nomor 25 Tahun 2025" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Tahun Terbit <span class="text-rose-600">*</span></label>
                        <input type="number" name="tahun" required value="{{ date('Y') }}" min="2000" max="2035" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Jenis Regulasi <span class="text-rose-600">*</span></label>
                        <select name="jenis_regulasi" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            @foreach(\App\Models\RegulasiHukum::JENIS_REGULASI as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Kategori Pengawasan <span class="text-rose-600">*</span></label>
                        <select name="kategori" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
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
                    <label class="block font-semibold text-ink mb-1">Ringkasan Eksekutif / Poin-Poin Pasal Kunci</label>
                    <textarea name="ringkasan_eksekutif" rows="4" placeholder="Tuliskan pasal-pasal kunci, batas nominal, atau ketentuan penting yang sering dijadikan acuan audit (bagian ini akan dibaca secara cerdas oleh AI Assistant)..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Unggah Berkas PDF (Maks 25MB)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs p-1.5 file:mr-2 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Visibilitas Akses <span class="text-rose-600">*</span></label>
                        <select name="visibilitas" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            <option value="publik">Publik & OPD (Bisa Diunduh Bebas)</option>
                            <option value="opd">Hanya Pengguna OPD Terdaftar</option>
                            <option value="internal">Internal APIP Inspektorat Saja</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalUploadRegulasi').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan & Indeks Regulasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Regulasi -->
    <div id="modalEditRegulasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-bold text-ink text-base">Edit Dokumen Regulasi</h3>
                </div>
                <button type="button" onclick="document.getElementById('modalEditRegulasi').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditRegulasi" method="POST" action="" enctype="multipart/form-data" class="space-y-3.5 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-ink mb-1">Judul Peraturan <span class="text-rose-600">*</span></label>
                    <input type="text" id="editJudul" name="judul" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Nomor Regulasi <span class="text-rose-600">*</span></label>
                        <input type="text" id="editNomor" name="nomor_regulasi" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Tahun Terbit <span class="text-rose-600">*</span></label>
                        <input type="number" id="editTahun" name="tahun" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Jenis Regulasi <span class="text-rose-600">*</span></label>
                        <select id="editJenis" name="jenis_regulasi" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            @foreach(\App\Models\RegulasiHukum::JENIS_REGULASI as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Kategori Pengawasan <span class="text-rose-600">*</span></label>
                        <select id="editKategori" name="kategori" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
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
                    <label class="block font-semibold text-ink mb-1">Ringkasan Eksekutif / Poin-Poin Pasal Kunci</label>
                    <textarea id="editRingkasan" name="ringkasan_eksekutif" rows="4" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Ganti Berkas PDF (Opsional)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs p-1.5 file:mr-2 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-ink">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Visibilitas Akses <span class="text-rose-600">*</span></label>
                        <select id="editVisibilitas" name="visibilitas" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            <option value="publik">Publik & OPD (Bisa Diunduh Bebas)</option>
                            <option value="opd">Hanya Pengguna OPD Terdaftar</option>
                            <option value="internal">Internal APIP Inspektorat Saja</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditRegulasi').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan Perubahan</button>
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

            let jenisVal = reg.jenis_regulasi || 'perkada';
            if (jenisVal === 'perbup') jenisVal = 'perkada';
            else if (jenisVal === 'permendagri') jenisVal = 'permen_lembaga';
            else if (jenisVal === 'keputusan_inspektur' || jenisVal === 'juknis') jenisVal = 'sk_kepala_pd';

            document.getElementById('editJenis').value = jenisVal;
            document.getElementById('editKategori').value = reg.kategori || 'umum';
            document.getElementById('editRingkasan').value = reg.ringkasan_eksekutif || '';
            document.getElementById('editVisibilitas').value = reg.visibilitas || 'publik';
            document.getElementById('modalEditRegulasi').classList.remove('hidden');
        }
    </script>
</x-app-layout>
