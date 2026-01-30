<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Formasi extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.formasi';

    protected $fillable = [
        'nama_formasi',
        'is_active',
    ];
}
