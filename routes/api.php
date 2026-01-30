<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\JabatanController;
use App\Http\Controllers\Api\FormasiController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\UnitKerjaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/karyawans/template-import', [\App\Http\Controllers\Api\KaryawanExportController::class, 'template']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('permission:karyawan.view')->group(function () {
        Route::get('/karyawans', [KaryawanController::class, 'index']);
        Route::get('/karyawans/{karyawan}', [KaryawanController::class, 'show']);
        
        // Export Routes
        Route::post('/karyawans/export', [\App\Http\Controllers\Api\KaryawanExportController::class, 'export']);
        Route::get('/karyawans/export/status/{exportId}', [\App\Http\Controllers\Api\KaryawanExportController::class, 'status']);
        Route::get('/karyawans/export/download/{exportId}', [\App\Http\Controllers\Api\KaryawanExportController::class, 'download']);
        Route::post('/karyawans/import', [App\Http\Controllers\Api\KaryawanImportController::class, 'import']);
});

    Route::middleware('permission:karyawan.create')->post('/karyawans', [KaryawanController::class, 'store']);

    Route::middleware('permission:karyawan.edit')->patch('/karyawans/{karyawan}/{section}', [KaryawanController::class, 'updateSection']);

    Route::middleware('permission:karyawan.delete')->delete('/karyawans/{karyawan}', [KaryawanController::class, 'destroy']);

    Route::middleware('permission:project.view')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
    });

    Route::middleware('permission:project.create')->post('/projects', [ProjectController::class, 'store']);

    Route::middleware('permission:project.edit')->patch('/projects/{project}/{section}', [ProjectController::class, 'updateSection']);

    Route::middleware('permission:project.edit')->patch('/projects/{project}/reactivate', [ProjectController::class, 'reactivate']);

    Route::middleware('permission:project.delete')->delete('/projects/{project}', [ProjectController::class, 'destroy']);

    Route::middleware('permission:jabatan.view')->group(function () {
        Route::get('/jabatans', [JabatanController::class, 'index']);
        Route::get('/jabatans/{jabatan}', [JabatanController::class, 'show']);
    });

    Route::middleware('permission:jabatan.create')->post('/jabatans', [JabatanController::class, 'store']);

    Route::middleware('permission:jabatan.edit')->patch('/jabatans/{jabatan}', [JabatanController::class, 'update']);

    Route::middleware('permission:jabatan.delete')->delete('/jabatans/{jabatan}', [JabatanController::class, 'destroy']);

    Route::middleware('permission:formasi.view')->group(function () {
        Route::get('/formasis', [FormasiController::class, 'index']);
        Route::get('/formasis/{formasi}', [FormasiController::class, 'show']);
    });

    Route::middleware('permission:formasi.create')->post('/formasis', [FormasiController::class, 'store']);

    Route::middleware('permission:formasi.edit')->patch('/formasis/{formasi}', [FormasiController::class, 'update']);

    Route::middleware('permission:formasi.delete')->delete('/formasis/{formasi}', [FormasiController::class, 'destroy']);

    Route::middleware('permission:izin.view')->group(function () {
        Route::get('/izins', [IzinController::class, 'index']);
        Route::get('/izins/{izin}', [IzinController::class, 'show']);
    });

    Route::middleware('permission:izin.create')->post('/izins', [IzinController::class, 'store']);

    Route::middleware('permission:izin.edit')->patch('/izins/{izin}', [IzinController::class, 'update']);

    Route::middleware('permission:izin.delete')->delete('/izins/{izin}', [IzinController::class, 'destroy']);

    // Bank routes
    Route::middleware('permission:bank.view')->group(function () {
        Route::get('/banks', [BankController::class, 'index']);
        Route::get('/banks/{bank}', [BankController::class, 'show']);
    });

    Route::middleware('permission:bank.create')->post('/banks', [BankController::class, 'store']);

    Route::middleware('permission:bank.edit')->patch('/banks/{bank}', [BankController::class, 'update']);

    Route::middleware('permission:bank.delete')->delete('/banks/{bank}', [BankController::class, 'destroy']);

    // Unit Kerja routes
    Route::middleware('permission:unit_kerja.view')->group(function () {
        Route::get('/unit-kerjas', [UnitKerjaController::class, 'index']);
        Route::get('/unit-kerjas/{unitKerja}', [UnitKerjaController::class, 'show']);
    });

    Route::middleware('permission:unit_kerja.create')->post('/unit-kerjas', [UnitKerjaController::class, 'store']);

    Route::middleware('permission:unit_kerja.edit')->patch('/unit-kerjas/{unitKerja}', [UnitKerjaController::class, 'update']);

    Route::middleware('permission:unit_kerja.delete')->delete('/unit-kerjas/{unitKerja}', [UnitKerjaController::class, 'destroy']);
});
