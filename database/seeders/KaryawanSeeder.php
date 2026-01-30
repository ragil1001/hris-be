<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\Bank;
use App\Models\Jabatan;
use App\Models\Formasi;
use App\Models\UnitKerja;
use App\Models\Project;
use App\Models\Keluarga;
use App\Models\AyahIbu;
use Carbon\Carbon;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        Karyawan::unsetEventDispatcher();
        User::unsetEventDispatcher();

        $employeeRole = Role::where('name', 'employee')->first();
        if (!$employeeRole) {
            $employeeRole = Role::create(['name' => 'employee', 'display_name' => 'Employee', 'is_active' => true]);
        }

        $banks = Bank::all();
        $units = UnitKerja::all();
        $projects = Project::all();
        $activeProjects = $projects->where('is_active', true);
        $inactiveProjects = $projects->where('is_active', false);

        // Pre-paired Jabatan & Formasi (Logic matching Seeder)
        $pairs = [
            ['jabatan' => 'Project Manager', 'formasi' => 'Management'],
            ['jabatan' => 'Leader Cleaning Service', 'formasi' => 'Cleaning Service'],
            ['jabatan' => 'Cleaner', 'formasi' => 'Cleaning Service'],
            ['jabatan' => 'Leader Security', 'formasi' => 'Security'],
            ['jabatan' => 'Security Guard', 'formasi' => 'Security'],
            ['jabatan' => 'Leader Gondola', 'formasi' => 'Gondola'],
            ['jabatan' => 'Teknisi Gondola', 'formasi' => 'Gondola'],
            ['jabatan' => 'Leader Gardener', 'formasi' => 'Gardener'],
            ['jabatan' => 'Tukang Kebun', 'formasi' => 'Gardener'],
            ['jabatan' => 'Leader Pest Control', 'formasi' => 'Pest Control'],
            ['jabatan' => 'Teknisi Pest Control', 'formasi' => 'Pest Control'],
            ['jabatan' => 'Admin Project', 'formasi' => 'Administrasi'],
            ['jabatan' => 'Receptionist', 'formasi' => 'Front Office'],
            ['jabatan' => 'Chief Engineering', 'formasi' => 'Engineering'],
            ['jabatan' => 'Teknisi ME', 'formasi' => 'Engineering'],
        ];

        // 33 Realistic Names
        $names = [
            'Budi Santoso', 'Siti Aminah', 'Rudi Hartono', 'Dewi Sartika', 'Agus Salim',
            'Rina Marlina', 'Joko Widodo', 'Megawati', 'Susilo Bambang', 'Abdurrahman Wahid',
            'B.J. Habibie', 'Soeharto', 'Soekarno', 'Hatta Rajasa', 'Sri Mulyani',
            'Erick Thohir', 'Sandiaga Uno', 'Prabowo Subianto', 'Ganjar Pranowo', 'Anies Baswedan',
            'Ridwan Kamil', 'Khofifah Indar', 'Tri Rismaharini', 'Basuki Tjahaja', 'Dedi Mulyadi',
            'Ahmad Dhani', 'Ari Lasso', 'Judika', 'Raisa Andriana', 'Isyana Sarasvati',
            'Tulus', 'Glenn Fredly', 'Tompi'
        ];

        foreach ($names as $index => $name) {
            try {
                // Generate basic data
                $dob = Carbon::now()->subYears(rand(20, 50))->subDays(rand(0, 365));
                $dobString = $dob->format('dmY'); // Password
                $nik = 'NIK' . sprintf('%05d', $index + 1);
                
                // Create User
                $user = User::firstOrCreate(
                    ['username' => $nik],
                    [
                        'name' => $name,
                        'password' => Hash::make($dobString),
                        'role_id' => $employeeRole->id,
                        'is_active' => true,
                    ]
                );

                // Determine Status (90% Active, 10% Resign)
                $isActive = rand(1, 10) <= 9;
                $status = $isActive ? 'AKTIF' : 'RESIGN';
                
                // Random Pair
                $pair = $pairs[array_rand($pairs)];
                $jabatan = Jabatan::where('nama_jabatan', $pair['jabatan'])->first();
                $formasi = Formasi::where('nama_formasi', $pair['formasi'])->first();

                // Assign Unit (Random)
                $unit = $units->isNotEmpty() ? $units->random() : null;

                // Assign Project
                $project = null;
                if ($isActive) {
                    $dice = rand(1, 100);
                    if ($dice <= 70 && $activeProjects->count() > 0) {
                        $project = $activeProjects->random();
                    } elseif ($dice <= 80 && $inactiveProjects->count() > 0) {
                        $project = $inactiveProjects->random();
                    }
                    // 20% None / HQ
                }

                // Create Karyawan
                $karyawan = Karyawan::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'user_id' => $user->id,
                        'nama' => $name,
                        'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                        'no_telepon' => '08' . rand(1000000000, 9999999999),
                        'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                        'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Yogyakarta'][rand(0, 4)],
                        'tanggal_lahir' => $dob->format('Y-m-d'),
                        'agama' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'][rand(0, 4)],
                        'golongan_darah' => ['A', 'B', 'AB', 'O'][rand(0, 3)],
                        'status_pernikahan' => rand(0, 1) ? 'MENIKAH' : 'BELUM_MENIKAH',
                        'pendidikan_terakhir' => ['SMA', 'SMK', 'D3', 'S1', 'S2'][rand(0, 4)],
                        
                        'jabatan_id' => $jabatan ? $jabatan->id : null,
                        'formasi_id' => $formasi ? $formasi->id : null,
                        'unit_kerja_id' => $unit ? $unit->id : null,
                        'penempatan_id' => $project ? $project->id : null,
                        'bu' => 'PT. Cleaning Service Indonesia',
                        
                        'status' => $status,
                        'control_status' => 'KONTRAK',
                        'tanggal_masuk' => Carbon::now()->subMonths(rand(1, 60))->format('Y-m-d'),
                        'tanggal_aktif' => Carbon::now()->subMonths(rand(1, 60))->format('Y-m-d'),
                        'tanggal_resign' => $status === 'RESIGN' ? Carbon::now()->subMonths(rand(1,6))->format('Y-m-d') : null,
                        
                        'gaji_pokok' => rand(3000000, 10000000),
                        'insentif' => rand(0, 1000000),
                        'uang_makan' => rand(500000, 1500000),
                        'bank_id' => $banks->isNotEmpty() ? $banks->random()->id : null,
                        'no_rekening' => rand(1000000000, 9999999999),
                        
                        'alamat' => 'Jl. Kebahagiaan No. ' . rand(1, 100),
                        'rt' => sprintf('%03d', rand(1, 20)),
                        'rw' => sprintf('%03d', rand(1, 10)),
                        'kelurahan' => 'Grogol',
                        'kecamatan' => 'Grogol Petamburan',
                        'kabupaten_kota' => 'Jakarta Barat',
                        'kode_pos' => '11450',
                        
                        'no_ktp' => '3171' . rand(100000000000, 999999999999),
                        'no_kk' => '3171' . rand(100000000000, 999999999999),
                        'no_bpjs_kesehatan' => '000' . rand(1000000000, 9999999999),
                        'bu_bpjs_kesehatan' => 'Kesehatan Utama',
                        'no_bpjs_ketenagakerjaan' => '000' . rand(10000000, 99999999),
                        'npp_bpjs_ketenagakerjaan' => 'JM' . rand(1000, 9999),
                        'no_jaminan_pensiun' => 'JP' . rand(10000000000, 99999999999),

                         'sisa_cuti' => 12,
                         'potongan_cuti_bersama' => 0,
                         'keterangan' => 'Karyawan Tetap',
                    ]
                );

                // Create Family Data (Spouse & Children)
                if ($karyawan->status_pernikahan === 'MENIKAH') {
                    // Spouse
                    $spouseGender = $karyawan->jenis_kelamin === 'L' ? 'ISTRI' : 'SUAMI';
                    Keluarga::firstOrCreate(
                        [
                            'karyawan_id' => $karyawan->id,
                            'hubungan' => $spouseGender
                        ],
                        [
                            'nama' => 'Pasangan of ' . $name,
                            'no_ktp' => '3171' . rand(100000000000, 999999999999),
                            'tempat_lahir' => 'Jakarta',
                            'tanggal_lahir' => Carbon::parse($karyawan->tanggal_lahir)->addYears(rand(-5, 5))->format('Y-m-d'),
                            'bpjs_kesehatan' => '000' . rand(1000000000, 9999999999),
                        ]
                    );

                    // Children (0-3)
                    $numChildren = rand(0, 3);
                    for ($i = 1; $i <= $numChildren; $i++) {
                        Keluarga::firstOrCreate(
                            [
                                'karyawan_id' => $karyawan->id,
                                'hubungan' => 'ANAK',
                                'urutan_anak' => $i
                            ],
                            [
                                'nama' => 'Child ' . $i . ' of ' . $name,
                                'no_ktp' => '3171' . rand(100000000000, 999999999999),
                                'tempat_lahir' => 'Jakarta',
                                'tanggal_lahir' => Carbon::now()->subYears(rand(1, 15))->format('Y-m-d'),
                                'bpjs_kesehatan' => '000' . rand(1000000000, 9999999999),
                            ]
                        );
                    }
                }

                // Create Ayah/Ibu
                AyahIbu::firstOrCreate(
                    ['karyawan_id' => $karyawan->id],
                    [
                        'nama_ayah' => 'Father of ' . $name,
                        'nama_ibu' => 'Mother of ' . $name,
                    ]
                );
            } catch (\Throwable $e) {
                // Log silently or to stderr if needed, but for now just catch to ensure loop continues
            }
        }
    }
}
