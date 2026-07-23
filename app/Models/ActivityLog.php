<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $table = 'activity_log';
    public $timestamps = false;
    protected $fillable = ['user_id','tabel','record_id','aksi','data_sebelum','data_sesudah','ip_address'];
    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
        'created_at'   => 'datetime',
    ];
    public function user() { return $this->belongsTo(User::class); }

    /** Log perubahan data secara otomatis */
    public static function catat(string $tabel, int $recordId, string $aksi, ?array $sebelum = null, ?array $sesudah = null): void
    {
        static::create([
            'user_id'      => auth()->id(),
            'tabel'        => $tabel,
            'record_id'    => $recordId,
            'aksi'         => $aksi,
            'data_sebelum' => $sebelum,
            'data_sesudah' => $sesudah,
            'ip_address'   => request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
