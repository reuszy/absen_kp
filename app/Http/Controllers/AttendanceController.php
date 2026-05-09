<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\DailyAttendance;
use App\Models\Faculty;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $logs = AttendanceLog::with(['staff.facultyData'])
                ->orderBy('scan_time', 'desc')
                ->paginate(20);

        return view('attendance.log', compact('logs'));
    }

    public function rekap(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $facultyId = $request->input('faculty_id');

        $faculties = Faculty::all();

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            if ($date->isWeekend()) {
                continue; 
            }
            $dates[] = $date->format('Y-m-d');
        }

        $query = DailyAttendance::with(['staff.facultyData'])
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('jam_masuk', 'asc');
                    
        if ($facultyId) {
            $query->whereHas('staff', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        $attendances = $query->get();

        $groupedData = [];
        
        $summary = [
            'total_pegawai' => 0,
            'total_hadir' => 0,
            'total_terlambat' => 0,
            'total_transport' => 0
        ];

        foreach ($attendances as $atten) {
            if (!in_array($atten->date, $dates)) {
                continue;
            }

            if (!$atten->staff) continue;

            $id = $atten->machine_id;

            if (!isset($groupedData[$id])) {
                $groupedData[$id] = [
                    'name' => $atten->staff->nama_lengkap ?? $atten->staff->nama,
                    'nip' => $atten->staff->nip ?? '-',
                    'jabatan' => $atten->staff->jabatan ?? 'Staff',
                    'unit_kerja' => $atten->staff->facultyData->nama_fakultas ?? 'Belum Set',
                    'attendance' => [],
                    'total_hadir' => 0,
                    'total_transport' => 0,
                    'total_terlambat' => 0
                ];
            }

            $groupedData[$id]['attendance'][$atten->date] = $atten;
            $groupedData[$id]['total_hadir']++;
            $groupedData[$id]['total_transport'] += $atten->uang_transport;

            if ($atten->status_kehadiran == 'Terlambat') {
                $groupedData[$id]['total_terlambat']++;
            }
        }

        $summary['total_pegawai'] = count($groupedData);
        foreach ($groupedData as $data) {
            $summary['total_hadir'] += $data['total_hadir'];
            $summary['total_terlambat'] += $data['total_terlambat'];
            $summary['total_transport'] += $data['total_transport'];
        }

        return view('attendance.rekap', compact(
            'groupedData', 
            'dates', 
            'startDate', 
            'endDate', 
            'faculties', 
            'facultyId', 
            'summary'
        ));
    }

    public function downloadPDF(Request $request)
    {
        $type = $request->input('type', 'custom');
        
        $endDateInput = $request->input('end_date', date('Y-m-d'));
        $targetDate = Carbon::parse($endDateInput);
        
        if ($type === 'daily') {
            $startDate = $targetDate->format('Y-m-d');
            $endDate = $targetDate->format('Y-m-d');
        } elseif ($type === 'weekly') {
            $startDate = $targetDate->startOfWeek()->format('Y-m-d'); 
            $endDate = $targetDate->endOfWeek()->format('Y-m-d');
        } elseif ($type === 'monthly') {
            $startDate = $targetDate->startOfMonth()->format('Y-m-d');
            $endDate = $targetDate->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $request->input('start_date', date('Y-m-d'));
            $endDate = $endDateInput;
        }

        $facultyId = $request->input('faculty_id');
        $selectedDepartmentName = 'SEMUA UNIT KERJA';

        if ($facultyId) {
            $fac = Faculty::find($facultyId);
            if($fac) $selectedDepartmentName = strtoupper($fac->nama_fakultas);
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            if ($date->isWeekend()) continue;
            $dates[] = $date->format('Y-m-d');
        }

        $query = DailyAttendance::with(['staff.facultyData'])
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('jam_masuk', 'asc');

        if ($facultyId) {
            $query->whereHas('staff', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        $attendances = $query->get();
        $groupedData = [];

        foreach ($attendances as $atten) {
            if (!in_array($atten->date, $dates)) continue;
            if (!$atten->staff) continue;

            $id = $atten->machine_id;
            
            if (!isset($groupedData[$id])) {
                $groupedData[$id] = [
                    'name' => $atten->staff->nama_lengkap ?? $atten->staff->nama,
                    'nip' => $atten->staff->nip ?? '-',
                    'jabatan' => $atten->staff->jabatan ?? '-',
                    'attendance' => [],
                    'total_hadir' => 0,
                    'total_transport' => 0
                ];
            }

            $groupedData[$id]['attendance'][$atten->date] = $atten;
            $groupedData[$id]['total_hadir']++;
            $groupedData[$id]['total_transport'] += $atten->uang_transport;
        }

        // Sorting Logic based on Hierarchy
        uasort($groupedData, function ($a, $b) {
            $levelA = $this->getJobLevel($a['jabatan']);
            $levelB = $this->getJobLevel($b['jabatan']);

            if ($levelA === $levelB) {
                return strcasecmp($a['name'], $b['name']);
            }

            return $levelA <=> $levelB;
        });

        $pdf = Pdf::loadView('rekap.rekap_pdf', [
            'groupedData' => $groupedData,
            'dates' => $dates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedDepartment' => $selectedDepartmentName, // Keep this for backward compatibility if needed, or remove if unused
            'selectedFaculty' => ($facultyId) ? $selectedDepartmentName : null, // Pass as selectedFaculty
            'type' => $type
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('Laporan_Absensi_' . $type . '_' . $startDate . '.pdf');
    }

    private function getJobLevel($position)
    {
        $position = trim($position);

        $levels = [
            'Rektor' => 1,
            'Wakil Rektor I' => 2,
            'Wakil Rektor II' => 3,
            'Wakil Rektor III' => 4,
            'Wakil Rektor IV' => 5,
            'Dekan' => 6,
            'Wakil Dekan' => 7,
            'Ketua Prodi' => 8,
            'Kepala' => 9,
            'Anggota' => 10,
            'Sekretaris' => 11,
            'Staf' => 12,
            'Pramubakti'=> 13,
        ];

        foreach ($levels as $key => $level) {
            if (stripos($position, $key) !== false) {
                return $level;
            }
        }

        return 99; // Default for others
    }
}