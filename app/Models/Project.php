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
        'waktu_toleransi',
    ];

    protected $appends = ['status'];

    protected $casts = [
        'is_active' => 'boolean',
        'radius_absensi' => 'integer',
        'waktu_toleransi' => 'integer',
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'AKTIF' : 'NONAKTIF';
    }

    public function izins()
    {
        return $this->belongsToMany(Izin::class, 'mst.project_izin', 'project_id', 'izin_id');
    }

    public function formasis()
    {
        return $this->belongsToMany(Formasi::class, 'mst.project_formasi', 'project_id', 'formasi_id')
            ->withPivot('is_active');
    }
}
