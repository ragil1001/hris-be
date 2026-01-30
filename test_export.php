<?php

use App\Exports\KaryawanTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Starting Export Test...\n";
    $export = new KaryawanTemplateExport();
    
    // Simulate what Excel::download does by storing to a file
    Excel::store($export, 'test_template.xlsx', 'local');
    
    file_put_contents('debug_log.txt', "Export Successful! File stored in storage/app/test_template.xlsx\n");
} catch (\Throwable $e) {
    $msg = "Export Failed!\n";
    $msg .= "Error: " . $e->getMessage() . "\n";
    $msg .= "Trace: " . $e->getTraceAsString() . "\n";
    file_put_contents('debug_log.txt', $msg);
}
