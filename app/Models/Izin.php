<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Izin extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.izin';

    protected $fillable = [
        'kategori',
        'sub_kategori',
        'jumlah_hari',
        'is_active',
    ];
}
