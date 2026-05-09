<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Staff;
use App\Models\WorkShift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PertanianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shiftReguler = WorkShift::where('nama_shift', 'Reguler')->first();
        $fakultasPert = Faculty::where('nama_fakultas', 'FAKULTAS PERTANIAN')->first();

        Staff::create([
            'users_id'      => null,
            'work_shift_id' => $shiftReguler->id,
            'faculty_id'    => $fakultasPert->id,
            'machine_id'    => '24870729',
            'nip'           => '24870729',
            'nama'          => 'Angga Gumilar Nopiyatna, SE.',
            'jabatan'       => 'Dosen Pertanian',
        ]);

        Staff::create([
            'users_id'      => null,
            'work_shift_id' => $shiftReguler->id,
            'faculty_id'    => $fakultasPert->id,
            'machine_id'    => '44840479',
            'nip'           => '44840479',
            'nama'          => 'Suhendi, S.Sos',
            'jabatan'       => 'Dosen Pertanian',
        ]);

    }
}
