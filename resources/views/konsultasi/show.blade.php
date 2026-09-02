<x-app-layout>
    <x-slot name="header">
        Detail Tiket Konsultasi #{{ $konsultasi->nomor_tiket }}
    </x-slot>

    <div class="space-y-6" x-data="{ showModalDisposisiInspektur: false, showModalDisposisiIrban: false, showModalTerbitkanBa: false }">
        <!-- Back Link & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('konsultasi.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center gap-1">
                &larr; Kembali ke Daftar Tiket Konsultasi
            </a>

            <div class="flex flex-wrap items-center gap-2">
                {{-- 1. Tombol Disposisi Tingkat 1 (Sisi Inspektur) --}}
                @if(auth()->user()->hasRole(['inspektur', 'super_admin', 'admin', 'sekretariat']))
                    <button type="button" @click="showModalDisposisiInspektur = true" class="px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-1.5 transition-all" style="background-color: #4338ca !important; color: #ffffff !important;">
                        ✍️ Disposisi Inspektur ke Irban
                    </button>
                @endif

                {{-- 2. Tombol Disposisi Tingkat 2 (Sisi Irban) --}}
                @if(auth()->user()->hasRole(['irban', 'admin_irban', 'inspektur', 'super_admin', 'admin']))
                    <button type="button" @click="showModalDisposisiIrban = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-1.5 transition-all" style="background-color: #d97706 !important; color: #ffffff !important;">
                        👥 Penugasan Tim APIP (Irban)
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

        <!-- Banner Status Disposisi Pimpinan -->
        @if(!$konsultasi->disposisi_inspektur_pada)
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⏳</span>
                    <div>
                        <span class="font-bold text-amber-900 dark:text-amber-200 block">Menunggu Arahan & Disposisi Inspektur Daerah</span>
                        <span class="text-amber-700 dark:text-amber-300 text-[11px]">Permohonan baru masuk dari OPD dan belum diarahkan oleh pimpinan ke Irban pembina.</span>
                    </div>
                </div>
                @if(auth()->user()->hasRole(['inspektur', 'super_admin', 'admin', 'sekretariat']))
                    <button type="button" @click="showModalDisposisiInspektur = true" class="px-4 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-bold rounded-xl shrink-0 shadow-md transition-all flex items-center gap-1.5" style="background-color: #4338ca !important; color: #ffffff !important;">
                        <span>✍️ Disposisikan Sekarang</span>
                    </button>
                @endif
            </div>
        @else
            <!-- Lembar Disposisi Resmi Inspektur -->
            <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-2xl border border-indigo-200 dark:border-indigo-900 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-black text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5 uppercase tracking-wider text-[11px]">
                        ✍️ Disposisi Pimpinan (Inspektur Daerah)
                    </span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 text-[11px]">
                        {{ $konsultasi->disposisi_inspektur_pada->format('d F Y H:i') }} WIB
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-slate-500">Diteruskan Kepada:</span>
                    <span class="px-2.5 py-1 bg-indigo-700 text-white font-black rounded-lg text-xs" style="background-color: #4338ca !important; color: #ffffff !important;">
                        {{ $konsultasi->irban?->nama_irban ?? 'Irban Terkait' }}
                    </span>
                    @if($konsultasi->inspekturPemberiDisposisi)
                        <span class="text-slate-400 text-[11px]">oleh {{ $konsultasi->inspekturPemberiDisposisi->nama }}</span>
                    @endif
                </div>
                @if($konsultasi->catatan_disposisi_inspektur)
                    <div class="mt-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-indigo-100 dark:border-indigo-950">
                        <span class="font-bold text-slate-400 block mb-0.5 text-[10px] uppercase">Catatan / Arahan Disposisi:</span>
                        <p class="text-slate-900 dark:text-white font-medium italic text-xs leading-relaxed">
                            "{{ $konsultasi->catatan_disposisi_inspektur }}"
                        </p>
                    </div>
                @endif
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
                            Usulan: {{ strtoupper($konsultasi->preferensi_metode) }} (Belum Ditetapkan)
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
                        <div class="max-w-xl p-4 rounded-2xl text-xs space-y-1 {{ $chat->tipe_pengirim === 'apip' ? 'bg-blue-600 text-white rounded-br-none shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-bl-none' }}">
                            <div class="flex items-center justify-between gap-4 text-[10px] opacity-75 font-semibold">
                                <span>{{ $chat->sender?->nama_display ?? ($chat->tipe_pengirim === 'apip' ? 'Tim Auditor APIP' : 'Pemohon OPD') }}</span>
                                <span>{{ $chat->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="leading-relaxed whitespace-pre-line">{{ $chat->pesan }}</p>
                            @if($chat->lampiran_file)
                                <div class="pt-1">
                                    <a href="{{ asset('storage/' . $chat->lampiran_file) }}" target="_blank" class="underline text-[11px] font-semibold hover:opacity-90">
                                        📎 Unduh Lampiran Berkas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada percakapan. Mulai diskusi atau berikan tanggapan konsultasi di bawah ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Kirim Pesan APIP -->
            <form method="POST" action="{{ route('konsultasi.chat', $konsultasi->id) }}" enctype="multipart/form-data" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                @csrf
                <div>
                    <textarea name="pesan" rows="3" required placeholder="Ketikkan tanggapan, klarifikasi regulasi, atau advis APIP untuk OPD..." class="w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-blue-500"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <input type="file" name="lampiran_file" class="text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 self-end sm:self-auto">
                        <span>Kirim Tanggapan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- ===================================================== --}}
        {{-- MODAL 1: DISPOSISI PIMPINAN (INSPEKTUR DAERAH KE IRBAN) --}}
        {{-- ===================================================== --}}
        <div x-show="showModalDisposisiInspektur" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl p-6 space-y-4 shadow-xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                            <span>✍️ Disposisi Pimpinan (Inspektur Daerah)</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Arahkan permohonan konsultasi ini kepada Irban yang membidangi.</p>
                    </div>
                    <button type="button" @click="showModalDisposisiInspektur = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.disposisi_inspektur', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Diteruskan Kepada Irban *</label>
                        <select name="irban_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-indigo-500">
                            @foreach($irbans as $irb)
                                <option value="{{ $irb->id }}" {{ $konsultasi->irban_id == $irb->id ? 'selected' : '' }}>
                                    {{ $irb->nama_irban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1">Catatan / Petunjuk Arahan Inspektur *</label>
                        <textarea name="catatan_disposisi_inspektur" rows="4" required placeholder="Tuliskan petunjuk arahan pimpinan, misal: 'Pelajari regulasi pengadaan barang dan dampingi OPD terkait...'" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-indigo-500">{{ $konsultasi->catatan_disposisi_inspektur }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" @click="showModalDisposisiInspektur = false" class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold rounded-xl shadow-md cursor-pointer" style="background-color: #4338ca !important; color: #ffffff !important;">
                            Kirim Disposisi ke Irban
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================================================== --}}
        {{-- MODAL 2: DISPOSISI TEKNIS & TIM AUDITOR (SISI IRBAN)  --}}
        {{-- ===================================================== --}}
        <div x-show="showModalDisposisiIrban" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-3xl p-6 space-y-4 shadow-xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm">Penugasan Tim APIP & Metode Konsultasi (Irban)</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tetapkan metode pelayanan dan susun Tim Auditor/PPUPD penanggap.</p>
                    </div>
                    <button type="button" @click="showModalDisposisiIrban = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
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
                        <button type="button" @click="showModalDisposisiIrban = false" class="px-4 py-2 bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-md cursor-pointer" style="background-color: #d97706 !important; color: #ffffff !important;">
                            Simpan & Mulai Konsultasi
                        </button>
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
