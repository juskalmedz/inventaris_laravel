<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopConfig extends Model
{
    use HasFactory;

    protected $table = 'kop_configs';

    protected $fillable = [
        'org_name',
        'div_name',
        'nomor_surat',
        'approver_title',
        'approver_name',
        'approver_nip',
        'maker_title',
        'maker_name',
        'maker_nip',
    ];
}
