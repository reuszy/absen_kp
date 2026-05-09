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
        $today = Carbon::today()->format('Y-m-d');

        // Statistics
        $totalStaff = Staff::count();
        $totalPegawai = $totalStaff;

        $hadirHariIni = DailyAttendance::where('date', $today)->count();
        
        $terlambatHariIni = DailyAttendance::where('date', $today)
                            ->where('status_kehadiran', 'Terlambat')
                            ->count();

        // Recent Activity (Logs) - Increased limit
        $recentLogs = AttendanceLog::with(['staff'])
                        ->orderBy('scan_time', 'desc')
                        ->take(8)
                        ->get();

        // Chart 1: Weekly Attendance Trend (Last 7 Days)
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6);
        $period = CarbonPeriod::create($startDate, $endDate);
        
        $dates = [];
        $attendanceCounts = [];
        
        foreach($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('d M'); // Label: 20 Jan
            $attendanceCounts[] = DailyAttendance::where('date', $formattedDate)->count();
        }

        // Chart 2: Today's Composition (Dosen vs Staff)
        // Get list of machine_ids present today
        $presentIds = DailyAttendance::where('date', $today)->pluck('machine_id');
        
        // Count how many are Dosen vs Staff
        $staffPresent = Staff::whereIn('machine_id', $presentIds)->count();
        // Fallback for unknown IDs (optional, but good for data integrity check)
        $unknownPresent = $hadirHariIni - ($staffPresent);


        return view('dashboard', compact(
            'totalPegawai', 
            'hadirHariIni', 
            'terlambatHariIni', 
            'recentLogs',
            'today',
            'dates',
            'attendanceCounts',
            'staffPresent',
            'unknownPresent',
            'totalStaff'
        ));
    }
}
