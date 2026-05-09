<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('absen:tarik')
    ->everyFiveMinutes()
    ->when(function () {
        // [MANUAL TOGGLE] 
        // Ubah true menjadi false jika Anda ingin mematikan tarikan data otomatis via cron
        $autoSyncEnabled = false; 

        return $autoSyncEnabled;
    });
