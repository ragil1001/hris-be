<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.training';

    protected $fillable = [
        'karyawan_id',
        'nama_training',
        'urutan_training',
        'tahun',
        'keterangan',
    ];
}
