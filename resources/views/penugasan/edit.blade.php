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
        Edit Penugasan — {{ $penugasan->no_spt }}
    </x-slot>

    <div class="max-w-4xl mx-auto"
        x-data="{
            isPerpanjangan: '{{ old('is_perpanjangan', $penugasan->penugasan_induk_id ? '1' : '0') }}',
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

            <!-- Banner Mode ST Perpanjangan vs ST Standar -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <label class="block font-bold text-slate-900 dark:text-white text-sm mb-2">Status Klasifikasi Penugasan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                        :class="isPerpanjangan == '0' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 shadow-xs font-bold' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
                        <input type="radio" name="is_perpanjangan" value="0" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="text-xs font-bold">📄 Surat Tugas Standar (Reguler)</p>
                            <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 mt-0.5">Penugasan mandiri dengan susunan objek dan tim kustom.</p>
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

            <!-- Card 1: Informasi Umum SPT -->
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

                <!-- Pilihan ST Induk (Hanya jika ST Perpanjangan) -->
                <div x-show="isPerpanjangan == '1'" x-transition class="p-4 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/60 rounded-2xl space-y-3">
                    <label class="block font-bold text-blue-900 dark:text-blue-300 text-xs">Pilih Surat Tugas Induk (ST yang Diperpanjang) <span class="text-rose-500">*</span></label>
                    <select name="penugasan_induk_id" x-model="selectedParentId" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-blue-500">
                        <option value="">-- Pilih ST Induk --</option>
                        @foreach($parentStList as $parentSt)
                            <option value="{{ $parentSt->id }}" {{ old('penugasan_induk_id', $penugasan->penugasan_induk_id) == $parentSt->id ? 'selected' : '' }}>
                                {{ $parentSt->no_spt }} — {{ Str::limit($parentSt->uraian_penugasan, 60) }}
                            </option>
                        @endforeach
                    </select>

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
                            <p class="text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">💡 Data Objek dan Tim otomatis disinkronkan dari ST Induk.</p>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Nomor SPT <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_spt" value="{{ old('no_spt', $penugasan->no_spt) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>

                    <!-- Multi-Irban Penanggung Jawab (Hanya jika BUKAN ST Perpanjangan) -->
                    <div x-show="isPerpanjangan == '0'" x-transition>
                        <label class="block font-semibold mb-1">Irban Penanggung Jawab <span class="text-rose-500">*</span> (Dapat memilih lebih dari 1)</label>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1 max-h-36 overflow-y-auto">
                            @foreach($irbans as $irban)
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                    <input type="checkbox" name="irban_ids[]" value="{{ $irban->id }}"
                                        {{ (is_array(old('irban_ids')) && in_array($irban->id, old('irban_ids'))) || in_array($irban->id, $selectedIrbanIds) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ $irban->nama_irban }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Uraian / Alasan Penugasan <span class="text-rose-500">*</span></label>
                    <textarea name="uraian_penugasan" rows="3" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('uraian_penugasan', $penugasan->uraian_penugasan) }}</textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold">Dasar Surat Perintah Tugas (Dasar Hukum / Rujukan Penugasan)</label>
                        <span class="text-[10px] text-slate-400">Dapat diedit / ditambah per penugasan untuk dicetak di Surat Tugas</span>
                    </div>
                    <textarea name="dasar_penugasan" rows="4" placeholder="1. Peraturan Daerah...&#10;2. Peraturan Bupati...&#10;3. PKPT Inspektorat Daerah..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500 leading-relaxed">{{ old('dasar_penugasan', $penugasan->dasar_penugasan) }}</textarea>
                    <p class="text-[10px] text-slate-400 mt-0.5">Teks dasar penugasan ini akan otomatis tercetak pada naskah dinas resmi Surat Perintah Tugas (SPT).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div x-show="isPerpanjangan == '0'" x-transition>
                        <label class="block font-semibold mb-1">Jenis Penugasan <span class="text-rose-500">*</span></label>
                        <select name="jenis_penugasan_id" :required="isPerpanjangan == '0'" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_penugasan_id', $penugasan->jenis_penugasan_id) == $j->id ? 'selected' : '' }}>
                                    [{{ ucfirst($j->kategori) }}] {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="isPerpanjangan == '0'" x-transition>
                        <label class="block font-semibold mb-1">Sumber Penugasan <span class="text-rose-500">*</span></label>
                        <select name="sumber_penugasan_id" :required="isPerpanjangan == '0'" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            @foreach($sumberList as $s)
                                <option value="{{ $s->id }}" {{ old('sumber_penugasan_id', $penugasan->sumber_penugasan_id) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div :class="isPerpanjangan == '1' ? 'sm:col-span-3' : ''">
                        <label class="block font-semibold mb-1">Status Penugasan <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            <option value="belum_berjalan" {{ old('status', $penugasan->status) == 'belum_berjalan' ? 'selected' : '' }}>Belum Berjalan</option>
                            <option value="berjalan" {{ old('status', $penugasan->status) == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ old('status', $penugasan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y-m-d') : '') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->format('Y-m-d') : '') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Card 2: Objek Penugasan & PKPPT Match (Hanya jika BUKAN ST Perpanjangan) -->
            <div x-show="isPerpanjangan == '0'" x-transition class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs">
                
                <h3 class="font-bold text-slate-900 dark:text-white text-base mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    2. Objek Penugasan & Integrasi PKPPT
                </h3>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Objek Penugasan Target <span class="text-rose-500">*</span></label>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-48 overflow-y-auto space-y-1">
                        @foreach($objekList as $obj)
                            <label class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="objek_ids[]" value="{{ $obj->id }}"
                                    {{ (is_array(old('objek_ids')) && in_array($obj->id, old('objek_ids'))) || in_array($obj->id, $selectedObjekIds) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $obj->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- PKPPT Selection (Hanya jika BUKAN ST Perpanjangan) -->
                <div>
                    <label class="block font-semibold mb-2">Kategori Perencanaan PKPPT</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_sesuai_pkppt" value="1" x-model="isPkppt" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Sesuai PKPPT (Terencana)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_sesuai_pkppt" value="0" x-model="isPkppt" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Non-PKPPT (Insidental)</span>
                        </label>
                    </div>

                    <div x-show="isPkppt == '1'" class="mt-3">
                        <label class="block font-semibold mb-1">Pilih Baris Kegiatan PKPPT Terkait</label>
                        <select name="pkppt_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
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

            <!-- Card 3: Susunan Personil Tim Pengawasan (Hanya jika BUKAN ST Perpanjangan) -->
            <div x-show="isPerpanjangan == '0'" x-transition class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    3. Susunan Personil Tim Pengawasan
                </h3>

                <!-- Wakil PJ -->
                <div>
                    <label class="block font-semibold mb-1">Wakil Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerWakilPjEdit')" placeholder="🔍 Cari nama / NIP Wakil Penanggung Jawab..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerWakilPjEdit" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_wakil_pj[]" value="{{ $u->id }}"
                                    {{ (is_array(old('tim_wakil_pj')) && in_array($u->id, old('tim_wakil_pj'))) || in_array($u->id, $selectedTim['tim_wakil_pj']) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Daltek -->
                <div>
                    <label class="block font-semibold mb-1">Pengendali Teknis <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerDaltekEdit')" placeholder="🔍 Cari nama / NIP Pengendali Teknis (Dalnis)..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerDaltekEdit" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_daltek[]" value="{{ $u->id }}"
                                    {{ (is_array(old('tim_daltek')) && in_array($u->id, old('tim_daltek'))) || in_array($u->id, $selectedTim['tim_daltek']) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Ketua Tim -->
                <div>
                    <label class="block font-semibold mb-1">Ketua Tim <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerKetuaEdit')" placeholder="🔍 Cari nama / NIP Ketua Tim..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerKetuaEdit" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_ketua[]" value="{{ $u->id }}"
                                    {{ (is_array(old('tim_ketua')) && in_array($u->id, old('tim_ketua'))) || in_array($u->id, $selectedTim['tim_ketua']) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Anggota Tim -->
                <div>
                    <label class="block font-semibold mb-1">Anggota Tim <span class="text-rose-500">*</span></label>
                    <input type="text" onkeyup="filterPersonil(this, 'containerAnggotaEdit')" placeholder="🔍 Cari nama / NIP Anggota Tim..." class="w-full mb-1.5 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] px-3 py-1.5 focus:ring-emerald-500 font-medium">
                    <div id="containerAnggotaEdit" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-40 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="item-personil flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
                                <input type="checkbox" name="tim_anggota[]" value="{{ $u->id }}"
                                    {{ (is_array(old('tim_anggota')) && in_array($u->id, old('tim_anggota'))) || in_array($u->id, $selectedTim['tim_anggota']) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $u->nama_display ?? $u->nama }} (NIP. {{ $u->nip ?? '-' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('penugasan.show', $penugasan->id) }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-lg">Simpan Perubahan</button>
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
                emptyMsg.className = 'empty-msg p-2 text-center text-slate-400 text-[11px]';
                emptyMsg.innerText = 'Tidak ada nama / NIP personil yang cocok.';
                container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = hasMatch ? 'none' : 'block';
        }
    </script>
</x-app-layout>
