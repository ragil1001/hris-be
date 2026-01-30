<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Jabatan extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.jabatan';

    protected $fillable = [
        'nama_jabatan',
        'is_active',
    ];
}
