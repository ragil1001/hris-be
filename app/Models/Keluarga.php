<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Keluarga extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.keluarga';

    protected $fillable = [
        'karyawan_id',
        'hubungan',
        'nama',
        'no_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'bpjs_kesehatan',
        'urutan_anak',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
