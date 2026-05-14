@extends('layouts.master')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .profile-card {
        text-align: center;
        padding: 15px 15px;
    }
    .profile-card img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 3px solid #191c24;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    }
    .profile-card h4 {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 1.1rem;
    }
    .profile-card p.text-muted {
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    .profile-details {
        text-align: left;
        margin-top: 15px;
    }
    .profile-details hr {
        border-top: 1px solid #2c2e33;
        margin: 10px 0;
    }
    .profile-details .detail-item {
        margin-bottom: 10px;
    }
    .profile-details .detail-item strong {
        display: block;
        color: #8f9ba6;
        font-size: 0.8rem;
        margin-bottom: 2px;
    }
    .profile-details .detail-item span {
        font-size: 0.95rem;
        color: #ffffff;
    }
    
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        font-size: 0.85em; /* Make calendar text slightly smaller */
    }
    /* Kalender Dark Theme overrides */
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #2c2e33;
    }
    .fc-theme-standard .fc-scrollgrid {
        border-color: #2c2e33;
    }
    .fc-col-header-cell {
        background-color: #191c24;
        color: #8f9ba6;
        padding: 10px 0 !important;
    }
    .fc-daygrid-day-number {
        color: #ffffff;
    }
    .fc-day-other .fc-daygrid-day-number {
        color: #6c7293;
    }
    .fc .fc-button-primary {
        background-color: #0090e7;
        border-color: #0090e7;
    }
    .fc .fc-button-primary:hover {
        background-color: #007bbd;
        border-color: #007bbd;
    }
    .fc .fc-button-primary:disabled {
        background-color: #191c24;
        border-color: #2c2e33;
        color: #6c7293;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        border: none;
    }
    .fc-event-title {
        font-weight: bold;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('staf.index') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Kolom Kiri: Profil -->
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body profile-card">
                <!-- Foto Dummy -->
                <img src="{{ asset('assets/images/faces/face15.jpg') }}" alt="Profile Image" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($staf->nama) }}&background=0D8ABC&color=fff&size=120'">
                
                <h4>{{ $staf->nama }}</h4>
                <p class="text-muted">{{ $staf->jabatan }}</p>
                
                @if($staf->status)
                    <span class="badge badge-success px-3 py-2">Aktif</span>
                @else
                    <span class="badge badge-danger px-3 py-2">Nonaktif</span>
                @endif

                <div class="profile-details">
                    <hr>
                    <div class="detail-item">
                        <strong>NIP</strong>
                        <span>{{ $staf->nip }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Unit Kerja / Fakultas</strong>
                        <span>{{ $staf->facultyData->nama_fakultas ?? '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>ID Mesin (Machine ID)</strong>
                        <span>{{ $staf->machine_id ?? 'Belum terhubung' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Kalender -->
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Riwayat Absensi & Cuti</h4>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var rawEvents = @json($events);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id',
            initialView: 'dayGridMonth',
            contentHeight: 400, // Limit height to avoid scrolling
            aspectRatio: 1.5,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: rawEvents,
            eventClick: function(info) {
                // Tampilkan detail saat event diklik (bisa pakai alert atau modal)
                alert('Status: ' + info.event.title + '\nTanggal: ' + info.event.start.toLocaleDateString('id-ID'));
            }
        });

        calendar.render();
    });
</script>
@endpush
