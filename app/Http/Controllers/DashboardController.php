<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\DailyAttendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalPegawai = Staff::whereNull('deleted_at')->count();
 
        $hadirHariIni = DailyAttendance::whereDate('date', $today)->count();
 
        $terlambatHariIni = DailyAttendance::whereDate('date', $today)
            ->where('status_kehadiran', 'Terlambat')
            ->count();

        $totalTransportBulanIni = DailyAttendance::whereYear('date', Carbon::today()->year)
            ->whereMonth('date', Carbon::today()->month)
            ->sum('uang_transport');
 
        
        // Chart 1: Tren 7 Hari Terakhir

        $startDate = Carbon::today()->subDays(6);
        $endDate   = Carbon::today();
 
        $attendanceIn7Days = DailyAttendance::whereBetween('date', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ])->get();
 
        $dates           = [];
        $attendanceCounts = [];
        $tepatWaktuData  = [];
        $terlambatData   = [];
 
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $dayData = $attendanceIn7Days->where('date', $dateStr);
 
            $dates[]             = $date->format('d M');
            $attendanceCounts[]  = $dayData->count();
            $tepatWaktuData[]    = $dayData->where('status_kehadiran', 'Tepat Waktu')->count();
            $terlambatData[]     = $dayData->where('status_kehadiran', 'Terlambat')->count();
        }
 
        
        // Chart 2: Komposisi Kehadiran Hari Ini

        $presentMachineIds = DailyAttendance::whereDate('date', $today)
            ->pluck('machine_id');
 
        $staffHadir = Staff::whereIn('machine_id', $presentMachineIds)
            ->whereNull('deleted_at')
            ->get();
 
        $pejabatPresent = 0;
        $staffPresent   = 0;
 
        foreach ($staffHadir as $s) {
            $jabatan = strtolower($s->jabatan ?? '');
            $isPejabat = str_contains($jabatan, 'dekan')
                || str_contains($jabatan, 'rektor')
                || str_contains($jabatan, 'ketua')
                || str_contains($jabatan, 'kepala')
                || str_contains($jabatan, 'wakil');
 
            $isPejabat ? $pejabatPresent++ : $staffPresent++;
        }

        $unknownPresent = $presentMachineIds->diff($staffHadir->pluck('machine_id'))->count();
 
        $recentLogs = AttendanceLog::with(['staff.facultyData'])
            ->orderBy('scan_time', 'desc')
            ->take(8)
            ->get();
 
        return view('dashboard', compact(
            'totalPegawai',
            'hadirHariIni',
            'terlambatHariIni',
            'totalTransportBulanIni',
            'recentLogs',
            'today',
            'dates',
            'attendanceCounts',
            'tepatWaktuData',
            'terlambatData',
            'staffPresent',
            'pejabatPresent',
            'unknownPresent',
        ));
    }
}
