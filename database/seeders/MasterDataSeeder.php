<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Formasi;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\Project;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bank
        Bank::firstOrCreate(
            ['nama_bank' => 'Bank Dummy'],
            ['is_active' => true]
        );

        // Formasi
        Formasi::firstOrCreate(
            ['nama_formasi' => 'Formasi Dummy'],
            ['is_active' => true]
        );

        // Jabatan
        Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Jabatan Dummy'],
            ['is_active' => true]
        );

        // Unit Kerja
        UnitKerja::firstOrCreate(
            ['nama_unit' => 'Unit Kerja Dummy'],
            ['is_active' => true]
        );

        // Project (Penempatan)
        Project::firstOrCreate(
            ['nama_project' => 'Project Dummy'],
            [
                'is_active' => true,
                'tanggal_mulai' => now(),
                'bagian' => 'Bagian Dummy',
                'longitude' => 0.0000000,
                'latitude' => 0.0000000,
                'radius_absensi' => 100,
                'pengecualian_formasi' => json_encode([]),
                'waktu_toleransi' => 15,
            ]
        );
    }
}
