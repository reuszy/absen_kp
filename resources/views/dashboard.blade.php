@extends('layouts.master')

@section('content')
<div class="content-wrapper">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="page-title text-white">Dashboard Absensi</h3>
            <p class="text-secondary mb-0">
                Ringkasan aktivitas dan statistik kehadiran —
                <span class="text-white">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────────────── --}}
    <div class="row">

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h3 class="mb-0">{{ $totalPegawai }}</h3>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-warning">
                                <span class="mdi mdi-account-group icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total Pegawai</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h3 class="mb-0 text-success">{{ $hadirHariIni }}</h3>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-success">
                                <span class="mdi mdi-account-check icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Hadir Hari Ini</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h3 class="mb-0 text-danger">{{ $terlambatHariIni }}</h3>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-danger">
                                <span class="mdi mdi-clock-alert icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Terlambat Hari Ini</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h3 class="mb-0 text-info" style="font-size:1.3rem;">
                                Rp {{ number_format($totalTransportBulanIni, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-info">
                                <span class="mdi mdi-cash-multiple icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">
                        Transport {{ \Carbon\Carbon::today()->translatedFormat('F Y') }}
                    </h6>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Progress Kehadiran ───────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <p class="mb-1 font-weight-bold text-white">Tingkat Kehadiran Hari Ini</p>
                            <p class="text-muted mb-0 small">{{ $hadirHariIni }} dari {{ $totalPegawai }} pegawai</p>
                        </div>
                        <div class="col-md-9">
                            @php
                                $persen   = $totalPegawai > 0 ? round(($hadirHariIni / $totalPegawai) * 100, 1) : 0;
                                $barColor = $persen >= 80 ? 'success' : ($persen >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 mr-3" style="height:8px;border-radius:4px;background:#2c2e3e;">
                                    <div class="progress-bar bg-{{ $barColor }}"
                                         style="width:{{ $persen }}%;border-radius:4px;transition:width .6s ease;">
                                    </div>
                                </div>
                                <span class="text-{{ $barColor }} font-weight-bold" style="min-width:48px;">
                                    {{ $persen }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts ───────────────────────────────────────────────────────────── --}}
    <div class="row">

        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Tren Kehadiran (7 Hari Terakhir)</h4>
                        <div class="d-flex" style="gap:12px;">
                            <small class="text-muted">
                                <span style="display:inline-block;width:10px;height:10px;background:#00d25b;border-radius:2px;margin-right:4px;"></span>Tepat Waktu
                            </small>
                            <small class="text-muted">
                                <span style="display:inline-block;width:10px;height:10px;background:#fc424a;border-radius:2px;margin-right:4px;"></span>Terlambat
                            </small>
                        </div>
                    </div>
                    <div style="position:relative;height:250px;">
                        <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Komposisi Kehadiran Hari Ini</h4>
                    @if($hadirHariIni > 0)
                        <div style="position:relative;height:200px;">
                            <canvas id="compositionChart"></canvas>
                        </div>
                        <div class="d-flex justify-content-center mt-3 flex-wrap" style="gap:12px;">
                            <small class="text-muted">
                                <span style="display:inline-block;width:10px;height:10px;background:#00d25b;border-radius:50%;margin-right:4px;"></span>Staf ({{ $staffPresent }})
                            </small>
                            <small class="text-muted">
                                <span style="display:inline-block;width:10px;height:10px;background:#ffab00;border-radius:50%;margin-right:4px;"></span>Pejabat ({{ $pejabatPresent }})
                            </small>
                            @if($unknownPresent > 0)
                            <small class="text-muted">
                                <span style="display:inline-block;width:10px;height:10px;background:#6c7293;border-radius:50%;margin-right:4px;"></span>Tdk Dikenal ({{ $unknownPresent }})
                            </small>
                            @endif
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height:220px;">
                            <span class="mdi mdi-calendar-remove text-muted" style="font-size:3rem;"></span>
                            <p class="text-muted mt-2 mb-0 small">Belum ada kehadiran hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Log Aktivitas Terkini ────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Log Aktivitas Terkini</h4>
                        <a href="{{ route('attendance.logs') }}" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jabatan / Unit</th>
                                    <th>Waktu Scan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                    @php
                                        $nama    = 'Tidak Dikenal (ID: ' . $log->machine_id . ')';
                                        $meta    = '-';
                                        $isKnown = false;
                                        $jabatan = '';

                                        if ($log->staff) {
                                            $isKnown = true;
                                            $nama    = $log->staff->nama;
                                            $jabatan = $log->staff->jabatan ?? 'Staf';
                                            $unit    = optional($log->staff->facultyData)->nama_fakultas ?? '-';
                                            $meta    = $jabatan . ' — ' . $unit;
                                        }

                                        $jLower    = strtolower($jabatan);
                                        $isPejabat = str_contains($jLower, 'dekan')
                                            || str_contains($jLower, 'rektor')
                                            || str_contains($jLower, 'ketua')
                                            || str_contains($jLower, 'kepala')
                                            || str_contains($jLower, 'wakil');
                                        $iconColor = $isPejabat ? 'warning' : ($isKnown ? 'primary' : 'secondary');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-rounded-circle icon-box-{{ $iconColor }} mr-2">
                                                    <i class="mdi {{ $isKnown ? 'mdi-account' : 'mdi-account-question' }}"></i>
                                                </div>
                                                <span class="pl-2 font-weight-bold">{{ $nama }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $meta }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->scan_time)->format('d M Y, H:i:s') }}</td>
                                        <td>
                                            @if($log->keterangan_sistem)
                                                <span class="badge badge-outline-info">{{ $log->keterangan_sistem }}</span>
                                            @else
                                                <span class="badge badge-outline-success">Scan Masuk</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <span class="mdi mdi-inbox-outline" style="font-size:2rem;"></span>
                                            <p class="mt-2 mb-0">Belum ada log aktivitas.</p>
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

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    var ctxWeekly = document.getElementById('weeklyAttendanceChart').getContext('2d');
    new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: @json($dates),
            datasets: [
                {
                    label: 'Tepat Waktu',
                    data: @json($tepatWaktuData),
                    backgroundColor: 'rgba(0, 210, 91, 0.75)',
                    borderColor: '#00d25b',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Terlambat',
                    data: @json($terlambatData),
                    backgroundColor: 'rgba(252, 66, 74, 0.75)',
                    borderColor: '#fc424a',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { color: '#6c7293' }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        color: '#6c7293',
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        footer: function(items) {
                            let total = items.reduce((s, i) => s + i.parsed.y, 0);
                            return 'Total hadir: ' + total + ' orang';
                        }
                    }
                }
            }
        }
    });

    @if($hadirHariIni > 0)
    var ctxComp = document.getElementById('compositionChart').getContext('2d');
    new Chart(ctxComp, {
        type: 'doughnut',
        data: {
            labels: ['Staf', 'Pejabat/Struktural', 'Tidak Dikenal'],
            datasets: [{
                data: [{{ $staffPresent }}, {{ $pejabatPresent }}, {{ $unknownPresent }}],
                backgroundColor: ['#00d25b', '#ffab00', '#6c7293'],
                borderColor: '#191c24',
                borderWidth: 2
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let pct   = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' orang (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
    @endif

});
</script>

@endsection