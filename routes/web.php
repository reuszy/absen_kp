<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use function PHPUnit\Framework\returnArgument;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [DashboardController::class, 'index']);

Route::get('/logs', [AttendanceController::class, 'index'])->name('attendance.logs');
Route::get('/rekap', [AttendanceController::class, 'rekap'])->name('attendance.rekap');
Route::get('/rekap/download-pdf', [AttendanceController::class, 'downloadPDF'])->name('attendance.download_pdf');

use App\Http\Controllers\StafController;
Route::resource('staf', StafController::class);