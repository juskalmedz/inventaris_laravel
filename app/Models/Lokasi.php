<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasis';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'penanggung_jawab',
        'keterangan',
    ];

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'lokasi_id');
    }
}
