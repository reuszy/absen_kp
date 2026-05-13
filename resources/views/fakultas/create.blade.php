@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Fakultas / Unit Kerja</h4>
                <p class="card-description">Tambahkan fakultas atau unit kerja baru</p>

                <form action="{{ route('fakultas.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nama_fakultas">Nama Fakultas / Unit</label>
                        <input type="text"
                               class="form-control @error('nama_fakultas') is-invalid @enderror"
                               id="nama_fakultas"
                               name="nama_fakultas"
                               placeholder="Contoh: Fakultas Teknik"
                               value="{{ old('nama_fakultas') }}"
                               autofocus>
                        @error('nama_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                    <a href="{{ route('fakultas.index') }}" class="btn btn-dark">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
