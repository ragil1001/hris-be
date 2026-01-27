<?php

namespace App\Services;

use App\Models\Project;
use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use App\Events\ProjectDeleted;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    public function create(array $data): Project
    {
        $data['is_active'] = $data['is_active'] ?? true;

        if (isset($data['pengecualian_formasi'])) {
            $data['pengecualian_formasi'] = json_encode($data['pengecualian_formasi']);
        }

        $project = Project::create($data);

        if (!empty($data['shifts'])) {
            $shiftsData = collect($data['shifts'])->map(function ($shift) {
                return [
                    'kode_shift' => $shift['kode_shift'],
                    'waktu_mulai' => $shift['waktu_mulai'],
                    'waktu_selesai' => $shift['waktu_selesai'],
                ];
            })->toArray();

            $project->shifts()->createMany($shiftsData);
        }

        event(new ProjectCreated($project, Auth::id()));

        return $project;
    }

    public function updateSection(Project $project, string $section, array $data): void
    {
        $changes = [];

        if ($section === 'informasi_project') {
            if (isset($data['nama_project'])) $changes['nama_project'] = $data['nama_project'];
            if (array_key_exists('tanggal_mulai', $data)) $changes['tanggal_mulai'] = $data['tanggal_mulai'];
            if (array_key_exists('bagian', $data)) $changes['bagian'] = $data['bagian'];
            if (isset($data['is_active'])) $changes['is_active'] = $data['is_active'];
        } elseif ($section === 'lokasi_peta') {
            if (array_key_exists('latitude', $data)) $changes['latitude'] = $data['latitude'];
            if (array_key_exists('longitude', $data)) $changes['longitude'] = $data['longitude'];
        } elseif ($section === 'pengaturan_absensi') {
            if (isset($data['radius_absensi'])) $changes['radius_absensi'] = $data['radius_absensi'];
            if (isset($data['waktu_toleransi'])) $changes['waktu_toleransi'] = $data['waktu_toleransi'];
        } elseif ($section === 'pengecualian_formasi') {
            $changes['pengecualian_formasi'] = json_encode($data['pengecualian_formasi'] ?? []);
        }

        if (!empty($changes)) {
            $project->update($changes);
            event(new ProjectUpdated($project, Auth::id(), $changes));
        }
    }

    public function delete(Project $project): void
    {
        $project->update(['is_active' => false]);
        event(new ProjectDeleted($project, Auth::id()));
    }
}
