<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Konsultasi extends Model
{
    use SoftDeletes;

    protected $table = 'konsultasi';

    protected $fillable = [
        'nomor_tiket', 'user_id', 'objek_penugasan_id', 'irban_id',
        'area_konsultasi', 'judul_permasalahan', 'uraian_permasalahan',
        'berkas_pendukung', 'catatan_disposisi_inspektur', 'disposisi_inspektur_oleh', 'disposisi_inspektur_pada',
        'preferensi_metode', 'metode_disetujui',
        'tanggal_tatap_muka', 'lokasi_tatap_muka', 'status',
        'kesimpulan_advis', 'berita_acara_pdf', 'is_faq_public', 'dibuat_oleh'
    ];

    protected $casts = [
        'tanggal_tatap_muka'        => 'datetime',
        'disposisi_inspektur_pada'  => 'datetime',
        'is_faq_public'             => 'boolean',
    ];

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspekturPemberiDisposisi()
    {
        return $this->belongsTo(User::class, 'disposisi_inspektur_oleh');
    }

    public function objekPenugasan()
    {
        return $this->belongsTo(ObjekPenugasan::class, 'objek_penugasan_id');
    }

    public function irban()
    {
        return $this->belongsTo(Irban::class, 'irban_id');
    }

    public function timUsers()
    {
        return $this->belongsToMany(User::class, 'konsultasi_tim', 'konsultasi_id', 'user_id')
                    ->withPivot('peran');
    }

    public function tim()
    {
        return $this->hasMany(KonsultasiTim::class, 'konsultasi_id');
    }

    public function chats()
    {
        return $this->hasMany(KonsultasiChat::class, 'konsultasi_id')->orderBy('created_at', 'asc');
    }

    public function getTopikAttribute(): string
    {
        return $this->judul_permasalahan ?: 'Konsultasi APIP';
    }

    public function getKategoriAttribute(): string
    {
        return $this->area_konsultasi ?: 'Umum';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'menunggu_disposisi' || $this->status === 'menunggu_disposisi_inspektur') {
            return $this->disposisi_inspektur_pada ? 'Menunggu Penugasan Tim Irban' : 'Menunggu Arahan Inspektur';
        }

        return match($this->status) {
            'menunggu_penugasan_tim' => 'Menunggu Penugasan Tim Irban',
            'berjalan'               => 'Sedang Berjalan',
            'selesai'                => 'Selesai (BA Terbit)',
            'ditolak'                => 'Ditolak',
            default                  => $this->status,
        };
    }
}
