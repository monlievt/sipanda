<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('master.users.index') }}" class="text-xs text-brand-ink hover:underline font-bold inline-flex items-center gap-1 mb-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Daftar Pegawai Internal
                </a>
                <h1 class="text-xl font-bold text-ink">Tambah Pegawai Internal APIP Baru</h1>
                <p class="text-xs text-ink-secondary mt-0.5">Daftarkan data auditor, PPUPD, pimpinan, atau pejabat pengawasan ke dalam database SINGAWASI</p>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-[10px] text-rose-800 dark:text-rose-300 text-xs space-y-1">
            <p class="font-bold flex items-center gap-1.5 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                Terdapat beberapa isian yang perlu diperbaiki:
            </p>
            <ul class="list-disc list-inside space-y-0.5 pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('master.users.store') }}" id="formCreateUser">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- KOLOM KIRI (FORM UTAMA) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- KARTU 1: IDENTITAS & KONTAK -->
                <div class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-hairline">
                        <span class="w-7 h-7 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center font-bold text-xs">1</span>
                        <div>
                            <h2 class="font-bold text-ink text-sm">Identitas & Kontak Pegawai</h2>
                            <p class="text-[11px] text-ink-secondary">Nama lengkap dengan gelar, NIP resmi, dan kontak aktif</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Nama Lengkap (dg Gelar) <span class="text-rose-600">*</span></label>
                            <input type="text" name="nama" id="inputNama" value="{{ old('nama') }}" required placeholder="Contoh: Ir. WIJIONO, S.T., M.MKes." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                            <p class="text-[10px] text-ink-muted mt-1">Gelar depan/belakang dicantumkan untuk format dokumen resmi.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Nama Panggilan / Sapaan</label>
                            <input type="text" name="nama_tanpa_gelar" id="inputNamaTanpaGelar" value="{{ old('nama_tanpa_gelar') }}" placeholder="Contoh: Pak Wijiono" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                            <p class="text-[10px] text-ink-muted mt-1">Digunakan untuk sapaan notifikasi & dasbor.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">NIP Pegawai (18 Digit) <span class="text-rose-600">*</span></label>
                            <input type="text" name="nip" id="inputNip" value="{{ old('nip') }}" required placeholder="197308051997031007" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs font-mono px-3.5 py-2.5">
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Email Dinas / Akun <span class="text-rose-600">*</span></label>
                            <input type="email" name="email" id="inputEmail" value="{{ old('email') }}" required placeholder="nama@trenggalekkab.go.id" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">No. WhatsApp / HP</label>
                            <input type="text" name="no_hp" id="inputNoHp" value="{{ old('no_hp') }}" placeholder="Nomor WhatsApp / HP aktif" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                        </div>
                    </div>
                </div>

                <!-- KARTU 2: STATUS KEPEGAWAIAN ASN (BAKU BKN) -->
                <div class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-hairline">
                        <span class="w-7 h-7 rounded-[6px] bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800 flex items-center justify-center font-bold text-xs">2</span>
                        <div>
                            <h2 class="font-bold text-ink text-sm">Status Kepegawaian & Jabatan Definitif ASN</h2>
                            <p class="text-[11px] text-ink-secondary">Pangkat, Golongan Ruang, dan Nomenklatur Jabatan Pegawai</p>
                        </div>
                    </div>

                    <!-- Pilihan Jabatan Baku & Kustom -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-xs text-ink">Pilih Jabatan Definitif Baku <span class="text-rose-600">*</span></label>
                            <label class="inline-flex items-center gap-1.5 text-xs text-ink-secondary cursor-pointer">
                                <input type="checkbox" id="checkCustomJabatan" class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                <span>Input Kustom / Manual</span>
                            </label>
                        </div>

                        <!-- Dropdown Pilihan Baku -->
                        <div id="containerSelectJabatan">
                            <select id="selectJabatanBaku" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5 font-semibold">
                                <option value="">-- Pilih Jabatan Baku di Lingkungan Inspektorat --</option>
                                @foreach($jabatanBaku as $kategori => $items)
                                    <optgroup label="{{ $kategori }}">
                                        @foreach($items as $jbt)
                                            <option value="{{ $jbt }}" {{ old('jabatan') === $jbt ? 'selected' : '' }}>{{ $jbt }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Input Text Jabatan (Aktual yang dikirim ke form) -->
                        <div id="containerTextJabatan" class="{{ old('jabatan') ? '' : 'hidden' }}">
                            <input type="text" name="jabatan" id="inputJabatan" value="{{ old('jabatan') }}" required placeholder="Ketik nama jabatan definitif pegawai..." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs font-bold text-ink px-3.5 py-2.5">
                        </div>
                    </div>

                    <!-- Golongan & Pangkat Berpasangan Baku -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Golongan Ruang ASN</label>
                            <select name="golongan" id="selectGolongan" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5 font-bold">
                                <option value="">-- Pilih Golongan Ruang --</option>
                                @foreach($pangkatGolongan as $gol => $pkt)
                                    <option value="{{ $gol }}" data-pangkat="{{ $pkt }}" {{ old('golongan') === $gol ? 'selected' : '' }}>
                                        Golongan {{ $gol }} | {{ $pkt }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-ink-muted mt-1">Memilih Golongan akan otomatis mengisi Pangkat di samping.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Pangkat ASN</label>
                            <input type="text" name="pangkat" id="inputPangkat" value="{{ old('pangkat') }}" placeholder="Contoh: Pembina Utama Muda" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
                        </div>
                    </div>
                </div>

                <!-- KARTU 3: PENUGASAN PIMPINAN & HAK AKSES SISTEM -->
                <div class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-hairline">
                        <span class="w-7 h-7 rounded-[6px] bg-surface-subtle text-ink border border-hairline-strong flex items-center justify-center font-bold text-xs">3</span>
                        <div>
                            <h2 class="font-bold text-ink text-sm">Penugasan Pimpinan & Hak Akses Sistem</h2>
                            <p class="text-[11px] text-ink-secondary">Penetapan role akses, unit penempatan, serta status Plt / Plh / Pj / Definitif</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Role / Peran di Sistem <span class="text-rose-600">*</span></label>
                            <select name="role" id="selectRole" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs font-bold text-ink px-3.5 py-2.5">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ (old('role') === $r->name || (!old('role') && $r->name === 'auditor')) ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Unit Penempatan / Irban</label>
                            <select name="irban_id" id="selectIrban" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
                                <option value="">-- Sekretariat / Umum --</option>
                                @foreach($irbans as $irban)
                                    <option value="{{ $irban->id }}" {{ old('irban_id') == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-ink mb-1.5">Status Penugasan Pimpinan <span class="text-rose-600">*</span></label>
                            <select name="status_jabatan" id="selectStatusJabatan" required class="w-full rounded-[6px] border border-hairline-strong bg-surface text-xs font-bold text-ink px-3.5 py-2.5">
                                <option value="definitif" {{ old('status_jabatan') === 'definitif' ? 'selected' : '' }}>Definitif (Pejabat Tetap)</option>
                                <option value="plt" {{ old('status_jabatan') === 'plt' ? 'selected' : '' }}>Plt. (Pelaksana Tugas)</option>
                                <option value="plh" {{ old('status_jabatan') === 'plh' ? 'selected' : '' }}>Plh. (Pelaksana Harian)</option>
                                <option value="pj" {{ old('status_jabatan') === 'pj' ? 'selected' : '' }}>Pj. (Penjabat)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- KARTU 4: KEAMANAN AKUN -->
                <div class="bg-surface rounded-[10px] p-6 sm:p-7 border border-hairline space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-hairline">
                        <span class="w-7 h-7 rounded-[6px] bg-surface-subtle text-ink border border-hairline-strong flex items-center justify-center font-bold text-xs">4</span>
                        <div>
                            <h2 class="font-bold text-ink text-sm">Keamanan & Password Awal</h2>
                            <p class="text-[11px] text-ink-secondary">Pengaturan kata sandi untuk login pegawai</p>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-ink mb-1.5">Password Awal Akun (Opsional)</label>
                        <input type="text" name="password" placeholder="Default: Sesuai NIP Pegawai" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3.5 py-2.5">
                        <p class="text-[10px] text-ink-muted mt-1">Jika dikosongkan, password awal otomatis diset sama dengan NIP pegawai.</p>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('master.users.index') }}" class="ledger-btn-secondary !text-xs">
                        Batal
                    </a>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">
                        Simpan Pegawai Baru
                    </button>
                </div>
            </div>

            <!-- KOLOM KANAN (STICKY LIVE PREVIEW & PANDUAN) -->
            <div class="space-y-6 lg:sticky lg:top-6">

                <!-- LIVE PREVIEW CARD -->
                <div class="bg-surface text-ink rounded-[10px] p-6 border border-hairline-strong space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-brand-ink">Live Preview Pegawai</span>
                        <span id="previewStatusBadge" class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800">Definitif</span>
                    </div>

                    <div class="space-y-1">
                        <h3 id="previewNama" class="text-base font-bold text-ink line-clamp-2">Nama Pegawai Lengkap</h3>
                        <p id="previewNip" class="text-xs font-mono text-ink-secondary">NIP. -</p>
                    </div>

                    <div class="pt-3 border-t border-hairline space-y-2 text-xs">
                        <div>
                            <span class="text-[10px] text-ink-muted block uppercase font-bold">Jabatan Definitif ASN:</span>
                            <p id="previewJabatanDefinitif" class="font-bold text-ink">Sekretaris</p>
                            <p id="previewPangkatGolongan" class="text-[11px] text-ink-secondary">-</p>
                        </div>

                        <div class="pt-2">
                            <span class="text-[10px] text-ink-muted block uppercase font-bold">Penugasan Resmi Dokumen / SPT:</span>
                            <div class="p-2.5 rounded-[6px] bg-brand-soft border border-brand-border text-brand-ink font-bold text-xs mt-1" id="previewPenugasanSistem">
                                Pejabat Definitif (Auditor)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANDUAN PENUGASAN PLT / DEFINITIF -->
                <div class="bg-blue-50 dark:bg-blue-950/30 rounded-[10px] p-5 border border-blue-200 dark:border-blue-900 space-y-3 text-xs text-blue-900 dark:text-blue-200">
                    <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide text-blue-900 dark:text-blue-200">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Contoh Logika Penugasan Pimpinan</span>
                    </div>

                    <div class="space-y-2.5 text-[11px] leading-relaxed">
                        <div class="p-3 bg-surface rounded-[6px] border border-blue-200 dark:border-blue-800">
                            <p class="font-bold text-ink">Kasus 1: Sekretaris menjabat Plt. Inspektur</p>
                            <p class="text-ink-secondary mt-0.5">
                                &bull; Jabatan Definitif = <code class="font-mono text-ink">Sekretaris</code><br>
                                &bull; Role = <code class="font-mono text-ink">Inspektur</code><br>
                                &bull; Status = <code class="font-mono text-ink">Plt.</code><br>
                                Hasil cetak SPT: <strong class="text-ink">Plt. INSPEKTUR KABUPATEN TRENGGALEK</strong>.
                            </p>
                        </div>

                        <div class="p-3 bg-surface rounded-[6px] border border-blue-200 dark:border-blue-800">
                            <p class="font-bold text-ink">Kasus 2: Auditor Madya menjabat Plt. Irban</p>
                            <p class="text-ink-secondary mt-0.5">
                                &bull; Jabatan Definitif = <code class="font-mono text-ink">Auditor Ahli Madya</code><br>
                                &bull; Role = <code class="font-mono text-ink">Irban</code>, Unit = <code class="font-mono text-ink">Irban Khusus</code><br>
                                &bull; Status = <code class="font-mono text-ink">Plt.</code><br>
                                Terbaca di sistem: <strong class="text-ink">Plt. Inspektur Pembantu Khusus</strong>.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputNama = document.getElementById('inputNama');
            const inputNip = document.getElementById('inputNip');
            const selectJabatanBaku = document.getElementById('selectJabatanBaku');
            const inputJabatan = document.getElementById('inputJabatan');
            const checkCustomJabatan = document.getElementById('checkCustomJabatan');
            const containerSelectJabatan = document.getElementById('containerSelectJabatan');
            const containerTextJabatan = document.getElementById('containerTextJabatan');
            const selectGolongan = document.getElementById('selectGolongan');
            const inputPangkat = document.getElementById('inputPangkat');
            const selectRole = document.getElementById('selectRole');
            const selectIrban = document.getElementById('selectIrban');
            const selectStatusJabatan = document.getElementById('selectStatusJabatan');

            // Live Preview Elements
            const previewNama = document.getElementById('previewNama');
            const previewNip = document.getElementById('previewNip');
            const previewJabatanDefinitif = document.getElementById('previewJabatanDefinitif');
            const previewPangkatGolongan = document.getElementById('previewPangkatGolongan');
            const previewPenugasanSistem = document.getElementById('previewPenugasanSistem');
            const previewStatusBadge = document.getElementById('previewStatusBadge');

            // Initial Setup for Jabatan Selection
            if (inputJabatan.value && selectJabatanBaku.value !== inputJabatan.value) {
                // If there's an existing custom value
                let matched = false;
                for (let i = 0; i < selectJabatanBaku.options.length; i++) {
                    if (selectJabatanBaku.options[i].value === inputJabatan.value) {
                        selectJabatanBaku.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched && inputJabatan.value.trim() !== '') {
                    checkCustomJabatan.checked = true;
                    containerSelectJabatan.classList.add('hidden');
                    containerTextJabatan.classList.remove('hidden');
                }
            } else if (selectJabatanBaku.value) {
                inputJabatan.value = selectJabatanBaku.value;
            }

            selectJabatanBaku.addEventListener('change', function() {
                inputJabatan.value = this.value;
                updatePreview();
            });

            checkCustomJabatan.addEventListener('change', function() {
                if (this.checked) {
                    containerSelectJabatan.classList.add('hidden');
                    containerTextJabatan.classList.remove('hidden');
                    inputJabatan.focus();
                } else {
                    containerSelectJabatan.classList.remove('hidden');
                    containerTextJabatan.classList.add('hidden');
                    if (selectJabatanBaku.value) {
                        inputJabatan.value = selectJabatanBaku.value;
                    }
                }
                updatePreview();
            });

            // Auto-sync Golongan to Pangkat
            selectGolongan.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const autoPangkat = selectedOpt.getAttribute('data-pangkat');
                if (autoPangkat) {
                    inputPangkat.value = autoPangkat;
                }
                updatePreview();
            });

            inputPangkat.addEventListener('input', updatePreview);
            inputNama.addEventListener('input', updatePreview);
            inputNip.addEventListener('input', updatePreview);
            inputJabatan.addEventListener('input', updatePreview);
            selectRole.addEventListener('change', updatePreview);
            selectIrban.addEventListener('change', updatePreview);
            selectStatusJabatan.addEventListener('change', updatePreview);

            function updatePreview() {
                // 1. Nama & NIP
                previewNama.innerText = inputNama.value.trim() || 'Nama Pegawai Lengkap';
                previewNip.innerText = inputNip.value.trim() ? 'NIP. ' + inputNip.value.trim() : 'NIP. -';

                // 2. Jabatan Definitif
                previewJabatanDefinitif.innerText = inputJabatan.value.trim() || 'Belum dipilih';

                // 3. Pangkat & Golongan
                const gol = selectGolongan.value;
                const pkt = inputPangkat.value.trim();
                if (pkt && gol) {
                    previewPangkatGolongan.innerText = pkt + ' (' + gol + ')';
                } else if (pkt) {
                    previewPangkatGolongan.innerText = pkt;
                } else if (gol) {
                    previewPangkatGolongan.innerText = 'Golongan ' + gol;
                } else {
                    previewPangkatGolongan.innerText = '-';
                }

                // 4. Status Badge & Penugasan Sistem
                const status = selectStatusJabatan.value;
                const role = selectRole.value;
                const irbanText = selectIrban.options[selectIrban.selectedIndex]?.text?.replace('--', '')?.trim() || 'Sekretariat';

                let prefix = '';
                if (status === 'plt') {
                    prefix = 'Plt. ';
                    previewStatusBadge.innerText = 'Plt.';
                    previewStatusBadge.className = 'px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';
                } else if (status === 'plh') {
                    prefix = 'Plh. ';
                    previewStatusBadge.innerText = 'Plh.';
                    previewStatusBadge.className = 'px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800';
                } else if (status === 'pj') {
                    prefix = 'Pj. ';
                    previewStatusBadge.innerText = 'Pj.';
                    previewStatusBadge.className = 'px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-surface-subtle text-ink border border-hairline-strong';
                } else {
                    previewStatusBadge.innerText = 'Definitif';
                    previewStatusBadge.className = 'px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
                }

                if (role === 'inspektur') {
                    previewPenugasanSistem.innerText = prefix ? `${prefix}Inspektur Daerah Kabupaten Trenggalek` : 'Inspektur Daerah (Definitif)';
                } else if (role === 'irban') {
                    previewPenugasanSistem.innerText = prefix ? `${prefix}${irbanText}` : `${irbanText} (Definitif)`;
                } else if (role === 'sekretaris') {
                    previewPenugasanSistem.innerText = prefix ? `${prefix}Sekretaris Inspektorat` : 'Sekretaris Inspektorat (Definitif)';
                } else {
                    previewPenugasanSistem.innerText = `${prefix ? prefix : 'Pejabat Definitif ('}${role.toUpperCase()}${prefix ? '' : ')'}`;
                }
            }

            updatePreview();
        });
    </script>
</x-app-layout>
