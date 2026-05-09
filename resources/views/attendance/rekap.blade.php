@extends('layouts.master')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h3 class="page-title">Rekap Absensi</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Absensi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Harian</li>
        </ol>
    </nav>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────────────────── --}}
<div class="row mb-4">

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-sm mb-1">Total Pegawai</p>
                        <h3 class="mb-0 font-weight-bold">{{ $summary['total_pegawai'] ?? 0 }}</h3>
                    </div>
                    <div style="background: rgba(0, 210, 91, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-account-group text-success" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Pegawai dalam filter ini</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-sm mb-1">Total Kehadiran</p>
                        <h3 class="mb-0 font-weight-bold">
                            {{ $summary['total_hadir'] ?? 0 }}
                            <span class="text-muted font-weight-normal" style="font-size: 0.9rem;">hari</span>
                        </h3>
                    </div>
                    <div style="background: rgba(0, 144, 231, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-calendar-check text-info" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Akumulasi hari hadir semua pegawai</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-sm mb-1">Total Terlambat</p>
                        <h3 class="mb-0 font-weight-bold text-danger">
                            {{ $summary['total_terlambat'] ?? 0 }}
                            <span class="text-muted font-weight-normal" style="font-size: 0.9rem;">kali</span>
                        </h3>
                    </div>
                    <div style="background: rgba(252, 66, 74, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-clock-alert text-danger" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                @php
                    $pctTerlambat = ($summary['total_hadir'] ?? 0) > 0
                        ? round(($summary['total_terlambat'] / $summary['total_hadir']) * 100, 1)
                        : 0;
                @endphp
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">
                    {{ $pctTerlambat }}% dari total kehadiran
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-sm mb-1">Est. Transport</p>
                        <h3 class="mb-0 font-weight-bold text-warning" style="font-size: 1.2rem;">
                            Rp {{ number_format($summary['total_transport'] ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div style="background: rgba(255, 171, 0, 0.1); padding: 12px; border-radius: 10px;">
                        <span class="mdi mdi-cash-multiple text-warning" style="font-size: 1.6rem;"></span>
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.78rem;">Total pengeluaran uang transport</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Tabel Rekap ──────────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card" style="border: 1px solid #2A3038;">
            <div class="card-body">

                {{-- Toolbar: Judul + Filter + Download --}}
                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap: 12px;">

                    {{-- Judul --}}
                    <div>
                        <h4 class="card-title mb-1">Data Absensi</h4>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            <span class="mdi mdi-calendar-range"></span>
                            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }}
                            &mdash;
                            {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
                            @if($facultyId)
                                &nbsp;·&nbsp;
                                <span class="badge badge-outline-info">{{ $faculties->firstWhere('id', $facultyId)?->nama_fakultas }}</span>
                            @endif
                        </p>
                    </div>

                    {{-- Filter Form --}}
                    <form action="{{ route('attendance.rekap') }}" method="GET"
                          class="d-flex align-items-center flex-wrap" style="gap: 8px;">

                        <div>
                            <label class="text-muted mb-0" style="font-size: 0.75rem; display: block;">Dari</label>
                            <input type="date" name="start_date"
                                   class="form-control form-control-sm text-white"
                                   value="{{ $startDate }}"
                                   style="background-color: #2A3038; border: 1px solid #484848; min-width: 130px;">
                        </div>

                        <div>
                            <label class="text-muted mb-0" style="font-size: 0.75rem; display: block;">Sampai</label>
                            <input type="date" name="end_date"
                                   class="form-control form-control-sm text-white"
                                   value="{{ $endDate }}"
                                   style="background-color: #2A3038; border: 1px solid #484848; min-width: 130px;">
                        </div>

                        <div>
                            <label class="text-muted mb-0" style="font-size: 0.75rem; display: block;">Unit Kerja</label>
                            <select name="faculty_id"
                                    class="form-control form-control-sm text-white"
                                    style="background-color: #2A3038; border: 1px solid #484848; min-width: 160px;">
                                <option value="">Semua Fakultas</option>
                                @foreach ($faculties as $fac)
                                    <option value="{{ $fac->id }}" {{ $facultyId == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="padding-top: 18px;">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                        </div>

                        <div style="padding-top: 18px;">
                            <div class="dropdown">
                                <button class="btn btn-danger btn-sm dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="mdi mdi-file-pdf"></i> Download PDF
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item"
                                       href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'daily']) }}">
                                        <i class="mdi mdi-calendar-today mr-2"></i> Harian
                                    </a>
                                    <a class="dropdown-item"
                                       href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'weekly']) }}">
                                        <i class="mdi mdi-calendar-week mr-2"></i> Mingguan
                                    </a>
                                    <a class="dropdown-item"
                                       href="{{ route('attendance.download_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'faculty_id' => $facultyId, 'type' => 'monthly']) }}">
                                        <i class="mdi mdi-calendar-month mr-2"></i> Bulanan
                                    </a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Legenda --}}
                <div class="d-flex align-items-center mb-3" style="gap: 16px; font-size: 0.78rem;">
                    <span class="text-muted">Keterangan:</span>
                    <span>
                        <span style="display:inline-block;width:10px;height:10px;background:rgba(252,66,74,0.15);border:1px solid #fc424a;border-radius:2px;margin-right:4px;"></span>
                        Terlambat
                    </span>
                    <span>
                        <span style="display:inline-block;width:10px;height:10px;background:rgba(0,210,91,0.12);border:1px solid #00d25b;border-radius:2px;margin-right:4px;"></span>
                        Hadir Tepat Waktu
                    </span>
                    <span>
                        <span style="display:inline-block;width:10px;height:10px;background:#2A3038;border:1px solid #484848;border-radius:2px;margin-right:4px;"></span>
                        Tidak Hadir
                    </span>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-bordered" style="width: 100%; min-width: 800px; font-size: 0.85rem;">
                        <thead>
                            <tr style="background-color: #1e2130;">
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; width: 36px;">No</th>
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; min-width: 90px;">NIP</th>
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; min-width: 160px;">Nama</th>
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; min-width: 120px;">Fakultas</th>
                                @foreach($dates as $date)
                                    <th class="text-center text-white" style="min-width: 90px; font-size: 0.78rem;">
                                        {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('ddd') }}
                                    </th>
                                @endforeach
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; width: 55px;">Hadir</th>
                                <th class="text-center text-white" rowspan="2" style="vertical-align: middle; min-width: 90px;">Transport</th>
                            </tr>
                            <tr style="background-color: #1e2130;">
                                @foreach($dates as $date)
                                    <th class="text-center text-muted" style="font-size: 0.75rem; font-weight: normal;">
                                        {{ \Carbon\Carbon::parse($date)->format('d M') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedData as $id => $data)
                            <tr>
                                <td class="text-center text-muted" style="font-size: 0.8rem;">{{ $loop->iteration }}</td>
                                <td class="text-center" style="font-size: 0.8rem; color: #9a9a9a;">{{ $data['nip'] }}</td>
                                <td class="font-weight-bold" style="padding-left: 10px;">{{ $data['name'] }}</td>
                                <td class="text-center" style="font-size: 0.8rem; color: #9a9a9a;">{{ $data['unit_kerja'] }}</td>

                                @foreach($dates as $date)
                                    @if(isset($data['attendance'][$date]))
                                        @php $atts = $data['attendance'][$date]; @endphp
                                        @if($atts->status_kehadiran == 'Terlambat')
                                            {{-- Sel merah untuk terlambat --}}
                                            <td class="text-center" style="background-color: rgba(252,66,74,0.1); border-color: rgba(252,66,74,0.3);">
                                                <div style="font-size: 0.78rem; color: #fc424a; font-weight: 600; line-height: 1.3;">
                                                    {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }}
                                                    –
                                                    {{ $atts->jam_pulang ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i') : '?' }}
                                                </div>
                                                <div style="font-size: 0.68rem; color: #fc424a; margin-top: 1px;">
                                                    Telat {{ $atts->terlambat_menit }} mnt
                                                </div>
                                            </td>
                                        @else
                                            {{-- Sel hijau muda untuk tepat waktu --}}
                                            <td class="text-center" style="background-color: rgba(0,210,91,0.06);">
                                                <div style="font-size: 0.78rem; line-height: 1.3;">
                                                    {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }}
                                                    –
                                                    {{ $atts->jam_pulang ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i') : '?' }}
                                                </div>
                                            </td>
                                        @endif
                                    @else
                                        {{-- Tidak hadir --}}
                                        <td class="text-center" style="background-color: #1e2130;">
                                            <span class="text-muted" style="font-size: 0.8rem;">–</span>
                                        </td>
                                    @endif
                                @endforeach

                                {{-- Kolom Hadir: warna berdasarkan persentase kehadiran --}}
                                @php
                                    $totalHariKerja = count($dates);
                                    $pctHadir = $totalHariKerja > 0
                                        ? ($data['total_hadir'] / $totalHariKerja) * 100
                                        : 0;
                                    $hadirColor = $pctHadir >= 80 ? '#00d25b' : ($pctHadir >= 50 ? '#ffab00' : '#fc424a');
                                @endphp
                                <td class="text-center font-weight-bold" style="color: {{ $hadirColor }};">
                                    {{ $data['total_hadir'] }}
                                    <div style="font-size: 0.65rem; color: #6c7293; font-weight: normal;">
                                        / {{ $totalHariKerja }}
                                    </div>
                                </td>

                                {{-- Transport: abu-abu jika 0, hijau jika dapat --}}
                                <td class="text-center font-weight-bold"
                                    style="color: {{ $data['total_transport'] > 0 ? '#00d25b' : '#6c7293' }};">
                                    @if($data['total_transport'] > 0)
                                        Rp {{ number_format($data['total_transport'], 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + count($dates) }}" class="text-center py-5">
                                    <span class="mdi mdi-inbox-outline text-muted" style="font-size: 2.5rem; display: block;"></span>
                                    <p class="text-muted mt-2 mb-0">Tidak ada data absensi pada periode ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                        {{-- ── Baris Total ── --}}
                        @if(count($groupedData) > 0)
                        <tfoot>
                            <tr style="background-color: #1e2130; border-top: 2px solid #484848;">
                                <td colspan="4" class="text-right font-weight-bold text-white" style="padding-right: 12px;">
                                    TOTAL
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $hadirPadaTanggal = collect($groupedData)->filter(fn($d) => isset($d['attendance'][$date]))->count();
                                    @endphp
                                    <td class="text-center" style="font-size: 0.78rem; color: #9a9a9a;">
                                        {{ $hadirPadaTanggal > 0 ? $hadirPadaTanggal . ' orang' : '–' }}
                                    </td>
                                @endforeach
                                <td class="text-center font-weight-bold text-white">
                                    {{ $summary['total_hadir'] }}
                                </td>
                                <td class="text-center font-weight-bold text-warning">
                                    Rp {{ number_format($summary['total_transport'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif

                    </table>
                </div>

                {{-- Info jumlah data --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        Menampilkan <strong>{{ count($groupedData) }}</strong> pegawai
                        dalam <strong>{{ count($dates) }}</strong> hari kerja
                    </p>
                    @if(count($groupedData) > 0)
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        Rata-rata kehadiran:
                        <strong class="text-white">
                            {{ count($dates) > 0 ? number_format($summary['total_hadir'] / max(count($groupedData), 1), 1) : 0 }}
                        </strong>
                        hari/pegawai
                    </p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@endsection