<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiTahunan extends Model
{
    protected $table = 'evaluasi_tahunan';

    protected $fillable = [
        'tahun_evaluasi',
        'irban_id',
        'persen_objek_terealisasi',
        'persen_laporan_tepat_waktu',
        'persen_tindak_lanjut_selesai',
        'catatan_evaluasi',
        'dibuat_oleh',
    ];

    public function irban()
    {
        return $this->belongsTo(Irban::class);
    }

    public function pembuatData()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
