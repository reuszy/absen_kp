<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\DailyAttendance;
use App\Models\Faculty;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');
        $status = $request->input('status'); // 'in' | 'out'
 
        $query = AttendanceLog::with(['staff.facultyData'])
            ->orderBy('scan_time', 'desc');
 
        // Filter by nama atau NIP
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staff', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%")
                       ->orWhere('nip', 'like', "%{$search}%");
                })->orWhere('machine_id', 'like', "%{$search}%");
            });
        }
 
        // Filter by tanggal
        if ($date) {
            $query->whereDate('scan_time', $date);
        }
 
        // Filter by status IN/OUT
        if ($status === 'in') {
            $query->where('status_scan', 0);
        } elseif ($status === 'out') {
            $query->where('status_scan', 1);
        }
 
        // Ringkasan hari ini (tidak ikut filter)
        $today           = Carbon::today()->toDateString();
        $totalHariIni    = AttendanceLog::whereDate('scan_time', $today)->count();
        $totalInHariIni  = AttendanceLog::whereDate('scan_time', $today)->where('status_scan', 0)->count();
        $totalOutHariIni = AttendanceLog::whereDate('scan_time', $today)->where('status_scan', 1)->count();
 
        $logs = $query->paginate(20)->withQueryString();
 
        return view('attendance.log', compact(
            'logs',
            'search',
            'date',
            'status',
            'totalHariIni',
            'totalInHariIni',
            'totalOutHariIni',
        ));
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

        $groupedData = $this->getReportData($startDate, $endDate, $facultyId, $dates);
        
        $summary = [
            'total_pegawai' => 0,
            'total_hadir' => 0,
            'total_terlambat' => 0,
            'total_transport' => 0
        ];

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

    private function getReportData($startDate, $endDate, $facultyId, $dates)
    {
        $staffQuery = Staff::with('facultyData')->orderBy('nama');
        if ($facultyId) {
            $staffQuery->where('faculty_id', $facultyId);
        }
        $allStaff = $staffQuery->get();

        $groupedData = [];
        foreach ($allStaff as $staff) {
            $groupedData[$staff->machine_id] = [
                'name' => $staff->nama_lengkap ?? $staff->nama,
                'nip' => $staff->nip ?? '-',
                'jabatan' => $staff->jabatan ?? '-',
                'unit_kerja' => $staff->facultyData->nama_fakultas ?? 'Belum Set',
                'attendance' => [],
                'leaves' => [],
                'total_hadir' => 0,
                'total_transport' => 0,
                'total_terlambat' => 0
            ];
        }

        $query = DailyAttendance::whereBetween('date', [$startDate, $endDate])->orderBy('jam_masuk', 'asc');
        if ($facultyId) {
            $query->whereHas('staff', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }
        $attendances = $query->get();

        foreach ($attendances as $atten) {
            if (!in_array($atten->date, $dates)) continue;
            
            $id = $atten->machine_id;
            if (isset($groupedData[$id])) {
                $groupedData[$id]['attendance'][$atten->date] = $atten;
                $groupedData[$id]['total_hadir']++;
                $groupedData[$id]['total_transport'] += $atten->uang_transport;

                if ($atten->status_kehadiran == 'Terlambat') {
                    $groupedData[$id]['total_terlambat']++;
                }
            }
        }

        // Fetch Leaves
        $leaves = \App\Models\Leave::with('staff')->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<', $startDate)
                     ->where('end_date', '>', $endDate);
              });
        });
        
        if ($facultyId) {
            $leaves->whereHas('staff', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }
        
        $leaves = $leaves->get();

        foreach ($leaves as $leave) {
            if ($leave->staff && isset($groupedData[$leave->staff->machine_id])) {
                $id = $leave->staff->machine_id;
                foreach ($dates as $date) {
                    if ($date >= $leave->start_date && $date <= $leave->end_date) {
                        if (!isset($groupedData[$id]['attendance'][$date])) {
                            $groupedData[$id]['leaves'][$date] = $leave->type;
                        }
                    }
                }
            }
        }

        $filteredData = [];
        foreach ($groupedData as $id => $data) {
            if ($data['total_hadir'] > 0 || count($data['leaves']) > 0) {
                $filteredData[$id] = $data;
            }
        }
        $groupedData = $filteredData;

        uasort($groupedData, function ($a, $b) {
            $levelA = $this->getJobLevel($a['jabatan']);
            $levelB = $this->getJobLevel($b['jabatan']);

            if ($levelA === $levelB) {
                return strcasecmp($a['name'], $b['name']);
            }

            return $levelA <=> $levelB;
        });

        return $groupedData;
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

        $groupedData = $this->getReportData($startDate, $endDate, $facultyId, $dates);

        $pdf = Pdf::loadView('rekap.rekap_pdf', [
            'groupedData' => $groupedData,
            'dates' => $dates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedDepartment' => $selectedDepartmentName,
            'selectedFaculty' => ($facultyId) ? $selectedDepartmentName : null,
            'type' => $type
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('Laporan_Absensi_' . $type . '_' . $startDate . '.pdf');
    }

    public function downloadExcel(Request $request)
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

        $groupedData = $this->getReportData($startDate, $endDate, $facultyId, $dates);

        $reportTitle = $facultyId ? 'ABSENSI SIDIK JARI UNIT KERJA ' . $selectedDepartmentName : 'ABSENSI SIDIK JARI UNIT (SEMUA FAKULTAS)';

        $data = [
            'groupedData' => $groupedData,
            'dates' => $dates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportTitle' => $reportTitle,
        ];

        return Excel::download(new AttendanceExport($data), 'Laporan_Absensi_' . $type . '_' . $startDate . '.xlsx');
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