<?php

namespace App\Services;

use App\Models\Izin;
use Illuminate\Support\Facades\Auth;

class IzinService
{
    public function create(array $data): Izin
    {
        $data['is_active'] = $data['is_active'] ?? true;

        $izin = Izin::create($data);

        // event(new IzinCreated($izin, Auth::id()));

        return $izin;
    }

    public function update(Izin $izin, array $data): void
    {
        $changes = array_intersect_key($data, array_flip(['kategori', 'sub_kategori', 'jumlah_hari', 'is_active']));

        if (!empty($changes)) {
            $izin->update($changes);
            // event(new IzinUpdated($izin, Auth::id(), $changes));
        }
    }

    public function delete(Izin $izin): void
    {
        $izin->update(['is_active' => false]);
        // event(new IzinDeleted($izin, Auth::id()));
    }
}
