<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AyahIbu extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.ayah_ibu';

    protected $fillable = [
        'karyawan_id',
        'nama_ayah',
        'nama_ibu',
    ];
}
