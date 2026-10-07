<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 'mutasis';

    protected $fillable = [
        'nomor_mutasi',
        'tanggal_permohonan',
        'inventaris_id',
        'lokasi_awal_id',
        'lokasi_baru_id',
        'departemen_baru_id',
        'karyawan_id',
        'created_by_user_id',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'alasan',
        'status',
        'catatan_approval',
        'keterangan',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function lokasiAwal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_awal_id');
    }

    public function lokasiBaru()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_baru_id');
    }

    public function departemenBaru()
    {
        return $this->belongsTo(Departemen::class, 'departemen_baru_id');
    }

    // Aliases for compatibility
    public function lokasiAsal()
    {
        return $this->lokasiAwal();
    }

    public function lokasiTujuan()
    {
        return $this->lokasiBaru();
    }

    public function departemenTujuan()
    {
        return $this->departemenBaru();
    }

    public function pemohon()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
