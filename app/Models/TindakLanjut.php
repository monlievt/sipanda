<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TindakLanjut extends Model
{
    use SoftDeletes;

    protected $table = 'tindak_lanjut';
    protected $fillable = [
        'penugasan_id', 'objek_penugasan_id', 'no_lhp', 'judul_lhp', 'tgl_lhp',
        'uraian_temuan', 'rekomendasi', 'nilai_diawasi_rp', 'nilai_rekomendasi_rp', 'berkas_dasar_lhp',
        'status_tindak_lanjut', 'tanggal_target', 'tanggal_selesai_aktual', 'dibuat_oleh',
    ];
    protected $casts = [
        'tgl_lhp'                => 'date',
        'nilai_diawasi_rp'       => 'float',
        'nilai_rekomendasi_rp'   => 'float',
        'tanggal_target'         => 'date',
        'tanggal_selesai_aktual' => 'date',
    ];

    public function penugasan()      { return $this->belongsTo(Penugasan::class); }
    public function objekPenugasan() { return $this->belongsTo(ObjekPenugasan::class, 'objek_penugasan_id'); }
    public function pembuatData()    { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function buktiTindakLanjut() { return $this->hasMany(BuktiTindakLanjut::class); }
    public function arsipDigital()   { return $this->hasMany(ArsipDigital::class, 'tindak_lanjut_id'); }
    public function rincianPenyetoran() { return $this->hasMany(RincianPenyetoranTl::class, 'tindak_lanjut_id'); }

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
}
