<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penugasan extends Model
{
    use SoftDeletes;

    protected $table = 'penugasan';

    protected $fillable = [
        'no_spt', 'tanggal_st', 'pkppt_id', 'penugasan_induk_id', 'jenis_st', 'is_sesuai_pkppt', 'uraian_penugasan', 'dasar_penugasan',
        'sumber_penugasan_id', 'jenis_penugasan_id', 'kelompok_pengawasan_id', 'jenis_pengawasan_spt', 'tanggal_mulai',
        'tanggal_selesai', 'status', 'status_persetujuan', 'catatan_revisi_spt', 'diverifikasi_oleh',
        'diverifikasi_pada', 'progres_persen', 'keterangan_hasil',
        'file_st', 'file_st_nama', 'file_st_diunggah_pada', 'file_st_diunggah_oleh',
        'irban_id', 'dibuat_oleh', 'diperbarui_oleh',
    ];

    protected $casts = [
        'tanggal_st'         => 'date',
        'tanggal_mulai'      => 'date',
        'tanggal_selesai'    => 'date',
        'diverifikasi_pada'  => 'datetime',
        'file_st_diunggah_pada' => 'datetime',
        'is_sesuai_pkppt'    => 'boolean',
        'progres_persen'     => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function kelompokPengawasan()
    {
        return $this->belongsTo(KelompokPengawasan::class, 'kelompok_pengawasan_id');
    }

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
        return $this->hasMany(Penugasan::class, 'penugasan_induk_id')->where('jenis_st', '!=', 'bantuan');
    }

    /** List ST Bantuan yang menginduk ke ST ini */
    public function stBantuan()
    {
        return $this->hasMany(Penugasan::class, 'penugasan_induk_id')->where('jenis_st', 'bantuan');
    }

    /** User yang mengunggah berkas ST resmi bertanda tangan */
    public function fileStDiunggahOleh()
    {
        return $this->belongsTo(User::class, 'file_st_diunggah_oleh');
    }

    public function isBantuan(): bool
    {
        return $this->jenis_st === 'bantuan';
    }

    public function isPerpanjangan(): bool
    {
        return $this->jenis_st === 'perpanjangan' || (!empty($this->penugasan_induk_id) && $this->jenis_st !== 'bantuan');
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

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function tindakLanjutPemantauan()
    {
        return $this->hasMany(TindakLanjut::class, 'st_pemantauan_id');
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

    public function scopeAccessibleBy($query, User $user)
    {
        // 1. Super Admin, Inspektur, Sekretaris, dan Tim Pelaporan memiliki akses penuh ke seluruh penugasan se-Kabupaten
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'pelaporan'])) {
            return $query;
        }

        // 2. Pengguna OPD: hanya penugasan yang mengawasi OPD terkait
        if ($user->isOpd() && $user->objek_penugasan_id) {
            return $query->whereHas('objekPenugasan', fn($sub) => $sub->where('objek_penugasan.id', $user->objek_penugasan_id));
        }

        // 3. Pejabat Irban dan Admin Irban: Melihat seluruh penugasan di bawah unit Irban-nya
        if ($user->hasRole(['irban', 'admin_irban'])) {
            return $query->where(function ($q) use ($user) {
                if ($user->irban_id) {
                    $q->where('irban_id', $user->irban_id)
                      ->orWhereHas('irbans', fn($sub) => $sub->where('irbans.id', $user->irban_id));
                }
                $q->orWhereHas('tim', fn($sub) => $sub->where('user_id', $user->id))
                  ->orWhere('dibuat_oleh', $user->id);
            });
        }

        // 4. Auditor / PPUPD / Sekretariat / Staf Pelaksana lainnya:
        // HANYA melihat penugasan di mana ia terdaftar dalam susunan tim ATAU pembuat data
        return $query->where(function ($q) use ($user) {
            $q->whereHas('tim', fn($sub) => $sub->where('user_id', $user->id))
              ->orWhere('dibuat_oleh', $user->id);
        });
    }

    public function canAccess(User $user): bool
    {
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'pelaporan'])) {
            return true;
        }

        if ($user->isOpd() && $user->objek_penugasan_id) {
            return $this->objekPenugasan()->where('objek_penugasan.id', $user->objek_penugasan_id)->exists();
        }

        // Pejabat Irban dan Admin Irban membawahi seluruh penugasan di bawah unitnya
        if ($user->hasRole(['irban', 'admin_irban'])) {
            if ($user->irban_id && ($this->irban_id == $user->irban_id || $this->irbans()->where('irbans.id', $user->irban_id)->exists())) {
                return true;
            }
        }

        // Auditor, PPUPD, Sekretariat, dan personil biasa: hanya jika terdaftar dalam tim pemeriksa atau pembuat data
        if ($this->isAnggotaTim($user) || $this->dibuat_oleh == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Memeriksa apakah user adalah Pimpinan Tim (Wakil Penanggung Jawab, Pengendali Teknis, atau Ketua Tim)
     */
    public function isPimpinanTim(User $user): bool
    {
        return $this->tim()
            ->where('user_id', $user->id)
            ->whereIn('peran', ['wakil_penanggung_jawab', 'pengendali_teknis', 'ketua_tim'])
            ->exists();
    }

    /**
     * Otorisasi hak menyelesaikan penugasan (Tandai Selesai 100%)
     * Khusus diberikan kepada Admin Irban unit terkait (dan Admin Sistem)
     */
    public function canComplete(User $user): bool
    {
        // 1. Super Admin & Administrator Sistem (backup operasional)
        if ($user->hasRole(['admin', 'administrator'])) {
            return true;
        }

        // 2. Admin Irban pada unit terkait penugasan
        if ($user->hasRole('admin_irban') && ($this->irban_id == $user->irban_id || $this->irbans()->where('irbans.id', $user->irban_id)->exists())) {
            return true;
        }

        return false;
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

    public function getStatusPersetujuanLabelAttribute(): string
    {
        return match($this->status_persetujuan) {
            'draft'      => 'Draft Konsep',
            'diajukan'   => 'Menunggu Verifikasi Irban',
            'disetujui'  => 'Disetujui / Terbit',
            'ditolak'    => 'Perlu Revisi',
            default      => ucfirst($this->status_persetujuan ?? 'Disetujui'),
        };
    }
}
