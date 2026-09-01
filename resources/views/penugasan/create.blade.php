<x-app-layout>
    <x-slot name="header">
        Input Penugasan Baru (Surat Perintah Tugas)
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('penugasan.store') }}" class="space-y-6">
            @csrf

            <!-- Card 1: Informasi Umum SPT -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    1. Informasi Umum Penugasan & Nomor SPT
                </h3>

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-700 dark:text-rose-300 text-xs space-y-1">
                        <p class="font-bold mb-1">Mohon lengkapi atau perbaiki isian berikut:</p>
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Nomor SPT <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_spt" value="{{ old('no_spt') }}" required placeholder="700/02/406.008/2025" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                        <p class="text-[10px] text-slate-400 mt-1">Harus unik. Sistem menolak jika nomor sudah terdaftar.</p>
                    </div>

                    <!-- Multi-Irban Penanggung Jawab -->
                    <div>
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

                <div class="mt-4 text-xs">
                    <label class="block font-semibold mb-1">Uraian Penugasan <span class="text-rose-500">*</span></label>
                    <textarea name="uraian_penugasan" rows="3" required placeholder="Jelaskan uraian/tujuan penugasan pengawasan ini..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('uraian_penugasan') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Penugasan <span class="text-rose-500">*</span></label>
                        <select name="jenis_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
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
                        <select name="sumber_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            <option value="">-- Pilih Sumber Penugasan --</option>
                            @foreach($sumberList as $s)
                                <option value="{{ $s->id }}" {{ old('sumber_penugasan_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                </div>

                <!-- ST Perpanjangan Flag & Alpine.js Searchable Combobox -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs"
                    x-data="{ isPerpanjangan: '{{ old('is_perpanjangan', '0') }}' }">
                    
                    <label class="block font-semibold mb-2">Apakah ini Surat Tugas Perpanjangan?</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_perpanjangan" value="0" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Bukan (Surat Tugas Baru)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_perpanjangan" value="1" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Ya, Ini ST Perpanjangan</span>
                        </label>
                    </div>

                    <!-- Alpine.js Combobox untuk ST Induk -->
                    <div x-show="isPerpanjangan == '1'" x-transition class="mt-3"
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('penugasan_induk_id') }}',
                            selectedLabel: '-- Pilih Nomor ST Induk --',
                            options: [
                                @foreach($parentStList as $pst)
                                    { id: '{{ $pst->id }}', label: 'No. SPT: {{ addslashes($pst->no_spt) }} — {{ addslashes(Str::limit($pst->uraian_penugasan, 60)) }}' },
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
                        
                        <label class="block font-semibold mb-1 text-emerald-700 dark:text-emerald-400">Pilih Surat Tugas Indikator (ST Induk yang diperpanjang) <span class="text-rose-500">*</span></label>
                        
                        <!-- Hidden Real Input -->
                        <input type="hidden" name="penugasan_induk_id" :value="isPerpanjangan == '1' ? selectedId : ''">

                        <div class="relative">
                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" @click.outside="open = false" 
                                class="w-full text-left px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold flex items-center justify-between shadow-xs focus:ring-2 focus:ring-emerald-500">
                                <span x-text="selectedId ? selectedLabel : '-- Klik untuk memilih ST Induk --'" class="truncate text-slate-800 dark:text-slate-200"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Popup dengan Search Input Langsung di Dalamnya -->
                            <div x-show="open" x-transition 
                                class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 space-y-2">
                                <input type="text" x-model="search" x-ref="searchInput" placeholder="🔍 Ketik nomor SPT / kata kunci di sini..." 
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
                                        Tidak ada nomor ST Induk yang cocok.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">LHP dan Tindak Lanjut akan otomatis terikat antara ST Baru dengan ST Induk.</p>
                    </div>
                </div>

                <!-- Flag Sesuai PKPPT & Alpine.js Searchable Combobox -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs"
                    x-data="{ isPkppt: '{{ old('is_sesuai_pkppt', '1') }}' }">
                    
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

                        <!-- Hidden Real Input -->
                        <input type="hidden" name="pkppt_id" :value="isPkppt == '1' ? selectedId : ''">

                        <div class="relative">
                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" @click.outside="open = false" 
                                class="w-full text-left px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold flex items-center justify-between shadow-xs focus:ring-2 focus:ring-emerald-500">
                                <span x-text="selectedId ? selectedLabel : '-- Klik untuk memilih Rencana PKPPT --'" class="truncate text-slate-800 dark:text-slate-200"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Popup dengan Search Input Langsung di Dalamnya -->
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

            <!-- Card 2: Objek Penugasan (OPD / Kecamatan Target) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs">
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

            <!-- Card 3: Susunan Personil Tim Pengawasan -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs space-y-4">
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
