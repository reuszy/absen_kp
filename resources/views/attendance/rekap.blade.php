@extends('layouts.master')

@section('content')

<div class="page-header">
    <h3 class="page-title"> Rekap Absensi </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Absensi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Harian</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gray-800 text-white" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-sm font-weight-bold text-gray-400 mb-1">Total Pegawai</p>
                        <h3 class="mb-0 font-weight-bold ml-2">{{ $summary['total_pegawai'] ?? 0 }}</h3>
                    </div>
                    <div class="icon icon-box-success" style="background: rgba(0, 210, 91, 0.1); padding: 10px; border-radius: 8px;">
                        <span class="mdi mdi-account-group text-success icon-item"></span>
                    </div>
                </div>
                <p class="text-muted text-xs mt-2 mb-0">Pegawai ditampilkan</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gray-800 text-white" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-sm font-weight-bold text-gray-400 mb-1">Total Kehadiran</p>
                        <h3 class="mb-0 font-weight-bold ml-2">{{ $summary['total_hadir'] ?? 0 }} <span class="text-xs text-muted">Hari</span></h3>
                    </div>
                    <div class="icon icon-box-info" style="background: rgba(143, 95, 232, 0.1); padding: 10px; border-radius: 8px;">
                        <span class="mdi mdi-calendar-check text-info icon-item"></span>
                    </div>
                </div>
                <p class="text-muted text-xs mt-2 mb-0">Akumulasi Hari</p>
            </div>
        </div>
    </div>

     <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gray-800 text-white" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-sm font-weight-bold text-gray-400 mb-1">Total Terlambat</p>
                        <h3 class="mb-0 font-weight-bold ml-2">{{ $summary['total_terlambat'] ?? 0 }} <span class="text-xs text-muted">x</span></h3>
                    </div>
                    <div class="icon icon-box-danger" style="background: rgba(252, 66, 74, 0.1); padding: 10px; border-radius: 8px;">
                        <span class="mdi mdi-clock-alert text-danger icon-item"></span>
                    </div>
                </div>
                <p class="text-muted text-xs mt-2 mb-0">Kali Keterlambatan</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gray-800 text-white" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-sm font-weight-bold text-gray-400 mb-1">Est. Transport</p>
                        <h4 class="mb-0 font-weight-bold ml-2 text-warning">Rp {{ number_format($summary['total_transport'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                    <div class="icon icon-box-warning" style="background: rgba(255, 171, 0, 0.1); padding: 10px; border-radius: 8px;">
                        <span class="mdi mdi-wallet-travel text-warning icon-item"></span>
                    </div>
                </div>
                <p class="text-danger text-xs mt-2 mb-0">Total Pengeluaran</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card" style="width: 100%;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    
                    <h4 class="card-title mb-0">
                        Data Absensi: 
                        <span class="text-muted" style="font-size: 0.9rem">
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                    </h4>
                    
                    <form action="{{ route('attendance.rekap') }}" method="GET" class="d-flex align-items-center mt-2 mt-md-0">
                        
                        <div class="me-2">
                            <input type="date" name="start_date" class="form-control text-white" 
                                value="{{ $startDate }}" 
                                style="background-color: #2A3038; border: 1px solid #484848; height: 38px;">
                        </div>

                        <span class="text-muted mx-2">s/d</span>

                        <div class="me-2">
                            <input type="date" name="end_date" class="form-control text-white" 
                                value="{{ $endDate }}" 
                                style="background-color: #2A3038; border: 1px solid #484848; height: 38px;">
                        </div>

                        <div class="me-2">
                            <select name="faculty_id" class="form-control text-white" style="background-color: #2A3038; border: 1px solid #484848; height: 38px;">
                                <option value="">Semua Fakultas</option>
                                @foreach ($faculties as $fac)
                                    <option value="{{ $fac->id }}" {{ $facultyId == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm me-2" style="height: 38px;">
                            <i class="mdi mdi-filter"></i> Filter
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 38px; display: inline-flex; align-items: center;">
                                <i class="mdi mdi-file-pdf me-1"></i> Download PDF
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'daily']) }}">Harian</a>
                                <a class="dropdown-item" href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'weekly']) }}">Mingguan</a>
                                <a class="dropdown-item" href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'monthly']) }}">Bulanan</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div style="width: 100%; min-width: 0;">
                    <div class="table-responsive" style="overflow-x: auto; white-space: nowrap; padding-bottom: 5px;">
                        <table class="table table-bordered table-contextual" style="width: 100%; min-width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> No </th>
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> NIP </th>
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> Nama </th>
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> Fakultas </th>
                                @foreach($dates as $date)
                                    <th class="text-white font-weight-bold text-center"> {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd') }} </th>
                                @endforeach
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> Hadir </th>
                                <th class="text-white font-weight-bold text-center" rowspan="2" style="vertical-align: middle;"> Transport </th>
                            </tr>
                            <tr>
                                @foreach($dates as $date)
                                    <th class="text-white font-weight-bold text-center" style="font-size: 0.8em;"> {{ \Carbon\Carbon::parse($date)->format('d M') }} </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedData as $id => $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $data['nip'] }}</td>
                                <td class="text-left font-weight-bold">{{ $data['name'] }}</td>
                                <td class="text-center">{{ $data['unit_kerja'] }}</td>

                                @foreach($dates as $date)
                                    <td class="text-center">
                                        @if(isset($data['attendance'][$date]))
                                            @php $atts = $data['attendance'][$date]; @endphp
                                            <div style="font-size: 0.9em;">
                                                {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }}
                                                -
                                                {{ $atts->jam_pulang ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i') : '?' }}
                                            </div>
                                            @if($atts->status_kehadiran == 'Terlambat')
                                                <span class="text-danger" style="font-size: 0.7em;">(Telat)</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="text-center font-weight-bold">{{ $data['total_hadir'] }}</td>
                                <td class="text-center text-success">Rp {{ number_format($data['total_transport'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + count($dates) }}" class="text-center py-5">
                                    <div class="text-muted">Tidak ada data absensi pada periode ini.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection