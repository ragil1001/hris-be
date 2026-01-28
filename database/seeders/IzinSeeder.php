<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $izins = [
            [
                'kategori' => 'Sakit',
                'sub_kategori' => null,
                'jumlah_hari' => null,
                'is_active' => true,
            ],
            [
                'kategori' => 'Izin Umum',
                'sub_kategori' => null,
                'jumlah_hari' => null,
                'is_active' => true,
            ],
        ];

        foreach ($izins as $izin) {
            \App\Models\Izin::firstOrCreate(
                [
                    'kategori' => $izin['kategori'],
                    'sub_kategori' => $izin['sub_kategori']
                ],
                $izin
            );
        }
    }
}
