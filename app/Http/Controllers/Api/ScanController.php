<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\DailyAttendance;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'scan_time'  => 'nullable|date_format:Y-m-d H:i:s',
            'verify_mode' => 'nullable|integer',
        ]);

        $machineId = $request->machine_id;
        $scanTime = $request->scan_time ? Carbon::parse($request->scan_time) : Carbon::now();
        $verifyMode = $request->verify_mode;
        
        $dateOnly = $scanTime->format('Y-m-d');
        $timeOnly = $scanTime->format('H:i:s');

        $log = AttendanceLog::create([
            'machine_id'  => $machineId,
            'scan_time'   => $scanTime,
            'status_scan' => 0,
            'verify_mode' => $verifyMode,
        ]);

        $staff = Staff::with('workShift')->where('machine_id', $machineId)->first();

        if (!$staff) {
            return response()->json([
                'message' => 'Staff Tidak Ditemukan (Machine ID tidak terdaftar)',
                'data'    => $log
            ], 404);
        }

        $shift = $staff->workShift;

        $jamMasukShift = $shift ? $shift->jam_masuk : '08:15:00';

        $daily = DailyAttendance::where('machine_id', $machineId)
                    ->where('date', $dateOnly)
                    ->first();

        if (!$daily) {

            $jadwalMasuk = Carbon::parse($jamMasukShift);
            $waktuScan   = Carbon::parse($timeOnly);
            
            $isTerlambat = $waktuScan > $jadwalMasuk;
            $menitTerlambat = 0;

            if ($isTerlambat) {
                $menitTerlambat = $jadwalMasuk->diffInMinutes($waktuScan);
            }

            DailyAttendance::create([
                'machine_id'       => $machineId,
                'date'             => $dateOnly,
                'jam_masuk'        => $timeOnly,
                'jam_pulang'       => null,
                'status_kehadiran' => $isTerlambat ? 'Terlambat' : 'Hadir',
                'terlambat_menit'  => $menitTerlambat,
                'uang_transport'   => 0
            ]);

            $log->update(['status_scan' => 0]);

            return response()->json([
                'message' => 'Absen Masuk Berhasil', 
                'type' => 'IN',
                'status' => $isTerlambat ? 'Terlambat ' . $menitTerlambat . ' menit' : 'Tepat Waktu'
            ]);

        } else {
            
            if ($daily->jam_pulang == null) {
                
                $nominalTransport = $shift->uang_transport ?? 0;
                
                $jamPulangShift = $shift->jam_pulang ?? '17:00:00'; 

                if ($daily->status_kehadiran === 'Terlambat') {
                    $nominalTransport = 0; 
                } 
                elseif ($timeOnly < $jamPulangShift) {
                    $nominalTransport = 0; 
                } 
                elseif ($timeOnly > '18:00:00') {
                    $nominalTransport = 0; 
                }

                $daily->update([
                    'jam_pulang'     => $timeOnly,
                    'uang_transport' => $nominalTransport
                ]);

                $log->update(['status_scan' => 1]);

                return response()->json([
                    'message'   => 'Absen Pulang Berhasil', 
                    'type'      => 'OUT',
                    'transport' => $nominalTransport
                ]);
            } else {
                return response()->json([
                    'message' => 'Sudah Absen Pulang Sebelumnya', 
                    'type'    => 'IGNORE'
                ]);
            }
        }
    }
}