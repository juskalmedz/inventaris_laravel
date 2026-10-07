<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenances';

    protected $fillable = [
        'tiket',
        'inventaris_id',
        'tanggal_lapor',
        'tanggal_selesai',
        'kondisi',
        'deskripsi',
        'teknisi',
        'biaya',
        'status',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }
}
