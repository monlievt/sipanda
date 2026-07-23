<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArsipDigital extends Model {
    protected $table = 'arsip_digital';
    protected $fillable = [
        'penugasan_id','tindak_lanjut_id','bukti_tindak_lanjut_id',
        'nama_file','path_file','ukuran_kb','mime_type','kategori','diunggah_oleh',
    ];
    public function penugasan()         { return $this->belongsTo(Penugasan::class); }
    public function tindakLanjut()      { return $this->belongsTo(TindakLanjut::class); }
    public function buktiTindakLanjut() { return $this->belongsTo(BuktiTindakLanjut::class); }
    public function pengunggah()        { return $this->belongsTo(User::class, 'diunggah_oleh'); }
}

