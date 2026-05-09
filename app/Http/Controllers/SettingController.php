<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Menampilkan form pengaturan.
     */
    public function index()
    {
        // Ambil Shift Reguler (ID = 1)
        $shift = WorkShift::find(1);

        // Jika kebetulan tidak ada shift reguler, buat object kosong agar view tidak error
        if (!$shift) {
            $shift = new WorkShift([
                'jam_masuk' => '08:15:00',
                'jam_pulang' => '17:00:00',
                'uang_transport' => 15000
            ]);
        }

        // Ambil semua setting (key-value)
        $settings = Setting::all()->pluck('value', 'key');

        return view('settings.index', compact('shift', 'settings'));
    }

    /**
     * Memperbarui pengaturan shift dan setting global.
     */
    public function update(Request $request)
    {
        $request->validate([
            // Validasi untuk WorkShift
            'jam_masuk' => 'required|date_format:H:i:s',
            'jam_pulang' => 'required|date_format:H:i:s',
            'uang_transport' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update WorkShift (Shift Reguler)
            $shift = WorkShift::find(1);
            if ($shift) {
                $shift->update([
                    'jam_masuk' => $request->jam_masuk,
                    'jam_pulang' => $request->jam_pulang,
                    'uang_transport' => $request->uang_transport,
                ]);
            } else {
                // Buat baru jika anehnya tidak ada
                WorkShift::create([
                    'id' => 1,
                    'nama_shift' => 'Reguler',
                    'jam_masuk' => $request->jam_masuk,
                    'jam_pulang' => $request->jam_pulang,
                    'uang_transport' => $request->uang_transport,
                ]);
            }

            DB::commit();
            return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('settings.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
