<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_barang',
        'barcode',
        'nama_barang',
        'kategori_id',
        'lokasi_id',
        'jenis_aset',
        'stok',
        'kondisi',
        'status',
        'harga_perkiraan',
        'deskripsi',
        'qr_code_data',
    ];

    public function getBarcodeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return '899' . str_pad($this->id, 9, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'stok' => 'integer',
        'harga_perkiraan' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'inventaris_id');
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'inventaris_id');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'inventaris_id');
    }

    public function mutasis()
    {
        return $this->hasMany(Mutasi::class, 'inventaris_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'inventaris_id');
    }
}
