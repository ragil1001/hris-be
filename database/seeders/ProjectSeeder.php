<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Shift;
use App\Models\Formasi;
use App\Models\Izin;
use Illuminate\Support\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::unsetEventDispatcher();
        Shift::unsetEventDispatcher();

        $projects = [
            [
                'nama_project' => 'Gedung Cyber 2 Tower',
                'bagian' => 'Cleaning Service',
                'latitude' => -6.2238,
                'longitude' => 106.8313, // Jakarta
                'is_active' => true,
            ],
            [
                'nama_project' => 'Mall Grand Indonesia',
                'bagian' => 'Security',
                'latitude' => -6.1950,
                'longitude' => 106.8200, // Jakarta
                'is_active' => true,
            ],
            [
                'nama_project' => 'Apartemen Taman Rasuna',
                'bagian' => 'Engineering',
                'latitude' => -6.2205,
                'longitude' => 106.8360, // Jakarta
                'is_active' => true,
            ],
            [
                'nama_project' => 'RSUD Dr. Soetomo',
                'bagian' => 'Cleaning Service',
                'latitude' => -7.2655,
                'longitude' => 112.7596, // Surabaya
                'is_active' => true,
            ],
            [
                'nama_project' => 'Bandara Juanda T2',
                'bagian' => 'Gondola',
                'latitude' => -7.3798,
                'longitude' => 112.7869, // Surabaya
                'is_active' => true,
            ],
            [
                'nama_project' => 'Trans Studio Bandung',
                'bagian' => 'Security',
                'latitude' => -6.9249,
                'longitude' => 107.6366, // Bandung
                'is_active' => true,
            ],
            [
                'nama_project' => 'Gedung Sate Maintenance',
                'bagian' => 'Gardener',
                'latitude' => -6.9025,
                'longitude' => 107.6188, // Bandung
                'is_active' => true,
            ],
            [
                'nama_project' => 'Simpang Lima Plaza',
                'bagian' => 'Pest Control',
                'latitude' => -6.9904,
                'longitude' => 110.4229, // Semarang
                'is_active' => true,
            ],
            [
                'nama_project' => 'Malioboro Mall',
                'bagian' => 'Cleaning Service',
                'latitude' => -7.7926,
                'longitude' => 110.3659, // Yogyakarta
                'is_active' => true,
            ],
            [
                'nama_project' => 'Kawasan Nusa Dua Bali',
                'bagian' => 'Gardener',
                'latitude' => -8.8028,
                'longitude' => 115.2280, // Bali
                'is_active' => true,
            ],
            [
                'nama_project' => 'Centre Point Medan',
                'bagian' => 'Security',
                'latitude' => 3.5901,
                'longitude' => 98.6780, // Medan
                'is_active' => true,
            ],
            [
                'nama_project' => 'Makassar Town Square',
                'bagian' => 'Engineering',
                'latitude' => -5.1486,
                'longitude' => 119.4316, // Makassar
                'is_active' => true,
            ],
            [
                'nama_project' => 'Proyek Lama Gudang C',
                'bagian' => 'Logistik',
                'latitude' => -6.1200,
                'longitude' => 106.9000, 
                'is_active' => false, // Inactive
            ],
            [
                'nama_project' => 'Renovasi Kantor Cabang A',
                'bagian' => 'Konstruksi',
                'latitude' => -6.2000,
                'longitude' => 106.8500,
                'is_active' => false, // Inactive
            ],
            [
                'nama_project' => 'Event PRJ Kemayoran (Temp)',
                'bagian' => 'Event',
                'latitude' => -6.1450,
                'longitude' => 106.8500,
                'is_active' => false, // Inactive
            ]
        ];

        $allFormasi = Formasi::pluck('id');
        $allIzin = Izin::pluck('id');

        foreach ($projects as $p) {
            try {
                $project = Project::firstOrCreate(
                    ['nama_project' => $p['nama_project']],
                    [
                        'bagian' => $p['bagian'],
                        'tanggal_mulai' => Carbon::now()->subMonths(rand(1, 24)),
                        'latitude' => $p['latitude'],
                        'longitude' => $p['longitude'],
                        'radius_absensi' => 100, // Standard
                        'waktu_toleransi' => 15,
                        'is_active' => $p['is_active'],
                    ]
                );

                // Create Shifts (1 to 3 shifts)
                $numShifts = rand(1, 3);
                
                for ($i = 1; $i <= $numShifts; $i++) {
                    $start = sprintf("%02d:00:00", ($i - 1) * 8 + 7); // 07, 15, 23
                    $end   = sprintf("%02d:00:00", (($i - 1) * 8 + 15) % 24);
                    
                    Shift::firstOrCreate(
                        [
                            'project_id' => $project->id,
                            'kode_shift' => "S{$i}",
                        ],
                        [
                            'waktu_mulai' => $start,
                            'waktu_selesai' => $end,
                        ]
                    );
                }
                

                // Attach Formasi Exceptions (Random 0-2 formasi)
                if ($allFormasi->count() > 0) {
                    $randomFormasi = $allFormasi->random(rand(0, min(2, $allFormasi->count())));
                    foreach ($randomFormasi as $fid) {
                        $project->formasis()->syncWithoutDetaching([$fid => ['is_active' => true]]);
                    }
                }
                
                // Attach Izin (Random 2-5 izin types)
                if ($allIzin->count() > 0) {
                     $randomIzin = $allIzin->random(rand(2, min(5, $allIzin->count())));
                     $project->izins()->syncWithoutDetaching($randomIzin);
                }
            } catch (\Exception $e) {
                echo "Error Seeding Project {$p['nama_project']}: " . $e->getMessage() . "\n";
            }
        }
    }
}
