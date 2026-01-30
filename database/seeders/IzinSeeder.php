<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Kategori Umum
            ['kategori' => 'Sakit', 'sub_kategori' => null, 'jumlah_hari' => null],
            ['kategori' => 'Izin Umum', 'sub_kategori' => null, 'jumlah_hari' => null],
            
            // Cuti Izin Khusus
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Pernikahan Karyawan', 'jumlah_hari' => 3],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Pernikahan Anak', 'jumlah_hari' => 2],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Khitanan/Baptis Anak', 'jumlah_hari' => 2],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Istri Melahirkan/Keguguran', 'jumlah_hari' => 2],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Keluarga Inti Meninggal', 'jumlah_hari' => 2], // Suami/Istri/Ortu/Mertua/Anak/Menantu
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Anggota Keluarga Serumah Meninggal', 'jumlah_hari' => 1],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Wisuda', 'jumlah_hari' => 1], // Anak/Karyawan? Asumsi Karyawan/Anak
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Ibadah Haji', 'jumlah_hari' => 40],
            ['kategori' => 'Cuti Izin Khusus', 'sub_kategori' => 'Ibadah Umroh', 'jumlah_hari' => 9],
        ];

        foreach ($data as $item) {
            Izin::firstOrCreate(
                [
                    'kategori' => $item['kategori'], 
                    'sub_kategori' => $item['sub_kategori']
                ],
                [
                    'jumlah_hari' => $item['jumlah_hari'],
                    'is_active' => true
                ]
            );
        }
    }
}
