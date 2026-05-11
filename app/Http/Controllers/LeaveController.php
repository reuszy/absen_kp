<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Staff;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type   = $request->input('type');

        $leaves = Leave::with('staff')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('staff', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $staffs = Staff::orderBy('nama')->get();
        return view('leaves.form', compact('staffs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Sakit,Izin,Cuti,Dinas Luar',
            'reason' => 'nullable|string'
        ]);

        Leave::create($request->all());

        return redirect()->route('leaves.index')->with('success', 'Data izin/cuti berhasil ditambahkan.');
    }

    public function edit(Leave $leaf) // Resource uses singular 'leaf' internally usually
    {
        $staffs = Staff::orderBy('nama')->get();
        $leave = $leaf;
        return view('leaves.form', compact('leave', 'staffs'));
    }

    public function update(Request $request, Leave $leaf)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Sakit,Izin,Cuti,Dinas Luar',
            'reason' => 'nullable|string'
        ]);

        $leaf->update($request->all());

        return redirect()->route('leaves.index')->with('success', 'Data izin/cuti berhasil diperbarui.');
    }

    public function destroy(Leave $leaf)
    {
        $leaf->delete();
        return redirect()->route('leaves.index')->with('success', 'Data izin/cuti berhasil dihapus.');
    }
}
