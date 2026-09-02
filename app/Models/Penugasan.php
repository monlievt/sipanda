<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penugasan extends Model
{
    use SoftDeletes;

    protected $table = 'penugasan';

    protected $fillable = [
        'no_spt', 'pkppt_id', 'penugasan_induk_id', 'is_sesuai_pkppt', 'uraian_penugasan', 'dasar_penugasan',
        'sumber_penugasan_id', 'jenis_penugasan_id', 'tanggal_mulai',
        'tanggal_selesai', 'status', 'progres_persen', 'keterangan_hasil',
        'irban_id', 'dibuat_oleh', 'diperbarui_oleh',
    ];

    protected $casts = [
        'tanggal_mulai'    => 'date',
        'tanggal_selesai'  => 'date',
        'is_sesuai_pkppt'  => 'boolean',
        'progres_persen'   => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function pkppt()
    {
        return $this->belongsTo(Pkppt::class);
    }

    /** Irban Utama (Single legacy fallback) */
    public function irban()
    {
        return $this->belongsTo(Irban::class);
    }

    /** Multi-Irban Penanggung Jawab */
    public function irbans()
    {
        return $this->belongsToMany(Irban::class, 'penugasan_irban');
    }

    /** Parent ST untuk Surat Tugas Perpanjangan */
    public function penugasanInduk()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_induk_id');
    }

    /** List ST Perpanjangan yang menginduk ke ST ini */
    public function stPerpanjangan()
    {
        return $this->hasMany(Penugasan::class, 'penugasan_induk_id');
    }

    public function sumberPenugasan()
    {
        return $this->belongsTo(SumberPenugasan::class);
    }

    public function jenisPenugasan()
    {
        return $this->belongsTo(JenisPenugasan::class);
    }

    public function objekPenugasan()
    {
        return $this->belongsToMany(ObjekPenugasan::class, 'penugasan_objek');
    }

    public function tim()
    {
        return $this->hasMany(PenugasanTim::class);
    }

    public function timUsers()
    {
        return $this->belongsToMany(User::class, 'penugasan_tim', 'penugasan_id', 'user_id')
                    ->withPivot('peran');
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class);
    }

    public function arsipDigital()
    {
        return $this->hasMany(ArsipDigital::class);
    }

    public function laporanHasil()
    {
        return $this->hasMany(LaporanHasil::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function pembuatData()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ─── Scope ────────────────────────────────────────────

    public function scopeTahun($query, $tahun)
    {
        return $query->whereYear('tanggal_mulai', $tahun);
    }

    public function scopeIrban($query, $irbanId)
    {
        return $query->where(function ($q) use ($irbanId) {
            $q->where('irban_id', $irbanId)
              ->orWhereHas('irbans', fn($sub) => $sub->where('irbans.id', $irbanId));
        });
    }

    public function scopeSesuaiPkppt($query)
    {
        return $query->where('is_sesuai_pkppt', true);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ─── Helper ───────────────────────────────────────────

    public function getIrbanListNamesAttribute(): string
    {
        if ($this->irbans->count() > 0) {
            return $this->irbans->pluck('nama_irban')->implode(', ');
        }
        return $this->irban?->nama_irban ?? '-';
    }

    public function isAnggotaTim(User $user): bool
    {
        return $this->tim()->where('user_id', $user->id)->exists();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum_berjalan' => 'Belum Berjalan',
            'berjalan'       => 'Berjalan',
            'selesai'        => 'Selesai',
            default          => $this->status,
        };
    }
}
