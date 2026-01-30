<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Bank extends Model
{
    use Auditable;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mst.bank';

    protected $fillable = [
        'nama_bank',
        'is_active',
    ];
}
