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

