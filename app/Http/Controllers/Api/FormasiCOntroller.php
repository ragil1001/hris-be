<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FormasiService;
use App\Models\Formasi;
use Illuminate\Http\Request;
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

        $formasis = $query->orderBy('nama_formasi')->paginate(20);

        return response()->json(['data' => $formasis]);
    }

    public function show(Formasi $formasi)
    {
        return response()->json(['data' => $formasi]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());

        $formasi = $this->service->create($validated);

        return response()->json(['data' => $formasi], 201);
    }

    public function update(Request $request, Formasi $formasi)
    {
        $validated = $request->validate($this->getValidationRules());

        $this->service->update($formasi, $validated);

        return response()->json(['data' => $formasi]);
    }

    public function destroy(Formasi $formasi)
    {
        $this->service->delete($formasi);

        return response()->json(['message' => 'Deleted successfully']);
    }

    protected function getValidationRules(): array
    {
        return [
            'nama_formasi' => 'required|string|max:255|unique:formasi,nama_formasi',
            'is_active' => 'boolean',
        ];
    }
}
