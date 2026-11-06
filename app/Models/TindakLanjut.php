<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TindakLanjut extends Model
{
    use SoftDeletes;

    protected $table = 'tindak_lanjut';
    protected $fillable = [
        'penugasan_id', 'st_pemantauan_id', 'objek_penugasan_id', 'no_lhp', 'judul_lhp', 'tgl_lhp',
        'kode_atribut_temuan_id', 'kode_atribut_rekomendasi_id', 'kode_temuan_lengkap', 'kode_rekomendasi',
        'uraian_temuan', 'rekomendasi', 'nilai_diawasi_rp', 'nilai_rekomendasi_rp', 'berkas_dasar_lhp',
        'status_tindak_lanjut', 'status_telaah', 'hasil_telaah_tim', 'status_rekomendasi_usulan', 'telaah_oleh', 'telaah_pada',
        'catatan_irban', 'irban_disetujui_oleh', 'irban_disetujui_pada',
        'catatan_inspektur', 'inspektur_disetujui_oleh', 'inspektur_disetujui_pada',
        'no_surat_pengantar', 'tgl_surat_pengantar', 'tujuan_surat_pengantar', 'tujuan_surat_objek_id',
        'tanggal_target', 'tanggal_selesai_aktual', 'dibuat_oleh',
    ];
    protected $casts = [
        'tgl_lhp'                => 'date',
        'tgl_surat_pengantar'    => 'date',
        'nilai_diawasi_rp'       => 'float',
        'nilai_rekomendasi_rp'   => 'float',
        'tanggal_target'         => 'date',
        'tanggal_selesai_aktual' => 'date',
        'telaah_pada'            => 'datetime',
        'irban_disetujui_pada'   => 'datetime',
        'inspektur_disetujui_pada'=> 'datetime',
    ];

    public function penugasan()             { return $this->belongsTo(Penugasan::class); }
    public function stPemantauan()          { return $this->belongsTo(Penugasan::class, 'st_pemantauan_id'); }
    public function objekPenugasan()        { return $this->belongsTo(ObjekPenugasan::class, 'objek_penugasan_id'); }
    public function tujuanSuratObjek()      { return $this->belongsTo(ObjekPenugasan::class, 'tujuan_surat_objek_id'); }
    public function kodeAtributTemuan()     { return $this->belongsTo(KodeAtributTemuan::class, 'kode_atribut_temuan_id'); }
    public function kodeAtributRekomendasi(){ return $this->belongsTo(KodeAtributRekomendasi::class, 'kode_atribut_rekomendasi_id'); }
    public function pembuatData()           { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penelaah()              { return $this->belongsTo(User::class, 'telaah_oleh'); }
    public function irbanPenyetuju()        { return $this->belongsTo(User::class, 'irban_disetujui_oleh'); }
    public function inspekturPenyetuju()    { return $this->belongsTo(User::class, 'inspektur_disetujui_oleh'); }
    public function buktiTindakLanjut()     { return $this->hasMany(BuktiTindakLanjut::class); }
    public function arsipDigital()          { return $this->hasMany(ArsipDigital::class, 'tindak_lanjut_id'); }
    public function rincianPenyetoran()     { return $this->hasMany(RincianPenyetoranTl::class, 'tindak_lanjut_id'); }

    public function getNamaObjekSasaranAttribute(): string
    {
        if ($this->objekPenugasan) {
            return $this->objekPenugasan->nama;
        }
        if ($this->penugasan && $this->penugasan->objekPenugasan->isNotEmpty()) {
            return $this->penugasan->objekPenugasan->pluck('nama')->implode(', ');
        }
        return '-';
    }

    public function getTotalSetorRpAttribute(): float
    {
        return (float) $this->rincianPenyetoran()->sum('nilai_setor_rp');
    }

    public function getFormattedNilaiRpAttribute(): string
    {
        if (! $this->nilai_rekomendasi_rp || $this->nilai_rekomendasi_rp <= 0) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->nilai_rekomendasi_rp, 0, ',', '.');
    }

    public function getFormattedTotalSetorAttribute(): string
    {
        $total = $this->total_setor_rp;
        if (! $total || $total <= 0) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_tindak_lanjut) {
            'selesai'             => 'Sesuai',
            'proses'              => 'Belum Sesuai',
            'menunggu_verifikasi' => 'Belum Sesuai (Menunggu Verifikasi)',
            'belum'               => 'Belum Ditindaklanjuti',
            'tdt'                 => 'Tidak Dapat Ditindaklanjuti (TDT)',
            default               => 'Belum Sesuai',
        };
    }

    public function getStatusUsulanLabelAttribute(): string
    {
        return match($this->status_rekomendasi_usulan) {
            'selesai'             => 'Sesuai (Usulan Tim)',
            'proses'              => 'Belum Sesuai (Usulan Tim)',
            'belum'               => 'Belum Ditindaklanjuti (Usulan Tim)',
            'tdt'                 => 'Tidak Dapat Ditindaklanjuti (Usulan Tim)',
            default               => '-',
        };
    }

    public function getStatusTelaahLabelAttribute(): string
    {
        return match($this->status_telaah) {
            'draft'               => 'Draft Telaah Tim',
            'diajukan_irban'      => 'Diajukan ke Irban',
            'revisi_irban', 'ditolak_irban' => 'Revisi dari Irban',
            'diajukan_inspektur'  => 'Diusulkan ke Inspektur',
            'revisi_inspektur', 'ditolak_inspektur' => 'Revisi dari Inspektur',
            'disetujui_inspektur' => 'Disetujui Inspektur (Final)',
            default               => 'Draft Telaah',
        };
    }

    public function isSiapGenerateDokumen(): bool
    {
        return $this->status_telaah === 'disetujui_inspektur';
    }

    public function isDisetujuiInspektur(): bool
    {
        return $this->status_telaah === 'disetujui_inspektur';
    }

    /**
     * Scope untuk membatasi data Tindak Lanjut berdasarkan hak akses pengguna
     */
    public function scopeAccessibleBy($query, User $user)
    {
        // 1. Super Admin, Inspektur, Sekretaris, dan Tim Pelaporan: Akses penuh ke seluruh matriks se-Kabupaten
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'pelaporan'])) {
            return $query;
        }

        // 2. Pengguna OPD: hanya TL yang bersangkutan dengan OPD terkait
        if ($user->isOpd() && $user->objek_penugasan_id) {
            return $query->where(function ($q) use ($user) {
                $q->where('objek_penugasan_id', $user->objek_penugasan_id)
                  ->orWhereHas('penugasan.objekPenugasan', fn($op) => $op->where('objek_penugasan.id', $user->objek_penugasan_id));
            });
        }

        // 3. Pejabat Irban dan Admin Irban: Menampilkan seluruh matriks TL di bawah unit Irban-nya
        if ($user->hasRole(['irban', 'admin_irban'])) {
            return $query->where(function ($q) use ($user) {
                if ($user->irban_id) {
                    $q->whereHas('penugasan', function ($pq) use ($user) {
                        $pq->where('irban_id', $user->irban_id)
                           ->orWhereHas('irbans', fn($ib) => $ib->where('irbans.id', $user->irban_id));
                    })
                    ->orWhereHas('stPemantauan', function ($sq) use ($user) {
                        $sq->where('irban_id', $user->irban_id)
                           ->orWhereHas('irbans', fn($ib) => $ib->where('irbans.id', $user->irban_id));
                    })
                    ->orWhereHas('pembuatData', function ($uq) use ($user) {
                        $uq->where('irban_id', $user->irban_id);
                    });
                }
                $q->orWhere('dibuat_oleh', $user->id)
                  ->orWhere('telaah_oleh', $user->id);
            });
        }

        // 4. Auditor / PPUPD / Anggota Tim Pengawasan:
        // HANYA menampilkan matriks TL sesuai penugasan yang diampunya / terdaftar dalam susunan tim
        return $query->where(function ($q) use ($user) {
            $q->whereHas('penugasan', function ($pq) use ($user) {
                $pq->whereHas('tim', fn($tq) => $tq->where('user_id', $user->id))
                   ->orWhere('dibuat_oleh', $user->id);
            })
            ->orWhereHas('stPemantauan', function ($sq) use ($user) {
                $sq->whereHas('tim', fn($tq) => $tq->where('user_id', $user->id))
                   ->orWhere('dibuat_oleh', $user->id);
            })
            ->orWhere('dibuat_oleh', $user->id)
            ->orWhere('telaah_oleh', $user->id);
        });
    }

    /**
     * Memeriksa apakah user berhak mengakses record tindak lanjut ini
     */
    public function canAccess(User $user): bool
    {
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris', 'pelaporan'])) {
            return true;
        }

        if ($user->isOpd() && $user->objek_penugasan_id) {
            return $this->objek_penugasan_id == $user->objek_penugasan_id
                || ($this->penugasan && $this->penugasan->objekPenugasan()->where('objek_penugasan.id', $user->objek_penugasan_id)->exists());
        }

        if ($user->hasRole(['irban', 'admin_irban'])) {
            if ($user->irban_id) {
                if ($this->penugasan && ($this->penugasan->irban_id == $user->irban_id || $this->penugasan->irbans()->where('irbans.id', $user->irban_id)->exists())) {
                    return true;
                }
                if ($this->stPemantauan && ($this->stPemantauan->irban_id == $user->irban_id || $this->stPemantauan->irbans()->where('irbans.id', $user->irban_id)->exists())) {
                    return true;
                }
                if ($this->pembuatData && $this->pembuatData->irban_id == $user->irban_id) {
                    return true;
                }
            }
            if ($this->dibuat_oleh == $user->id || $this->telaah_oleh == $user->id) {
                return true;
            }
        }

        // Auditor / PPUPD / lainnya
        if ($this->dibuat_oleh == $user->id || $this->telaah_oleh == $user->id) {
            return true;
        }
        if ($this->penugasan && ($this->penugasan->isAnggotaTim($user) || $this->penugasan->dibuat_oleh == $user->id)) {
            return true;
        }
        if ($this->stPemantauan && ($this->stPemantauan->isAnggotaTim($user) || $this->stPemantauan->dibuat_oleh == $user->id)) {
            return true;
        }

        return false;
    }
}
