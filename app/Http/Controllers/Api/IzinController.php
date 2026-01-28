<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IzinService;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class IzinController extends Controller
{
    protected $service;

    public function __construct(IzinService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Izin::query();

        if ($request->filled('search')) {
            $query->where('kategori', 'ilike', '%' . $request->search . '%')
                  ->orWhere('sub_kategori', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $izins = $query->orderBy('kategori')->paginate(20);

        return response()->json(['data' => $izins]);
    }

    public function show(Izin $izin)
    {
        return response()->json(['data' => $izin]);
    }

    public function store(Request $request)
    {
        $kategori = $request->input('kategori');
        $subKategori = $request->input('sub_kategori');
        
        // Check FIRST if izin with same kategori+sub_kategori (case-insensitive) exists but is inactive
        if ($kategori) {
            $query = Izin::where('kategori', 'ilike', $kategori)->where('is_active', false);
            if ($subKategori) {
                $query->where('sub_kategori', 'ilike', $subKategori);
            } else {
                $query->whereNull('sub_kategori');
            }
            $existingInactive = $query->first();

            if ($existingInactive) {
                // Reactivate the existing izin instead of creating new one
                $existingInactive->update(['is_active' => true]);
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Izin/Cuti berhasil diaktifkan kembali'
                ], 200);
            }
        }

        $validated = $request->validate($this->getValidationRules(), [], [
            'kategori' => 'Kategori',
            'sub_kategori' => 'Sub Kategori',
            'jumlah_hari' => 'Jumlah Hari',
        ]);

        $izin = $this->service->create($validated);

        return response()->json(['data' => $izin], 201);
    }

    public function update(Request $request, Izin $izin)
    {
        $validated = $request->validate($this->getValidationRules($izin->id), [], [
            'kategori' => 'Kategori',
            'sub_kategori' => 'Sub Kategori',
            'jumlah_hari' => 'Jumlah Hari',
        ]);

        $this->service->update($izin, $validated);

        return response()->json(['data' => $izin]);
    }

    public function destroy(Izin $izin)
    {
        // Soft delete: set is_active to false instead of deleting
        $izin->update(['is_active' => false]);

        return response()->json(['message' => 'Izin/Cuti berhasil dinonaktifkan']);
    }

    protected function getValidationRules(?int $ignoreId = null): array
    {
        return [
            'kategori' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive composite unique validation
                function ($attribute, $value, $fail) use ($ignoreId) {
                    $request = request();
                    $subKategori = $request->input('sub_kategori');
                    
                    $query = Izin::where('kategori', 'ilike', $value)
                        ->where('is_active', true);
                    
                    if ($subKategori) {
                        $query->where('sub_kategori', 'ilike', $subKategori);
                    } else {
                        $query->whereNull('sub_kategori');
                    }

                    if ($ignoreId) {
                        $query->where('id', '!=', $ignoreId);
                    }

                    if ($query->exists()) {
                        $fail('Kombinasi Kategori dan Sub Kategori tersebut sudah ada.');
                    }
                },
            ],
            'sub_kategori' => 'nullable|string|max:255',
            'jumlah_hari' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
