<?php

namespace App\Services;

use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;

class JabatanService
{
    public function create(array $data): Jabatan
    {
        $data['is_active'] = $data['is_active'] ?? true;

        $jabatan = Jabatan::create($data);

        // event(new JabatanCreated($jabatan, Auth::id()));

        return $jabatan;
    }

    public function update(Jabatan $jabatan, array $data): void
    {
        $changes = array_intersect_key($data, array_flip(['nama_jabatan', 'is_active']));

        if (!empty($changes)) {
            $jabatan->update($changes);
            // event(new JabatanUpdated($jabatan, Auth::id(), $changes));
        }
    }

    public function delete(Jabatan $jabatan): void
    {
        $jabatan->update(['is_active' => false]);
        // event(new JabatanDeleted($jabatan, Auth::id()));
    }
}
