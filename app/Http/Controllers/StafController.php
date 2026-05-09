<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Staff;
use Illuminate\Http\Request;

class StafController extends Controller
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

        return view('staf.index', compact('stafs'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        return view('staf.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:staff,nip',
            'nama' => 'required',
            'jabatan' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();

        $data['machine_id'] = $request->nip;
        $data['work_shift_id'] = $request->work_shift_id ?? 1;

        Staff::create($data);

        return redirect()->route('staf.index')->with('success', 'Data staf berhasil ditambahkan.');
    }

    public function show(Staff $staff)
    {
        // Not used
    }

    public function edit(Staff $staf)
    {
        $faculties = Faculty::all();
        return view('staf.edit', compact('staf', 'faculties'));
    }

    public function update(Request $request, Staff $staf)
    {
        $request->validate([
            'nip' => 'required|unique:staff,nip,' . $staf->id,
            'nama' => 'required',
            'jabatan' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['machine_id'] = $request->nip;
        $data['work_shift_id'] = $request->work_shift_id ?? 1;

        $staf->update($data);

        return redirect()->route('staf.index')->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(Staff $staf)
    {
        $staf->delete();

        return redirect()->route('staf.index')->with('success', 'Data staf berhasil dihapus.');
    }
}
