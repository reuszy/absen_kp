@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Perangkat Fingerprint</h4>
                    <a href="{{ route('fingerprint.create') }}" class="btn btn-primary btn-sm">+ Tambah Perangkat</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-bs-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-bs-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lokasi</th>
                                <th>IP Lokal</th>
                                <th>IP VPN</th>
                                <th>Port</th>
                                <th>Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $key => $device)
                            <tr>
                                <td>{{ $devices->firstItem() + $key }}</td>
                                <td>{{ $device->nama_lokasi }}</td>
                                <td><code>{{ $device->ip }}</code></td>
                                <td><code>{{ $device->vpn }}</code></td>
                                <td>{{ $device->port }}</td>
                                <td>{{ $device->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('fingerprint.test', $device->id) }}"
                                       class="btn btn-info btn-sm icon-btn"
                                       title="Test Koneksi">
                                        <i class="mdi mdi-lan-connect"></i>
                                    </a>
                                    <a href="{{ route('fingerprint.edit', $device->id) }}" class="btn btn-warning btn-sm icon-btn">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('fingerprint.destroy', $device->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus perangkat \'{{ $device->nama_lokasi }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm icon-btn">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada perangkat yang terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $devices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
