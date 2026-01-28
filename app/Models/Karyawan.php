<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    protected $table = 'mst.karyawan';

    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'tanggal_masuk',
        'tanggal_resign',
        'tanggal_aktif',
        'status_karyawan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'golongan_darah',
        'no_telepon',
        'no_wa',
        'email',
        'alamat',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'kode_pos',
        'no_ktp',
        'no_kk',
        'no_bpjs_kesehatan',
        'npp_bpjs_kesehatan',
        'bu_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
        'npp_bpjs_ketenagakerjaan',
        'no_npwp',
        'status_pernikahan',
        'jumlah_anak',
        'pendidikan_terakhir',
        'nama_institusi',
        'tahun_lulus',
        'no_rekening',
        'nama_rekening',
        'bank_id',
        'gaji_pokok',
        'insentif',
        'uang_makan',
        'potongan',
        'formasi_id',
        'jabatan_id',
        'unit_kerja_id',
        'penempatan_id',
        'awal_mulai_cuti',
        'masa_berlaku_cuti',
        'potongan_cuti_bersama',
        'sisa_cuti',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_resign' => 'date',
        'tanggal_aktif' => 'date',
        'tanggal_lahir' => 'date',
        'awal_mulai_cuti' => 'date',
        'masa_berlaku_cuti' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function formasi(): BelongsTo
    {
        return $this->belongsTo(Formasi::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'penempatan_id');
    }

    public function keluarga(): HasMany
    {
        return $this->hasMany(Keluarga::class);
    }

    public function ayahIbu(): HasOne
    {
        return $this->hasOne(AyahIbu::class);
    }

    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->age : null;
    }

    public function getLamaKerjaAttribute(): string
    {
        $start = Carbon::parse($this->tanggal_masuk);
        $end = $this->tanggal_resign ? Carbon::parse($this->tanggal_resign) : Carbon::now();
        $years = $start->diffInYears($end);
        $months = $start->diffInMonths($end) % 12;
        return "{$years} tahun {$months} bulan";
    }

    public function getStatusAttribute(): string
    {
        return $this->deleted_at ? 'NONAKTIF' : 'AKTIF';
    }
}
