<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Shift extends Model
{
    use HasFactory, Auditable;

    protected $table = 'mst.shift';

    protected $fillable = [
        'project_id',
        'kode_shift',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
