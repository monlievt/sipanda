<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkhtisarLaporan extends Model
{
    use HasFactory;

    protected $table = 'ikhtisar_laporan';

    protected $fillable = [
        'tahun',
        'periode',
        'judul',
        'nomor_surat',
        'tanggal_laporan',
        'tanggal_awal_periode',
        'tanggal_akhir_periode',
        'simpulan',
        'hambatan',
        'rekomendasi',
        'catatan_khusus',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_laporan'       => 'date',
        'tanggal_awal_periode'  => 'date',
        'tanggal_akhir_periode' => 'date',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getPeriodeLabelAttribute(): string
    {
        return match($this->periode) {
            'triwulan_1' => 'Triwulan I (Januari – Maret)',
            'triwulan_2' => 'Triwulan II (April – Juni)',
            'triwulan_3' => 'Triwulan III (Juli – September)',
            'triwulan_4' => 'Triwulan IV (Oktober – Desember)',
            'semester_1' => 'Semester I (Januari – Juni)',
            'semester_2' => 'Semester II (Juli – Desember)',
            'tahunan'    => 'Tahunan (Januari – Desember)',
            default      => ucfirst(str_replace('_', ' ', $this->periode)),
        };
    }
}
