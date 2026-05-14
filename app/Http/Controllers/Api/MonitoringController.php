<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\DailyAttendance;
use App\Models\Faculty;
use App\Models\Leave;
use App\Models\Staff;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * GET /api/dashboard
     * Statistik ringkas kehadiran + tren 7 hari + log terbaru.
     */
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $totalPegawai        = Staff::whereNull('deleted_at')->count();
        $hadirHariIni        = DailyAttendance::whereDate('date', $today)->count();
        $terlambatHariIni    = DailyAttendance::whereDate('date', $today)->where('status_kehadiran', 'Terlambat')->count();
        $totalTransportBulan = DailyAttendance::whereYear('date', Carbon::today()->year)
            ->whereMonth('date', Carbon::today()->month)
            ->sum('uang_transport');

        $startDate = Carbon::today()->subDays(6);
        $endDate   = Carbon::today();

        $attendanceIn7Days = DailyAttendance::whereBetween('date', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ])->get();

        $leavesIn7Days = Leave::where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(fn($q2) => $q2->where('start_date', '<', $startDate)->where('end_date', '>', $endDate));
        })->get();

        $tren = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateStr   = $date->toDateString();
            $dayData   = $attendanceIn7Days->where('date', $dateStr);
            $izinCount = $leavesIn7Days->filter(fn($l) => $dateStr >= $l->start_date && $dateStr <= $l->end_date)->count();

            $tren[] = [
                'tanggal'     => $date->format('d M'),
                'hadir'       => $dayData->count(),
                'tepat_waktu' => $dayData->where('status_kehadiran', 'Hadir')->count(),
                'terlambat'   => $dayData->where('status_kehadiran', 'Terlambat')->count(),
                'izin'        => $izinCount,
            ];
        }

        $recentLogs = AttendanceLog::with(['staff.facultyData'])
            ->orderBy('scan_time', 'desc')
            ->take(8)
            ->get()
            ->map(fn($log) => [
                'nama'        => $log->staff->nama ?? 'Unknown',
                'fakultas'    => $log->staff->facultyData->nama_fakultas ?? '-',
                'scan_time'   => $log->scan_time,
                'status_scan' => $log->status_scan == 0 ? 'Masuk' : 'Pulang',
            ]);

        return response()->json([
            'tanggal'               => $today,
            'total_pegawai'         => $totalPegawai,
            'hadir_hari_ini'        => $hadirHariIni,
            'terlambat_hari_ini'    => $terlambatHariIni,
            'total_transport_bulan' => $totalTransportBulan,
            'tren_7_hari'           => $tren,
            'log_terbaru'           => $recentLogs,
        ]);
    }

    /**
     * GET /api/attendance/logs
     * Log scan absensi dengan filter: search, date, status (in|out), per_page.
     */
    public function logs(Request $request)
    {
        $query = AttendanceLog::with(['staff.facultyData'])->orderBy('scan_time', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staff', fn($q2) =>
                    $q2->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%")
                )->orWhere('machine_id', 'like', "%{$search}%");
            });
        }

        if ($date = $request->input('date')) {
            $query->whereDate('scan_time', $date);
        }

        if ($request->input('status') === 'in') {
            $query->where('status_scan', 0);
        } elseif ($request->input('status') === 'out') {
            $query->where('status_scan', 1);
        }

        $today           = Carbon::today()->toDateString();
        $totalHariIni    = AttendanceLog::whereDate('scan_time', $today)->count();
        $totalInHariIni  = AttendanceLog::whereDate('scan_time', $today)->where('status_scan', 0)->count();
        $totalOutHariIni = AttendanceLog::whereDate('scan_time', $today)->where('status_scan', 1)->count();

        $perPage = min((int) $request->input('per_page', 20), 100);
        $logs = $query->paginate($perPage)->through(fn($log) => [
            'id'          => $log->id,
            'machine_id'  => $log->machine_id,
            'nama'        => $log->staff->nama ?? 'Unknown',
            'nip'         => $log->staff->nip ?? '-',
            'fakultas'    => $log->staff->facultyData->nama_fakultas ?? '-',
            'scan_time'   => $log->scan_time,
            'status_scan' => $log->status_scan == 0 ? 'Masuk' : 'Pulang',
        ]);

        return response()->json([
            'ringkasan_hari_ini' => [
                'total'  => $totalHariIni,
                'masuk'  => $totalInHariIni,
                'pulang' => $totalOutHariIni,
            ],
            'logs' => $logs,
        ]);
    }

    /**
     * GET /api/attendance/rekap
     * Rekap kehadiran per pegawai. Filter: start_date, end_date, faculty_id.
     */
    public function rekap(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'faculty_id' => 'nullable|integer|exists:faculties,id',
        ]);

        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate   = $request->input('end_date', date('Y-m-d'));
        $facultyId = $request->input('faculty_id');

        $dates = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if (!$date->isWeekend()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        $groupedData = $this->buildRekapData($startDate, $endDate, $facultyId, $dates);

        $summary = ['total_pegawai' => count($groupedData), 'total_hadir' => 0, 'total_terlambat' => 0, 'total_transport' => 0];
        foreach ($groupedData as $data) {
            $summary['total_hadir']     += $data['total_hadir'];
            $summary['total_terlambat'] += $data['total_terlambat'];
            $summary['total_transport'] += $data['total_transport'];
        }

        return response()->json([
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'tanggal_kerja'   => $dates,
            'summary'         => $summary,
            'data'            => array_values($groupedData),
        ]);
    }

    /**
     * GET /api/staff
     * Daftar pegawai dengan filter: search, status (1|0), per_page.
     */
    public function staff(Request $request)
    {
        $query = Staff::with(['facultyData', 'workShift'])
            ->whereNull('deleted_at')
            ->when($request->input('search'), fn($q, $search) =>
                $q->where(fn($q2) =>
                    $q2->where('nip', 'like', "%{$search}%")
                       ->orWhere('nama', 'like', "%{$search}%")
                       ->orWhere('jabatan', 'like', "%{$search}%")
                )
            )
            ->when($request->has('status'), fn($q) =>
                $q->where('status', $request->boolean('status'))
            )
            ->orderBy('nama');

        $perPage = min((int) $request->input('per_page', 15), 100);

        $staff = $query->paginate($perPage)->through(fn($s) => [
            'id'         => $s->id,
            'machine_id' => $s->machine_id,
            'nip'        => $s->nip,
            'nama'       => $s->nama,
            'jabatan'    => $s->jabatan,
            'fakultas'   => $s->facultyData->nama_fakultas ?? '-',
            'shift'      => $s->workShift->nama_shift ?? '-',
            'status'     => $s->status ? 'Aktif' : 'Non-Aktif',
        ]);

        return response()->json($staff);
    }

    private function buildRekapData(string $startDate, string $endDate, ?int $facultyId, array $dates): array
    {
        $staffQuery = Staff::with('facultyData')->orderBy('nama');
        if ($facultyId) {
            $staffQuery->where('faculty_id', $facultyId);
        }

        $groupedData = [];
        foreach ($staffQuery->get() as $staff) {
            $groupedData[$staff->machine_id] = [
                'nama'            => $staff->nama,
                'nip'             => $staff->nip ?? '-',
                'jabatan'         => $staff->jabatan ?? '-',
                'unit_kerja'      => $staff->facultyData->nama_fakultas ?? '-',
                'kehadiran'       => [],
                'total_hadir'     => 0,
                'total_terlambat' => 0,
                'total_transport' => 0,
            ];
        }

        $attendanceQuery = DailyAttendance::whereBetween('date', [$startDate, $endDate]);
        if ($facultyId) {
            $attendanceQuery->whereHas('staff', fn($q) => $q->where('faculty_id', $facultyId));
        }

        foreach ($attendanceQuery->get() as $att) {
            if (!in_array($att->date, $dates) || !isset($groupedData[$att->machine_id])) continue;

            $groupedData[$att->machine_id]['kehadiran'][$att->date] = [
                'jam_masuk'        => $att->jam_masuk,
                'jam_pulang'       => $att->jam_pulang,
                'status_kehadiran' => $att->status_kehadiran,
                'terlambat_menit'  => $att->terlambat_menit,
                'uang_transport'   => $att->uang_transport,
            ];
            $groupedData[$att->machine_id]['total_hadir']++;
            $groupedData[$att->machine_id]['total_transport'] += $att->uang_transport;
            if ($att->status_kehadiran === 'Terlambat') {
                $groupedData[$att->machine_id]['total_terlambat']++;
            }
        }

        $leavesQuery = Leave::with('staff')->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(fn($q2) => $q2->where('start_date', '<', $startDate)->where('end_date', '>', $endDate));
        });
        if ($facultyId) {
            $leavesQuery->whereHas('staff', fn($q) => $q->where('faculty_id', $facultyId));
        }

        foreach ($leavesQuery->get() as $leave) {
            if (!$leave->staff || !isset($groupedData[$leave->staff->machine_id])) continue;
            $id = $leave->staff->machine_id;
            foreach ($dates as $date) {
                if ($date >= $leave->start_date && $date <= $leave->end_date && !isset($groupedData[$id]['kehadiran'][$date])) {
                    $groupedData[$id]['kehadiran'][$date] = ['status_kehadiran' => $leave->type];
                }
            }
        }

        $groupedData = array_filter($groupedData, fn($d) => count($d['kehadiran']) > 0);

        uasort($groupedData, function ($a, $b) {
            $diff = $this->jobLevel($a['jabatan']) <=> $this->jobLevel($b['jabatan']);
            return $diff !== 0 ? $diff : strcasecmp($a['nama'], $b['nama']);
        });

        return $groupedData;
    }

    private function jobLevel(string $jabatan): int
    {
        $levels = [
            'Rektor' => 1, 'Wakil Rektor I' => 2, 'Wakil Rektor II' => 3,
            'Wakil Rektor III' => 4, 'Wakil Rektor IV' => 5,
            'Dekan' => 6, 'Wakil Dekan' => 7, 'Ketua Prodi' => 8,
            'Kepala' => 9, 'Anggota' => 10, 'Sekretaris' => 11,
            'Staf' => 12, 'Pramubakti' => 13,
        ];

        foreach ($levels as $key => $level) {
            if (stripos($jabatan, $key) !== false) return $level;
        }

        return 99;
    }
}
