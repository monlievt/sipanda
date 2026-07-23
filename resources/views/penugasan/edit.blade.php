<x-app-layout>
    <x-slot name="header">
        Edit Penugasan — {{ $penugasan->no_spt }}
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('penugasan.update', $penugasan->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Card 1: Informasi Umum SPT -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base mb-4 flex items-center gap-2">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Nomor SPT <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_spt" value="{{ old('no_spt', $penugasan->no_spt) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>

                    <!-- Multi-Irban Penanggung Jawab -->
                    <div>
                        <label class="block font-semibold mb-1">Irban Penanggung Jawab <span class="text-rose-500">*</span></label>
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

                <div class="mt-4 text-xs">
                    <label class="block font-semibold mb-1">Uraian Penugasan <span class="text-rose-500">*</span></label>
                    <textarea name="uraian_penugasan" rows="3" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ old('uraian_penugasan', $penugasan->uraian_penugasan) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Penugasan <span class="text-rose-500">*</span></label>
                        <select name="jenis_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            @foreach($jenisList as $j)
                                <option value="{{ $j->id }}" {{ old('jenis_penugasan_id', $penugasan->jenis_penugasan_id) == $j->id ? 'selected' : '' }}>
                                    [{{ ucfirst($j->kategori) }}] {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Sumber Penugasan <span class="text-rose-500">*</span></label>
                        <select name="sumber_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                            @foreach($sumberList as $s)
                                <option value="{{ $s->id }}" {{ old('sumber_penugasan_id', $penugasan->sumber_penugasan_id) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Status Pelaksanaan <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                            <option value="belum_berjalan" {{ old('status', $penugasan->status) === 'belum_berjalan' ? 'selected' : '' }}>Belum Berjalan</option>
                            <option value="berjalan" {{ old('status', $penugasan->status) === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ old('status', $penugasan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y-m-d') : '') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->format('Y-m-d') : '') }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs"
                    x-data="{ isPerpanjangan: '{{ old('is_perpanjangan', $penugasan->penugasan_induk_id ? '1' : '0') }}' }">
                    <label class="block font-semibold mb-2">Apakah ini Surat Tugas Perpanjangan?</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_perpanjangan" value="0" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Bukan (Surat Tugas Standar)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_perpanjangan" value="1" x-model="isPerpanjangan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ms-2 font-medium">Ya (ST Perpanjangan)</span>
                        </label>
                    </div>

                    <div x-show="isPerpanjangan == '1'" class="mt-3">
                        <label class="block font-semibold mb-1">Pilih Surat Tugas Induk</label>
                        <select name="penugasan_induk_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="">-- Pilih ST Induk --</option>
                            @foreach($parentStList as $parentSt)
                                <option value="{{ $parentSt->id }}" {{ old('penugasan_induk_id', $penugasan->penugasan_induk_id) == $parentSt->id ? 'selected' : '' }}>
                                    {{ $parentSt->no_spt }} — {{ Str::limit($parentSt->uraian_penugasan, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 2: Objek Penugasan & PKPPT Match -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs"
                x-data="{ isPkppt: '{{ old('is_sesuai_pkppt', $penugasan->is_sesuai_pkppt ? '1' : '0') }}' }">
                
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

            <!-- Card 3: Susunan Personil Tim Pengawasan -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    3. Susunan Personil Tim Pengawasan
                </h3>

                <!-- Wakil PJ -->
                <div>
                    <label class="block font-semibold mb-1">Wakil Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
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
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
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
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-36 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
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
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 max-h-40 overflow-y-auto space-y-1">
                        @foreach($usersList as $u)
                            <label class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer p-1 rounded hover:bg-white dark:hover:bg-slate-700">
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
</x-app-layout>
