<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceLog;
use App\Models\FingerprintDevice;
use App\Http\Controllers\Api\ScanController; 
use Illuminate\Http\Request;
use Jmrashed\Zkteco\Lib\ZKTeco; 

class TarikAbsen extends Command
{
    protected $signature = 'absen:tarik {device_id?}'; 
    protected $description = 'Tarik data log dari database fingerprint_devices';

    public function handle()
    {
        ini_set('memory_limit', '-1'); 
        set_time_limit(0); 

        $filterStartDate = '2025-12-01';
        $filterEndDate   = '2026-02-28';

        $deviceId = $this->argument('device_id');

        if ($deviceId) {
            $devices = FingerprintDevice::where('id', $deviceId)->get();
            if ($devices->isEmpty()) {
                $this->error("Device ID $deviceId tidak ditemukan.");
                return;
            }
        } else {
            $devices = FingerprintDevice::all();
        }

        $scanController = new ScanController();

        foreach ($devices as $device) {
            $this->info("Memproses: {$device->name} ({$device->vpn})");
            
            $zk = new ZKTeco($device->vpn, $device->port); 
            
            if (!$zk->connect()) {
                $this->error("Gagal konek ke {$device->name}. Skip.");
                continue;
            }

            $attendance = $zk->getAttendance();
            
            if (empty($attendance)) {
                $this->info("Data kosong.");
                $zk->disconnect();
                continue;
            }

            $collection = collect($attendance);
            $sortedAttendance = $collection->sortBy('timestamp');

            $bar = $this->output->createProgressBar(count($sortedAttendance));
            $bar->start();

            foreach ($sortedAttendance as $log) {
                $machineId = $log['id']; 
                $timestamp = $log['timestamp'];
                $verifyMode = $log['type'] ?? 1;

                
                if ($timestamp == '0000-00-00 00:00:00') continue;
                $dateOnly = date('Y-m-d', strtotime($timestamp));
                if ($dateOnly < $filterStartDate || $dateOnly > $filterEndDate) continue;

                
                $exists = AttendanceLog::where('machine_id', $machineId)
                            ->where('scan_time', $timestamp)
                            ->exists();

                if (!$exists) {
                    $request = new Request([
                        'machine_id' => $machineId,
                        'scan_time'  => $timestamp,
                        'verify_mode' => $verifyMode
                    ]);

                    try {
                        $scanController->store($request);
                    } catch (\Exception $e) { }
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $zk->enableDevice(); 
            $zk->disconnect(); 
        }

        $this->info("Semua Proses Selesai!");
    }
}