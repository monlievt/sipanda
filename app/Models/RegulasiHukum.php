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
