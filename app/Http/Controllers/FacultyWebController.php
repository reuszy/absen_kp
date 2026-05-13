<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacultyWebController extends Controller
{
    public function index()
    {
        $faculties = Faculty::orderBy('nama_fakultas')->paginate(15);
        return view('fakultas.index', compact('faculties'));
    }

    public function create()
    {
        return view('fakultas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required|string|max:255|unique:faculties,nama_fakultas',
        ], [
            'nama_fakultas.unique' => 'Nama fakultas/unit ini sudah terdaftar.',
        ]);

        Faculty::create(['nama_fakultas' => $request->nama_fakultas]);

        return redirect()->route('fakultas.index')->with('success', 'Fakultas/Unit berhasil ditambahkan.');
    }

    public function edit(Faculty $fakultas)
    {
        return view('fakultas.edit', compact('fakultas'));
    }

    public function update(Request $request, Faculty $fakultas)
    {
        $request->validate([
            'nama_fakultas' => ['required', 'string', 'max:255', Rule::unique('faculties', 'nama_fakultas')->ignore($fakultas->id)],
        ], [
            'nama_fakultas.unique' => 'Nama fakultas/unit ini sudah terdaftar.',
        ]);

        $fakultas->update(['nama_fakultas' => $request->nama_fakultas]);

        return redirect()->route('fakultas.index')->with('success', 'Fakultas/Unit berhasil diperbarui.');
    }

    public function destroy(Faculty $fakultas)
    {
        if (Staff::where('faculty_id', $fakultas->id)->exists()) {
            return redirect()->route('fakultas.index')->with('error', 'Gagal! Fakultas/Unit ini masih digunakan oleh data staf.');
        }

        $fakultas->delete();

        return redirect()->route('fakultas.index')->with('success', 'Fakultas/Unit berhasil dihapus.');
    }
}
