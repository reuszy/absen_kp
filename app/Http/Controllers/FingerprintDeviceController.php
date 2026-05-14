<?php

namespace App\Http\Controllers;

use App\Models\FingerprintDevice;
use Illuminate\Http\Request;
use Jmrashed\Zkteco\Lib\ZKTeco;

class FingerprintDeviceController extends Controller
{
    public function index()
    {
        $devices = FingerprintDevice::orderBy('nama_lokasi')->paginate(15);
        return view('fingerprint.index', compact('devices'));
    }


    public function create()
    {
        return view('fingerprint.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'ip'          => 'required|string|max:45',
            'vpn'         => 'required|string|max:45',
            'port'        => 'required|integer|min:1|max:65535',
        ]);

        FingerprintDevice::create($request->only('nama_lokasi', 'ip', 'vpn', 'port'));

        return redirect()->route('fingerprint.index')->with('success', 'Perangkat fingerprint berhasil ditambahkan.');
    }


    public function edit(FingerprintDevice $fingerprint)
    {
        return view('fingerprint.edit', compact('fingerprint'));
    }


    public function update(Request $request, FingerprintDevice $fingerprint)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'ip'          => 'required|string|max:45',
            'vpn'         => 'required|string|max:45',
            'port'        => 'required|integer|min:1|max:65535',
        ]);

        $fingerprint->update($request->only('nama_lokasi', 'ip', 'vpn', 'port'));

        return redirect()->route('fingerprint.index')->with('success', 'Perangkat fingerprint berhasil diperbarui.');
    }


    public function destroy(FingerprintDevice $fingerprint)
    {
        $fingerprint->delete();

        return redirect()->route('fingerprint.index')->with('success', 'Perangkat fingerprint berhasil dihapus.');
    }

    
    public function testConnection(FingerprintDevice $fingerprint)
    {
        try {
            $zk = new ZKTeco($fingerprint->vpn, $fingerprint->port);
            if ($zk->connect()) {
                $zk->disconnect();
                return redirect()->route('fingerprint.index')
                    ->with('success', "Koneksi ke \"{$fingerprint->nama_lokasi}\" ({$fingerprint->vpn}:{$fingerprint->port}) berhasil.");
            }

            return redirect()->route('fingerprint.index')
                ->with('error', "Gagal terhubung ke \"{$fingerprint->nama_lokasi}\" ({$fingerprint->vpn}:{$fingerprint->port}). Periksa IP, VPN, dan port.");
        } catch (\Exception $e) {
            return redirect()->route('fingerprint.index')
                ->with('error', "Error saat menghubungi \"{$fingerprint->nama_lokasi}\": {$e->getMessage()}");
        }
    }
}
