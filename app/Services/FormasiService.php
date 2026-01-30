<?php

namespace App\Services;

use App\Models\Formasi;
use Illuminate\Support\Facades\Auth;

class FormasiService
{
    public function create(array $data): Formasi
    {
        $data['is_active'] = $data['is_active'] ?? true;

        $formasi = Formasi::create($data);

        // event(new FormasiCreated($formasi, Auth::id()));

        return $formasi;
    }

    public function update(Formasi $formasi, array $data): void
    {
        $changes = array_intersect_key($data, array_flip(['nama_formasi', 'is_active']));

        if (!empty($changes)) {
            $formasi->update($changes);
            // event(new FormasiUpdated($formasi, Auth::id(), $changes));
        }
    }

    public function delete(Formasi $formasi): void
    {
        $formasi->update(['is_active' => false]);
        // event(new FormasiDeleted($formasi, Auth::id()));
    }
}
