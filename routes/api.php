<?php

use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\SimulasiAbsenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Tarik absen dari Alat Fingerprint
Route::post('/scan', [ScanController::class, 'store']);


// Data Fakultas
Route::apiResource('faculties', FacultyController::class);


// Simulasi Absen
Route::prefix('simulasi')->group(function () {
    Route::get('/staff', [SimulasiAbsenController::class, 'listStaff']);
    Route::post('/scan', [SimulasiAbsenController::class, 'simulasiScan']);
    Route::post('/reset', [SimulasiAbsenController::class, 'resetHariIni']);
});
