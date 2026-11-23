<x-app-layout>
    <x-slot name="header">
        Detail Tiket Konsultasi #{{ $konsultasi->nomor_tiket }}
    </x-slot>

    <div class="space-y-6" x-data="{ showModalDisposisiInspektur: false, showModalDisposisiIrban: false, showModalTerbitkanBa: false }">
        <!-- Back Link & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('konsultasi.index') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink inline-flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Kembali ke Daftar Tiket Konsultasi</span>
            </a>

            <div class="flex flex-wrap items-center gap-2">
                {{-- 1. Tombol Disposisi Tingkat 1 (Sisi Inspektur) --}}
                @if(auth()->user()->hasRole(['inspektur', 'super_admin', 'admin', 'sekretariat']))
                    <button type="button" @click="showModalDisposisiInspektur = true" class="ledger-btn-secondary text-xs">
                        Disposisi Inspektur ke Irban
                    </button>
                @endif

                {{-- 2. Tombol Disposisi Tingkat 2 (Sisi Irban) --}}
                @if(auth()->user()->hasRole(['irban', 'admin_irban', 'inspektur', 'super_admin', 'admin']))
                    <button type="button" @click="showModalDisposisiIrban = true" class="ledger-btn-secondary text-xs">
                        Penugasan Tim APIP (Irban)
                    </button>
                @endif

                @if($konsultasi->status === 'berjalan' || $konsultasi->status === 'selesai')
                    <button type="button" @click="showModalTerbitkanBa = true" class="ledger-btn-primary text-xs">
                        {{ $konsultasi->status === 'selesai' ? 'Edit Kesimpulan & Advis' : 'Formulasi Advis & Terbitkan BA' }}
                    </button>
                @endif

                @if($konsultasi->status === 'selesai')
                    <a href="{{ route('konsultasi.cetak_ba', $konsultasi->id) }}" target="_blank" class="ledger-btn-secondary text-xs">
                        <svg class="w-4 h-4 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Berita Acara (PDF)</span>
                    </a>

                    <form method="POST" action="{{ route('konsultasi.toggle_faq', $konsultasi->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3.5 py-2 min-h-[2.5rem] font-semibold text-xs rounded-[6px] border transition-colors {{ $konsultasi->is_faq_public ? 'bg-purple-700 hover:bg-purple-800 text-white border-transparent' : 'bg-surface hover:bg-surface-subtle text-ink border-hairline-strong' }}">
                            {{ $konsultasi->is_faq_public ? 'Terbit di Bank FAQ (Publik)' : 'Jadikan Artikel FAQ Publik' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-brand-soft border border-brand-border rounded-[10px] text-brand-ink text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Banner Status Disposisi Pimpinan -->
        @if(!$konsultasi->disposisi_inspektur_pada)
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 rounded-[10px] border border-amber-200 dark:border-amber-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-[6px] bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-amber-900 dark:text-amber-200 block">Menunggu Arahan & Disposisi Inspektur Daerah</span>
                        <span class="text-amber-800 dark:text-amber-300 text-[11px]">Permohonan baru masuk dari OPD dan belum diarahkan oleh pimpinan ke Irban pembina.</span>
                    </div>
                </div>
                @if(auth()->user()->hasRole(['inspektur', 'super_admin', 'admin', 'sekretariat']))
                    <button type="button" @click="showModalDisposisiInspektur = true" class="ledger-btn-primary text-xs shrink-0">
                        Disposisikan Sekarang
                    </button>
                @endif
            </div>
        @else
            <!-- Lembar Disposisi Resmi Inspektur -->
            <div class="p-4 bg-blue-50/70 dark:bg-blue-950/30 rounded-[10px] border border-blue-200 dark:border-blue-800/50 space-y-2.5 text-xs">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-semibold text-blue-900 dark:text-blue-200 uppercase tracking-wider text-[11px]">
                        Disposisi Pimpinan (Inspektur Daerah)
                    </span>
                    <span class="font-semibold text-blue-700 dark:text-blue-300 text-[11px] tabular-nums">
                        {{ $konsultasi->disposisi_inspektur_pada->format('d F Y H:i') }} WIB
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium text-ink-secondary">Diteruskan Kepada:</span>
                    <span class="px-2 py-0.5 bg-blue-700 text-white font-semibold rounded-[4px] text-xs">
                        {{ $konsultasi->irban?->nama_irban ?? 'Irban Terkait' }}
                    </span>
                    @if($konsultasi->inspekturPemberiDisposisi)
                        <span class="text-ink-muted text-[11px]">oleh {{ $konsultasi->inspekturPemberiDisposisi->nama }}</span>
                    @endif
                </div>
                @if($konsultasi->catatan_disposisi_inspektur)
                    <div class="mt-2 p-3 bg-surface rounded-[6px] border border-hairline">
                        <span class="font-semibold text-ink-muted block mb-0.5 text-[10px] uppercase tracking-wider">Catatan / Arahan Disposisi:</span>
                        <p class="text-ink font-medium italic text-xs leading-relaxed">
                            "{{ $konsultasi->catatan_disposisi_inspektur }}"
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Card Informasi Utama Tiket Konsultasi -->
        <div class="p-6 bg-surface rounded-[10px] border border-hairline space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-hairline">
                <div>
                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 block font-mono">No. Tiket: {{ $konsultasi->nomor_tiket }}</span>
                    <h2 class="text-lg font-bold text-ink leading-tight mt-0.5">{{ $konsultasi->judul_permasalahan }}</h2>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-[4px] text-xs font-semibold uppercase tracking-wider border
                        {{ $konsultasi->status === 'menunggu_disposisi' ? 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50' : '' }}
                        {{ $konsultasi->status === 'berjalan' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50' : '' }}
                        {{ $konsultasi->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50' : '' }}">
                        Status: {{ $konsultasi->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="block text-ink-muted font-semibold text-[11px]">Pemohon & Objek OPD</span>
                    <span class="font-semibold text-ink block mt-0.5">{{ $konsultasi->objekPenugasan?->nama ?? $konsultasi->pemohon?->nama }}</span>
                </div>

                <div>
                    <span class="block text-ink-muted font-semibold text-[11px]">Irban Pembina Wilayah</span>
                    <span class="font-semibold text-ink block mt-0.5">{{ $konsultasi->irban?->nama_irban ?? '-' }}</span>
                </div>

                <div>
                    <span class="block text-ink-muted font-semibold text-[11px]">Area / Topik Konsultasi</span>
                    <span class="font-semibold text-blue-700 dark:text-blue-400 uppercase block mt-0.5">{{ $konsultasi->area_konsultasi }}</span>
                </div>

                <div>
                    <span class="block text-ink-muted font-semibold text-[11px]">Metode Disetujui</span>
                    <span class="font-semibold text-ink block mt-0.5">
                        @if($konsultasi->metode_disetujui)
                            {{ $konsultasi->metode_disetujui === 'online' ? 'Online Chat' : 'Tatap Muka (' . ($konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('d/m/Y H:i') : '-') . ')' }}
                        @else
                            Usulan: {{ strtoupper($konsultasi->preferensi_metode) }} (Belum Ditetapkan)
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-4 bg-surface-subtle rounded-[10px] border border-hairline space-y-1.5">
                <span class="text-xs font-semibold text-ink-secondary block">Uraian Permasalahan Konsultasi:</span>
                <p class="text-xs text-ink leading-relaxed whitespace-pre-line">{{ $konsultasi->uraian_permasalahan }}</p>
                @if($konsultasi->berkas_pendukung)
                    <div class="pt-2">
                        <a href="{{ asset('storage/' . $konsultasi->berkas_pendukung) }}" target="_blank" class="text-xs font-semibold text-brand-ink hover:underline inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span>Lihat Berkas Lampiran Pendukung</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Susunan Tim APIP Ditunjuk -->
            @if($konsultasi->tim->isNotEmpty())
                <div class="pt-3 border-t border-hairline">
                    <span class="text-xs font-semibold text-ink-secondary block mb-2">Susunan Tim Konsultasi APIP Ditunjuk:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 text-xs">
                        @foreach($konsultasi->tim as $tMember)
                            <div class="p-2.5 bg-surface-subtle rounded-[6px] border border-hairline">
                                <span class="text-[10px] text-ink-muted font-semibold uppercase tracking-wider block">{{ $tMember->peran_label }}</span>
                                <span class="font-semibold text-ink block mt-0.5">{{ $tMember->user?->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Kesimpulan Advis APIP jika Selesai -->
            @if($konsultasi->kesimpulan_advis)
                <div class="p-4 bg-brand-soft rounded-[10px] border border-brand-border space-y-1.5">
                    <span class="text-xs font-semibold text-brand-ink block">Kesimpulan & Advis Resmi APIP:</span>
                    <p class="text-xs text-ink leading-relaxed whitespace-pre-line">{{ $konsultasi->kesimpulan_advis }}</p>
                </div>
            @endif
        </div>

        <!-- Ruang Chat Interaktif 2 Arah APIP dan OPD -->
        <div class="bg-surface rounded-[10px] border border-hairline p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-hairline pb-3">
                <h3 class="font-semibold text-ink text-[15px] flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand"></span>
                    <span>Ruang Percakapan & Advis Konsultasi Online ({{ $konsultasi->chats->count() }} Pesan)</span>
                </h3>
            </div>

            <!-- List Chat Bubble -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                @forelse($konsultasi->chats as $chat)
                    <div class="flex flex-col {{ $chat->tipe_pengirim === 'apip' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-xl p-4 rounded-[10px] text-xs space-y-1.5 {{ $chat->tipe_pengirim === 'apip' ? 'bg-brand text-white rounded-br-none' : 'bg-surface-subtle border border-hairline text-ink rounded-bl-none' }}">
                            <div class="flex items-center justify-between gap-4 text-[11px] font-semibold {{ $chat->tipe_pengirim === 'apip' ? 'text-emerald-100' : 'text-ink-muted' }}">
                                <span>{{ $chat->sender?->nama_display ?? ($chat->tipe_pengirim === 'apip' ? 'Tim Auditor APIP' : 'Pemohon OPD') }}</span>
                                <span class="tabular-nums">{{ $chat->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="leading-relaxed whitespace-pre-line">{{ $chat->pesan }}</p>
                            @if($chat->lampiran_file)
                                <div class="pt-1">
                                    <a href="{{ asset('storage/' . $chat->lampiran_file) }}" target="_blank" class="underline text-[11px] font-semibold inline-flex items-center gap-1 hover:opacity-90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span>Unduh Lampiran Berkas</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-ink-muted">
                        Belum ada percakapan. Mulai diskusi atau berikan tanggapan konsultasi di bawah ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Kirim Pesan APIP -->
            <form method="POST" action="{{ route('konsultasi.chat', $konsultasi->id) }}" enctype="multipart/form-data" class="pt-3 border-t border-hairline space-y-3">
                @csrf
                <div>
                    <textarea name="pesan" rows="3" required placeholder="Ketikkan tanggapan, klarifikasi regulasi, atau advis APIP untuk OPD..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <input type="file" name="lampiran_file" class="text-xs text-ink-secondary file:mr-2 file:py-1.5 file:px-3 file:rounded-[6px] file:border file:border-hairline-strong file:text-xs file:font-semibold file:bg-surface-subtle file:text-ink hover:file:bg-hairline">
                    <button type="submit" class="ledger-btn-primary text-xs self-end sm:self-auto">
                        <span>Kirim Tanggapan</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ===================================================== --}}
        {{-- MODAL 1: DISPOSISI PIMPINAN (INSPEKTUR DAERAH KE IRBAN) --}}
        {{-- ===================================================== --}}
        <div x-show="showModalDisposisiInspektur" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface w-full max-w-lg rounded-[10px] p-6 space-y-4 shadow-lg border border-hairline">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <div>
                        <h3 class="font-semibold text-ink text-[15px]">Disposisi Pimpinan (Inspektur Daerah)</h3>
                        <p class="text-[11px] text-ink-muted mt-0.5">Arahkan permohonan konsultasi ini kepada Irban yang membidangi.</p>
                    </div>
                    <button type="button" @click="showModalDisposisiInspektur = false" class="text-ink-muted hover:text-ink text-lg font-bold px-1">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.disposisi_inspektur', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-ink mb-1.5">Diteruskan Kepada Irban *</label>
                        <select name="irban_id" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-medium">
                            @foreach($irbans as $irb)
                                <option value="{{ $irb->id }}" {{ $konsultasi->irban_id == $irb->id ? 'selected' : '' }}>
                                    {{ $irb->nama_irban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1.5">Catatan / Petunjuk Arahan Inspektur *</label>
                        <textarea name="catatan_disposisi_inspektur" rows="4" required placeholder="Tuliskan petunjuk arahan pimpinan, misal: 'Pelajari regulasi pengadaan barang dan dampingi OPD terkait...'" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">{{ $konsultasi->catatan_disposisi_inspektur }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-hairline flex justify-end gap-2">
                        <button type="button" @click="showModalDisposisiInspektur = false" class="ledger-btn-secondary text-xs">Batal</button>
                        <button type="submit" class="ledger-btn-primary text-xs">
                            Kirim Disposisi ke Irban
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================================================== --}}
        {{-- MODAL 2: DISPOSISI TEKNIS & TIM AUDITOR (SISI IRBAN)  --}}
        {{-- ===================================================== --}}
        <div x-show="showModalDisposisiIrban" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface w-full max-w-2xl rounded-[10px] p-6 space-y-4 shadow-lg border border-hairline">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <div>
                        <h3 class="font-semibold text-ink text-[15px]">Penugasan Tim APIP & Metode Konsultasi (Irban)</h3>
                        <p class="text-[11px] text-ink-muted mt-0.5">Tetapkan metode pelayanan dan susun Tim Auditor/PPUPD penanggap.</p>
                    </div>
                    <button type="button" @click="showModalDisposisiIrban = false" class="text-ink-muted hover:text-ink text-lg font-bold px-1">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.disposisi', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-ink mb-1.5">Setujui Metode Konsultasi *</label>
                        <select name="metode_disetujui" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                            <option value="online" {{ $konsultasi->preferensi_metode === 'online' ? 'selected' : '' }}>Online Chat (Percakapan Daring)</option>
                            <option value="offline" {{ $konsultasi->preferensi_metode === 'offline' ? 'selected' : '' }}>Tatap Muka (Pertemuan Langsung di Inspektorat)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-ink mb-1.5">Tanggal & Jam Tatap Muka (Jika Offline)</label>
                            <input type="datetime-local" name="tanggal_tatap_muka" value="{{ $konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-ink mb-1.5">Lokasi Pertemuan (Jika Offline)</label>
                            <input type="text" name="lokasi_tatap_muka" value="{{ $konsultasi->lokasi_tatap_muka ?? 'Ruang Konsultasi Inspektorat Kab. Trenggalek' }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-hairline">
                        <span class="font-semibold text-ink block">Penunjukan Susunan Tim APIP:</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-ink-secondary mb-1.5">Penanggung Jawab *</label>
                                <select name="tim_pj[]" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['penanggung_jawab']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-ink-secondary mb-1.5">Pengendali Teknis *</label>
                                <select name="tim_daltek[]" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['pengendali_teknis']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-ink-secondary mb-1.5">Ketua Tim *</label>
                                <select name="tim_ketua[]" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['ketua_tim']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-ink-secondary mb-1.5">Anggota Tim *</label>
                                <select name="tim_anggota[]" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                    @foreach($usersList as $u)
                                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedTim['anggota_tim']) ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-hairline flex justify-end gap-2">
                        <button type="button" @click="showModalDisposisiIrban = false" class="ledger-btn-secondary text-xs">Batal</button>
                        <button type="submit" class="ledger-btn-primary text-xs">
                            Simpan & Mulai Konsultasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL FORMULASI ADVIS & TERBITKAN BERITA ACARA -->
        <div x-show="showModalTerbitkanBa" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-surface w-full max-w-2xl rounded-[10px] p-6 space-y-4 shadow-lg border border-hairline">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <h3 class="font-semibold text-ink text-[15px]">Formulasi Advis & Terbitkan Berita Acara Konsultasi</h3>
                    <button type="button" @click="showModalTerbitkanBa = false" class="text-ink-muted hover:text-ink text-lg font-bold px-1">&times;</button>
                </div>

                <form method="POST" action="{{ route('konsultasi.terbitkan_ba', $konsultasi->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-ink mb-1.5">Kesimpulan Advis & Solusi APIP *</label>
                        <textarea name="kesimpulan_advis" rows="6" required placeholder="Tuliskan kesimpulan akhir, arahan regulasi, dan poin advis resmi Inspektorat untuk pemohon OPD..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">{{ $konsultasi->kesimpulan_advis }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-hairline flex justify-end gap-2">
                        <button type="button" @click="showModalTerbitkanBa = false" class="ledger-btn-secondary text-xs">Batal</button>
                        <button type="submit" class="ledger-btn-primary text-xs">Terbitkan Berita Acara PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
