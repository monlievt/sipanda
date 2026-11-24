<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'penugasan_id',
        'jenis',
        'judul',
        'pesan',
        'url_target',
        'status',
        'dikirim_pada',
        'dibaca_pada',
    ];

    protected $casts = [
        'dikirim_pada' => 'datetime',
        'dibaca_pada'  => 'datetime',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class);
    }

    // ─── Helper Methods ───────────────────────────────────

    /**
     * Normalisasi url_target menjadi path relatif internal agar tidak terikat
     * pada domain lama (misal sipanda.* saat aplikasi sudah pindah ke singawasi.*)
     * sekaligus mencegah open redirect ke host eksternal.
     */
    public static function normalizeUrlTarget(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }

        $parts = parse_url($trimmed);
        if ($parts === false) {
            return null;
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) && $parts['fragment'] !== '' ? '#' . $parts['fragment'] : '';

        return $path . $query . $fragment;
    }

    public function setUrlTargetAttribute(?string $value): void
    {
        $this->attributes['url_target'] = self::normalizeUrlTarget($value);
    }

    public function getResolvedUrlTargetAttribute(): ?string
    {
        return self::normalizeUrlTarget($this->attributes['url_target'] ?? null);
    }

    public function markAsRead(): bool
    {
        if (is_null($this->dibaca_pada)) {
            return $this->update(['dibaca_pada' => now()]);
        }
        return true;
    }

    public function getIsReadAttribute(): bool
    {
        return ! is_null($this->dibaca_pada);
    }

    // ─── Scope ────────────────────────────────────────────

    public function scopeBelumDibaca($query)
    {
        return $query->whereNull('dibaca_pada');
    }

    public function scopeSudahDibaca($query)
    {
        return $query->whereNotNull('dibaca_pada');
    }

    public function scopeUntukUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

