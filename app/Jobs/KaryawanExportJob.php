<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exports\KaryawanTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class KaryawanExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportId;

    public function __construct($exportId)
    {
        $this->exportId = $exportId;
    }

    public function handle(): void
    {
        $cacheKey = "export_progress_{$this->exportId}";
        Cache::put($cacheKey, ['status' => 'processing', 'progress' => 10, 'message' => 'Menyiapkan data...'], 600);

        try {
            $fileName = "exports/karyawan_export_{$this->exportId}.xlsx";
            
            // Note: Excel::store is synchronous. 
            // For real-time progress for 1000s of rows, we update cache before and after.
            Cache::put($cacheKey, ['status' => 'processing', 'progress' => 40, 'message' => 'Membuat file Excel...'], 600);
            
            Excel::store(new KaryawanTemplateExport(), $fileName, 'public');

            Cache::put($cacheKey, [
                'status' => 'completed', 
                'progress' => 100, 
                'message' => 'Selesai!',
                'file_url' => Storage::url($fileName)
            ], 600);
            
        } catch (\Exception $e) {
            Cache::put($cacheKey, [
                'status' => 'failed', 
                'progress' => 0, 
                'message' => 'Gagal: ' . $e->getMessage()
            ], 600);
        }
    }
}
