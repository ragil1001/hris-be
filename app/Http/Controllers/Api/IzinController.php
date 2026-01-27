<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IzinService;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $validated = $request->validate($this->getValidationRules());

        $izin = $this->service->create($validated);

        return response()->json(['data' => $izin], 201);
    }

    public function update(Request $request, Izin $izin)
    {
        $validated = $request->validate($this->getValidationRules());

        $this->service->update($izin, $validated);

        return response()->json(['data' => $izin]);
    }

    public function destroy(Izin $izin)
    {
        $this->service->delete($izin);

        return response()->json(['message' => 'Deleted successfully']);
    }

    protected function getValidationRules(): array
    {
        return [
            'kategori' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'jumlah_hari' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
