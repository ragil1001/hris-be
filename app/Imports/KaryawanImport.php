<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\UnitKerja;
use App\Models\Jabatan;
use App\Models\Formasi;
use App\Models\Project;
use App\Models\Bank;
use App\Models\Keluarga;
use App\Models\AyahIbu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KaryawanImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];

        // 1. First Pass: Validate All Rows & Collect Data
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 4;

            // Skip completely empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            // Validation
            $missing = [];
            if (empty(trim((string)($row['nik'] ?? '')))) $missing[] = 'NIK';
            if (empty(trim((string)($row['nama'] ?? '')))) $missing[] = 'Nama';
            if (empty(trim((string)($row['tempat_lahir'] ?? '')))) $missing[] = 'Tempat Lahir';
            if (empty(trim((string)($row['tanggal_lahir'] ?? '')))) $missing[] = 'Tanggal Lahir';

            if (!empty($missing)) {
                $name = !empty(trim($row['nama'] ?? '')) ? " atas nama " . trim($row['nama']) : "";
                $errors[] = "Baris $rowNumber$name: Belum mengisi " . implode(', ', $missing);
                continue;
            }

            $validData[] = $row;
        }

        // 2. If any validation errors, abort BEFORE doing any DB transactions
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        // 3. Second Pass: Process valid rows in a single batch transaction if possible, 
        // or per-row to ensure partial successes don't leave inconsistent state on error.
        // We'll use a single transaction for efficiency now that we know validation passed.
        DB::transaction(function () use ($validData) {
            foreach ($validData as $row) {
                $this->processRow($row);
            }
        });
    }

    private function processRow($row)
    {
        // 1. Lookup Related IDs (Auto-create if missing, except Project)
        $unitKerjaId = $this->getOrCreateLookupId(UnitKerja::class, 'nama_unit', $row['unit_kerja']);
        $jabatanId = $this->getOrCreateLookupId(Jabatan::class, 'nama_jabatan', $row['jabatan']);
        $formasiId = $this->getOrCreateLookupId(Formasi::class, 'nama_formasi', $row['formasi']);
        $penempatanId = $this->getLookupId(Project::class, 'nama_project', $row['penempatan']); // Strict lookup
        $bankId = $this->getOrCreateLookupId(Bank::class, 'nama_bank', $row['bank']);

        // 2. Prepare Data
        $karyawanData = [
            'nama' => $row['nama'],
            'tanggal_masuk' => $this->parseDate($row['tanggal_masuk_kerja']),
            'gaji_pokok' => $this->parseCurrency($row['gaji']),
            'insentif' => $this->parseCurrency($row['insentif']),
            'uang_makan' => $this->parseCurrency($row['uang_makan']),
            'control_status' => strtoupper(trim($row['control_status'] ?? 'KONTRAK')),
            'keterangan' => $row['keterangan'],
            'status' => strtoupper(trim($row['aktifresign'] ?? 'AKTIF')),
            'tanggal_resign' => $this->parseDate($row['tanggal_resign']),
            'tanggal_aktif' => $this->parseDate($row['tanggal_aktif']),
            'jenis_kelamin' => strtoupper(substr(trim($row['kode_jenis_kelamin'] ?? 'L'), 0, 1)) === 'P' ? 'P' : 'L',
            'unit_kerja_id' => $unitKerjaId,
            'jabatan_id' => $jabatanId,
            'formasi_id' => $formasiId,
            'penempatan_id' => $penempatanId,
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir']),
            'no_kk' => (string)($row['no_kk'] ?? ''),
            'no_ktp' => (string)($row['no_ktp'] ?? ''),
            'no_bpjs_ketenagakerjaan' => (string)($row['bpjs_ketenagakerjaan'] ?? ''),
            'npp_bpjs_ketenagakerjaan' => $row['npp_bpjs_ketenagakerjaan'],
            'no_bpjs_kesehatan' => (string)($row['bpjs_kesehatan'] ?? ''),
            'bu_bpjs_kesehatan' => $row['bu_bpjs_kesehatan'],
            'alamat' => $row['alamat'],
            'rt' => $row['rt'],
            'rw' => $row['rw'],
            'kelurahan' => $row['kelurahan'],
            'kecamatan' => $row['kecamatan'],
            'kabupaten_kota' => $row['kabupatenkota'],
            'kode_pos' => $row['kode_pos'],
            'agama' => $row['agama'],
            'email' => $row['email'],
            'no_telepon' => (string)($row['no_telpwa'] ?? ''),
            'pendidikan_terakhir' => $row['pendidikan_terakhir'],
            'golongan_darah' => $row['golongan_darah'],
            'status_pernikahan' => $row['status_tkk'],
            'bank_id' => $bankId,
            'no_rekening' => (string)($row['nomor_rekening'] ?? ''),
            'no_jaminan_pensiun' => (string)($row['jaminan_pensiun'] ?? ''),
            'bu' => $row['bu'],
            'awal_mulai_cuti' => $this->parseDate($row['awal_mulai_cuti']),
            'masa_berlaku_cuti' => $this->parseDate($row['masa_berlaku_cuti']),
            'potongan_cuti_bersama' => (int)($row['potongan_cuti_bersama'] ?? 0),
            'sisa_cuti' => (int)($row['sisa_cuti'] ?? 0),
        ];

        // 3. Create or Update Karyawan
        $karyawan = Karyawan::updateOrCreate(
            ['nik' => (string)$row['nik']],
            $karyawanData
        );

        // 4. Handle Keluarga (Spouse)
        if (!empty($row['nama_istrisuami'])) {
            Keluarga::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'hubungan' => in_array($karyawan->jenis_kelamin, ['L', 'Laki-laki']) ? 'ISTRI' : 'SUAMI',
                ],
                [
                    'nama' => $row['nama_istrisuami'],
                    'no_ktp' => (string)($row['nomor_ktp_istrisuami'] ?? ''),
                    'tempat_lahir' => $row['tempat_lahir_istrisuami'],
                    'tanggal_lahir' => $this->parseDate($row['tanggal_lahir_istrisuami']),
                    'bpjs_kesehatan' => $row['nomor_bpjs_istrisuami'],
                ]
            );
        }

        // 5. Handle Children (Dynamic)
        foreach ($row->keys() as $key) {
            if (preg_match('/^nama_anak_(\d+)$/', $key, $matches)) {
                $idx = $matches[1];
                $this->syncChild(
                    $karyawan, 
                    $idx, 
                    $row["nama_anak_$idx"], 
                    $row["nomor_ktp_anak_$idx"], 
                    $row["tempat_lahir_anak_$idx"], 
                    $row["tanggal_lahir_anak_$idx"], 
                    $row["nomor_bpjs_kesehatan_anak_$idx"]
                );
            }
        }

        // 6. Handle Ayah Ibu
        if (!empty($row['nama_bapak_kandung']) || !empty($row['nama_ibu_kandung'])) {
            AyahIbu::updateOrCreate(
                ['karyawan_id' => $karyawan->id],
                [
                    'nama_ayah' => $row['nama_bapak_kandung'],
                    'nama_ibu' => $row['nama_ibu_kandung'],
                ]
            );
        }
    }

    private function getLookupId($model, $column, $value)
    {
        if (empty($value)) return null;
        
        $record = $model::where($column, 'like', trim($value))->first();
        return $record ? $record->id : null;
    }

    private function getOrCreateLookupId($model, $column, $value)
    {
        if (empty($value)) return null;
        
        $record = $model::firstOrCreate([$column => trim($value)]);
        return $record ? $record->id : null;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // If it's a numeric value from Excel (date serial)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }

            // Try common Indonesian format: "30 Januari 2026"
            $months = [
                'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04', 'Mei' => '05', 'Juni' => '06',
                'Juli' => '07', 'Agustus' => '08', 'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
            ];
            
            $cleanValue = trim($value);
            foreach ($months as $name => $num) {
                if (stripos($cleanValue, $name) !== false) {
                    $cleanValue = str_ireplace($name, $num, $cleanValue);
                    return Carbon::createFromFormat('d m Y', $cleanValue);
                }
            }

            return Carbon::parse($value);
        } catch (\Exception $e) {
            Log::warning("Gagal parse tanggal: " . $value . " - " . $e->getMessage());
            return null;
        }
    }

    private function parseCurrency($value)
    {
        if (empty($value)) return 0;
        
        // Remove "Rp", dots (thousands separator), and trim
        $clean = preg_replace('/[^0-9]/', '', $value);
        return (float) $clean;
    }

    private function parseStatusPernikahan($value)
    {
        return !empty($value) ? trim($value) : null;
    }

    private function syncChild($karyawan, $order, $nama, $ktp, $tempat, $tanggal, $bpjs)
    {
        if (empty($nama)) return;

        Keluarga::updateOrCreate(
            [
                'karyawan_id' => $karyawan->id,
                'hubungan' => 'ANAK',
                'urutan_anak' => $order,
            ],
            [
                'nama' => $nama,
                'no_ktp' => (string)$ktp,
                'tempat_lahir' => $tempat,
                'tanggal_lahir' => $this->parseDate($tanggal),
                'bpjs_kesehatan' => $bpjs,
            ]
        );
    }
}
