<?php

namespace App\Services;

use App\Models\FaqArtikel;
use App\Models\RegulasiHukum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApipAiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY', ''));
        $this->model  = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash'));
    }

    /**
     * Cek apakah API Key Gemini tersedia.
     */
    public function hasApiKey(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Proses pertanyaan pengguna berbasis dokumen regulasi & FAQ yang ada di sistem.
     */
    public function ask(string $question, ?string $kategori = null): array
    {
        $cleanQuestion = trim($question);
        if (empty($cleanQuestion)) {
            return [
                'success' => false,
                'answer'  => 'Pertanyaan tidak boleh kosong.',
                'sources' => [],
                'is_ai'   => false,
            ];
        }

        // 1. Ekstrak kata kunci pencarian
        $keywords = $this->extractKeywords($cleanQuestion);

        // 2. Ambil dokumen Regulasi Hukum yang relevan
        $regulasiQuery = RegulasiHukum::query();
        if ($kategori && $kategori !== 'semua') {
            $regulasiQuery->where('kategori', $kategori);
        }

        $regulasiQuery->where(function ($q) use ($keywords, $cleanQuestion) {
            $q->where('judul', 'like', "%{$cleanQuestion}%")
              ->orWhere('nomor_regulasi', 'like', "%{$cleanQuestion}%")
              ->orWhere('ringkasan_eksekutif', 'like', "%{$cleanQuestion}%");

            foreach ($keywords as $kw) {
                if (strlen($kw) >= 3) {
                    $q->orWhere('ringkasan_eksekutif', 'like', "%{$kw}%")
                      ->orWhere('teks_konten', 'like', "%{$kw}%")
                      ->orWhere('judul', 'like', "%{$kw}%");
                }
            }
        });

        $relevantRegulasi = $regulasiQuery->take(4)->get();

        // 3. Ambil artikel FAQ yang relevan
        $faqQuery = FaqArtikel::published();
        if ($kategori && $kategori !== 'semua') {
            $faqQuery->where('kategori', $kategori);
        }

        $faqQuery->where(function ($q) use ($keywords, $cleanQuestion) {
            $q->where('pertanyaan', 'like', "%{$cleanQuestion}%")
              ->orWhere('jawaban', 'like', "%{$cleanQuestion}%");

            foreach ($keywords as $kw) {
                if (strlen($kw) >= 3) {
                    $q->orWhere('pertanyaan', 'like', "%{$kw}%")
                      ->orWhere('jawaban', 'like', "%{$kw}%")
                      ->orWhere('dasar_hukum_rujukan', 'like', "%{$kw}%");
                }
            }
        });

        $relevantFaq = $faqQuery->take(3)->get();

        // 4. Siapkan Daftar Sumber Dokumen untuk Referensi User
        $sources = [];
        foreach ($relevantRegulasi as $r) {
            $sources[] = [
                'type'     => 'regulasi',
                'judul'    => $r->judul,
                'nomor'    => $r->nomor_regulasi,
                'tahun'    => $r->tahun,
                'kategori' => ucfirst($r->kategori),
                'url'      => route('regulasi.public.download', $r->id),
            ];
        }

        foreach ($relevantFaq as $f) {
            $sources[] = [
                'type'     => 'faq',
                'judul'    => $f->pertanyaan,
                'nomor'    => $f->dasar_hukum_rujukan ?: 'FAQ Resmi APIP',
                'tahun'    => $f->created_at->format('Y'),
                'kategori' => ucfirst($f->kategori),
                'url'      => route('faq.index', ['search' => $f->pertanyaan]),
            ];
        }

        // 5. Jika AI API Key tersedia -> Kirim ke Google Gemini dengan Strict System Instruction
        if ($this->hasApiKey()) {
            $aiAnswer = $this->callGeminiApi($cleanQuestion, $relevantRegulasi, $relevantFaq);
            if ($aiAnswer) {
                return [
                    'success' => true,
                    'answer'  => $aiAnswer,
                    'sources' => $sources,
                    'is_ai'   => true,
                ];
            }
        }

        // 6. Fallback (Tanpa API Key atau jika API timeout) -> Rekap jawaban berbasis database lokal
        return $this->buildFallbackResponse($cleanQuestion, $relevantRegulasi, $relevantFaq, $sources);
    }

    /**
     * Panggil Google Gemini API dengan Strict Document-Only Grounding.
     */
    protected function callGeminiApi(string $question, $regulasiDocs, $faqDocs): ?string
    {
        $contextText = "";

        // Tambahkan konteks dari Dokumen Regulasi
        if ($regulasiDocs->isNotEmpty()) {
            $contextText .= "=== DOKUMEN REGULASI RESMI KABUPATEN TRENGGALEK & NASIONAL ===\n";
            foreach ($regulasiDocs as $i => $r) {
                $contextText .= "[" . ($i + 1) . "] {$r->nomor_regulasi} tentang {$r->judul} (Tahun {$r->tahun}, Kategori: {$r->kategori})\n";
                if ($r->ringkasan_eksekutif) {
                    $contextText .= "Ringkasan & Pasal Kunci:\n{$r->ringkasan_eksekutif}\n";
                }
                if ($r->teks_konten) {
                    $cuplikanTeks = Str::limit($r->teks_konten, 2000);
                    $contextText .= "Kutipan Teks:\n{$cuplikanTeks}\n";
                }
                $contextText .= "--------------------------------------------------\n";
            }
        }

        // Tambahkan konteks dari FAQ Resmi APIP
        if ($faqDocs->isNotEmpty()) {
            $contextText .= "\n=== BANK ARTIKEL FAQ / ADVIS RESMI APIP ===\n";
            foreach ($faqDocs as $j => $f) {
                $contextText .= "[" . ($j + 1) . "] Tanya: {$f->pertanyaan}\n";
                $contextText .= "Dasar Hukum: {$f->dasar_hukum_rujukan}\n";
                $contextText .= "Jawaban: {$f->jawaban}\n";
                $contextText .= "--------------------------------------------------\n";
            }
        }

        // Jika tidak ada dokumen sama sekali yang cocok di database
        if (empty($contextText)) {
            $contextText = "(Tidak ada dokumen regulasi yang secara spesifik memuat kata kunci pertanyaan ini di database SIPANDA).";
        }

        $systemInstruction = "Anda adalah Asisten Penasihat Virtual APIP (Aparat Pengawasan Intern Pemerintah) Inspektorat Kabupaten Trenggalek. " .
            "Tugas Anda adalah memberikan jawaban konsultasi regulasi pengawasan, pertanggungjawaban keuangan daerah, pengadaan barang/jasa, dan tata kelola pemerintahan yang akurat, profesional, dan formal.\n\n" .
            "ATURAN MUTLAK:\n" .
            "1. Anda HANYA BOLEH menjawab berdasarkan informasi dari DOKUMEN REGULASI dan BANK ARTIKEL FAQ yang dilampirkan di bawah.\n" .
            "2. DILARANG MENGARANG atau berspekulasi di luar dokumen yang disediakan.\n" .
            "3. Selalu sebutkan secara eksplisit rujukan nama regulasi, nomor, tahun, dan pasalnya di dalam jawaban Anda.\n" .
            "4. Jika informasi TIDAK DITEMUKAN atau tidak cukup lengkap dalam dokumen terlampir, sampaikan secara jujur: 'Mohon maaf, ketentuan perihal hal tersebut belum tercantum secara lengkap dalam basis data regulasi kami saat ini. Silakan mengajukan permohonan konsultasi resmi melalui portal e-Consulting SIPANDA agar dapat ditelaah langsung oleh Tim Auditor Irban terkait.'\n" .
            "5. Gunakan format Markdown yang rapi (gunakan bold, bullet points, dan sub-judul).\n\n" .
            "BERIKUT ADALAH DOKUMEN BASIS DATA REGULASI SIPANDA:\n" . $contextText;

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $payload = [
                'contents' => [
                    [
                        'role'  => 'user',
                        'parts' => [
                            ['text' => "Pertanyaan Pemohon: {$question}"]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'generationConfig' => [
                    'temperature'     => 0.2, // Rendah agar strictly factual & tidak halusinasi
                    'maxOutputTokens' => 1200,
                ]
            ];

            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $candidates = $response->json('candidates', []);
                if (! empty($candidates)) {
                    $text = $candidates[0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return trim($text);
                    }
                }
            } else {
                Log::warning("[SIPANDA AI] Gemini API Error status {$response->status()}: " . $response->body());
            }

        } catch (\Throwable $e) {
            Log::error("[SIPANDA AI] Exception saat memanggil Gemini API: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fallback cerdas tanpa API Key: menghasilkan ringkasan langsung dari database.
     */
    protected function buildFallbackResponse(string $question, $regulasiDocs, $faqDocs, array $sources): array
    {
        // Jika ada FAQ yang langsung cocok
        if ($faqDocs->isNotEmpty()) {
            $topFaq = $faqDocs->first();
            $answer = "### 💡 Jawaban Berdasarkan Basis FAQ Resmi APIP:\n\n" .
                      "**Pertanyaan Terkait:** {$topFaq->pertanyaan}\n\n" .
                      "{$topFaq->jawaban}\n\n" .
                      "📌 **Dasar Hukum Rujukan:** `{$topFaq->dasar_hukum_rujukan}`\n\n" .
                      "_Catatan: Anda dapat mengunduh dokumen regulasi pendukung pada daftar sumber di bawah._";

            return [
                'success' => true,
                'answer'  => $answer,
                'sources' => $sources,
                'is_ai'   => false,
            ];
        }

        // Jika ada Regulasi yang cocok
        if ($regulasiDocs->isNotEmpty()) {
            $topReg = $regulasiDocs->first();
            $answer = "### 📑 Dokumen Regulasi Terkait Ditemukan:\n\n" .
                      "Berdasarkan **{$topReg->nomor_regulasi}** tentang *{$topReg->judul}* (Kategori: " . ucfirst($topReg->kategori) . "):\n\n" .
                      "**Ringkasan Intisari Regulasi:**\n" .
                      ($topReg->ringkasan_eksekutif ?: 'Silakan unduh dokumen lengkap untuk mempelajari seluruh pasal dan ketentuan teknis.') . "\n\n" .
                      "Silakan unduh dokumen resmi pada tautan berkas terlampir di bawah.";

            return [
                'success' => true,
                'answer'  => $answer,
                'sources' => $sources,
                'is_ai'   => false,
            ];
        }

        // Jika tidak ada data yang cocok
        return [
            'success' => true,
            'answer'  => "Mohon maaf, kata kunci perihal *'{$question}'* belum ditemukan dalam basis data regulasi kami saat ini.\n\n" .
                         "Silakan ajukan permohonan konsultasi langsung melalui menu **e-Consulting** agar dapat ditelaah secara mendalam oleh Tim Auditor / Irban terkait.",
            'sources' => [],
            'is_ai'   => false,
        ];
    }

    /**
     * Ekstrak kata kunci dari pertanyaan.
     */
    protected function extractKeywords(string $text): array
    {
        $stopwords = ['apakah', 'bagaimana', 'apa', 'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan', 'adalah', 'bisa', 'bolehkah', 'syarat', 'cara', 'dalam', 'ini', 'itu', 'atau', 'agar', 'jika'];
        $words = preg_split('/[\s,\.\?\!\:\;]+/', strtolower($text));
        
        return array_values(array_filter($words, function ($w) use ($stopwords) {
            return strlen($w) >= 3 && ! in_array($w, $stopwords);
        }));
    }
}
