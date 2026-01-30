<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\KaryawanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class KaryawanImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:51200', // 50MB max
        ]);

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 minutes

        try {
            $file = $request->file('file');
            Log::info('Import Karyawan: ' . $file->getClientOriginalName()); 
            // If the user has thousands of rows, we might need a job + progress tracking like export.
            Excel::import(new KaryawanImport, $file);

            return response()->json([
                'message' => 'Data karyawan berhasil diimport',
                'status' => 'success'
            ]);

        } catch (\Throwable $e) {
            Log::error('Import Karyawan Error: ' . $e->getMessage());
            
            // If it contains "Baris", it's likely a validation error from KaryawanImport
            $statusCode = str_contains($e->getMessage(), 'Baris') ? 422 : 500;

            return response()->json([
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
                'status' => 'error'
            ], $statusCode);
        }
    }
}
