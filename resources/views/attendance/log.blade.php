@extends('layouts.master')

@section('content')

<div class="page-header">
    <h3 class="page-title">Log Absen</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Absensi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Raw Data</li>
        </ol>
    </nav>
</div>

{{-- ── Stat Cards ───────────────────────────────────────────────────────────── --}}
<div class="row mb-4">

    <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Total Scan Hari Ini</p>
                        <h3 class="mb-0 font-weight-bold">{{ $totalHariIni }}</h3>
                    </div>
                    <div style="background: rgba(0, 144, 231, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-fingerprint text-info" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Log masuk mesin sidik jari hari ini</p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Scan IN Hari Ini</p>
                        <h3 class="mb-0 font-weight-bold text-success">{{ $totalInHariIni }}</h3>
                    </div>
                    <div style="background: rgba(0, 210, 91, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-login text-success" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pegawai yang sudah absen masuk</p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Scan OUT Hari Ini</p>
                        <h3 class="mb-0 font-weight-bold text-danger">{{ $totalOutHariIni }}</h3>
                    </div>
                    <div style="background: rgba(252, 66, 74, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-logout text-danger" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pegawai yang sudah absen pulang</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Tabel Log ────────────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">

                {{-- Toolbar --}}
                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap: 12px;">
                    <div>
                        <h4 class="card-title mb-1">Data Log Absensi</h4>
                        <p class="text-muted mb-0" style="font-size: 0.82rem;">
                            Menampilkan <strong class="text-white">{{ $logs->total() }}</strong> entri
                            @if($search || $date || $status)
                                &nbsp;<span class="badge badge-outline-warning">Filter aktif</span>
                            @endif
                        </p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        @if($search || $date || $status)
                            <a href="{{ route('attendance.logs') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-close"></i> Reset
                            </a>
                        @endif
                        <a href="{{ route('attendance.logs', request()->query()) }}"
                           class="btn btn-outline-success btn-sm">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </a>
                    </div>
                </div>

                {{-- Filter Form — 3 kolom saja (tanpa Metode) --}}
                <form action="{{ route('attendance.logs') }}" method="GET" class="mb-4">
                    <div class="row" style="row-gap: 10px;">

                        <div class="col-md-5">
                            <label class="text-muted mb-1" style="font-size: 0.75rem;">Cari Nama / NIP</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#2A3038; border-color:#484848;">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="search"
                                       value="{{ $search }}"
                                       class="form-control text-white"
                                       placeholder="Nama atau NIP..."
                                       style="background-color:#2A3038; border-color:#484848;">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted mb-1" style="font-size: 0.75rem;">Tanggal</label>
                            <input type="date" name="date"
                                   value="{{ $date }}"
                                   class="form-control form-control-sm text-white"
                                   style="background-color:#2A3038; border-color:#484848;">
                        </div>

                        <div class="col-md-2">
                            <label class="text-muted mb-1" style="font-size: 0.75rem;">Status</label>
                            <select name="status"
                                    class="form-control form-control-sm text-white"
                                    style="background-color:#2A3038; border-color:#484848;">
                                <option value="">Semua</option>
                                <option value="in"  {{ $status === 'in'  ? 'selected' : '' }}>IN</option>
                                <option value="out" {{ $status === 'out' ? 'selected' : '' }}>OUT</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                        </div>

                    </div>
                </form>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-bordered" style="font-size: 0.85rem;">
                        <thead style="background-color: #1e2130;">
                            <tr>
                                <th class="text-center text-white" style="width: 45px;">No.</th>
                                <th class="text-white" style="min-width: 100px;">NIP</th>
                                <th class="text-white" style="min-width: 200px;">Nama</th>
                                <th class="text-white" style="min-width: 120px;">Unit Kerja</th>
                                <th class="text-white" style="min-width: 140px;">Waktu Scan</th>
                                <th class="text-center text-white" style="width: 70px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $index => $log)
                            @php $isUnknown = !$log->staff; @endphp
                            <tr style="{{ $isUnknown ? 'background-color: rgba(252,66,74,0.04);' : '' }}">

                                <td class="text-center text-muted" style="font-size: 0.8rem;">
                                    {{ $logs->firstItem() + $index }}
                                </td>

                                <td style="color: #9a9a9a; font-size: 0.82rem;">
                                    {{ $log->staff->nip ?? '—' }}
                                </td>

                                <td>
                                    @if($log->staff)
                                        <span class="font-weight-bold text-white">
                                            {{ $log->staff->nama_lengkap ?? $log->staff->nama }}
                                        </span>
                                    @else
                                        <div class="d-flex align-items-center" style="gap: 6px;">
                                            <span class="mdi mdi-account-question text-danger"></span>
                                            <span class="text-danger" style="font-style: italic; font-size: 0.82rem;">Tidak Dikenal</span>
                                            <span class="badge badge-outline-danger" style="font-size: 0.65rem;">ID: {{ $log->machine_id }}</span>
                                        </div>
                                    @endif
                                </td>

                                <td style="color: #9a9a9a; font-size: 0.82rem;">
                                    {{ optional($log->staff?->facultyData)->nama_fakultas ?? '—' }}
                                </td>

                                <td>
                                    <span style="color: #9a9a9a; font-size: 0.78rem;">
                                        {{ \Carbon\Carbon::parse($log->scan_time)->format('d M Y') }}
                                    </span>
                                    <br>
                                    <span class="font-weight-bold text-white">
                                        {{ \Carbon\Carbon::parse($log->scan_time)->format('H:i:s') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($log->status_scan == 0)
                                        <span class="badge badge-success" style="font-size: 0.78rem; padding: 5px 10px;">
                                            <i class="mdi mdi-login"></i> IN
                                        </span>
                                    @elseif($log->status_scan == 1)
                                        <span class="badge badge-danger" style="font-size: 0.78rem; padding: 5px 10px;">
                                            <i class="mdi mdi-logout"></i> OUT
                                        </span>
                                    @else
                                        <span class="badge badge-warning">?</span>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <span class="mdi mdi-inbox-outline text-muted" style="font-size: 2.5rem; display: block;"></span>
                                    <p class="text-muted mt-2 mb-0">
                                        @if($search || $date || $status)
                                            Tidak ada log yang cocok dengan filter.
                                            <a href="{{ route('attendance.logs') }}" class="text-info">Reset filter</a>
                                        @else
                                            Belum ada data log absensi.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── Footer: info + pagination ringkas ── --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap" style="gap: 8px;">

                    {{-- Kiri: info entri --}}
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        Menampilkan
                        <strong class="text-white">{{ $logs->firstItem() ?? 0 }}</strong>–<strong class="text-white">{{ $logs->lastItem() ?? 0 }}</strong>
                        dari <strong class="text-white">{{ $logs->total() }}</strong> entri
                    </p>

                    {{-- Kanan: pagination simple (prev/next + halaman aktif saja) --}}
                    @if($logs->hasPages())
                    <ul class="pagination pagination-sm mb-0" style="flex-wrap: nowrap;">

                        {{-- Prev --}}
                        <li class="page-item {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $logs->previousPageUrl() }}"
                               style="background:#2A3038; border-color:#484848; color:#9a9a9a;">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        </li>

                        {{-- Halaman: tampilkan max 5 nomor di sekitar halaman aktif --}}
                        @php
                            $current    = $logs->currentPage();
                            $last       = $logs->lastPage();
                            $start      = max(1, $current - 2);
                            $end        = min($last, $current + 2);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->url(1) }}"
                                   style="background:#2A3038; border-color:#484848; color:#9a9a9a;">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link" style="background:#1e2130; border-color:#484848; color:#6c7293;">…</span>
                                </li>
                            @endif
                        @endif

                        @for($p = $start; $p <= $end; $p++)
                            <li class="page-item {{ $p === $current ? 'active' : '' }}">
                                <a class="page-link" href="{{ $logs->url($p) }}"
                                   style="{{ $p === $current ? '' : 'background:#2A3038; border-color:#484848; color:#9a9a9a;' }}">
                                    {{ $p }}
                                </a>
                            </li>
                        @endfor

                        @if($end < $last)
                            @if($end < $last - 1)
                                <li class="page-item disabled">
                                    <span class="page-link" style="background:#1e2130; border-color:#484848; color:#6c7293;">…</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->url($last) }}"
                                   style="background:#2A3038; border-color:#484848; color:#9a9a9a;">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Next --}}
                        <li class="page-item {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $logs->nextPageUrl() }}"
                               style="background:#2A3038; border-color:#484848; color:#9a9a9a;">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </li>

                    </ul>
                    @endif

                </div>

            </div>
        </div>
    </div>
</div>

@endsection