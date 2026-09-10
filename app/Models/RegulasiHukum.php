<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulasiHukum extends Model
{
    use HasFactory;

    protected $table = 'regulasi_hukum';

    protected $fillable = [
        'judul',
        'nomor_regulasi',
        'tahun',
        'jenis_regulasi',
        'kategori',
        'ringkasan_eksekutif',
        'file_path',
        'nama_file_asli',
        'ukuran_kb',
        'teks_konten',
        'visibilitas',
        'is_dasar_spt_baku',
        'diunduh_count',
        'diunggah_oleh',
    ];

    protected $casts = [
        'tahun'             => 'integer',
        'diunduh_count'     => 'integer',
        'is_dasar_spt_baku' => 'boolean',
    ];

    public const JENIS_REGULASI = [
        'uu_perppu'          => 'Undang-Undang (UU) / Peraturan Pemerintah Pengganti Undang-Undang (Perppu)',
        'pp'                 => 'Peraturan Pemerintah (PP)',
        'perpres'            => 'Peraturan Presiden (Perpres)',
        'permen_lembaga'     => 'Peraturan Menteri/Lembaga/Badan Negara',
        'sk_menteri_lembaga' => 'Surat Keputusan Menteri/Lembaga/Badan Negara',
        'perda'              => 'Peraturan Daerah',
        'perkada'            => 'Peraturan Kepala Daerah',
        'sk_kepala_daerah'   => 'Surat Keputusan Kepala Daerah',
        'surat_edaran'       => 'Surat Edaran',
        'sk_kepala_pd'       => 'Surat Keputusan Kepala Perangkat Daerah',
    ];

    public const HIERARKI_LEVELS = [
        'uu_perppu'          => 10,
        'pp'                 => 20,
        'perpres'            => 30,
        'permen_lembaga'     => 40,
        'sk_menteri_lembaga' => 50,
        'perda'              => 60,
        'perkada'            => 70,
        'perbup'             => 70,
        'sk_kepala_daerah'   => 80,
        'surat_edaran'       => 90,
        'sk_kepala_pd'       => 100,
        'keputusan_inspektur'=> 100,
        'juknis'             => 100,
        'disposisi'          => 110,
        'surat'              => 110,
        'lainnya'            => 120,
    ];

    public function getHierarkiOrderAttribute(): int
    {
        return self::HIERARKI_LEVELS[$this->jenis_regulasi] ?? 999;
    }

    public function getFormatDasarSptAttribute(): string
    {
        $nomor = trim($this->nomor_regulasi);
        $jenis = $this->jenis_regulasi;

        // Pastikan prefix "Nomor" jika belum ada kata "Nomor" / "No."
        $formatNomor = preg_match('/^(nomor|no\.)/i', $nomor) ? $nomor : 'Nomor ' . $nomor;

        $prefix = match ($jenis) {
            'uu_perppu'          => 'Undang-Undang ' . $formatNomor,
            'pp'                 => 'Peraturan Pemerintah ' . $formatNomor,
            'perpres'            => 'Peraturan Presiden ' . $formatNomor,
            'permen_lembaga'     => 'Peraturan Menteri/Lembaga ' . $formatNomor,
            'sk_menteri_lembaga' => 'Keputusan Menteri/Lembaga ' . $formatNomor,
            'perda'              => 'Peraturan Daerah Kabupaten Trenggalek ' . $formatNomor,
            'perkada', 'perbup'  => 'Peraturan Bupati Trenggalek ' . $formatNomor,
            'sk_kepala_daerah'   => 'Keputusan Bupati Trenggalek ' . $formatNomor,
            'surat_edaran'       => 'Surat Edaran ' . $formatNomor,
            'sk_kepala_pd', 'keputusan_inspektur', 'juknis' => 'Keputusan Kepala Perangkat Daerah / Inspektur ' . $formatNomor,
            default              => ($this->jenis_regulasi_label ?? 'Peraturan') . ' ' . $formatNomor,
        };

        $prefix = preg_replace('/\s+/', ' ', trim($prefix));
        return $prefix . ' tentang ' . trim($this->judul) . ';';
    }

    public function getJenisRegulasiLabelAttribute(): string
    {
        $key = $this->jenis_regulasi;
        if (isset(self::JENIS_REGULASI[$key])) {
            return self::JENIS_REGULASI[$key];
        }

        return match ($key) {
            'perbup'              => 'Peraturan Kepala Daerah',
            'permendagri'         => 'Peraturan Menteri/Lembaga/Badan Negara',
            'keputusan_inspektur' => 'Surat Keputusan Kepala Perangkat Daerah',
            'juknis'              => 'Surat Keputusan Kepala Perangkat Daerah',
            default               => ucwords(str_replace('_', ' ', $key ?? '-')),
        };
    }

    // ─── Relasi ───────────────────────────────────────────

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function faqTerkait()
    {
        return $this->hasMany(FaqArtikel::class, 'regulasi_hukum_id');
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopePublik($query)
    {
        return $query->where('visibilitas', 'publik');
    }

    public function scopeKategori($query, ?string $kategori)
    {
        if ($kategori && $kategori !== 'semua') {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_regulasi', 'like', "%{$search}%")
                  ->orWhere('ringkasan_eksekutif', 'like', "%{$search}%")
                  ->orWhere('teks_konten', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeDasarSptBaku($query)
    {
        return $query->where('is_dasar_spt_baku', true);
    }
}
