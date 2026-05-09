<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WorkShiftSeeder::class,
            FacultySeeder::class,
            UserAdminSeeder::class,
            StaffSeeder::class,
        ]);
    }
}
