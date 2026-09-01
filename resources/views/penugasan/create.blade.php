@php
    $parentStOptions = $parentStList->map(function($pst) {
        return [
            'id' => (string) $pst->id,
            'label' => 'No. SPT: ' . $pst->no_spt . ' — ' . \Illuminate\Support\Str::limit($pst->uraian_penugasan, 50),
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
        Input Penugasan Baru (Surat Perintah Tugas)
    </x-slot>

    <div class="max-w-4xl mx-auto"
        x-data="{
            isPerpanjangan: '{{ old('is_perpanjangan', '0') }}',
            isPkppt: '{{ old('is_sesuai_pkppt', '1') }}',
            selectedParentId: '{{ old('penugasan_induk_id') }}',
            parentOptions: {{ Js::from($parentStOptions) }},
            get selectedParent() {
                return this.parentOptions.find(p => p.id == this.selectedParentId) || null;
            }
        }">

        <form method="POST" action="{{ route('penugasan.store') }}" class="space-y-6">
            @csrf

            <!-- Banner Mode ST Perpanjangan vs ST Baru -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <label class="block font-bold text-slate-900 dark:text-white text-sm mb-2">Jenis Pembuatan Surat Tugas (SPT) <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                        :class="isPerpanjangan == '0' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 shadow-xs font-bold' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
                        <input type="radio" name="is_perpanjangan" value="0" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="text-xs font-bold">📄 Surat Tugas Baru (Reguler)</p>
                            <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 mt-0.5">Penugasan mandiri baru dengan input objek dan tim lengkap.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                        :class="isPerpanjangan == '1' ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/40 text-blue-950 dark:text-blue-200 shadow-xs font-bold' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
                        <input type="radio" name="is_perpanjangan" value="1" x-model="isPerpanjangan" class="text-blue-600 focus:ring-blue-500">
                        <div>
                            <p class="text-xs font-bold">🔄 Surat Tugas Perpanjangan</p>
                            <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 mt-0.5">Mewarisi data Objek, Tim, PKPPT & Irban otomatis dari ST Induk.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Card 1: Informasi SPT -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4 text-xs">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    1. Informasi Penugasan & Nomor SPT
                </h3>

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-700 dark:text-rose-300 text-xs space-y-1">
                        <p class="font-bold mb-1">Mohon lengkapi atau perbaiki isian berikut:</p>
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Pilihan ST Induk (Hanya muncul jika ST Perpanjangan) -->
                <div x-show="isPerpanjangan == '1'" x-transition class="p-4 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/60 rounded-2xl space-y-3"
                    x-data="{
                        open: false,
                        search: '',
                        selectedLabel: '-- Klik untuk memilih Nomor ST Induk --',
                        get filteredOptions() {
                            if (!this.search.trim()) return parentOptions;
                            return parentOptions.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase().trim()) || o.no_spt.toLowerCase().includes(this.search.toLowerCase().trim()));
                        },
                        select(opt) {
                            selectedParentId = opt.id;
                            this.selectedLabel = opt.label;
                            this.open = false;
                            this.search = '';
                        },
                        init() {
                            if (selectedParentId) {
                                const found = parentOptions.find(o => o.id == selectedParentId);
                                if (found) this.selectedLabel = found.label;
                            }
                        }
                    }">
                    
                    <label class="block font-bold text-blue-900 dark:text-blue-300 text-xs">Pilih Surat Tugas Indikator (ST Induk yang Diperpanjang) <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="penugasan_induk_id" :value="isPerpanjangan == '1' ? selectedParentId : ''">

                    <div class="relative">
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.parentSearchInput.focus())" @click.outside="open = false" 
                            class="w-full text-left px-3.5 py-2.5 rounded-xl border border-blue-300 dark:border-blue-700 bg-white dark:bg-slate-800 text-xs font-semibold flex items-center justify-between shadow-xs focus:ring-2 focus:ring-blue-500">
                            <span x-text="selectedParentId ? selectedLabel : '-- Klik untuk memilih ST Induk --'" class="truncate text-slate-800 dark:text-slate-200"></span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition 
                            class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 space-y-2">
                            <input type="text" x-model="search" x-ref="parentSearchInput" placeholder="🔍 Cari nomor SPT / uraian ST Induk di sini..." 
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2 focus:ring-2 focus:ring-blue-500 font-medium">
                            
                            <div class="max-h-52 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <div @click="select(opt)" 
                                        class="px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-950/60 hover:text-blue-700 dark:hover:text-blue-300 rounded-lg cursor-pointer transition-colors"
                                        :class="{ 'bg-blue-50 text-blue-700 dark:bg-blue-950/80 font-bold': selectedParentId == opt.id }">
                                        <span x-text="opt.label"></span>
                                    </div>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="p-3 text-center text-slate-400 text-xs">
                                    Tidak ada nomor ST Induk yang cocok.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Info Otomatis Pewarisan Data ST Induk -->
                    <template x-if="selectedParent">
                        <div class="p-3.5 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl space-y-2.5">
                            <div class="font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5 text-xs">
                                <span>✨ Data yang Otomatis Diwarisi dari ST Induk:</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-slate-700 dark:text-slate-300">
                                <div><strong>🏢 Objek Sasaran:</strong> <span class="font-semibold text-slate-900 dark:text-white" x-text="selectedParent.objek"></span></div>
                                <div><strong>🏛️ Irban:</strong> <span class="font-semibold text-slate-900 dark:text-white" x-text="selectedParent.irban"></span></div>
                                <div><strong>📑 Jenis Pengawasan:</strong> <span class="font-semibold text-slate-900 dark:text-white" x-text="selectedParent.jenis"></span></div>
                                <div><strong>📌 Sumber:</strong> <span class="font-semibold text-slate-900 dark:text-white" x-text="selectedParent.sumber"></span></div>
                                <div class="sm:col-span-2"><strong>👥 Susunan Tim:</strong> <span class="font-semibold text-slate-900 dark:text-white" x-text="selectedParent.tim"></span></div>
                            </div>
                            <p class="text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">💡 Anda tidak perlu memilih ulang Objek & Tim agar data konsisten dan realisasi PKPPT tidak terhitung ganda.</p>
                        </div>
                    </template>
                </div>

                <!-- Form Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Nomor SPT Perpanjangan / Baru <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_spt" value="{{ old('no_spt') }}" required placeholder="mis. 700/02.P1/406.008/2026" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                        <p class="text-[10px] text-slate-400 mt-1">Harus unik di dalam sistem.</p>
                    </div>

                    <!-- Multi-Irban Penanggung Jawab (Hanya tampil jika BUKAN ST Perpanjangan) -->
                    <div x-show="isPerpanjangan == '0'" x-transition>
                        <label class="block font-semibold mb-1">Irban Penanggung Jawab <span class="text-rose-500">*</span> (Dapat memilih lebih dari 1)</label>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1 max-h-36 overflow-y-auto">
                            @foreach($irbans as $irban)
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                    <input type="checkbox" name="irban_ids[]" value="{{ $irban->id }}"
                                        {{ (is_array(old('irban_ids')) && in_array($irban->id, old('irban_ids'))) || (auth()->user()->irban_id == $irban->id) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ $irban->nama_irban }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Uraian / Alasan Penugasan <span class="text-rose-500">*</span></label>
                    <textarea name="uraian_penugasan" rows="3" required placeholder="Jelaskan uraian atau alasan perpanjangan penugasan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('uraian_penugasan') }}</textarea>
                </div>

                <!-- Jenis & Sumber (Hanya tampil jika BUKAN ST Perpanjangan) -->
                <div x-show="isPerpanjangan == '0'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Penugasan <span class="text-rose-500">*</span></label>
                        <select name="jenis_penugasan_id" :required="isPerpanjangan == '0'" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            <option value="">-- Pilih Jenis Penugasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_penugasan_id') == $j->id ? 'selected' : '' }}>
                                    [{{ ucfirst($j->kategori) }}] {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Sumber Penugasan <span class="text-rose-500">*</span></label>
                        <select name="sumber_penugasan_id" :required="isPerpanjangan == '0'" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            <option value="">-- Pilih Sumber Penugasan --</option>
                            @foreach($sumberList as $s)
                                <option value="{{ $s->id }}" {{ old('sumber_penugasan_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tanggal Mulai & Selesai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Flag Sesuai PKPPT (Hanya tampil jika BUKAN ST Perpanjangan) -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800"
                    x-show="isPerpanjangan == '0'" x-transition>
                    
                    <label class="block font-semibold mb-2">Kesesuaian dengan PKPPT</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_sesuai_pkppt" value="1" x-model="isPkppt" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Sesuai PKPPT</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_sesuai_pkppt" value="0" x-model="isPkppt" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Di Luar PKPPT (Non-PKPPT)</span>
                        </label>
                    </div>

                    <!-- Alpine.js Combobox untuk PKPPT -->
                    <div x-show="isPkppt == '1'" x-transition class="mt-3"
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('pkppt_id') }}',
                            selectedLabel: '-- Pilih Rencana PKPPT --',
                            options: [
                                @foreach($pkpptList as $pk)
                                    { id: '{{ $pk->id }}', label: '[{{ addslashes($pk->irban?->nama_irban ?? 'Semua Irban') }}] {{ addslashes($pk->area_pengawasan) }} (Target: {{ $pk->jumlah_laporan_rencana }} Laporan)' },
                                @endforeach
                            ],
                            get filteredOptions() {
                                if (!this.search.trim()) return this.options;
                                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase().trim()));
                            },
                            select(opt) {
                                this.selectedId = opt.id;
                                this.selectedLabel = opt.label;
                                this.open = false;
                                this.search = '';
                            },
                            init() {
                                if (this.selectedId) {
                                    const found = this.options.find(o => o.id == this.selectedId);
                                    if (found) this.selectedLabel = found.label;
                                }
                            }
                        }">

                        <label class="block font-semibold mb-1">Pilih Baris Rencana PKPPT Terkait <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="pkppt_id" :value="(isPerpanjangan == '0' && isPkppt == '1') ? selectedId : ''">

                        <div class="relative">
                            <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" @click.outside="open = false" 
                                class="w-full text-left px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold flex items-center justify-between shadow-xs focus:ring-2 focus:ring-emerald-500">
                                <span x-text="selectedId ? selectedLabel : '-- Klik untuk memilih Rencana PKPPT --'" class="truncate text-slate-800 dark:text-slate-200"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition 
                                class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 space-y-2">
                                <input type="text" x-model="search" x-ref="searchInput" placeholder="🔍 Ketik nama area / Irban di sini..." 
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2 focus:ring-2 focus:ring-emerald-500 font-medium">
                                
                                <div class="max-h-52 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                                    <template x-for="opt in filteredOptions" :key="opt.id">
                                        <div @click="select(opt)" 
                                            class="px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-lg cursor-pointer transition-colors"
                                            :class="{ 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 font-bold': selectedId == opt.id }">
                                            <span x-text="opt.label"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions.length === 0" class="p-3 text-center text-slate-400 text-xs">
                                        Tidak ada baris PKPPT yang cocok.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Objek Penugasan (Hanya jika BUKAN ST Perpanjangan) -->
            <div x-show="isPerpanjangan == '0'" x-transition class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            2. Objek Penugasan (OPD / Kecamatan Target) <span class="text-rose-500">*</span>
                        </h3>
                        <p class="text-slate-500 mt-0.5">Pilih satu atau beberapa instansi target pemeriksaan.</p>
                    </div>

                    <button type="button" onclick="document.getElementById('modalTambahObjekFast').classList.remove('hidden')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-semibold rounded-xl text-xs flex items-center gap-1 border border-blue-200">
                        <span>+ Tambah Objek Baru</span>
                    </button>
                </div>

                <!-- Filter Objek Penugasan -->
                <div class="mb-2">
                    <input type="text" id="searchObjek" onkeyup="filterObjekOptions()" placeholder="🔍 Cari nama instansi / kecamatan target..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-1.5 focus:ring-blue-500 font-medium">
                </div>

                <div id="containerObjekList" class="max-h-48 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($objekList as $objek)
                        <label class="item-objek inline-flex items-center gap-2 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 cursor-pointer">
                            <input type="checkbox" name="objek_ids[]" value="{{ $objek->id }}" {{ is_array(old('objek_ids')) && in_array($objek->id, old('objek_ids')) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $objek->nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Card 3: Susunan Personil Tim Pengawasan (Hanya jika BUKAN ST Perpanjangan) -->
            <div x-show="isPerpanjangan == '0'" x-transition class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-base mb-1 flex items-center gap-2">
                    <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                    3. Susunan Personil Tim Pengawasan <span class="text-rose-500">*</span>
                </h3>

                <!-- Wakil Penanggung Jawab -->
                <div>
                    <label class="block font-semibold mb-1">Wakil Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerWakilPj')" placeholder="🔍 Cari nama / NIP Wakil Penanggung Jawab..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerWakilPj" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_wakil_pj[]" value="{{ $u->id }}"
                                    {{ is_array(old('tim_wakil_pj')) && in_array($u->id, old('tim_wakil_pj')) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Pengendali Teknis -->
                <div>
                    <label class="block font-semibold mb-1">Pengendali Teknis <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerDaltek')" placeholder="🔍 Cari nama / NIP Pengendali Teknis (Dalnis)..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerDaltek" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_daltek[]" value="{{ $u->id }}"
                                    {{ is_array(old('tim_daltek')) && in_array($u->id, old('tim_daltek')) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Ketua Tim -->
                <div>
                    <label class="block font-semibold mb-1">Ketua Tim <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerKetua')" placeholder="🔍 Cari nama / NIP Ketua Tim..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerKetua" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_ketua[]" value="{{ $u->id }}"
                                    {{ is_array(old('tim_ketua')) && in_array($u->id, old('tim_ketua')) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Anggota Tim -->
                <div>
                    <label class="block font-semibold mb-1">Anggota Tim <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerAnggota')" placeholder="🔍 Cari nama / NIP Anggota Tim..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerAnggota" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-40 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_anggota[]" value="{{ $u->id }}"
                                    {{ is_array(old('tim_anggota')) && in_array($u->id, old('tim_anggota')) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('penugasan.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/25 transition-all">
                    Simpan Penugasan (SPT)
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Fast Tambah Objek Baru -->
    <div id="modalTambahObjekFast" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Manual Objek Penugasan Baru</h3>
                <button onclick="document.getElementById('modalTambahObjekFast').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.objek-penugasan.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1">Nama Instansi / Objek <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. Dinas Ketahanan Pangan / Desa Sambirejo" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Kategori Objek <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="opd">OPD (Dinas / Badan / RSUD)</option>
                        <option value="kecamatan">Kecamatan</option>
                        <option value="desa">Desa</option>
                        <option value="kelurahan">Kelurahan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahObjekFast').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md">Simpan Objek</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Filter Logic untuk Objek & Personil -->
    <script>
        function filterObjekOptions() {
            const input = document.getElementById('searchObjek');
            const filter = input.value.toLowerCase().trim();
            const items = document.querySelectorAll('.item-objek');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    item.style.display = "inline-flex";
                } else {
                    item.style.display = "none";
                }
            });
        }

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
                emptyMsg.className = 'empty-msg p-2 text-center text-slate-400 text-[11px]';
                emptyMsg.innerText = 'Tidak ada nama / NIP personil yang cocok.';
                container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = hasMatch ? 'none' : 'block';
        }
    </script>
</x-app-layout>
