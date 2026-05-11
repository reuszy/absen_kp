@extends('layouts.master')

@section('content')
<div class="page-header">
    <h3 class="page-title"> {{ isset($leave) ? 'Edit Izin/Cuti' : 'Tambah Izin/Cuti Baru' }} </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('leaves.index') }}">Izin & Cuti</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ isset($leave) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card offset-md-2">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Data Ketidakhadiran</h4>
                <p class="card-description"> Masukkan data pegawai yang berhalangan hadir. </p>

                <form action="{{ isset($leave) ? route('leaves.update', $leave->id) : route('leaves.store') }}" method="POST" class="forms-sample">
                    @csrf
                    @if(isset($leave))
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="staff_display">Pegawai</label>
                        <input list="staff_list" type="text" class="form-control @error('staff_id') is-invalid @enderror" id="staff_display" name="staff_display" style="color: white;" placeholder="-- Ketik Nama atau NIP Pegawai --" value="{{ old('staff_display', isset($leave) ? ($leave->staff->nip ? $leave->staff->nip . ' - ' : '') . $leave->staff->nama : '') }}" autocomplete="off">
                        <datalist id="staff_list">
                            @foreach($staffs as $staff)
                                <option data-id="{{ $staff->id }}" value="{{ $staff->nip ? $staff->nip . ' - ' : '' }} {{ $staff->nama }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="staff_id" id="staff_id" value="{{ old('staff_id', $leave->staff_id ?? '') }}">
                        @error('staff_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', isset($leave) ? $leave->start_date : '') }}">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Tanggal Selesai</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', isset($leave) ? $leave->end_date : '') }}">
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type">Jenis Ketidakhadiran</label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" style="color: white;">
                            <option value="Sakit" {{ old('type', $leave->type ?? '') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Izin" {{ old('type', $leave->type ?? '') == 'Izin' ? 'selected' : '' }}>Izin</option>
                            <option value="Cuti" {{ old('type', $leave->type ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Dinas Luar" {{ old('type', $leave->type ?? '') == 'Dinas Luar' ? 'selected' : '' }}>Dinas Luar</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="reason">Keterangan / Alasan</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" placeholder="Cth: Dirawat di RS, atau Ada urusan keluarga">{{ old('reason', $leave->reason ?? '') }}</textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                    <a href="{{ route('leaves.index') }}" class="btn btn-dark">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var displayInput = document.getElementById('staff_display');
        var hiddenInput = document.getElementById('staff_id');
        var options = document.querySelectorAll('#staff_list option');

        displayInput.addEventListener('input', function(e) {
            var val = e.target.value;
            hiddenInput.value = ''; // Reset

            for (var i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    hiddenInput.value = options[i].getAttribute('data-id');
                    break;
                }
            }
        });
    });
</script>
@endsection
