<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Formasi;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\Project;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Bank
        Bank::firstOrCreate(
            ['nama_bank' => 'Bank Central Dummy'],
            ['is_active' => true]
        );

        Bank::firstOrCreate(
            ['nama_bank' => 'Bank Nasional Dummy'],
            ['is_active' => true]
        );

        // Formasi
        $formasi1 = Formasi::firstOrCreate(
            ['nama_formasi' => 'Staff Administrasi'],
            ['is_active' => true]
        );

        $formasi2 = Formasi::firstOrCreate(
            ['nama_formasi' => 'Teknisi Lapangan'],
            ['is_active' => true]
        );

        $formasi3 = Formasi::firstOrCreate(
            ['nama_formasi' => 'Supervisor'],
            ['is_active' => true]
        );

        // Jabatan
        $jabatan1 = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Manager Proyek'],
            ['is_active' => true]
        );

        $jabatan2 = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Koordinator Shift'],
            ['is_active' => true]
        );

        $jabatan3 = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Operator'],
            ['is_active' => true]
        );

        // Unit Kerja
        UnitKerja::firstOrCreate(
            ['nama_unit' => 'Unit Operasional Jakarta'],
            ['is_active' => true]
        );

        UnitKerja::firstOrCreate(
            ['nama_unit' => 'Unit Maintenance Surabaya'],
            ['is_active' => true]
        );

        // Project (dengan pengecualian formasi contoh)
        $project1 = Project::firstOrCreate(
            ['nama_project' => 'Proyek Pembangunan Gedung A'],
            [
                'is_active' => true,
                'tanggal_mulai' => Carbon::now()->subMonths(3),
                'bagian' => 'Konstruksi',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius_absensi' => 150,
                'pengecualian_formasi' => json_encode([$formasi3->id]),
                'waktu_toleransi' => 10,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['nama_project' => 'Proyek Pemeliharaan Jalan Tol'],
            [
                'is_active' => true,
                'tanggal_mulai' => Carbon::now()->subMonths(1),
                'bagian' => 'Infrastruktur',
                'latitude' => -7.2504,
                'longitude' => 112.7688,
                'radius_absensi' => 200,
                'pengecualian_formasi' => json_encode([$formasi1->id]),
                'waktu_toleransi' => 15,
            ]
        );

        $project3 = Project::firstOrCreate(
            ['nama_project' => 'Proyek Kebersihan Kawasan Industri'],
            [
                'is_active' => false, // contoh non-aktif
                'tanggal_mulai' => Carbon::now()->subYear(),
                'bagian' => 'Cleaning Service',
                'latitude' => -6.9147,
                'longitude' => 107.6098,
                'radius_absensi' => 100,
                'pengecualian_formasi' => json_encode([]),
                'waktu_toleransi' => 5,
            ]
        );

        // Shifts untuk setiap project
        $shifts = [
            ['kode_shift' => 'P1', 'waktu_mulai' => '07:00:00', 'waktu_selesai' => '15:00:00'],
            ['kode_shift' => 'P2', 'waktu_mulai' => '15:00:00', 'waktu_selesai' => '23:00:00'],
            ['kode_shift' => 'P3', 'waktu_mulai' => '23:00:00', 'waktu_selesai' => '07:00:00'],
        ];

        foreach ([$project1, $project2, $project3] as $project) {
            foreach ($shifts as $shiftData) {
                Shift::firstOrCreate(
                    [
                        'project_id' => $project->id,
                        'kode_shift' => $shiftData['kode_shift'],
                    ],
                    [
                        'waktu_mulai' => $shiftData['waktu_mulai'],
                        'waktu_selesai' => $shiftData['waktu_selesai'],
                    ]
                );
            }
        }
    }
}
