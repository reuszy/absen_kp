<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::orderBy('nama_fakultas', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Fakultas',
            'data' => $faculties
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required|string|max:255|unique:faculties,nama_fakultas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi Gagal',
                'data' => $validator->errors()
            ], 422);
        }

        $faculty = Faculty::create([
            'nama_fakultas' => $request->nama_fakultas
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Fakultas Berhasil Ditambahkan',
            'data' => $faculty
        ], 201);
    }


    public function show(int $id)
    {
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Data Fakultas Tidak Ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Fakultas',
            'data'    => $faculty
        ], 200);
    }


    public function update(Request $request, int $id)
    {
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Data Fakultas Tidak Ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_fakultas' => 'required|string|max:255|unique:faculties,nama_fakultas,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $faculty->update([
            'nama_fakultas' => $request->nama_fakultas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Fakultas Berhasil Diupdate',
            'data'    => $faculty
        ], 200);
    }


    public function destroy(int $id)
    {
        $faculty = Faculty::find($id);

        if (!$faculty) {
            return response()->json([
                'success' => false,
                'message' => 'Data Fakultas Tidak Ditemukan',
            ], 404);
        }

        $isUsed = Staff::where('faculty_id', $id)->exists();
        if ($isUsed) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Fakultas ini sedang digunakan oleh data Staff.',
            ], 400);
        }

        $faculty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Fakultas Berhasil Dihapus',
        ], 200);
    }
}
