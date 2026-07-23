<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianRisiko extends Model
{
    protected $table = 'penilaian_risiko';

    protected $fillable = [
        'objek_penugasan_id',
        'tahun_perencanaan',
        'skor_aging',
        'skor_anggaran',
        'skor_temuan',
        'skor_tindak_lanjut_mandek',
        'skor_pengaduan_khusus',
        'skor_total',
        'catatan_penyesuaian_manual',
        'dihitung_pada',
    ];

    protected $casts = [
        'dihitung_pada' => 'datetime',
    ];

    public function objekPenugasan()
    {
        return $this->belongsTo(ObjekPenugasan::class, 'objek_penugasan_id');
    }
}
