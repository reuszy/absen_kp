<?php

namespace Database\Seeders;

use App\Models\WorkShift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkShift::create([
            'nama_shift' => 'Reguler',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
        ]);
    }
}
