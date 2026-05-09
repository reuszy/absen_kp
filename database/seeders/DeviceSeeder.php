<?php

namespace Database\Seeders;

use App\Models\FingerprintDevice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = [
            ['nama_lokasi' => 'Universitas', 'ip' => '103.165.225.227:1009', 'vpn' => '12.12.14.5', 'port' => 4370],
            ['nama_lokasi' => 'MSJ GD. PERPUSTAKAAN', 'ip' => '103.165.225.227:1011', 'vpn' => '12.12.15.5', 'port' => 4370],
            ['nama_lokasi' => 'Fakutlas Hukum', 'ip' => '103.165.225.227:998', 'vpn' => '12.12.6.5', 'port' => 4370],
            ['nama_lokasi' => 'Fakultas Pendidikan dan Sains', 'ip' => '103.165.225.227:990', 'vpn' => '12.12.4.5', 'port' => 4370],
            ['nama_lokasi' => 'FISIP', 'ip' => '103.165.225.227:996', 'vpn' => '12.12.5.5', 'port' => 4370],
            ['nama_lokasi' => 'Fakultas Pertanian', 'ip' => '103.165.225.227:1003', 'vpn' => '12.12.7.5', 'port' => 4370],
            ['nama_lokasi' => 'Fakultas Teknik', 'ip' => '103.165.225.227:1007', 'vpn' => '12.12.9.5', 'port' => 4370],
            ['nama_lokasi' => 'PASCASARJANA', 'ip' => '103.165.225.227:1024', 'vpn' => '12.12.20.5', 'port' => 4370],
        ];

        foreach ($devices as $dev) {
            FingerprintDevice::create($dev);
        }
    }
}
