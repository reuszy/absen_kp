@extends('layouts.master')

@section('content')

<div class="page-header">
    <h3 class="page-title"> Log Absen </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Absensi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Raw Data</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Data Log Absensi</h4>
                    <a href="{{ route('attendance.logs') }}" class="btn btn-outline-success btn-sm">
                        <i class="mdi mdi-refresh"></i> Refresh Data
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-contextual">
                        <thead>
                            <tr>
                                <th class="text-white font-weight-bold"> No. </th>
                                <th class="text-white font-weight-bold"> NIP </th>
                                <th class="text-white font-weight-bold"> Nama </th>
                                <th class="text-white font-weight-bold"> Waktu Scan </th>
                                <th class="text-white font-weight-bold"> Machine ID </th>
                                <th class="text-white font-weight-bold"> Verifikasi </th>
                                <th class="text-white font-weight-bold"> Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $index => $log)
                            <tr>
                                <td class="text-white">{{ $logs->firstItem() + $index }}</td>

                                <td class="text-white">
                                    @if($log->dosen)
                                        {{ $log->dosen->nip ?? '-' }}
                                    @elseif($log->staff)
                                        {{ $log->staff->nip ?? '-' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($log->staff)
                                        <span class="font-weight-bold text-white">{{ $log->staff->nama_lengkap ?? $log->staff->nama }}</span>
                                    @else
                                        <span class="text-danger font-italic">Tidak Dikenal</span>
                                    @endif
                                </td>

                                <td class="text-white">
                                    {{ \Carbon\Carbon::parse($log->scan_time)->format('d M Y') }}
                                    <br>
                                    <span class="font-weight-bold">{{ \Carbon\Carbon::parse($log->scan_time)->format('H:i:s') }}</span>
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-outline-secondary">{{ $log->machine_id }}</span>
                                </td>

                                <td>
                                    @if($log->verify_mode == 1 || $log->verify_mode == 0)
                                        <label class="badge badge-info">FINGER</label>
                                    @elseif($log->verify_mode == 4)
                                        <label class="badge badge-warning">KARTU</label>
                                    @elseif($log->verify_mode == 15)
                                        <label class="badge badge-primary">WAJAH</label>
                                    @else
                                        <label class="badge badge-outline-light">Lainnya ({{ $log->verify_mode }})</label>
                                    @endif
                                </td>

                                <td>
                                    @if($log->status_scan == 0)
                                        <label class="badge badge-success">IN</label>
                                    @elseif($log->status_scan == 1)
                                        <label class="badge badge-danger">OUT</label>
                                    @else
                                        <label class="badge badge-warning">Undefined</label>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">Belum ada data absensi yang masuk.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }} 
                </div>

            </div>
        </div>
    </div>
</div>

@endsection