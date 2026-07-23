<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianPenyetoranTl extends Model
{
    protected $table = 'rincian_penyetoran_tl';

    protected $fillable = [
        'tindak_lanjut_id',
        'mata_uang',
        'nilai_setor_rp',
        'nama_bank',
        'no_referensi_ntpn',
        'tgl_setor',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'nilai_setor_rp' => 'float',
        'tgl_setor'      => 'date',
    ];

    public function tindakLanjut()
    {
        return $this->belongsTo(TindakLanjut::class, 'tindak_lanjut_id');
    }

    public function pembuatData()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getFormattedNilaiSetorAttribute(): string
    {
        return 'Rp ' . number_format($this->nilai_setor_rp, 0, ',', '.');
    }
}
