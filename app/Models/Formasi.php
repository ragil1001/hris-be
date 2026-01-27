<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formasi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.formasi';

    protected $fillable = [
        'nama_formasi',
        'is_active',
    ];
}
