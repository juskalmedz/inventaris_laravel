<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'nama',
        'departemen',
        'jabatan',
        'email',
        'no_hp',
        'status',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'karyawan_id');
    }

    public function mutasis()
    {
        return $this->hasMany(Mutasi::class, 'karyawan_id');
    }
}
