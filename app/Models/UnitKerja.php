<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.unit_kerja';

    protected $fillable = [
        'nama_unit',
        'is_active',
    ];
}
