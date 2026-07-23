<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BuktiTindakLanjut extends Model {
    protected $table = 'bukti_tindak_lanjut';
    protected $fillable = [
        'tindak_lanjut_id','diunggah_oleh','catatan_opd',
        'status_verifikasi','catatan_verifikasi','diverifikasi_oleh','diverifikasi_pada',
    ];
    protected $casts = ['diverifikasi_pada' => 'datetime'];

    public function tindakLanjut()    { return $this->belongsTo(TindakLanjut::class); }
    public function pengunggah()      { return $this->belongsTo(User::class, 'diunggah_oleh'); }
    public function verifikator()     { return $this->belongsTo(User::class, 'diverifikasi_oleh'); }
    public function arsipDigital()    { return $this->hasMany(ArsipDigital::class, 'bukti_tindak_lanjut_id'); }

    public function scopeMenunggu($q) { return $q->where('status_verifikasi', 'menunggu'); }
}
