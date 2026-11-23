@php
    $parentStOptions = $parentStList->map(function($pst) {
        return [
            'id' => (string) $pst->id,
            'label' => 'No. SPT: ' . $pst->no_spt . ' | ' . \Illuminate\Support\Str::limit($pst->uraian_penugasan, 50),
            'no_spt' => $pst->no_spt,
            'uraian' => $pst->uraian_penugasan,
            'jenis' => $pst->jenisPenugasan?->nama ?? '-',
            'sumber' => $pst->sumberPenugasan?->nama ?? '-',
            'irban' => $pst->irbans->pluck('nama_irban')->join(', ') ?: ($pst->irban?->nama_irban ?? '-'),
            'objek' => $pst->objekPenugasan->pluck('nama')->join(', ') ?: '-',
            'tim' => $pst->tim->map(fn($t) => ($t->user?->nama_display ?? $t->user?->nama ?? '-') . ' (' . str_replace('_', ' ', ucfirst($t->peran)) . ')')->join(', '),
        ];
    });
@endphp

<x-app-layout>
    <x-slot name="header">
        Edit Penugasan: {{ $penugasan->no_spt }}
    </x-slot>

    <div class="max-w-4xl mx-auto"
        x-data="{
            jenisSt: '{{ old('jenis_st', $penugasan->jenis_st ?? ($penugasan->penugasan_induk_id ? 'perpanjangan' : 'reguler')) }}',
            isPkppt: '{{ old('is_sesuai_pkppt', $penugasan->is_sesuai_pkppt ? '1' : '0') }}',
            selectedParentId: '{{ old('penugasan_induk_id', $penugasan->penugasan_induk_id) }}',
            parentOptions: {{ Js::from($parentStOptions) }},
            get selectedParent() {
                return this.parentOptions.find(p => p.id == this.selectedParentId) || null;
            }
        }">

        <form method="POST" action="{{ route('penugasan.update', $penugasan->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Banner Mode ST Reguler vs ST Perpanjangan vs ST Bantuan -->
            <div class="bg-surface rounded-[10px] p-5 border border-hairline">
                <label class="block font-semibold text-ink text-sm mb-2">Status Klasifikasi Penugasan</label>
                <input type="hidden" name="jenis_st" :value="jenisSt">
                <input type="hidden" name="is_perpanjangan" :value="jenisSt === 'perpanjangan' ? '1' : '0'">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-start gap-3 p-3.5 rounded-[6px] border cursor-pointer transition-colors"
                        :class="jenisSt === 'reguler' ? 'border-brand-border bg-brand-soft text-brand-ink font-semibold' : 'border-hairline hover:bg-surface-subtle text-ink-secondary'">
                        <input type="radio" name="jenis_st_radio" value="reguler" x-model="jenisSt" class="mt-0.5 text-brand focus:ring-brand">
                        <div>
                            <p class="text-xs font-semibold text-ink">Surat Tugas Standar (Reguler)</p>
                            <p class="text-[11px] font-normal text-ink-secondary mt-0.5">Penugasan mandiri dengan susunan objek dan tim kustom.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-[6px] border cursor-pointer transition-colors"
                        :class="jenisSt === 'perpanjangan' ? 'border-blue-300 dark:border-blue-700 bg-blue-50/70 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 font-semibold' : 'border-hairline hover:bg-surface-subtle text-ink-secondary'">
                        <input type="radio" name="jenis_st_radio" value="perpanjangan" x-model="jenisSt" class="mt-0.5 text-blue-700 focus:ring-blue-600">
                        <div>
                            <p class="text-xs font-semibold text-ink">Surat Tugas Perpanjangan</p>
                            <p class="text-[11px] font-normal text-ink-secondary mt-0.5">Mewarisi data Objek, Tim, PKPPT & Irban otomatis dari ST Induk.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-[6px] border cursor-pointer transition-colors"
                        :class="jenisSt === 'bantuan' ? 'border-cyan-300 dark:border-cyan-700 bg-cyan-50/70 dark:bg-cyan-950/40 text-cyan-900 dark:text-cyan-200 font-semibold' : 'border-hairline hover:bg-surface-subtle text-ink-secondary'">
                        <input type="radio" name="jenis_st_radio" value="bantuan" x-model="jenisSt" class="mt-0.5 text-cyan-700 focus:ring-cyan-600">
                        <div>
                            <p class="text-xs font-semibold text-ink">Surat Tugas Bantuan</p>
                            <p class="text-[11px] font-normal text-ink-secondary mt-0.5">Menginduk ke ST Utama. Hanya berisi personil Anggota Tim.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Card 1: Informasi Umum SPT -->
            <div class="bg-surface rounded-[10px] p-6 border border-hairline space-y-4 text-xs">
                <h3 class="font-bold text-ink text-base flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-brand rounded-full" aria-hidden="true"></span>
                    1. Informasi Penugasan & Nomor SPT
                </h3>

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-[6px] text-red-800 dark:text-red-300 text-xs space-y-1">
                        <p class="font-semibold mb-1">Mohon lengkapi atau perbaiki isian berikut:</p>
                        @foreach ($errors->all() as $error)
                            <p>- {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Pilihan ST Induk (Jika ST Perpanjangan atau ST Bantuan) -->
                <div x-show="jenisSt === 'perpanjangan' || jenisSt === 'bantuan'" x-transition class="p-4 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-[10px] space-y-3">
                    <label class="block font-semibold text-blue-800 dark:text-blue-300 text-xs">
                        <span x-text="jenisSt === 'bantuan' ? 'Pilih Surat Tugas Utama (ST Induk yang Dibantu)' : 'Pilih Surat Tugas Induk (ST yang Diperpanjang)'"></span>
                        <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <select name="penugasan_induk_id" x-model="selectedParentId" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Pilih ST Induk / Utama --</option>
                        @foreach($parentStList as $parentSt)
                            <option value="{{ $parentSt->id }}" {{ old('penugasan_induk_id', $penugasan->penugasan_induk_id) == $parentSt->id ? 'selected' : '' }}>
                                {{ $parentSt->no_spt }} | {{ Str::limit($parentSt->uraian_penugasan, 60) }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Panel Info Otomatis Pewarisan Data ST Induk -->
                    <template x-if="selectedParent">
                        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-[6px] space-y-2.5">
                            <div class="font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 text-xs">
                                <span>Data yang Otomatis Terhubung dari ST Induk:</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-ink-secondary">
                                <div><strong>Objek Sasaran:</strong> <span class="font-semibold text-ink" x-text="selectedParent.objek"></span></div>
                                <div><strong>Irban:</strong> <span class="font-semibold text-ink" x-text="selectedParent.irban"></span></div>
                                <div><strong>Jenis Pengawasan:</strong> <span class="font-semibold text-ink" x-text="selectedParent.jenis"></span></div>
                                <div><strong>Sumber:</strong> <span class="font-semibold text-ink" x-text="selectedParent.sumber"></span></div>
                                <div class="sm:col-span-2" x-show="jenisSt === 'perpanjangan'"><strong>Susunan Tim Induk:</strong> <span class="font-semibold text-ink" x-text="selectedParent.tim"></span></div>
                            </div>
                            <p class="text-[11px] text-emerald-800 dark:text-emerald-300 font-medium">Catatan: Data Objek dan Pengawasan otomatis disinkronkan dari ST Induk.</p>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink mb-1">Nomor SPT <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" name="no_spt" value="{{ old('no_spt', $penugasan->no_spt) }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1">Tanggal Naskah SPT <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="date" name="tanggal_st" value="{{ old('tanggal_st', $penugasan->tanggal_st?->format('Y-m-d') ?? $penugasan->created_at?->format('Y-m-d')) }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                        <p class="text-[11px] text-ink-muted mt-1">Dapat diisi tanggal mundur jika diperlukan.</p>
                    </div>
                </div>

                <!-- Multi-Irban Penanggung Jawab (Hanya jika ST Standar / Reguler) -->
                <div x-show="jenisSt === 'reguler'" x-transition>
                    <label class="block font-semibold text-ink mb-1">Irban Penanggung Jawab <span class="text-red-700 dark:text-red-400">*</span> (Dapat memilih lebih dari 1)</label>
                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-1 max-h-36 overflow-y-auto">
                        @foreach($irbans as $irban)
                            <label class="flex items-center gap-2 text-xs font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                <input type="checkbox" name="irban_ids[]" value="{{ $irban->id }}"
                                    {{ (is_array(old('irban_ids')) && in_array($irban->id, old('irban_ids'))) || in_array($irban->id, $selectedIrbanIds) ? 'checked' : '' }}
                                    class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                <span>{{ $irban->nama_irban }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Uraian Penugasan <span class="text-red-700 dark:text-red-400">*</span></label>
                    <textarea name="uraian_penugasan" rows="3" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">{{ old('uraian_penugasan', $penugasan->uraian_penugasan) }}</textarea>
                </div>

                <!-- Komponen Interaktif Dasar Penugasan (Bank Regulasi & Disposisi) -->
                <div x-data="{
                    openRegulasiModal: false,
                    openSuratModal: false,
                    searchRegulasi: '',
                    suratNomor: '',
                    suratTanggal: '',
                    suratPerihal: '',
                    allRegulasi: {{ Js::from($allRegulasi) }},
                    dasarItems: [
                        @foreach($regulasiBaku as $rb)
                            '{{ addslashes($rb->format_dasar_spt) }}',
                        @endforeach
                    ],
                    dasarText: `{{ old('dasar_penugasan', $penugasan->dasar_penugasan) }}`,
                    
                    syncToTextarea() {
                        let res = '';
                        this.dasarItems.forEach((item, idx) => {
                            let clean = item.replace(/^\d+\.\s*/, '').trim();
                            res += (idx + 1) + '. ' + clean + '\n';
                        });
                        this.dasarText = res.trim();
                    },

                    getHierarkiScore(text) {
                        let t = (text || '').toLowerCase();
                        if (t.includes('undang-undang') || t.includes('perppu')) return 10;
                        if (t.includes('peraturan pemerintah')) return 20;
                        if (t.includes('peraturan presiden') || t.includes('perpres')) return 30;
                        if (t.includes('peraturan menteri') || t.includes('permendagri') || t.includes('permen')) return 40;
                        if (t.includes('keputusan menteri') || t.includes('kepmen')) return 50;
                        if (t.includes('peraturan daerah') || t.includes('perda')) return 60;
                        if (t.includes('peraturan bupati') || t.includes('peraturan kepala daerah') || t.includes('perbup') || t.includes('perkada')) return 70;
                        if (t.includes('keputusan bupati') || t.includes('keputusan kepala daerah') || t.includes('surat keputusan')) return 80;
                        if (t.includes('surat edaran')) return 90;
                        if (t.includes('keputusan kepala perangkat daerah') || t.includes('keputusan inspektur')) return 100;
                        if (t.includes('surat perintah') || t.includes('disposisi') || t.includes('nota dinas')) return 110;
                        return 120;
                    },

                    urutkanHierarki() {
                        this.dasarItems.sort((a, b) => this.getHierarkiScore(a) - this.getHierarkiScore(b));
                        this.syncToTextarea();
                    },

                    tambahRegulasi(reg) {
                        let nomor = (reg.nomor_regulasi || '').trim();
                        let formatNomor = (/^(nomor|no\.)/i.test(nomor)) ? nomor : 'Nomor ' + nomor;
                        let prefix = '';

                        switch (reg.jenis_regulasi) {
                            case 'uu_perppu':
                                prefix = 'Undang-Undang ' + formatNomor;
                                break;
                            case 'pp':
                                prefix = 'Peraturan Pemerintah ' + formatNomor;
                                break;
                            case 'perpres':
                                prefix = 'Peraturan Presiden ' + formatNomor;
                                break;
                            case 'permen_lembaga':
                                prefix = 'Peraturan Menteri/Lembaga ' + formatNomor;
                                break;
                            case 'sk_menteri_lembaga':
                                prefix = 'Keputusan Menteri/Lembaga ' + formatNomor;
                                break;
                            case 'perda':
                                prefix = 'Peraturan Daerah Kabupaten Trenggalek ' + formatNomor;
                                break;
                            case 'perkada':
                            case 'perbup':
                                prefix = 'Peraturan Bupati Trenggalek ' + formatNomor;
                                break;
                            case 'sk_kepala_daerah':
                                prefix = 'Keputusan Bupati Trenggalek ' + formatNomor;
                                break;
                            case 'surat_edaran':
                                prefix = 'Surat Edaran ' + formatNomor;
                                break;
                            case 'sk_kepala_pd':
                            case 'keputusan_inspektur':
                            case 'juknis':
                                prefix = 'Keputusan Kepala Perangkat Daerah / Inspektur ' + formatNomor;
                                break;
                            default:
                                prefix = (reg.jenis_regulasi || 'Peraturan').toUpperCase() + ' ' + formatNomor;
                        }

                        let itemText = prefix.replace(/\s+/g, ' ').trim() + ' tentang ' + (reg.judul || '').trim() + ';';
                        if (!this.dasarItems.includes(itemText)) {
                            this.dasarItems.push(itemText);
                            this.urutkanHierarki();
                        }
                        this.openRegulasiModal = false;
                        this.searchRegulasi = '';
                    },

                    tambahSuratDisposisi() {
                        if (!this.suratNomor.trim()) return;
                        let itemText = 'Surat Perintah / Disposisi Nomor ' + this.suratNomor.trim() + 
                                       (this.suratTanggal ? ' tanggal ' + this.suratTanggal : '') + 
                                       (this.suratPerihal ? ' perihal ' + this.suratPerihal.trim() : '') + ';';
                        this.dasarItems.push(itemText);
                        this.urutkanHierarki();
                        this.suratNomor = '';
                        this.suratTanggal = '';
                        this.suratPerihal = '';
                        this.openSuratModal = false;
                    },

                    resetDasarBaku() {
                        if (confirm('Kembalikan dasar penugasan ke regulasi standar baku Pemkab Trenggalek?')) {
                            this.dasarItems = [
                                @foreach($regulasiBaku as $rb)
                                    '{{ addslashes($rb->format_dasar_spt) }}',
                                @endforeach
                            ];
                            this.urutkanHierarki();
                        }
                    },

                    get filteredRegulasi() {
                        if (!this.searchRegulasi.trim()) return this.allRegulasi.slice(0, 10);
                        let s = this.searchRegulasi.toLowerCase();
                        return this.allRegulasi.filter(r => 
                            r.judul.toLowerCase().includes(s) || 
                            r.nomor_regulasi.toLowerCase().includes(s)
                        ).slice(0, 15);
                    }
                }" class="p-4 bg-surface-subtle rounded-[10px] border border-hairline space-y-3">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-hairline pb-2">
                        <div>
                            <label class="block font-semibold text-ink text-xs">Dasar Surat Perintah Tugas (SPT)</label>
                            <p class="text-[11px] text-ink-muted">Rujukan regulasi, dasar hukum pengawasan, atau surat disposisi kepala daerah</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="openRegulasiModal = true" class="px-2.5 py-1 bg-surface hover:bg-brand-soft text-emerald-800 dark:text-emerald-300 font-semibold text-[11px] rounded-[6px] border border-emerald-200 dark:border-emerald-800 transition-colors">
                                + Bank Regulasi
                            </button>
                            <button type="button" @click="openSuratModal = true" class="px-2.5 py-1 bg-surface hover:bg-blue-50 dark:hover:bg-blue-950/40 text-blue-800 dark:text-blue-300 font-semibold text-[11px] rounded-[6px] border border-blue-200 dark:border-blue-800 transition-colors">
                                + Surat / Disposisi
                            </button>
                            <button type="button" @click="urutkanHierarki()" class="px-2.5 py-1 bg-surface hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-semibold text-[11px] rounded-[6px] border border-amber-200 dark:border-amber-800 transition-colors" title="Urutkan dari Perda, Perbup, SK Bupati, hingga Disposisi">
                                Urutkan Hierarki
                            </button>
                            <button type="button" @click="resetDasarBaku()" class="px-2 py-1 text-ink-secondary hover:text-ink text-[11px] font-semibold underline transition-colors" title="Reset ke Regulasi Baku">
                                Reset Baku
                            </button>
                        </div>
                    </div>

                    <textarea name="dasar_penugasan" x-model="dasarText" rows="4" placeholder="1. Peraturan Daerah...&#10;2. Peraturan Bupati...&#10;3. PKPT Inspektorat Daerah..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs leading-relaxed font-sans"></textarea>
                    <p class="text-[11px] text-ink-muted">Teks di atas otomatis tersusun sesuai hierarki perundang-undangan (Perda &gt; Perbup &gt; SK Kepala Daerah &gt; Disposisi) dan dapat Anda sunting secara langsung.</p>

                    <!-- Modal Picker: Tambah dari Bank Regulasi -->
                    <div x-show="openRegulasiModal" style="display: none;" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
                        <div @click.outside="openRegulasiModal = false" class="bg-surface rounded-[10px] max-w-lg w-full p-4 border border-hairline shadow-lg space-y-3">
                            <div class="flex items-center justify-between border-b border-hairline pb-2">
                                <h4 class="font-bold text-xs text-ink flex items-center gap-1.5">
                                    <span>Pilih Regulasi dari Bank Data APIP</span>
                                </h4>
                                <button type="button" @click="openRegulasiModal = false" class="text-ink-muted hover:text-ink text-base font-bold leading-none" aria-label="Tutup">&times;</button>
                            </div>

                            <input type="text" x-model="searchRegulasi" placeholder="Ketik nomor atau judul peraturan (mis. 45, PKPT, 10)..." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">

                            <div class="max-h-60 overflow-y-auto divide-y divide-hairline text-xs">
                                <template x-for="r in filteredRegulasi" :key="r.id">
                                    <button type="button" @click="tambahRegulasi(r)" class="w-full text-left p-2.5 hover:bg-brand-soft rounded-[6px] transition-colors space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 bg-surface-subtle border border-hairline text-ink-secondary font-semibold uppercase text-[10px] rounded-[4px]" x-text="r.jenis_regulasi"></span>
                                            <span class="font-mono font-semibold text-brand-ink text-[11px]" x-text="r.nomor_regulasi"></span>
                                        </div>
                                        <p class="font-semibold text-ink line-clamp-2 text-[11px]" x-text="r.judul"></p>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Picker: Tambah Surat Masuk / Disposisi -->
                    <div x-show="openSuratModal" style="display: none;" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
                        <div @click.outside="openSuratModal = false" class="bg-surface rounded-[10px] max-w-md w-full p-4 border border-hairline shadow-lg space-y-3">
                            <div class="flex items-center justify-between border-b border-hairline pb-2">
                                <h4 class="font-bold text-xs text-ink flex items-center gap-1.5">
                                    <span>Tambah Dasar Berupa Surat Masuk / Disposisi</span>
                                </h4>
                                <button type="button" @click="openSuratModal = false" class="text-ink-muted hover:text-ink text-base font-bold leading-none" aria-label="Tutup">&times;</button>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                <div>
                                    <label class="block font-semibold mb-1 text-ink-secondary text-[11px]">Nomor Surat / Disposisi <span class="text-red-700 dark:text-red-400">*</span></label>
                                    <input type="text" x-model="suratNomor" placeholder="mis. 700/125/406.008/2026" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                                </div>
                                <div>
                                    <label class="block font-semibold mb-1 text-ink-secondary text-[11px]">Tanggal Surat</label>
                                    <input type="date" x-model="suratTanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                                </div>
                                <div>
                                    <label class="block font-semibold mb-1 text-ink-secondary text-[11px]">Perihal / Asal Surat</label>
                                    <input type="text" x-model="suratPerihal" placeholder="mis. Disposisi Bupati Trenggalek perihal Audit Khusus..." class="w-full rounded-[6px] border border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
                                </div>
                                <button type="button" @click="tambahSuratDisposisi()" class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-[6px] text-xs transition-colors">
                                    + Sisipkan ke Dasar SPT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div x-show="isPerpanjangan == '0'" x-transition>
                        <label class="block font-semibold text-ink mb-1">Jenis Penugasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select name="jenis_penugasan_id" :required="jenisSt === 'reguler'" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_penugasan_id', $penugasan->jenis_penugasan_id) == $j->id ? 'selected' : '' }}>
                                    [{{ ucfirst($j->kategori) }}] {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="jenisSt === 'reguler'" x-transition>
                        <label class="block font-semibold text-ink mb-1">Sumber Penugasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select name="sumber_penugasan_id" :required="jenisSt === 'reguler'" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            @foreach($sumberList as $s)
                                <option value="{{ $s->id }}" {{ old('sumber_penugasan_id', $penugasan->sumber_penugasan_id) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div :class="jenisSt !== 'reguler' ? 'sm:col-span-2' : ''">
                        <label class="block font-semibold text-ink mb-1">Status Penugasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select name="status" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            <option value="belum_berjalan" {{ old('status', $penugasan->status) == 'belum_berjalan' ? 'selected' : '' }}>Belum Berjalan</option>
                            <option value="berjalan" {{ old('status', $penugasan->status) == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ old('status', $penugasan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <!-- Kelompok / Kluster Pengawasan (Bahan ILHP) -->
                <div x-show="jenisSt === 'reguler'" x-transition>
                    <label class="block font-semibold text-ink mb-1">Kelompok / Kluster Pengawasan (Bahan ILHP)</label>
                    <select name="kelompok_pengawasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Pilih Kelompok Pengawasan (Opsional) --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}" {{ old('kelompok_pengawasan_id', $penugasan->kelompok_pengawasan_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelompok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Tanggal Mulai <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y-m-d') : '') }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Tanggal Selesai <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->format('Y-m-d') : '') }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>
            </div>

            <!-- Card 2: Objek Penugasan & PKPPT Match (Hanya jika ST Standar / Reguler) -->
            <div x-show="jenisSt === 'reguler'" x-transition class="bg-surface rounded-[10px] p-6 border border-hairline text-xs">
                
                <h3 class="font-bold text-ink text-base mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-brand rounded-full" aria-hidden="true"></span>
                    2. Objek Penugasan & Integrasi PKPPT
                </h3>

                <div class="mb-4">
                    <label class="block font-semibold text-ink mb-1">Objek Penugasan Target <span class="text-red-700 dark:text-red-400">*</span></label>
                    <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline max-h-48 overflow-y-auto space-y-1">
                        @foreach($objekList as $obj)
                            <label class="flex items-center gap-2 font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                <input type="checkbox" name="objek_ids[]" value="{{ $obj->id }}"
                                    {{ (is_array(old('objek_ids')) && in_array($obj->id, old('objek_ids'))) || in_array($obj->id, $selectedObjekIds) ? 'checked' : '' }}
                                    class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                <span>{{ $obj->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- PKPPT Selection (Hanya jika ST Standar / Reguler) -->
                <div>
                    <label class="block font-semibold text-ink mb-2">Kategori Perencanaan PKPPT</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer text-ink">
                            <input type="radio" name="is_sesuai_pkppt" value="1" x-model="isPkppt" class="text-brand focus:ring-brand">
                            <span class="ms-2 font-medium">Sesuai PKPPT (Terencana)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer text-ink">
                            <input type="radio" name="is_sesuai_pkppt" value="0" x-model="isPkppt" class="text-brand focus:ring-brand">
                            <span class="ms-2 font-medium">Non-PKPPT (Insidental)</span>
                        </label>
                    </div>

                    <div x-show="isPkppt == '1'" class="mt-3">
                        <label class="block font-semibold text-ink mb-1">Pilih Baris Kegiatan PKPPT Terkait</label>
                        <select name="pkppt_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            <option value="">-- Pilih Kegiatan PKPPT --</option>
                            @foreach($pkpptList as $pk)
                                <option value="{{ $pk->id }}" {{ old('pkppt_id', $penugasan->pkppt_id) == $pk->id ? 'selected' : '' }}>
                                    {{ $pk->area_pengawasan }} ({{ $pk->irban?->nama_irban ?? 'Semua' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 3: Susunan Personil Tim Pengawasan (Tampil jika ST Reguler atau ST Bantuan) -->
            <div x-show="jenisSt === 'reguler' || jenisSt === 'bantuan'" x-transition class="bg-surface rounded-[10px] p-6 border border-hairline text-xs space-y-4">
                <h3 class="font-bold text-ink text-base flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-brand rounded-full" aria-hidden="true"></span>
                    <span x-text="jenisSt === 'bantuan' ? '3. Susunan Personil Tim Bantuan' : '3. Susunan Personil Tim Pengawasan'"></span>
                </h3>

                <!-- Info Khusus ST Bantuan -->
                <div x-show="jenisSt === 'bantuan'" class="p-3.5 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 rounded-[6px] text-cyan-900 dark:text-cyan-200">
                    <p class="font-semibold flex items-center gap-1.5 text-xs">
                        <span>Ketentuan Format Tim ST Bantuan:</span>
                    </p>
                    <p class="text-[11px] mt-1 leading-relaxed">
                        Pada Surat Tugas Bantuan, struktur Penanggung Jawab, Wakil PJ, Pengendali Teknis, dan Ketua Tim <strong>menginduk pada Surat Tugas Utama</strong>. Anda hanya perlu menentukan <strong>Anggota Tim Bantuan</strong> di bawah ini.
                    </p>
                </div>

                <!-- Bagian Wakil PJ, Dalnis, Ketua Tim (Hanya untuk ST Reguler) -->
                <div x-show="jenisSt === 'reguler'" x-transition class="space-y-4">
                    <!-- Wakil PJ -->
                    <div>
                        <label class="block font-semibold text-ink mb-1">Wakil Penanggung Jawab <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" onkeyup="filterPersonil(this, 'containerWakilPjEdit')" placeholder="Cari nama / NIP Wakil Penanggung Jawab..." class="w-full mb-1.5 rounded-[6px] border-hairline-strong bg-surface text-ink text-[11px] px-3 py-1.5 font-medium">
                        <div id="containerWakilPjEdit" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline max-h-36 overflow-y-auto space-y-1">
                            @foreach($usersList as $u)
                                <label class="item-personil flex items-center gap-2 font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                    <input type="checkbox" name="tim_wakil_pj[]" value="{{ $u->id }}"
                                        {{ (is_array(old('tim_wakil_pj')) && in_array($u->id, old('tim_wakil_pj'))) || in_array($u->id, $selectedTim['tim_wakil_pj']) ? 'checked' : '' }}
                                        class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                    <span>{{ $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Daltek -->
                    <div>
                        <label class="block font-semibold text-ink mb-1">Pengendali Teknis <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" onkeyup="filterPersonil(this, 'containerDaltekEdit')" placeholder="Cari nama / NIP Pengendali Teknis (Dalnis)..." class="w-full mb-1.5 rounded-[6px] border-hairline-strong bg-surface text-ink text-[11px] px-3 py-1.5 font-medium">
                        <div id="containerDaltekEdit" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline max-h-36 overflow-y-auto space-y-1">
                            @foreach($usersList as $u)
                                <label class="item-personil flex items-center gap-2 font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                    <input type="checkbox" name="tim_daltek[]" value="{{ $u->id }}"
                                        {{ (is_array(old('tim_daltek')) && in_array($u->id, old('tim_daltek'))) || in_array($u->id, $selectedTim['tim_daltek']) ? 'checked' : '' }}
                                        class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                    <span>{{ $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Ketua Tim -->
                    <div>
                        <label class="block font-semibold text-ink mb-1">Ketua Tim <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" onkeyup="filterPersonil(this, 'containerKetuaEdit')" placeholder="Cari nama / NIP Ketua Tim..." class="w-full mb-1.5 rounded-[6px] border-hairline-strong bg-surface text-ink text-[11px] px-3 py-1.5 font-medium">
                        <div id="containerKetuaEdit" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline max-h-36 overflow-y-auto space-y-1">
                            @foreach($usersList as $u)
                                <label class="item-personil flex items-center gap-2 font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                    <input type="checkbox" name="tim_ketua[]" value="{{ $u->id }}"
                                        {{ (is_array(old('tim_ketua')) && in_array($u->id, old('tim_ketua'))) || in_array($u->id, $selectedTim['tim_ketua']) ? 'checked' : '' }}
                                        class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                    <span>{{ $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Anggota Tim -->
                <div>
                    <label class="block font-semibold text-ink mb-1">
                        <span x-text="jenisSt === 'bantuan' ? 'Anggota Tim Bantuan' : 'Anggota Tim'"></span>
                        <span class="text-red-700 dark:text-red-400">*</span>
                    </label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerAnggotaEdit')" placeholder="Cari nama / NIP Anggota Tim..." class="w-full mb-1.5 rounded-[6px] border-hairline-strong bg-surface text-ink text-[11px] px-3 py-1.5 font-medium">
                    <div id="containerAnggotaEdit" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline max-h-40 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-medium text-ink cursor-pointer p-1 rounded-[4px] hover:bg-surface">
                                <input type="checkbox" name="tim_anggota[]" value="{{ $u->id }}"
                                    {{ (is_array(old('tim_anggota')) && in_array($u->id, old('tim_anggota'))) || in_array($u->id, $selectedTim['tim_anggota']) ? 'checked' : '' }}
                                    class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                                <span>{{ $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('penugasan.show', $penugasan->id) }}" class="ledger-btn-secondary !text-xs">Batal</a>
                <button type="submit" class="ledger-btn-primary !text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        function filterPersonil(input, containerId) {
            const filter = input.value.toLowerCase().trim();
            const container = document.getElementById(containerId);
            const items = container.querySelectorAll('.item-personil');
            let hasMatch = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    item.style.display = "flex";
                    hasMatch = true;
                } else {
                    item.style.display = "none";
                }
            });

            let emptyMsg = container.querySelector('.empty-msg');
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'empty-msg p-2 text-center text-ink-muted text-[11px]';
                emptyMsg.innerText = 'Tidak ada nama / NIP personil yang cocok.';
                container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = hasMatch ? 'none' : 'block';
        }
    </script>
</x-app-layout>
