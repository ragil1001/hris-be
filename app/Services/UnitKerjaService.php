<?php

namespace App\Services;

use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;

class UnitKerjaService
{
    public function create(array $data): UnitKerja
    {
        $data['is_active'] = $data['is_active'] ?? true;

        $unitKerja = UnitKerja::create($data);

        // event(new UnitKerjaCreated($unitKerja, Auth::id()));

        return $unitKerja;
    }

    public function update(UnitKerja $unitKerja, array $data): void
    {
        $changes = array_intersect_key($data, array_flip(['nama_unit', 'is_active']));

        if (!empty($changes)) {
            $unitKerja->update($changes);
            // event(new UnitKerjaUpdated($unitKerja, Auth::id(), $changes));
        }
    }

    public function delete(UnitKerja $unitKerja): void
    {
        $unitKerja->update(['is_active' => false]);
        // event(new UnitKerjaDeleted($unitKerja, Auth::id()));
    }
}
