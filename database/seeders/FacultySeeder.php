<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            'Fakultas Hukum',
            'Fakultas Ekonomi dan Bisnis',
            'Fakultas Keguruan dan Ilmu Pendidikan',
            'Fakultas Ilmu Sosial dan Politik',
            'Fakultas Pertanian',
            'Fakultas Teknik',
            'Fakultas Kedokteran',
            'Sekolah Pascasarjana'
        ];

        foreach ($faculties as $name) {
            Faculty::create(['nama_fakultas' => $name]);
        }
    }
}
