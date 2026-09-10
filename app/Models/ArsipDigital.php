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

    /** Scope penyaringan arsip digital berdasarkan hak akses pengguna */
    public function scopeAccessibleBy($query, User $user)
    {
        // 1. Super Admin, Inspektur, Sekretaris: akses semua arsip digital
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris'])) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            // Berkas yang diunggah oleh user sendiri
            $q->where('diunggah_oleh', $user->id)
              // ATAU berkas terkait penugasan yang memiliki izin akses
              ->orWhereHas('penugasan', function ($pq) use ($user) {
                  $pq->accessibleBy($user);
              });
        });
    }

    /** Cek otorisasi apakah user berhak melihat/mengunduh arsip ini */
    public function canAccess(User $user): bool
    {
        // 1. Super Admin, Inspektur, Sekretaris memiliki akses penuh
        if ($user->hasRole(['admin', 'administrator', 'inspektur', 'sekretaris'])) {
            return true;
        }

        // 2. Pengunggah file sendiri selalu boleh akses
        if ($this->diunggah_oleh == $user->id) {
            return true;
        }

        // 3. Jika berkas tertaut penugasan, ikuti hak akses penugasan (Tim SPT / Irban terkait)
        if ($this->penugasan) {
            return $this->penugasan->canAccess($user);
        }

        return false;
    }
}

