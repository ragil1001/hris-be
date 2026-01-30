<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Formasi;

class JabatanFormasiSeeder extends Seeder
{
    public function run(): void
    {
        // Pairs of Jabatan -> Formasi (can be many-to-many logically, but we keep it simple here)
        $pairs = [
            ['jabatan' => 'Project Manager', 'formasi' => 'Management'],
            ['jabatan' => 'Site Manager', 'formasi' => 'Management'],
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
            ['jabatan' => 'General Affair', 'formasi' => 'GA'],
        ];

        foreach ($pairs as $pair) {
            Jabatan::firstOrCreate(['nama_jabatan' => $pair['jabatan']], ['is_active' => true]);
            Formasi::firstOrCreate(['nama_formasi' => $pair['formasi']], ['is_active' => true]);
        }
    }
}
