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
                    <a href="{{ route('staf.index') }}" class="btn btn-secondary">Reset</a>
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
                                    <a href="{{ route('staf.edit', $staf->id) }}" class="btn btn-warning btn-sm icon-btn">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('staf.destroy', $staf->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm icon-btn">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $stafs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
