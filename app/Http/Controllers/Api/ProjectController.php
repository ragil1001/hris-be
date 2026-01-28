<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Models\Project;
use App\Models\Formasi;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    protected $service;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->search, fn($q, $s) => $q->where('nama_project', 'ilike', "%{$s}%"))
            ->when($request->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);

        $projects->getCollection()->transform(function ($project) {
            $project->lokasi = $project->latitude && $project->longitude
                ? "{$project->latitude}, {$project->longitude}"
                : null;
            return $project;
        });

        return response()->json($projects);
    }

    public function show(Project $project)
    {
        $project->load(['shifts', 'izins', 'formasis']);

        return response()->json(['data' => $project]);
    }

    public function store(Request $request)
    {
        $namaProject = $request->input('nama_project');
        
        // Check FIRST if project with same name (case-insensitive) exists but is inactive
        if ($namaProject) {
            $existingInactive = Project::where('nama_project', 'ilike', $namaProject)
                ->where('is_active', false)
                ->first();

            if ($existingInactive) {
                // Reactivate the existing project instead of creating new one
                $existingInactive->update(['is_active' => true]);
                $existingInactive->load('shifts');
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Project berhasil diaktifkan kembali'
                ], 200);
            }
        }

        $validator = Validator::make($request->all(), $this->getRules('create'), [], [
            'nama_project' => 'Nama Project',
            'shifts.*.kode_shift' => 'Kode Shift',
            'shifts.*.waktu_mulai' => 'Waktu Mulai',
            'shifts.*.waktu_selesai' => 'Waktu Selesai',
        ]);

        $validator->after(function ($validator) use ($request) {
            $shifts = $request->input('shifts', []);

            foreach ($shifts as $index => $shift) {
                // Skip if times are missing
                if (!isset($shift['waktu_mulai']) || !isset($shift['waktu_selesai'])) {
                    continue; 
                }
                
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i:s', $shift['waktu_mulai']);
                    $end = \Carbon\Carbon::createFromFormat('H:i:s', $shift['waktu_selesai']);

                    if ($end->lessThan($start)) {
                        $end->addDay();
                    }

                    if (!$end->greaterThan($start)) {
                        $validator->errors()->add("shifts.{$index}.waktu_selesai", 'The end time must be after the start time (overnight shifts are allowed).');
                    }
                } catch (\Exception $e) {
                   // Invalid format will be caught by main validation rules, so safe to ignore or continue
                   continue;
                }
            }
        });

        $validated = $validator->validate();

        $project = $this->service->create($validated);

        $project->load('shifts');

        return response()->json(['data' => $project], 201);
    }

    public function updateSection(Request $request, Project $project, string $section)
    {
        $validated = $request->validate($this->getRules($section, $project), [], [
            'nama_project' => 'Nama Project',
            'tanggal_mulai' => 'Tanggal Mulai',
            'radius_absensi' => 'Radius Absensi',
            'waktu_toleransi' => 'Waktu Toleransi',
            'formasis' => 'Pengecualian Radius',
            'izins' => 'Kategori Izin',
            'shifts' => 'Shift Project',
        ]);

        $this->service->updateSection($project, $section, $validated);

        return response()->json(['data' => $project->fresh()->load('shifts')]);
    }

    public function destroy(Project $project)
    {
        // Soft delete: set is_active to false instead of deleting
        $this->service->delete($project);

        return response()->json(['message' => 'Project berhasil dinonaktifkan']);
    }

    public function reactivate(Project $project)
    {
        // Reactivate: set is_active back to true
        Project::where('id', $project->id)->update(['is_active' => 1]);

        return response()->json([
            'message' => 'Project berhasil diaktifkan kembali',
            'data' => $project->fresh()
        ]);
    }

    private function getRules(string $section, ?Project $project = null): array
    {
        // Custom case-insensitive unique validation
        $uniqueNamaRule = function ($attribute, $value, $fail) use ($project) {
            $query = Project::where('nama_project', 'ilike', $value)
                ->where('is_active', true);
            
            if ($project) {
                $query->where('id', '!=', $project->id);
            }
            
            if ($query->exists()) {
                $fail('Nama Project sudah digunakan.');
            }
        };

        $rules = [
            'create' => [
                'nama_project' => ['required', 'string', 'max:255', $uniqueNamaRule],
                'tanggal_mulai' => 'nullable|date',
                'bagian' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'radius_absensi' => 'integer|min:0',
                'waktu_toleransi' => 'integer|min:0',
                'formasis' => 'nullable|array',
                'formasis.*' => [Rule::exists(Formasi::class, 'id')],
                'izins' => 'nullable|array',
                'izins.*' => [Rule::exists(Izin::class, 'id')],
                'is_active' => 'boolean',
                'shifts' => 'nullable|array',
                'shifts.*.kode_shift' => 'required|string|max:10|distinct',
                'shifts.*.waktu_mulai' => 'required|date_format:H:i:s',
                'shifts.*.waktu_selesai' => 'required|date_format:H:i:s',
            ],
            'informasi_project' => [
                'nama_project' => ['sometimes', 'required', 'string', 'max:255', $uniqueNamaRule],
                'tanggal_mulai' => 'nullable|date',
                'bagian' => 'nullable|string|max:100',
                'is_active' => 'boolean',
            ],
            'lokasi_peta' => [
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ],
            'pengaturan_absensi' => [
                'radius_absensi' => 'integer|min:0',
                'waktu_toleransi' => 'integer|min:0',
                'formasis' => 'nullable|array',
                'formasis.*' => [Rule::exists(Formasi::class, 'id')],
                'izins' => 'nullable|array',
                'izins.*' => [Rule::exists(Izin::class, 'id')],
            ],
            'shift_project' => [
                'shifts' => 'required|array',
                'shifts.*.kode_shift' => 'required|string|max:10|distinct',
                'shifts.*.waktu_mulai' => 'required|date_format:H:i:s',
                'shifts.*.waktu_selesai' => 'required|date_format:H:i:s',
            ],
        ];

        return $rules[$section] ?? throw ValidationException::withMessages(['section' => 'Invalid section']);
    }
}
