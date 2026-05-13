<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyWebController;
use App\Http\Controllers\FingerprintDeviceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StafController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// Protected Routes (Auth Required)
Route::middleware('auth')->group(function () {

    // Pengaturan Akun: Semua role
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');

    // Dashboard: Semua role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index']);

    // Log & Rekap: Semua role
    Route::get('/logs', [AttendanceController::class, 'index'])->name('attendance.logs');
    Route::get('/rekap', [AttendanceController::class, 'rekap'])->name('attendance.rekap');
    Route::get('/rekap/download-pdf', [AttendanceController::class, 'downloadPDF'])->name('attendance.download_pdf');
    Route::get('/rekap/download-excel', [AttendanceController::class, 'downloadExcel'])->name('attendance.download_excel');

    // Manajemen Izin & Cuti: Semua role
    Route::resource('leaves', LeaveController::class);

    // Data Staf: CRU untuk semua role
    Route::resource('staf', StafController::class)->except(['destroy']);

    // Pengaturan & Delete Staf: Hanya Admin
    Route::middleware('role:admin')->group(function () {
        Route::delete('staf/{staf}', [StafController::class, 'destroy'])->name('staf.destroy');
        Route::resource('user', UserController::class)->except(['show']);
        
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/simulasi-absen', fn() => view('simulasi.index'))->name('simulasi.index');

        Route::resource('fakultas', FacultyWebController::class)->except(['show'])->parameters(['fakultas' => 'fakultas']);
        Route::resource('fingerprint', FingerprintDeviceController::class)->except(['show']);
    });

});