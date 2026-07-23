<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapasitasSdm extends Model
{
    protected $table = 'kapasitas_sdm';

    protected $fillable = [
        'irban_id',
        'tahun_perencanaan',
        'jumlah_hari_tersedia',
        'catatan',
    ];

    public function irban()
    {
        return $this->belongsTo(Irban::class);
    }
}
