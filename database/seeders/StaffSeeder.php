<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Staff;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shiftReguler = WorkShift::where('nama_shift', 'Reguler')->first();
        $fakultasTeknik = Faculty::where('nama_fakultas', 'Fakultas Teknik')->first();
        $fakultasEkon   = Faculty::where('nama_fakultas', 'Fakultas Ekonomi dan Bisnis')->first();
        
        $userHafid = User::create([
            'nama'      => 'Hafid Ramadhan',
            'email'     => 'hafidr@kampus.ac.id',
            'password'  => Hash::make('password'),
            'role'      => 'staff',
        ]);

        Staff::create([
            'users_id'      => $userHafid->id,
            'work_shift_id' => $shiftReguler->id,
            'faculty_id'    => $fakultasTeknik->id,
            'machine_id'    => 20,
            'nip'           => '199005052019032005',
            'nama'          => 'Hafid Ramadhan',
            'jabatan'       => 'Staff Keuangan',
        ]);

        $userDedi = User::create([
            'nama'      => 'Dedi Muhammad',
            'email'     => 'dedi@kampus.ac.id',
            'password'  => Hash::make('password'),
            'role'      => 'staff',
        ]);

        Staff::create([
            'users_id'      => $userDedi->id,
            'work_shift_id' => $shiftReguler->id,
            'faculty_id'    => $fakultasEkon->id,
            'machine_id'    => 21,
            'nip'           => '123456789',
            'nama'          => 'Dedi Muhammad',
            'jabatan'       => 'Staff IT',
        ]);
    }
}
