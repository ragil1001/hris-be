<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('permission:karyawan.view')->group(function () {
        Route::get('/karyawan', [KaryawanController::class, 'index']);
        Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show']);
    });

    Route::middleware('permission:karyawan.create')->post('/karyawan', [KaryawanController::class, 'store']);

    Route::middleware('permission:karyawan.edit')->group(function () {
        Route::patch('/karyawan/{karyawan}/reset-password', [KaryawanController::class, 'resetPassword']);
        Route::patch('/karyawan/{karyawan}/{section}', [KaryawanController::class, 'updateSection']);
    });

    Route::middleware('permission:karyawan.delete')->delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy']);

    Route::middleware('permission:project.view')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
    });

    Route::middleware('permission:project.create')->post('/projects', [ProjectController::class, 'store']);

    Route::middleware('permission:project.edit')->patch('/projects/{project}/{section}', [ProjectController::class, 'updateSection']);

    Route::middleware('permission:project.delete')->delete('/projects/{project}', [ProjectController::class, 'destroy']);
});
