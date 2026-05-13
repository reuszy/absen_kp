@extends('layouts.master')

@section('content')
<div class="row">

    {{-- Info Akun --}}
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Informasi Akun</h4>
                <p class="card-description">Data akun yang sedang login</p>

                <div class="form-group">
                    <label class="text-muted small">Nama Lengkap</label>
                    <p class="font-weight-bold">{{ $user->nama }}</p>
                </div>
                <div class="form-group">
                    <label class="text-muted small">Email</label>
                    <p class="font-weight-bold">{{ $user->email }}</p>
                </div>
                <div class="form-group">
                    <label class="text-muted small">Role</label>
                    <p>
                        <span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-info' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                </div>
                <div class="form-group mb-0">
                    <label class="text-muted small">Status Akun</label>
                    <p>
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ganti Password</h4>
                <p class="card-description">Masukkan password lama untuk konfirmasi, lalu isi password baru</p>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-bs-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('account.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">Password Lama</label>
                        <input type="password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password"
                               name="current_password"
                               placeholder="Masukkan password lama Anda"
                               autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Ulangi password baru"
                               autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Simpan Password</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
