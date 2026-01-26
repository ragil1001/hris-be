<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'error' => 'Unauthorized Access',
    ], 403);
});
