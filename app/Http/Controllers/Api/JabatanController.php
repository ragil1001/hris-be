<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JabatanService;
use App\Models\Jabatan;
use Illuminate\Http\Request;
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

        $jabatans = $query->orderBy('nama_jabatan')->paginate(20);

        return response()->json(['data' => $jabatans]);
    }

    public function show(Jabatan $jabatan)
    {
        return response()->json(['data' => $jabatan]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());

        $jabatan = $this->service->create($validated);

        return response()->json(['data' => $jabatan], 201);
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate($this->getValidationRules());

        $this->service->update($jabatan, $validated);

        return response()->json(['data' => $jabatan]);
    }

    public function destroy(Jabatan $jabatan)
    {
        $this->service->delete($jabatan);

        return response()->json(['message' => 'Deleted successfully']);
    }

    protected function getValidationRules(): array
    {
        return [
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
            'is_active' => 'boolean',
        ];
    }
}
