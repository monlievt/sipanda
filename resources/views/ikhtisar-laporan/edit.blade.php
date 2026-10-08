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
                <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Tampilan Dokumen
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Edit Parameter Dokumen & Resume AI</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui judul, nomor surat, narasi resume temuan & rekomendasi, serta status finalisasi laporan.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showModalApiKey = true" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700 inline-flex items-center gap-1.5 shadow-xs transition-all cursor-pointer">
                    <span>⚙️ Pengaturan Gemini Key</span>
                    <span class="w-2 h-2 rounded-full" :class="apiKey ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('ikhtisar-laporan.update', $ikhtisarLaporan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-6">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $ikhtisarLaporan->judul) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $ikhtisarLaporan->nomor_surat) }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500 font-mono">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Dokumen <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Section 2: Resume Catatan/Temuan & Rekomendasi -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block font-black text-slate-900 dark:text-white text-xs">
                                Bagian 2: Resume Catatan/Temuan serta Saran/Rekomendasi (AI Summary) <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Ringkasan eksekutif temuan yang dapat digenerate ulang dengan AI atau disunting secara bebas.</p>
                        </div>

                        <button type="button" @click="generateAiResume()" :disabled="isGenerating" class="px-3.5 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-xs inline-flex items-center gap-1.5 cursor-pointer transition-all">
                            <span x-show="!isGenerating">🤖 Generate Ulang dengan AI</span>
                            <span x-show="isGenerating" class="inline-flex items-center gap-1">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menganalisis...
                            </span>
                        </button>
                    </div>

                    <textarea name="resume_ai" x-model="resumeText" rows="8" required placeholder="Tuliskan resume temuan dan rekomendasi pengawasan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white leading-relaxed p-3.5 focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <!-- Catatan Tambahan & Status -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-4 grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-8">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Khusus Tambahan</label>
                        <input type="text" name="catatan_khusus" value="{{ old('catatan_khusus', $ikhtisarLaporan->catatan_khusus) }}" placeholder="Catatan opsional..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Dokumen</label>
                        <select name="status" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                            <option value="draft" {{ $ikhtisarLaporan->status === 'draft' ? 'selected' : '' }}>📝 Draf (Dapat Diedit)</option>
                            <option value="final" {{ $ikhtisarLaporan->status === 'final' ? 'selected' : '' }}>✓ Final Resmi Siap Cetak</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 cursor-pointer">
                        Simpan Perubahan Dokumen
                    </button>
                </div>
            </div>
        </form>

        <!-- MODAL PENGATURAN GEMINI API KEY -->
        <div x-show="showModalApiKey" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" x-cloak>
            <div @click.away="showModalApiKey = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 text-purple-600">
                        <span class="text-xl">⚙️</span>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm">Pengaturan Google Gemini API Key</h3>
                    </div>
                    <button type="button" @click="showModalApiKey = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">&times;</button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Fitur <strong>AI Summary Temuan & Rekomendasi</strong> menggunakan Google Gemini Flash yang <strong>100% Gratis</strong> tanpa biaya berlangganan.
                    </p>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Google Gemini API Key <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" x-model="apiKeyInput" placeholder="Tempel API Key AI Studio di sini (AIzaSy...)" class="w-full text-xs font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan Model AI</label>
                        <select x-model="modelInput" class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                            <option value="gemini-1.5-flash">Gemini 1.5 Flash (Sangat Cepat & Gratis)</option>
                            <option value="gemini-2.0-flash">Gemini 2.0 Flash (Generasi Terbaru & Gratis)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800 text-[11px] text-purple-900 dark:text-purple-300 flex items-start gap-2">
                        <span>💡</span>
                        <div>
                            <span>Dapatkan API Key gratis di </span>
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-bold underline text-purple-700 dark:text-purple-300 hover:text-purple-900">
                                Google AI Studio (aistudio.google.com) &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showModalApiKey = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" @click="saveApiKey()" :disabled="isSavingKey" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs shadow-md cursor-pointer">
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
                                alert("✓ Resume temuan berhasil diperbarui!\n(" + data.note + ")");
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
                            alert("✓ Pengaturan Gemini API Key berhasil disimpan!");
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
