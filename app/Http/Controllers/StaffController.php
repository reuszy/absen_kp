<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendance;
use App\Models\Faculty;
use App\Models\Leave;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $stafs = Staff::with('facultyData')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nip', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%")
                      ->orWhere('jabatan', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.index', compact('stafs'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        return view('staff.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'        => 'required|unique:staff,nip',
            'nama'       => 'required',
            'jabatan'    => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'status'     => 'required|boolean',
        ]);

        Staff::create([
            'work_shift_id' => $request->work_shift_id ?? 1,
            'machine_id'    => $request->nip,
            'nip'           => $request->nip,
            'nama'          => $request->nama,
            'jabatan'       => $request->jabatan,
            'faculty_id'    => $request->faculty_id,
            'status'        => $request->status,
        ]);

        return redirect()->route('staff.index')->with('success', 'Data staf berhasil ditambahkan.');
    }

    public function show(Staff $staf)
    {
        $staf->load('facultyData');

        $events = [];

        // Fetch Daily Attendance
        if ($staf->machine_id) {
            $attendances = DailyAttendance::where('machine_id', $staf->machine_id)->get();
            foreach ($attendances as $atten) {
                $status = $atten->status_kehadiran;
                $color = 'green';
                if ($status == 'Terlambat') {
                    $color = 'orange';
                } elseif ($status == 'Alpa') {
                    $color = 'red';
                }

                $title = $status;
                if ($atten->jam_masuk) {
                    $masuk = Carbon::parse($atten->jam_masuk)->format('H:i');
                    $title .= " ({$masuk})";
                }

                $events[] = [
                    'title' => $title,
                    'start' => $atten->date,
                    'color' => $color,
                    'allDay' => true
                ];
            }

            // Fetch Leaves
            $leaves = Leave::where('staff_id', $staf->id)->get();
            foreach ($leaves as $leave) {
                $color = '#17a2b8'; // default blue
                if (strtolower($leave->type) == 'sakit') {
                    $color = '#fd7e14'; // orange
                } elseif (strtolower($leave->type) == 'izin') {
                    $color = '#6f42c1'; // purple
                } elseif (strtolower($leave->type) == 'cuti') {
                    $color = '#20c997'; // teal
                }

                $events[] = [
                    'title' => strtoupper($leave->type) . ($leave->reason ? ' - ' . $leave->reason : ''),
                    'start' => $leave->start_date,
                    'end' => Carbon::parse($leave->end_date)->addDay()->format('Y-m-d'), // Exclusive end date for fullcalendar
                    'color' => $color,
                    'allDay' => true
                ];
            }
        }

        return view('staff.show', compact('staf', 'events'));
    }

    public function edit(Staff $staf)
    {
        $faculties = Faculty::all();
        return view('staff.edit', compact('staf', 'faculties'));
    }

    public function update(Request $request, Staff $staf)
    {
        $request->validate([
            'nip'        => 'required|unique:staff,nip,' . $staf->id,
            'nama'       => 'required',
            'jabatan'    => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'status'     => 'required|boolean',
        ]);

        $data = $request->all();
        $data['machine_id'] = $request->nip;
        $data['work_shift_id'] = $request->work_shift_id ?? 1;

        $staf->update($data);

        return redirect()->route('staff.index')->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(Staff $staf)
    {
        $staf->delete();

        return redirect()->route('staff.index')->with('success', 'Data staf berhasil dihapus.');
    }
}
