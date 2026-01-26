<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1'); // 10 attempts per minute
});

// Protected routes - requires authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Example: Admin only routes
    // Route::middleware('role:admin')->group(function () {
    //     Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    // });

    // Example: Multiple roles
    // Route::middleware('role:admin,manager')->group(function () {
    //     Route::get('/reports', [ReportController::class, 'index']);
    // });

    // Example: Permission-based
    // Route::middleware('permission:user.create')->group(function () {
    //     Route::post('/users', [UserController::class, 'store']);
    // });
});
