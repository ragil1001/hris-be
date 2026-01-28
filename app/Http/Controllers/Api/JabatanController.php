<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JabatanService;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JabatanController extends Controller
{
    protected $service;

    public function __construct(JabatanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Jabatan::query();

        if ($request->filled('search')) {
            $query->where('nama_jabatan', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->input('per_page', 10);
        $jabatans = $query->orderBy('nama_jabatan')->paginate($perPage);

        return response()->json($jabatans);
    }

    public function show(Jabatan $jabatan)
    {
        return response()->json(['data' => $jabatan]);
    }


    public function store(Request $request)
    {
        $namaJabatan = $request->input('nama_jabatan');
        
        // Check FIRST if jabatan with same name (case-insensitive) exists but is inactive
        if ($namaJabatan) {
            $existingInactive = Jabatan::where('nama_jabatan', 'ilike', $namaJabatan)
                ->where('is_active', false)
                ->first();

            if ($existingInactive) {
                // Reactivate the existing jabatan instead of creating new one
                $existingInactive->update(['is_active' => true]);
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Jabatan berhasil diaktifkan kembali'
                ], 200);
            }
        }

        // Only validate if no inactive record to reactivate
        $validated = $request->validate($this->getValidationRules(), [], [
            'nama_jabatan' => 'Nama Jabatan'
        ]);

        // Create new jabatan
        $jabatan = $this->service->create($validated);

        return response()->json(['data' => $jabatan], 201);
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        // Validate but ignore current record for unique check
        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation ignoring self
                function ($attribute, $value, $fail) use ($jabatan) {
                    $exists = Jabatan::where('nama_jabatan', 'ilike', $value)
                        ->where('is_active', true)
                        ->where('id', '!=', $jabatan->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Jabatan sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ], [], [
            'nama_jabatan' => 'Nama Jabatan'
        ]);

        $this->service->update($jabatan, $validated);

        return response()->json(['data' => $jabatan]);
    }

    public function destroy(Jabatan $jabatan)
    {
        // Soft delete: set is_active to false instead of deleting
        $jabatan->update(['is_active' => false]);

        return response()->json(['message' => 'Jabatan berhasil dinonaktifkan']);
    }

    protected function getValidationRules(): array
    {
        return [
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation
                function ($attribute, $value, $fail) {
                    $exists = Jabatan::where('nama_jabatan', 'ilike', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Jabatan sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ];
    }
}
