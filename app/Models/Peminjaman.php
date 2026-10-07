<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'nomor_pinjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'rencana_kembali',
        'karyawan_id',
        'inventaris_id',
        'jumlah',
        'status',
        'keperluan',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'catatan',
        'approved_by',
        'approved_at',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'Dipinjam') return false;
        $deadline = Carbon::parse($this->tanggal_kembali ?? $this->rencana_kembali);
        return Carbon::now()->startOfDay()->gt($deadline->startOfDay());
    }
}
