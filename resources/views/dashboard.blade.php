@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="page-title text-white">Dashboard Absensi</h3>
            <p class="text-secondary">Ringkasan aktivitas dan statistik kehadiran kampus.</p>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">{{ $totalPegawai }}</h3>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-warning">
                                <span class="mdi mdi-account-group icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total Personil</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0 text-success">{{ $hadirHariIni }}</h3>
                            </div>
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
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0 text-danger">{{ $terlambatHariIni }}</h3>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-danger">
                                <span class="mdi mdi-clock-alert icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Terlambat</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0 text-primary">{{ number_format(($totalPegawai > 0 ? ($hadirHariIni / $totalPegawai) * 100 : 0), 1) }}%</h3>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-primary">
                                <span class="mdi mdi-chart-donut icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Persentase Kehadiran</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Line Chart: Weekly Limit -->
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tren Kehadiran (7 Hari Terakhir)</h4>
                    <canvas id="weeklyAttendanceChart" style="height:250px"></canvas>
                </div>
            </div>
        </div>
        <!-- Pie Chart: Composition -->
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Komposisi Kehadiran Hari Ini</h4>
                    <canvas id="compositionChart" style="height:250px"></canvas>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <div class="mr-3">
                            <span class="dot-indicator bg-success"></span> Dosen
                        </div>
                        <div>
                            <span class="dot-indicator bg-primary"></span> Staff
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs Row -->
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
                                    <th> Nama </th>
                                    <th> Jabatan/Unit </th>
                                    <th> Waktu Scan </th>
                                    <th> Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                    @php
                                        $name = '-';
                                        $meta = '-';
                                        $isDosen = false;
                                        if($log->dosen) {
                                            $name = $log->dosen->nama;
                                            $meta = 'Dosen - ' . $log->dosen->fakultas;
                                            $isDosen = true;
                                        } elseif($log->staff) {
                                            $name = $log->staff->nama;
                                            $meta = 'Staff - ' . $log->staff->unit_kerja;
                                        } else {
                                            $name = 'Unknown (ID: ' . $log->machine_id . ')';
                                        }
                                    @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-rounded-circle icon-box-{{ $isDosen ? 'success' : 'primary' }} mr-2">
                                                <i class="mdi {{ $isDosen ? 'mdi-teach' : 'mdi-account' }}"></i>
                                            </div>
                                            <span class="pl-2 font-weight-bold">{{ $name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted"> {{ $meta }} </td>
                                    <td> {{ $log->scan_time }} </td>
                                    <td>
                                        <div class="badge badge-outline-success">Log Masuk</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data scan hari ini.</td>
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
<!-- Using CDN to ensure it works, or use local asset if version matches. Local asset is 'assets/vendors/chart.js/Chart.min.js' 
     But to be safe and ensure flexibility I'll write the script inline to use the loaded library -->
     
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Weekly Chart
    var ctxWeekly = document.getElementById('weeklyAttendanceChart').getContext('2d');
    var weeklyChart = new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: @json($dates),
            datasets: [{
                label: 'Jumlah Hadir',
                data: @json($attendanceCounts),
                backgroundColor: 'rgba(0, 210, 91, 0.5)', // Greenish
                borderColor: 'rgba(0, 210, 91, 1)',
                borderWidth: 1,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#6c7293'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c7293'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Composition Pie Chart
    var ctxComp = document.getElementById('compositionChart').getContext('2d');
    var compositionChart = new Chart(ctxComp, {
        type: 'doughnut',
        data: {
            labels: ['Staff', 'Lainnya'],
            datasets: [{
                data: [{{ $staffPresent }}, {{ $unknownPresent }}],
                backgroundColor: [
                    '#00d25b', // Success Green
                    '#0090e7', // Primary Blue
                    '#8f5fe8'  // Purple
                ],
                borderColor: '#191c24',
                borderWidth: 2
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

@endsection
