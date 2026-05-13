@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Fakultas / Unit Kerja</h4>
                <p class="card-description">Ubah nama fakultas atau unit kerja</p>

                <form action="{{ route('fakultas.update', $fakultas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nama_fakultas">Nama Fakultas / Unit</label>
                        <input type="text"
                               class="form-control @error('nama_fakultas') is-invalid @enderror"
                               id="nama_fakultas"
                               name="nama_fakultas"
                               value="{{ old('nama_fakultas', $fakultas->nama_fakultas) }}"
                               autofocus>
                        @error('nama_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Update</button>
                    <a href="{{ route('fakultas.index') }}" class="btn btn-dark">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
