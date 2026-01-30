<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\KaryawanTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Response;
use App\Jobs\KaryawanExportJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KaryawanExportController extends Controller
{
    public function template()
    {
        return Excel::download(new KaryawanTemplateExport(true), 'Template_Import_Karyawan.xlsx');
    }

    public function export()
    {
        // Hapus file export lama sebelum membuat yang baru
        $files = Storage::disk('public')->files('exports');
        if (!empty($files)) {
            Storage::disk('public')->delete($files);
        }

        $exportId = Str::uuid()->toString();
        Cache::put("export_progress_{$exportId}", ['status' => 'pending', 'progress' => 0, 'message' => 'Menunggu antrian...'], 300);
        
        KaryawanExportJob::dispatch($exportId);
        
        return response()->json([
            'export_id' => $exportId
        ]);
    }

    public function status($exportId)
    {
        $progress = Cache::get("export_progress_{$exportId}");
        
        if (!$progress) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        return response()->json($progress);
    }

    public function download($exportId)
    {
        $fileName = "exports/karyawan_export_{$exportId}.xlsx";
        
        if (!Storage::disk('public')->exists($fileName)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }
        
        return Storage::disk('public')->download($fileName, "Data_Karyawan_" . date('Ymd_His') . ".xlsx");
    }
}
