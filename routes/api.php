<?php

use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\ScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/scan', [ScanController::class, 'store']);

Route::apiResource('faculties', FacultyController::class);
