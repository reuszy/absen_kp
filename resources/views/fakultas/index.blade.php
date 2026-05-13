@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manajemen Fakultas / Unit Kerja</h4>
                    <a href="{{ route('fakultas.create') }}" class="btn btn-primary btn-sm">+ Tambah Fakultas</a>
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
                                <th>Nama Fakultas / Unit</th>
                                <th>Jumlah Staf</th>
                                <th>Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $key => $faculty)
                            <tr>
                                <td>{{ $faculties->firstItem() + $key }}</td>
                                <td>{{ $faculty->nama_fakultas }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $faculty->staff()->count() }} staf</span>
                                </td>
                                <td>{{ $faculty->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('fakultas.edit', $faculty->id) }}" class="btn btn-warning btn-sm icon-btn">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('fakultas.destroy', $faculty->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus fakultas \'{{ $faculty->nama_fakultas }}\'? Pastikan tidak ada staf yang terdaftar.')">
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
                                <td colspan="5" class="text-center text-muted">Belum ada data fakultas/unit.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $faculties->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
