<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            'Bank Central Asia (BCA)',
            'Bank Mandiri',
            'Bank Negara Indonesia (BNI)',
            'Bank Rakyat Indonesia (BRI)',
            'CIMB Niaga',
            'Bank Danamon',
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate(['nama_bank' => $bank], ['is_active' => true]);
        }
    }
}
