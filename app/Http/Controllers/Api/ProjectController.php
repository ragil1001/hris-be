<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            ->select('id', 'nama_project', 'tanggal_mulai', 'bagian', 'latitude', 'longitude', 'radius_absensi', 'waktu_toleransi', 'is_active')
            ->when($request->search, fn($q, $s) => $q->where('nama_project', 'ilike', "%{$s}%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        $projects->getCollection()->transform(function ($project) {
            $project->lokasi = $project->latitude && $project->longitude
                ? "{$project->latitude}, {$project->longitude}"
                : null;
            $project->status = $project->status;
            return $project;
        });

        return response()->json($projects);
    }

    public function show(Project $project)
    {
        $project->load('shifts');

        return response()->json($project);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules('create'));

        $validator->after(function ($validator) use ($request) {
            $shifts = $request->input('shifts', []);

            foreach ($shifts as $index => $shift) {
                $start = \Carbon\Carbon::createFromFormat('H:i:s', $shift['waktu_mulai']);
                $end = \Carbon\Carbon::createFromFormat('H:i:s', $shift['waktu_selesai']);

                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                if (!$end->greaterThan($start)) {
                    $validator->errors()->add("shifts.{$index}.waktu_selesai", 'The end time must be after the start time (overnight shifts are allowed).');
                }
            }
        });

        $validated = $validator->validate();

        $project = $this->service->create($validated);

        $project->load('shifts');

        return response()->json($project, 201);
    }

    public function updateSection(Request $request, Project $project, string $section)
    {
        $validated = $request->validate($this->getRules($section, $project));

        $this->service->updateSection($project, $section, $validated);

        return response()->json($project->fresh()->load('shifts'));
    }

    public function destroy(Project $project)
    {
        $this->service->delete($project);

        return response()->json(['message' => 'Delete project successful']);
    }

    private function getRules(string $section, ?Project $project = null): array
    {
        $uniqueNamaRule = 'unique:project,nama_project';
        if ($project) {
            $uniqueNamaRule .= ',' . $project->id;
        }

        $rules = [
            'create' => [
                'nama_project' => "required|string|max:255|{$uniqueNamaRule}",
                'tanggal_mulai' => 'nullable|date',
                'bagian' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'radius_absensi' => 'integer|min:0',
                'waktu_toleransi' => 'integer|min:0',
                'pengecualian_formasi' => 'nullable|array',
                'pengecualian_formasi.*' => 'exists:formasi,id',
                'is_active' => 'boolean',
                'shifts' => 'nullable|array',
                'shifts.*.kode_shift' => 'required|string|max:10|distinct',
                'shifts.*.waktu_mulai' => 'required|date_format:H:i:s',
                'shifts.*.waktu_selesai' => 'required|date_format:H:i:s',
            ],
            'informasi_project' => [
                'nama_project' => "sometimes|required|string|max:255|{$uniqueNamaRule}",
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
            ],
            'pengecualian_formasi' => [
                'pengecualian_formasi' => 'nullable|array',
                'pengecualian_formasi.*' => 'exists:formasi,id',
            ],
        ];

        return $rules[$section] ?? throw ValidationException::withMessages(['section' => 'Invalid section']);
    }
}
