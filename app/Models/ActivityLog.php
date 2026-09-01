<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $table = 'activity_log';
    public $timestamps = false;
    protected $fillable = ['user_id','tabel','record_id','aksi','data_sebelum','data_sesudah','ip_address','created_at'];
    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
        'created_at'   => 'datetime',
    ];
    public function user() { return $this->belongsTo(User::class); }

    /**
     * Catat perubahan data ke audit log.
     * Otomatis mendeteksi user yang sedang login (guard web atau opd).
     * Jika tidak ada sesi aktif (mis. dari Scheduler/Console), user_id disimpan null.
     */
    public static function catat(string $tabel, int $recordId, string $aksi, ?array $sebelum = null, ?array $sesudah = null): void
    {
        // Coba ambil user dari guard web (internal), fallback ke guard opd
        $userId = auth()->id() ?? auth()->guard('opd')->id() ?? null;

        static::create([
            'user_id'      => $userId,
            'tabel'        => $tabel,
            'record_id'    => $recordId,
            'aksi'         => $aksi,
            'data_sebelum' => $sebelum,
            'data_sesudah' => $sesudah,
            'ip_address'   => request()?->ip(),
            'created_at'   => now(),
        ]);
    }
}
