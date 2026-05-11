@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Management Staf</h4>
                    <a href="{{ route('staf.create') }}" class="btn btn-primary btn-sm">+ Tambah Staf</a>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('staf.index') }}" method="GET" class="d-flex mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Cari NIP, Nama, atau Jabatan..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary mr-2">Cari</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Fakultas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stafs as $key => $staf)
                            <tr>
                                <td>{{ $stafs->firstItem() + $key }}</td>
                                <td>{{ $staf->nip }}</td>
                                <td>{{ $staf->nama }}</td>
                                <td>{{ $staf->jabatan }}</td>
                                <td>{{ $staf->facultyData->nama_fakultas ?? '-' }}</td>
                                <td>
                                    @if($staf->status)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staf.show', $staf->id) }}" class="btn btn-info btn-sm icon-btn" title="Detail & Kalender">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('staf.edit', $staf->id) }}" class="btn btn-warning btn-sm icon-btn">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <form action="{{ route('staf.destroy', $staf->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm icon-btn">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Footer: info + pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap" style="gap: 8px;">

                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        Menampilkan
                        <strong class="text-white">{{ $stafs->firstItem() ?? 0 }}</strong>–<strong class="text-white">{{ $stafs->lastItem() ?? 0 }}</strong>
                        dari <strong class="text-white">{{ $stafs->total() }}</strong> data staf
                    </p>

                    @if($stafs->hasPages())
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $stafs->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $stafs->previousPageUrl() }}"
                            style="background:#2A3038; border-color:#484848; color:#9a9a9a;">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        </li>
                        @php
                            $current = $stafs->currentPage();
                            $last    = $stafs->lastPage();
                            $start   = max(1, $current - 2);
                            $end     = min($last, $current + 2);
                        @endphp
                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $stafs->url(1) }}"
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
                                <a class="page-link" href="{{ $stafs->url($p) }}"
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
                                <a class="page-link" href="{{ $stafs->url($last) }}"
                                style="background:#2A3038; border-color:#484848; color:#9a9a9a;">{{ $last }}</a>
                            </li>
                        @endif
                        <li class="page-item {{ !$stafs->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $stafs->nextPageUrl() }}"
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
