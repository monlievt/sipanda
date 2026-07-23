<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama', 'nama_tanpa_gelar', 'nip', 'email', 'no_hp',
        'password', 'google_id', 'jabatan', 'pangkat', 'golongan',
        'irban_id', 'is_active', 'tipe_akun', 'objek_penugasan_id',
        'status_undangan', 'token_undangan', 'token_kedaluwarsa',
    ];

    protected $hidden = ['password', 'remember_token', 'token_undangan'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'token_kedaluwarsa' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function irban()
    {
        return $this->belongsTo(Irban::class);
    }

    public function objekPenugasan()
    {
        return $this->belongsTo(ObjekPenugasan::class);
    }

    public function penugasanSebagaiTim()
    {
        return $this->belongsToMany(Penugasan::class, 'penugasan_tim', 'user_id', 'penugasan_id')
                    ->withPivot('peran');
    }

    public function penugasanDibuat()
    {
        return $this->hasMany(Penugasan::class, 'dibuat_oleh');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    // ─── Scope & Helper ───────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('tipe_akun', 'internal');
    }

    public function scopeOpd($query)
    {
        return $query->where('tipe_akun', 'opd');
    }

    /** Scope otomatis filter per Irban untuk role irban & admin_irban */
    public function scopeScopedByIrban($query, ?int $irbanId = null)
    {
        $id = $irbanId ?? auth()->user()?->irban_id;
        if ($id) {
            $query->where('irban_id', $id);
        }
        return $query;
    }

    public function isOpd(): bool
    {
        return $this->tipe_akun === 'opd';
    }

    public function getNameAttribute(): string
    {
        return $this->nama ?? '';
    }

    public function getNamaDisplayAttribute(): string
    {
        return $this->nama_tanpa_gelar ?? $this->nama;
    }

    public function getTokenMasihBerlakuAttribute(): bool
    {
        return $this->token_kedaluwarsa && $this->token_kedaluwarsa->isFuture();
    }
}
