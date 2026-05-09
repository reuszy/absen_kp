@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="page-title text-white">
                <i class="mdi mdi-cogs mr-1"></i> Pengaturan Sistem
            </h3>
            <p class="text-secondary">Kelola konfigurasi kehadiran dan parameter aplikasi.</p>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="mdi mdi-clock-outline"></i> Pengaturan Presensi</h4>
                        <p class="card-description">Konfigurasi jam kerja dan uang transport (Shift Reguler).</p>
                        
                        <div class="form-group">
                            <label for="jam_masuk">Jam Masuk (Batas Keterlambatan)</label>
                            <input type="time" step="1" class="form-control @error('jam_masuk') is-invalid @enderror" 
                                   id="jam_masuk" name="jam_masuk" 
                                   value="{{ old('jam_masuk', $shift->jam_masuk) }}" required>
                            @error('jam_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jam_pulang">Jam Pulang</label>
                            <input type="time" step="1" class="form-control @error('jam_pulang') is-invalid @enderror" 
                                   id="jam_pulang" name="jam_pulang" 
                                   value="{{ old('jam_pulang', $shift->jam_pulang) }}" required>
                            @error('jam_pulang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="uang_transport">Uang Transport (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-dark text-white">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('uang_transport') is-invalid @enderror" 
                                       id="uang_transport" name="uang_transport" 
                                       value="{{ old('uang_transport', $shift->uang_transport) }}" 
                                       min="0" required>
                            </div>
                            <small class="text-muted">Nominal yang diberikan jika staf pulang tidak lebih awal.</small>
                            @error('uang_transport')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mt-4 text-right">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                                <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
