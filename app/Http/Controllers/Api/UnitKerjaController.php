<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnitKerjaService;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitKerjaController extends Controller
{
    protected $service;

    public function __construct(UnitKerjaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = UnitKerja::query();

        if ($request->filled('search')) {
            $query->where('nama_unit', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Keep backward compatibility with is_active param
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $perPage = $request->input('per_page', 10);
        $unitKerjas = $query->latest('id')->paginate($perPage);

        return response()->json($unitKerjas);
    }

    public function show(UnitKerja $unitKerja)
    {
        return response()->json(['data' => $unitKerja]);
    }


    public function store(Request $request)
    {
        $namaUnit = $request->input('nama_unit');
        
        // Check FIRST if unit kerja with same name (case-insensitive) exists but is inactive
        if ($namaUnit) {
            $existingInactive = UnitKerja::where('nama_unit', 'ilike', $namaUnit)
                ->where('is_active', false)
                ->first();

            if ($existingInactive) {
                // Reactivate the existing unit kerja instead of creating new one
                $existingInactive->update(['is_active' => true]);
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Unit Kerja berhasil diaktifkan kembali'
                ], 200);
            }
        }

        // Only validate if no inactive record to reactivate
        $validated = $request->validate($this->getValidationRules(), [], [
            'nama_unit' => 'Nama Unit Kerja'
        ]);

        // Create new unit kerja
        $unitKerja = $this->service->create($validated);

        return response()->json(['data' => $unitKerja], 201);
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        // Validate but ignore current record for unique check
        $validated = $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation ignoring self
                function ($attribute, $value, $fail) use ($unitKerja) {
                    $exists = UnitKerja::where('nama_unit', 'ilike', $value)
                        ->where('is_active', true)
                        ->where('id', '!=', $unitKerja->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Unit Kerja sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ], [], [
            'nama_unit' => 'Nama Unit Kerja'
        ]);

        $this->service->update($unitKerja, $validated);

        return response()->json(['data' => $unitKerja]);
    }

    public function destroy(UnitKerja $unitKerja)
    {
        // Soft delete: set is_active to false instead of deleting
        $this->service->delete($unitKerja);

        return response()->json(['message' => 'Unit Kerja berhasil dinonaktifkan']);
    }

    protected function getValidationRules(): array
    {
        return [
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation
                function ($attribute, $value, $fail) {
                    $exists = UnitKerja::where('nama_unit', 'ilike', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Unit Kerja sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ];
    }
}
