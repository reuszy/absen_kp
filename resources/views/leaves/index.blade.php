@extends('layouts.master')

@section('content')

<div class="page-header">
    <h3 class="page-title">Manajemen Izin & Cuti</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Izin & Cuti</li>
        </ol>
    </nav>
</div>

{{-- ── Stat Cards ───────────────────────────────────────────────────────────── --}}
@php
    $totalSakit = $leaves->getCollection()->where('type', 'Sakit')->count();
    $totalIzin  = $leaves->getCollection()->where('type', 'Izin')->count();
    $totalCuti  = $leaves->getCollection()->where('type', 'Cuti')->count();
@endphp
<div class="row mb-4">

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Total Pengajuan</p>
                        <h3 class="mb-0 font-weight-bold">{{ $leaves->total() }}</h3>
                    </div>
                    <div style="background: rgba(0,144,231,0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-clipboard-list text-info" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Semua jenis izin tercatat</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Sakit</p>
                        <h3 class="mb-0 font-weight-bold text-warning">{{ $totalSakit }}</h3>
                    </div>
                    <div style="background: rgba(255,171,0,0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-medical-bag text-warning" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pada halaman ini</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Izin</p>
                        <h3 class="mb-0 font-weight-bold text-info">{{ $totalIzin }}</h3>
                    </div>
                    <div style="background: rgba(0,144,231,0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-account-clock text-info" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pada halaman ini</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.82rem;">Cuti</p>
                        <h3 class="mb-0 font-weight-bold text-success">{{ $totalCuti }}</h3>
                    </div>
                    <div style="background: rgba(0,210,91,0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-beach text-success" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pada halaman ini</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Tabel ────────────────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">

                {{-- Toolbar --}}
                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap: 12px;">
                    <div>
                        <h4 class="card-title mb-1">Daftar Izin Pegawai</h4>
                        <p class="text-muted mb-0" style="font-size: 0.82rem;">
                            Menampilkan <strong class="text-white">{{ $leaves->total() }}</strong> data
                            @if(request('search') || request('type'))
                                &nbsp;<span class="badge badge-outline-warning">Filter aktif</span>
                            @endif
                        </p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        @if(request('search') || request('type'))
                            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-close"></i> Reset
                            </a>
                        @endif
                        <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Tambah Izin
                        </a>
                    </div>
                </div>

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
                         style="background: rgba(0,210,91,0.1); border: 1px solid rgba(0,210,91,0.3); color: #00d25b;">
                        <i class="mdi mdi-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" style="color: #00d25b;">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Filter Form --}}
                <form action="{{ route('leaves.index') }}" method="GET" class="mb-4">
                    <div class="row" style="row-gap: 10px;">

                        <div class="col-md-6">
                            <label class="text-muted mb-1" style="font-size: 0.75rem;">Cari Nama / NIP</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#2A3038; border-color:#484848;">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="search"
                                       value="{{ request('search') }}"
                                       class="form-control text-white"
                                       placeholder="Nama pegawai atau NIP..."
                                       style="background-color:#2A3038; border-color:#484848;">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted mb-1" style="font-size: 0.75rem;">Jenis</label>
                            <select name="type"
                                    class="form-control form-control-sm text-white"
                                    style="background-color:#2A3038; border-color:#484848;">
                                <option value="">Semua Jenis</option>
                                <option value="Sakit" {{ request('type') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="Izin"  {{ request('type') === 'Izin'  ? 'selected' : '' }}>Izin</option>
                                <option value="Cuti"  {{ request('type') === 'Cuti'  ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
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
                                <th class="text-center text-white" style="width: 45px;">#</th>
                                <th class="text-white" style="min-width: 180px;">Nama Pegawai</th>
                                <th class="text-white" style="min-width: 110px;">Tanggal Mulai</th>
                                <th class="text-white" style="min-width: 110px;">Tanggal Selesai</th>
                                <th class="text-center text-white" style="width: 60px;">Durasi</th>
                                <th class="text-center text-white" style="width: 80px;">Jenis</th>
                                <th class="text-white" style="min-width: 160px;">Keterangan</th>
                                <th class="text-center text-white" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $key => $leave)
                            @php
                                $start    = \Carbon\Carbon::parse($leave->start_date);
                                $end      = \Carbon\Carbon::parse($leave->end_date);
                                $durasi   = $start->diffInDays($end) + 1;
                            @endphp
                            <tr>
                                <td class="text-center text-muted" style="font-size: 0.8rem;">
                                    {{ $leaves->firstItem() + $key }}
                                </td>

                                <td>
                                    <div class="font-weight-bold text-white">{{ $leave->staff->nama ?? '-' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $leave->staff->nip ?? '-' }}</div>
                                </td>

                                <td style="color: #c8c8c8;">
                                    {{ $start->translatedFormat('d M Y') }}
                                </td>

                                <td style="color: #c8c8c8;">
                                    {{ $end->translatedFormat('d M Y') }}
                                </td>

                                {{-- Durasi --}}
                                <td class="text-center">
                                    <span style="font-weight: 600; color: #9a9a9a; font-size: 0.82rem;">
                                        {{ $durasi }}
                                        <span style="font-size: 0.7rem; font-weight: normal;">hari</span>
                                    </span>
                                </td>

                                {{-- Badge Jenis --}}
                                <td class="text-center">
                                    @if($leave->type === 'Sakit')
                                        <span class="badge badge-warning">
                                            <i class="mdi mdi-medical-bag"></i> Sakit
                                        </span>
                                    @elseif($leave->type === 'Izin')
                                        <span class="badge badge-info">
                                            <i class="mdi mdi-account-clock"></i> Izin
                                        </span>
                                    @elseif($leave->type === 'Cuti')
                                        <span class="badge badge-success">
                                            <i class="mdi mdi-beach"></i> Cuti
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">{{ $leave->type }}</span>
                                    @endif
                                </td>

                                <td style="color: #9a9a9a; font-size: 0.82rem;">
                                    {{ $leave->reason ?? '—' }}
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center" style="white-space: nowrap;">
                                    <a href="{{ route('leaves.edit', $leave->id) }}"
                                    class="btn btn-warning btn-sm btn-icon"
                                    title="Edit"
                                    style="padding: 4px 8px; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px;">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('leaves.destroy', $leave->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data izin {{ $leave->staff->nama ?? '' }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-sm btn-icon"
                                                title="Hapus"
                                                style="padding: 4px 8px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <span class="mdi mdi-clipboard-remove-outline text-muted" style="font-size: 2.5rem; display: block;"></span>
                                    <p class="text-muted mt-2 mb-0">
                                        @if(request('search') || request('type'))
                                            Tidak ada data yang cocok dengan filter.
                                            <a href="{{ route('leaves.index') }}" class="text-info">Reset filter</a>
                                        @else
                                            Belum ada data izin/cuti.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer: info + pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap" style="gap: 8px;">

                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        Menampilkan
                        <strong class="text-white">{{ $leaves->firstItem() ?? 0 }}</strong>–<strong class="text-white">{{ $leaves->lastItem() ?? 0 }}</strong>
                        dari <strong class="text-white">{{ $leaves->total() }}</strong> data
                    </p>

                    @if($leaves->hasPages())
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $leaves->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $leaves->previousPageUrl() }}"
                               style="background:#2A3038; border-color:#484848; color:#9a9a9a;">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        </li>
                        @php
                            $current = $leaves->currentPage();
                            $last    = $leaves->lastPage();
                            $start   = max(1, $current - 2);
                            $end     = min($last, $current + 2);
                        @endphp
                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $leaves->url(1) }}"
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
                                <a class="page-link" href="{{ $leaves->url($p) }}"
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
                                <a class="page-link" href="{{ $leaves->url($last) }}"
                                   style="background:#2A3038; border-color:#484848; color:#9a9a9a;">{{ $last }}</a>
                            </li>
                        @endif
                        <li class="page-item {{ !$leaves->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $leaves->nextPageUrl() }}"
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