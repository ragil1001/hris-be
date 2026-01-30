<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\KaryawanTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Response;

class KaryawanExportController extends Controller
{
    public function template()
    {
        return Excel::download(new KaryawanTemplateExport, 'Template_Import_Karyawan.xlsx');
    }
}
