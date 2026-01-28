<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $query = Bank::query();

        if ($request->filled('search')) {
            $query->where('nama_bank', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->get('per_page', 20);
        $banks = $query->orderBy('nama_bank')->paginate($perPage);

        return response()->json($banks);
    }

    public function show(Bank $bank)
    {
        return response()->json(['data' => $bank]);
    }

    public function store(Request $request)
    {
        $namaBank = $request->input('nama_bank');
        
        // Check FIRST if bank with same name (case-insensitive) exists but is inactive
        if ($namaBank) {
            $existingInactive = Bank::where('nama_bank', 'ilike', $namaBank)
                ->where('is_active', false)
                ->first();

            if ($existingInactive) {
                // Reactivate the existing bank instead of creating new one
                $existingInactive->update(['is_active' => true]);
                return response()->json([
                    'data' => $existingInactive,
                    'message' => 'Bank berhasil diaktifkan kembali'
                ], 200);
            }
        }

        $validated = $request->validate([
            'nama_bank' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation
                function ($attribute, $value, $fail) {
                    $exists = Bank::where('nama_bank', 'ilike', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Bank sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ], [], [
            'nama_bank' => 'Nama Bank'
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $bank = Bank::create($validated);

        return response()->json(['data' => $bank], 201);
    }

    public function update(Request $request, Bank $bank)
    {
        // Validate but ignore current record for unique check
        $validated = $request->validate([
            'nama_bank' => [
                'required',
                'string',
                'max:255',
                // Custom case-insensitive unique validation ignoring self
                function ($attribute, $value, $fail) use ($bank) {
                    $exists = Bank::where('nama_bank', 'ilike', $value)
                        ->where('is_active', true)
                        ->where('id', '!=', $bank->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nama Bank sudah digunakan.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ], [], [
            'nama_bank' => 'Nama Bank'
        ]);

        $bank->update($validated);

        return response()->json(['data' => $bank]);
    }

    public function destroy(Bank $bank)
    {
        // Soft delete: set is_active to false instead of deleting
        $bank->update(['is_active' => false]);

        return response()->json(['message' => 'Bank berhasil dinonaktifkan']);
    }
}
