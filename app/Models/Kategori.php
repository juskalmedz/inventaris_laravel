<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'keterangan',
    ];

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'kategori_id');
    }
}
