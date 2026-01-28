<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FormasiService;
use App\Models\Formasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FormasiController extends Controller
{
    protected $service;

    public function __construct(FormasiService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Formasi::query();

        if ($request->filled('search')) {
            $query->where('nama_formasi', 'ilike', '%' . $request->search . '%');
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
        $formasis = $query->orderBy('nama_formasi')->paginate($perPage);

        return response()->json($formasis);
    }

    public function show(Formasi $formasi)
    {
        return response()->json(['data' => $formasi]);
    }


    public function store(Request $request)
    {
        $namaFormasi = $request->input('nama_formasi');
        
        // Check FIRST if formasi with same name (case-insensitive) exists but is inactive
        if ($namaFormasi) {
            $existingInactive = Formasi::where('nama_formasi', 'ilike', $namaFormasi)
                ->where('is_active', false)
                ->first();

            if ($existingInactive) {
                // Reactivate the existing formasi instead of creating new one
                $existingInactive->update(['is_active' => true]);
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Formasi berhasil diaktifkan kembali'
                ], 200);
            }
        }

        // Only validate if no inactive record to reactivate
        $validated = $request->validate($this->getValidationRules(), [], [
            'nama_formasi' => 'Nama Formasi'
        ]);

        // Create new formasi
        $formasi = $this->service->create($validated);

        return response()->json(['data' => $formasi], 201);
    }

    public function update(Request $request, Formasi $formasi)
    {
        // Validate but ignore current record for unique check
        $validated = $request->validate([
            'nama_formasi' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation ignoring self
                function ($attribute, $value, $fail) use ($formasi) {
                    $exists = Formasi::where('nama_formasi', 'ilike', $value)
                        ->where('is_active', true)
                        ->where('id', '!=', $formasi->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Formasi sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ], [], [
            'nama_formasi' => 'Nama Formasi'
        ]);

        $this->service->update($formasi, $validated);

        return response()->json(['data' => $formasi]);
    }

    public function destroy(Formasi $formasi)
    {
        // Soft delete: set is_active to false instead of deleting
        $formasi->update(['is_active' => false]);

        return response()->json(['message' => 'Formasi berhasil dinonaktifkan']);
    }

    protected function getValidationRules(): array
    {
        return [
            'nama_formasi' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation
                function ($attribute, $value, $fail) {
                    $exists = Formasi::where('nama_formasi', 'ilike', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Formasi sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ];
    }
}
