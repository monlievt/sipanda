<x-app-layout>
    <x-slot name="header">
        Edit Dokumen Ikhtisar Laporan (ILHP)
    </x-slot>

    <div class="space-y-6" x-data="ilhpEditor({
        tahun: {{ $ikhtisarLaporan->tahun }},
        periode: '{{ $ikhtisarLaporan->periode }}',
        apiKey: '{{ addslashes($geminiApiKey ?? '') }}',
        model: '{{ addslashes($geminiModel ?? 'gemini-1.5-flash') }}',
        currentResume: '{{ addslashes($ikhtisarLaporan->resume_ai ?? '') }}'
    })">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="text-xs text-brand-ink hover:underline font-semibold inline-flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Tampilan Dokumen</span>
                </a>
                <h2 class="text-xl font-bold text-ink tracking-tight">Edit Parameter Dokumen & Resume AI</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Perbarui judul, nomor surat, narasi resume temuan & rekomendasi, serta status finalisasi laporan.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showModalApiKey = true" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                    <svg class="w-4 h-4 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan Gemini Key</span>
                    <span class="w-2 h-2 rounded-full" :class="apiKey ? 'bg-emerald-600' : 'bg-amber-600'"></span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('ikhtisar-laporan.update', $ikhtisarLaporan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface p-6 rounded-[10px] border border-hairline space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-6">
                        <label class="block font-semibold text-ink mb-1">Judul Dokumen <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $ikhtisarLaporan->judul) }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold text-ink mb-1">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $ikhtisarLaporan->nomor_surat) }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-mono">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold text-ink mb-1">Tanggal Dokumen <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                    </div>
                </div>

                <!-- Section 2: Resume Catatan/Temuan & Rekomendasi -->
                <div class="border-t border-hairline pt-5 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block font-semibold text-ink text-xs">
                                Bagian 2: Resume Catatan/Temuan serta Saran/Rekomendasi (AI Summary) <span class="text-red-700 dark:text-red-400">*</span>
                            </label>
                            <p class="text-xs text-ink-secondary">Ringkasan eksekutif temuan yang dapat digenerate ulang dengan AI atau disunting secara bebas.</p>
                        </div>

                        <button type="button" @click="generateAiResume()" :disabled="isGenerating" class="ledger-btn-secondary !text-xs !py-1.5 !px-3.5 !min-h-0 disabled:opacity-50">
                            <span x-show="!isGenerating">Generate Ulang dengan AI</span>
                            <span x-show="isGenerating" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-ink" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menganalisis...
                            </span>
                        </button>
                    </div>

                    <textarea name="resume_ai" x-model="resumeText" rows="8" required placeholder="Tuliskan resume temuan dan rekomendasi pengawasan..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs text-ink leading-relaxed p-3.5"></textarea>
                </div>

                <!-- Catatan Tambahan & Status -->
                <div class="border-t border-hairline pt-4 grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-8">
                        <label class="block font-semibold text-ink mb-1">Catatan Khusus Tambahan</label>
                        <input type="text" name="catatan_khusus" value="{{ old('catatan_khusus', $ikhtisarLaporan->catatan_khusus) }}" placeholder="Catatan opsional..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block font-semibold text-ink mb-1">Status Dokumen</label>
                        <select name="status" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="draft" {{ $ikhtisarLaporan->status === 'draft' ? 'selected' : '' }}>Draf (Dapat Diedit)</option>
                            <option value="final" {{ $ikhtisarLaporan->status === 'final' ? 'selected' : '' }}>Final Resmi Siap Cetak</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-hairline">
                    <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="ledger-btn-secondary !text-xs">
                        Batal
                    </a>
                    <button type="submit" class="ledger-btn-primary !text-xs">
                        Simpan Perubahan Dokumen
                    </button>
                </div>
            </div>
        </form>

        <!-- MODAL PENGATURAN GEMINI API KEY -->
        <div x-show="showModalApiKey" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
            <div @click.away="showModalApiKey = false" class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-lg w-full space-y-4">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <h3 class="font-semibold text-ink text-sm">Pengaturan Google Gemini API Key</h3>
                    <button type="button" @click="showModalApiKey = false" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">&times;</button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <p class="text-ink-secondary leading-relaxed">
                        Fitur <strong>AI Summary Temuan & Rekomendasi</strong> menggunakan Google Gemini Flash yang <strong>100% Gratis</strong> tanpa biaya berlangganan.
                    </p>

                    <div>
                        <label class="block font-semibold text-ink mb-1">
                            Google Gemini API Key <span class="text-red-700 dark:text-red-400">*</span>
                        </label>
                        <input type="password" x-model="apiKeyInput" placeholder="Tempel API Key AI Studio di sini (AIzaSy...)" class="w-full text-xs font-mono rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1">Pilihan Model AI</label>
                        <select x-model="modelInput" class="w-full text-xs font-semibold rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                            <option value="gemini-1.5-flash">Gemini 1.5 Flash (Sangat Cepat & Gratis)</option>
                            <option value="gemini-2.0-flash">Gemini 2.0 Flash (Generasi Terbaru & Gratis)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-[6px] border border-blue-200 dark:border-blue-800 text-xs text-blue-900 dark:text-blue-200">
                        <span>Dapatkan API Key gratis di </span>
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-semibold underline text-blue-800 dark:text-blue-300 hover:text-blue-900">
                            Google AI Studio (aistudio.google.com)
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" @click="showModalApiKey = false" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">Batal</button>
                    <button type="button" @click="saveApiKey()" :disabled="isSavingKey" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
                        <span x-show="!isSavingKey">Simpan Pengaturan API Key</span>
                        <span x-show="isSavingKey">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ilhpEditor(config) {
            return {
                tahun: config.tahun,
                periode: config.periode,
                apiKey: config.apiKey,
                apiKeyInput: config.apiKey,
                modelInput: config.model || 'gemini-1.5-flash',
                resumeText: config.currentResume || '',
                isGenerating: false,
                isSavingKey: false,
                showModalApiKey: false,

                async generateAiResume() {
                    this.isGenerating = true;
                    try {
                        const res = await fetch("{{ route('ikhtisar-laporan.generate_resume_ai') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                tahun: this.tahun,
                                periode: this.periode
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.resumeText = data.resume;
                            if (data.note) {
                                alert("Resume temuan berhasil diperbarui!\n(" + data.note + ")");
                            }
                        } else {
                            alert(data.message || "Gagal memproses analisis AI.");
                            if (data.resume) {
                                this.resumeText = data.resume;
                            }
                        }
                    } catch (e) {
                        alert("Terjadi kesalahan koneksi saat memanggil AI: " + e.message);
                    } finally {
                        this.isGenerating = false;
                    }
                },

                async saveApiKey() {
                    if (!this.apiKeyInput.trim()) {
                        alert("API Key tidak boleh kosong.");
                        return;
                    }

                    this.isSavingKey = true;
                    try {
                        const res = await fetch("{{ route('ikhtisar-laporan.save_gemini_key') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                gemini_api_key: this.apiKeyInput.trim(),
                                gemini_model: this.modelInput
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.apiKey = this.apiKeyInput.trim();
                            this.showModalApiKey = false;
                            alert("Pengaturan Gemini API Key berhasil disimpan!");
                        } else {
                            alert(data.message || "Gagal menyimpan API Key.");
                        }
                    } catch (e) {
                        alert("Kesalahan saat menyimpan: " + e.message);
                    } finally {
                        this.isSavingKey = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
