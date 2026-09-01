<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqArtikel extends Model
{
    use HasFactory;

    protected $table = 'faq_artikel';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'kategori',
        'regulasi_hukum_id',
        'dasar_hukum_rujukan',
        'is_published',
        'urutan',
        'dilihat_count',
        'dibuat_oleh',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'urutan'       => 'integer',
        'dilihat_count'=> 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function regulasi()
    {
        return $this->belongsTo(RegulasiHukum::class, 'regulasi_hukum_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
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
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum_rujukan', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
