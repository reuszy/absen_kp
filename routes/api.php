<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\ManualPresenceController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\ScanController;
use Illuminate\Support\Facades\Route;


// Tarik absen dari Alat Fingerprint
Route::post('/scan', [ScanController::class, 'store']);


// Auth API
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
    });
});

// Absen Manual (publik, dipakai oleh halaman web via session)
Route::prefix('manualPresence')->group(function () {
    Route::get('/staff', [ManualPresenceController::class, 'listStaff']);
    Route::post('/scan', [ManualPresenceController::class, 'manualScan']);
    Route::post('/reset', [ManualPresenceController::class, 'resetHariIni']);
});

// Faculties (dilindungi Sanctum token)
Route::middleware('auth:sanctum')->prefix('faculties')->group(function () {
    Route::get('/', [FacultyController::class, 'index']);
    Route::post('/', [FacultyController::class, 'store']);
    Route::get('/{id}', [FacultyController::class, 'show']);
    Route::put('/{id}', [FacultyController::class, 'update']);
    Route::delete('/{id}', [FacultyController::class, 'destroy']);
});

// Monitoring (dilindungi Sanctum token)
Route::middleware('auth:sanctum')->prefix('monitoring')->group(function () {
    Route::get('/dashboard', [MonitoringController::class, 'dashboard']);
    Route::get('/attendance/logs', [MonitoringController::class, 'logs']);
    Route::get('/attendance/rekap', [MonitoringController::class, 'rekap']);
    Route::get('/staff', [MonitoringController::class, 'staff']);
});
