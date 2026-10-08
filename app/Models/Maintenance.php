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
        'nomor_tiket',
        'inventaris_id',
        'jenis_maintenance',
        'tanggal_mulai',
        'tanggal_lapor',
        'tanggal_selesai',
        'kondisi',
        'deskripsi',
        'deskripsi_masalah',
        'teknisi',
        'vendor',
        'biaya',
        'estimasi_biaya',
        'biaya_aktual',
        'tindakan',
        'tindakan_perbaikan',
        'status',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'estimasi_biaya' => 'decimal:2',
        'biaya_aktual' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            // Sinkronisasi otomatis pasangan atribut jika salah satu tidak diisi
            if (empty($model->nomor_tiket) && !empty($model->tiket)) {
                $model->nomor_tiket = $model->tiket;
            } elseif (empty($model->tiket) && !empty($model->nomor_tiket)) {
                $model->tiket = $model->nomor_tiket;
            }

            if (empty($model->vendor) && !empty($model->teknisi)) {
                $model->vendor = $model->teknisi;
            } elseif (empty($model->teknisi) && !empty($model->vendor)) {
                $model->teknisi = $model->vendor;
            }

            if (empty($model->deskripsi_masalah) && !empty($model->deskripsi)) {
                $model->deskripsi_masalah = $model->deskripsi;
            } elseif (empty($model->deskripsi) && !empty($model->deskripsi_masalah)) {
                $model->deskripsi = $model->deskripsi_masalah;
            }

            if (empty($model->tanggal_mulai) && !empty($model->tanggal_lapor)) {
                $model->tanggal_mulai = $model->tanggal_lapor;
            } elseif (empty($model->tanggal_lapor) && !empty($model->tanggal_mulai)) {
                $model->tanggal_lapor = $model->tanggal_mulai;
            }

            if (empty($model->biaya_aktual) && !empty($model->biaya)) {
                $model->biaya_aktual = $model->biaya;
            } elseif (empty($model->biaya) && !empty($model->biaya_aktual)) {
                $model->biaya = $model->biaya_aktual;
            }

            if (empty($model->estimasi_biaya) && !empty($model->biaya)) {
                $model->estimasi_biaya = $model->biaya;
            }
        });
    }

    // Accessor Fallbacks
    public function getNomorTiketAttribute($value)
    {
        return $value ?: ($this->attributes['tiket'] ?? 'MTN-' . $this->id);
    }

    public function getTiketAttribute($value)
    {
        return $value ?: ($this->attributes['nomor_tiket'] ?? 'MTN-' . $this->id);
    }

    public function getBiayaAktualAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return (float) $value;
        }
        return (float) ($this->attributes['biaya'] ?? $this->attributes['estimasi_biaya'] ?? 0);
    }

    public function getEstimasiBiayaAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return (float) $value;
        }
        return (float) ($this->attributes['biaya'] ?? 0);
    }

    public function getVendorAttribute($value)
    {
        return $value ?: ($this->attributes['teknisi'] ?? '-');
    }

    public function getTeknisiAttribute($value)
    {
        return $value ?: ($this->attributes['vendor'] ?? '-');
    }

    public function getDeskripsiMasalahAttribute($value)
    {
        return $value ?: ($this->attributes['deskripsi'] ?? '-');
    }

    public function getTanggalMulaiAttribute($value)
    {
        return $value ?: ($this->attributes['tanggal_lapor'] ?? '-');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }
}
