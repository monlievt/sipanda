<x-app-layout>
    <x-slot name="header">
        Detail Tiket Konsultasi #{{ $konsultasi->nomor_tiket }}
    </x-slot>

    <div class="space-y-6" x-data="{ showModalDisposisi: false, showModalTerbitkanBa: false }">
        <!-- Back Link & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('konsultasi.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center gap-1">
                &larr; Kembali ke Daftar Tiket Konsultasi
            </a>

            <div class="flex flex-wrap items-center gap-2">
                @if(auth()->user()->hasRole(['admin', 'sekretariat', 'inspektur', 'irban', 'admin_irban']))
                    <button type="button" @click="showModalDisposisi = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                        👥 Disposisi & Penunjukan Tim APIP
                    </button>
                @endif

                @if($konsultasi->status === 'berjalan' || $konsultasi->status === 'selesai')
                    <button type="button" @click="showModalTerbitkanBa = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                        📄 {{ $konsultasi->status === 'selesai' ? 'Edit Kesimpulan & Advis' : 'Formulasi Advis & Terbitkan BA' }}
                    </button>
                @endif

                @if($konsultasi->status === 'selesai')
                    <a href="{{ route('konsultasi.cetak_ba', $konsultasi->id) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                        🖨️ Cetak Berita Acara (PDF)
                    </a>

                    <form method="POST" action="{{ route('konsultasi.toggle_faq', $konsultasi->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 {{ $konsultasi->is_faq_public ? 'bg-purple-700 hover:bg-purple-800 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }} font-bold text-xs rounded-xl shadow-xs">
                            {{ $konsultasi->is_faq_public ? '⭐ Terbit di Bank FAQ (Publik)' : '➕ Jadikan Artikel FAQ Publik' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card Informasi Utama Tiket Konsultasi -->
        <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block font-mono">No. Tiket: {{ $konsultasi->nomor_tiket }}</span>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ $konsultasi->judul_permasalahan }}</h2>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider
                        {{ $konsultasi->status === 'menunggu_disposisi' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}
                        {{ $konsultasi->status === 'berjalan' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                        {{ $konsultasi->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}">
                        Status: {{ $konsultasi->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Pemohon & Objek OPD</span>
                    <span class="font-bold text-slate-900 dark:text-white block">{{ $konsultasi->objekPenugasan?->nama ?? $konsultasi->pemohon?->nama }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Irban Pembina Wilayah</span>
                    <span class="font-bold text-slate-900 dark:text-white block">{{ $konsultasi->irban?->nama_irban ?? '-' }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Area / Topik Konsultasi</span>
                    <span class="font-bold text-blue-700 dark:text-blue-400 uppercase block">{{ $konsultasi->area_konsultasi }}</span>
                </div>

                <div>
                    <span class="block text-slate-400 font-semibold text-[11px]">Metode Disetujui</span>
                    <span class="font-bold text-slate-900 dark:text-white block">
                        @if($konsultasi->metode_disetujui)
                            {{ $konsultasi->metode_disetujui === 'online' ? '💬 Online Chat' : '🤝 Tatap Muka (' . ($konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('d/m/Y H:i') : '-') . ')' }}
                        @else
                            Usulan: {{ strtoupper($konsultasi->preferensi_metode) }} (Belum Disposisi)
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Uraian Permasalahan Konsultasi:</span>
                <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $konsultasi->uraian_permasalahan }}</p>
                @if($konsultasi->berkas_pendukung)
                    <div class="pt-2">
                        <a href="{{ asset('storage/' . $konsultasi->berkas_pendukung) }}" target="_blank" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1">
                            📎 Lihat Berkas Lampiran Pendukung
                        </a>
                    </div>
                @endif
            </div>

            <!-- Susunan Tim APIP Ditunjuk -->
            @if($konsultasi->tim->isNotEmpty())
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">Susunan Tim Konsultasi APIP Ditunjuk:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                        @foreach($konsultasi->tim as $tMember)
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">{{ $tMember->peran_label }}</span>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $tMember->user?->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Kesimpulan Advis APIP jika Selesai -->
            @if($konsultasi->kesimpulan_advis)
                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-900 space-y-1">
                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-200 block">💡 Kesimpulan & Advis Resmi APIP:</span>
                    <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $konsultasi->kesimpulan_advis }}</p>
                </div>
            @endif
        </div>

        <!-- Ruang Chat Interaktif 2 Arah APIP ↔ OPD -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-black text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    💬 Ruang Percakapan & Advis Konsultasi Online ({{ $konsultasi->chats->count() }} Pesan):
                </h3>
            </div>

            <!-- List Chat Bubble -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                @forelse($konsultasi->chats as $chat)
                    <div class="flex flex-col {{ $chat->tipe_pengirim === 'apip' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-2xl p-4 rounded-2xl text-xs space-y-1.5 shadow-xs
                            {{ $chat->tipe_pengirim === 'apip' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-bl-none' }}">
                            <div class="flex items-center justify-between gap-4 font-bold text-[10px] opacity-80 border-b border-white/20 pb-1 mb-1">
                                <span>{{ $chat->sender?->nama ?? ($chat->tipe_pengirim === 'apip' ? 'Tim APIP Inspektorat' : 'Pemohon OPD') }}</span>
                                <span>{{ $chat->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="whitespace-pre-line leading-relaxed">{{ $chat->pesan }}</p>
                            @if($chat->lampiran_file)
                                <div class="pt-1">
                                    <a href="{{ asset('storage/' . $chat->lampiran_file) }}" target="_blank" class="font-bold underline text-[11px]">
                                        📎 Unduh Berkas Lampiran
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs font-medium">
                        Belum ada riwayat percakapan. Kirim pesan balasan di bawah ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Kirim Pesan Balasan Chat (APIP) -->
            <form method="POST" action="{{ route('konsultasi.chat', $konsultasi->id) }}" enctype="multipart/form-data" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tulis Tanggapan / Advis APIP:</label>
                    <textarea name="pesan" rows="3" required placeholder="Tuliskan penjelasan advis, regulasi acuan, atau tanggapan untuk OPD..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <input type="file" name="lampiran_file" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                        ✈️ Kirim Tanggapan Advis
                    </button>
                </div>
            </form>
        </div>

        <!-- MODAL DISPOSISI TIM APIP -->
        <div x-show="showModalDisposisi" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-3xl p-6 space-y-4 shadow-xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-sm">Disposisi Tim & Metode Konsultasi APIP</h3>
                    <button type="button" @click="showModalDisposisi = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.disposisi', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Setujui Metode Konsultasi *</label>
                        <select name="metode_disetujui" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="online" {{ $konsultasi->preferensi_metode === 'online' ? 'selected' : '' }}>💬 Online Chat (Percakapan Daring)</option>
                            <option value="offline" {{ $konsultasi->preferensi_metode === 'offline' ? 'selected' : '' }}>🤝 Tatap Muka (Pertemuan Langsung di Inspektorat)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Tanggal & Jam Tatap Muka (Jika Offline)</label>
                            <input type="datetime-local" name="tanggal_tatap_muka" value="{{ $konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Lokasi Pertemuan (Jika Offline)</label>
                            <input type="text" name="lokasi_tatap_muka" value="{{ $konsultasi->lokasi_tatap_muka ?? 'Ruang Konsultasi Inspektorat Kab. Trenggalek' }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="font-bold text-slate-800 dark:text-slate-200 block">Penunjukan Susunan Tim APIP:</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penanggung Jawab *</label>
                                <select name="tim_pj[]" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['penanggung_jawab']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pengendali Teknis *</label>
                                <select name="tim_daltek[]" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['pengendali_teknis']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ketua Tim *</label>
                                <select name="tim_ketua[]" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['ketua_tim']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Anggota Tim *</label>
                                <select name="tim_anggota[]" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['anggota_tim']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" @click="showModalDisposisi = false" class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs">Simpan Disposisi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL FORMULASI ADVIS & TERBITKAN BERITA ACARA -->
        <div x-show="showModalTerbitkanBa" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-3xl p-6 space-y-4 shadow-xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-sm">Formulasi Advis & Terbitkan Berita Acara Konsultasi</h3>
                    <button type="button" @click="showModalTerbitkanBa = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.terbitkan_ba', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Kesimpulan Advis & Solusi APIP *</label>
                        <textarea name="kesimpulan_advis" rows="6" required placeholder="Tuliskan kesimpulan akhir, arahan regulasi, dan poin advis resmi Inspektorat untuk pemohon OPD..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500">{{ $konsultasi->kesimpulan_advis }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" @click="showModalTerbitkanBa = false" class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">Terbitkan Berita Acara PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
