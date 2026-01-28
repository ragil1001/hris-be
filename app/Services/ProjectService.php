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

        $project = Project::create($data);

        // Handle Pengecualian Radius (Formasi)
        if (isset($data['formasis'])) {
            $project->formasis()->sync($data['formasis']);
        }

        // Handle Izin (Permissions)
        $izinsToAttach = $data['izins'] ?? [];
        
        // Auto-attach "Sakit" and "Izin Umum" if not already selected (though sync will handle uniqueness)
        // Find IDs for "Sakit" and "Keterangan Pribadi" (Izin Umum) based on Kategori/Sub
        // Assuming we look them up or they are passed.
        // Better: Look them up by name to be safe.
        $defaultIzins = \App\Models\Izin::where(function($q) {
            $q->whereNull('sub_kategori')
              ->whereIn('kategori', ['Sakit', 'Izin Umum']);
        })->orWhere(function($q) {
             // Backup or specific matching if needed
             $q->where('kategori', 'Sakit');
        })->pluck('id')->toArray();
        
        $allIzins = array_unique(array_merge($izinsToAttach, $defaultIzins));
        $project->izins()->sync($allIzins);

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

        // Reload relations for response
        $project->load(['izins', 'formasis', 'shifts']);

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
            
            // Handle Relation Updates in this section if passed
            if (isset($data['formasis'])) {
               $project->formasis()->sync($data['formasis']);
               // We don't track relation changes in $changes array for simple audit log yet, or we could add a note.
            }
            if (isset($data['izins'])) {
                 // Ensure defaults are kept
                $defaultIzins = \App\Models\Izin::where(function($q) {
                    $q->whereNull('sub_kategori')
                    ->whereIn('kategori', ['Sakit', 'Izin Umum']);
                })->pluck('id')->toArray();
                
                $allIzins = array_unique(array_merge($data['izins'], $defaultIzins));
                $project->izins()->sync($allIzins);
            }
        } elseif ($section === 'shift_project') {
             if (isset($data['shifts'])) {
                // Delete existing shifts
                $project->shifts()->delete();

                // Create new shifts
                $shiftsData = collect($data['shifts'])->map(function ($shift) {
                    return [
                        'kode_shift' => $shift['kode_shift'],
                        'waktu_mulai' => $shift['waktu_mulai'],
                        'waktu_selesai' => $shift['waktu_selesai'],
                    ];
                })->toArray();

                $project->shifts()->createMany($shiftsData);
             }
        } 
        
        if (!empty($changes)) {
            $project->update($changes);
            event(new ProjectUpdated($project, Auth::id(), $changes));
        }
    }

    public function delete(Project $project): void
    {
        Project::where('id', $project->id)->update(['is_active' => 0]);
        
        event(new ProjectDeleted($project, Auth::id()));
    }
}
