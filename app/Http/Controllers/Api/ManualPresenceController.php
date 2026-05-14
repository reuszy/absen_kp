<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ScanController;
use App\Models\Staff;
use App\Models\DailyAttendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManualPresenceController extends Controller
{
    public function index()
    {
        return view('manual-presence.index');
    }


    /**
     * GET /api/simulasi/staff
     * Ambil daftar staff aktif untuk dipilih di halaman simulasi.
     */
    public function listStaff()
    {
        $staff = Staff::with('workShift', 'facultyData')
            ->where('status', true)
            ->get()
            ->map(function ($s) {
                $daily = DailyAttendance::where('machine_id', $s->machine_id)
                    ->where('date', Carbon::today()->format('Y-m-d'))
                    ->first();

                $statusHariIni = 'Belum Absen';
                if ($daily && $daily->jam_pulang) {
                    $statusHariIni = 'Sudah Pulang';
                } elseif ($daily && $daily->jam_masuk) {
                    $statusHariIni = 'Sudah Masuk';
                }

                return [
                    'id'            => $s->id,
                    'machine_id'    => $s->machine_id,
                    'nip'           => $s->nip,
                    'nama'          => $s->nama,
                    'jabatan'       => $s->jabatan,
                    'fakultas'      => $s->facultyData->nama_fakultas ?? '-',
                    'shift'         => $s->workShift->nama_shift ?? '-',
                    'jam_masuk_shift'  => $s->workShift->jam_masuk ?? '08:00:00',
                    'jam_pulang_shift' => $s->workShift->jam_pulang ?? '17:00:00',
                    'status_hari_ini'  => $statusHariIni,
                    'daily'         => $daily ? [
                        'jam_masuk'        => $daily->jam_masuk,
                        'jam_pulang'       => $daily->jam_pulang,
                        'status_kehadiran' => $daily->status_kehadiran,
                        'terlambat_menit'  => $daily->terlambat_menit,
                        'uang_transport'   => $daily->uang_transport,
                    ] : null,
                ];
            });

        return response()->json([
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'jam_sekarang' => Carbon::now()->format('H:i:s'),
            'staff' => $staff,
        ]);
    }

    /**
     * POST /api/simulasi/scan
     * Simulasi scan absen — memanggil logic ScanController.
     * 
     * Param:
     *  - machine_id (required): ID mesin staf
     *  - scan_time (optional): Waktu simulasi "Y-m-d H:i:s" (default: sekarang)
     */
    public function manualScan(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'scan_time'  => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        // Teruskan ke ScanController yang sudah ada
        $scanController = app(ScanController::class);

        return $scanController->store($request);
    }

    /**
     * POST /api/simulasi/reset
     * Reset data absensi hari ini (untuk demo ulang).
     * 
     * Param:
     *  - machine_id (optional): Reset untuk staf tertentu. Jika kosong, reset semua.
     *  - date (optional): Tanggal yang direset. Default: hari ini.
     */
    public function resetHariIni(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $machineId = $request->input('machine_id');

        $queryDaily = DailyAttendance::where('date', $date);
        $queryLog   = AttendanceLog::whereDate('scan_time', $date);

        if ($machineId) {
            $queryDaily->where('machine_id', $machineId);
            $queryLog->where('machine_id', $machineId);
        }

        $deletedDaily = $queryDaily->delete();
        $deletedLogs  = $queryLog->delete();

        return response()->json([
            'message' => 'Data absensi berhasil direset.',
            'tanggal' => $date,
            'machine_id' => $machineId ?? 'semua',
            'deleted_daily' => $deletedDaily,
            'deleted_logs'  => $deletedLogs,
        ]);
    }
}
