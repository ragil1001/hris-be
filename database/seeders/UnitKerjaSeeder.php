<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['QMS', 'TS3'];

        foreach ($units as $unit) {
            UnitKerja::firstOrCreate(['nama_unit' => $unit], ['is_active' => true]);
        }
    }
}
