<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Role;
use App\Models\Keluarga;
use App\Models\AyahIbu;
use App\Jobs\LogAuditJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Service class for managing karyawan operations.
 */
class KaryawanService
{
    /**
     * Create a new karyawan.
     *
     * @param array $data
     * @return Karyawan
     */
    public function create(array $data): Karyawan
    {
        $nik = $data['nik'] ?? Str::random(10);
        $tanggalLahir = Carbon::parse($data['tanggal_lahir']);
        $password = $tanggalLahir->format('ddmmY');

        $user = User::create([
            'username' => $nik,
            'password' => Hash::make($password),
            'name' => $data['nama'],
            'role_id' => Role::where('name', 'employee')->first()->id,
            'is_active' => true,
        ]);

        $data['user_id'] = $user->id;
        $data['nik'] = $nik;
        $data['control_status'] = $data['control_status'] ?? 'KONTRAK';
        
        // Auto-resign logic
        if (!empty($data['tanggal_resign'])) {
            $data['status'] = 'RESIGN';
        } else {
            $data['status'] = $data['status'] ?? 'AKTIF';
        }

        $data['tanggal_aktif'] = $data['tanggal_aktif'] ?? now();
        $data['sisa_cuti'] = 12;

        $karyawan = Karyawan::create($data);

        // event(new KaryawanCreated($karyawan, auth()->id()));

        return $karyawan;
    }

    /**
     * Update karyawan details.
     *
     * @param Karyawan $karyawan
     * @param array $data
     * @return Karyawan
     */
    public function update(Karyawan $karyawan, array $data): Karyawan
    {
        $oldData = $karyawan->toArray();

        // Auto-resign logic
        if (!empty($data['tanggal_resign'])) {
            $data['status'] = 'RESIGN';
        }

        $karyawan->update($data);
        if ($karyawan->wasChanged()) {
            $changes = $karyawan->getChanges();
            $meta = [
                'model' => get_class($karyawan),
                'model_id' => $karyawan->id,
                'details' => $karyawan->toArray(),
                'changes' => [
                    'before' => array_intersect_key($oldData, $changes),
                    'after' => $changes
                ]
            ];
            LogAuditJob::dispatch(auth()->id(), 'Updated Karyawan', request()->ip(), request()->userAgent(), $meta);
        }

        return $karyawan;
    }

    /**
     * Update keluarga details for karyawan.
     *
     * @param Karyawan $karyawan
     * @param array $data
     * @return void
     */
    public function updateKeluarga(Karyawan $karyawan, array $data): void
    {
        $oldData = [
            'keluarga' => $karyawan->keluarga->toArray(),
            'ayah_ibu' => $karyawan->ayahIbu ? $karyawan->ayahIbu->toArray() : null,
        ];

        // Update spouse
        if (isset($data['istri_suami'])) {
            $spouseData = $data['istri_suami'];
            $spouseData['karyawan_id'] = $karyawan->id;
            if (isset($spouseData['id'])) {
                $spouse = Keluarga::findOrFail($spouseData['id']);
                $spouse->update($spouseData);
            } else {
                Keluarga::create($spouseData);
            }
        }

        // Update children
        if (isset($data['anak'])) {
            $incomingIds = [];
            foreach ($data['anak'] as $anakData) {
                $anakData['karyawan_id'] = $karyawan->id;
                $anakData['hubungan'] = 'ANAK';
                if (isset($anakData['id'])) {
                    $anak = Keluarga::findOrFail($anakData['id']);
                    $anak->update($anakData);
                    $incomingIds[] = $anak->id;
                } else {
                    $newAnak = Keluarga::create($anakData);
                    $incomingIds[] = $newAnak->id;
                }
            }
            // Delete children not in incoming
            $karyawan->keluarga()->where('hubungan', 'ANAK')->whereNotIn('id', $incomingIds)->delete();
        }

        // Update parents
        if (isset($data['orang_tua'])) {
            $parentsData = $data['orang_tua'];
            $parentsData['karyawan_id'] = $karyawan->id;
            if ($karyawan->ayahIbu) {
                $karyawan->ayahIbu->update($parentsData);
            } else {
                AyahIbu::create($parentsData);
            }
        }

        // Consolidate changes into metadata
        $meta = [
            'section' => 'informasi_keluarga',
            'model' => get_class($karyawan),
            'model_id' => $karyawan->id,
            'details' => [
                'updated_keluarga' => isset($data['istri_suami']) || isset($data['anak']),
                'updated_orang_tua' => isset($data['orang_tua'])
            ]
        ];

        LogAuditJob::dispatch(auth()->id(), 'Updated Karyawan', request()->ip(), request()->userAgent(), $meta);
    }

    /**
     * Soft delete karyawan.
     *
     * @param Karyawan $karyawan
     * @return void
     */
    public function delete(Karyawan $karyawan, ?string $tanggalResign = null): void
    {
        if ($tanggalResign) {
            $karyawan->update([
                'status' => 'RESIGN',
                'tanggal_resign' => $tanggalResign,
            ]);
        } else {
            $karyawan->delete();
        }
        // event(new KaryawanDeleted($karyawan, auth()->id()));
    }

    /**
     * Reset password for karyawan.
     *
     * @param Karyawan $karyawan
     * @return void
     */
    public function resetPassword(Karyawan $karyawan): void
    {
        if ($karyawan->user) {
            $tanggalLahir = Carbon::parse($karyawan->tanggal_lahir);
            $password = $tanggalLahir->format('ddmmY');
            $karyawan->user->update(['password' => Hash::make($password)]);
            // event(new KaryawanUpdated($karyawan, auth()->id(), ['password_reset' => true]));
        }
    }
}
