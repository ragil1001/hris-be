<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.project';

    protected $fillable = [
        'nama_project',
        'is_active',
        'tanggal_mulai',
        'bagian',
        'longitude',
        'latitude',
        'radius_absensi',
        'pengecualian_formasi',
        'waktu_toleransi',
    ];

    protected $casts = [
        'pengecualian_formasi' => 'array',
    ];
}
