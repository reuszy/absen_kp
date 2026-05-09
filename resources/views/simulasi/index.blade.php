@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title text-white">
                        <i class="mdi mdi-play-circle-outline mr-1"></i> Simulasi Absen
                    </h3>
                    <p class="text-secondary">Simulasikan scan absen masuk & pulang untuk demo/testing.</p>
                </div>
                <div>
                    <span class="badge badge-dark p-2" id="currentTime" style="font-size: 1rem;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Result --}}
    <div class="row" id="alertRow" style="display:none;">
        <div class="col-12">
            <div class="alert" id="alertBox" role="alert">
                <span id="alertText"></span>
                <button type="button" class="close" onclick="document.getElementById('alertRow').style.display='none'">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Control Bar --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <label class="mb-0 text-muted" style="white-space:nowrap;">Waktu Simulasi:</label>
                            <input type="datetime-local" id="simTime" class="form-control form-control-sm" style="max-width:260px;">
                            <button class="btn btn-outline-info btn-sm" onclick="setNow()" title="Set ke waktu sekarang">
                                <i class="mdi mdi-clock-outline"></i> Sekarang
                            </button>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <button class="btn btn-info btn-sm" onclick="refreshStaff()">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="resetAll()" id="btnResetAll">
                                <i class="mdi mdi-delete-sweep"></i> Reset Semua Absen Hari Ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Staff Cards --}}
    <div class="row" id="staffContainer">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Memuat data staff...</p>
        </div>
    </div>
</div>

<style>
    .sim-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .sim-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .status-belum { background: rgba(108,114,147,0.2); color: #6c7293; }
    .status-masuk { background: rgba(0,210,91,0.15); color: #00d25b; }
    .status-pulang { background: rgba(0,144,231,0.15); color: #0090e7; }
    .btn-scan {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.25s ease;
    }
    .btn-scan:hover { transform: scale(1.03); }
    .btn-scan:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .btn-masuk { background: linear-gradient(135deg, #00d25b, #00b84d); border: none; color: #fff; }
    .btn-pulang { background: linear-gradient(135deg, #0090e7, #0078c4); border: none; color: #fff; }
    .btn-reset-single { background: none; border: 1px solid rgba(252,75,108,0.4); color: #fc4b6c; }
    .btn-reset-single:hover { background: rgba(252,75,108,0.1); color: #fc4b6c; }
    .detail-row { font-size: 0.8rem; color: #6c7293; }
    .detail-row span { color: #e4e6eb; }
    .card-staff-name { font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 2px; }
    .card-staff-meta { font-size: 0.8rem; color: #6c7293; }
</style>

<script>
    const BASE = '/api/simulasi';

    // Set current time on load
    function setNow() {
        const now = new Date();
        const offset = now.getTimezoneOffset();
        const local = new Date(now.getTime() - offset * 60000);
        document.getElementById('simTime').value = local.toISOString().slice(0, 16);
    }

    function getSimTime() {
        const val = document.getElementById('simTime').value;
        if (!val) return null;
        // Convert datetime-local to Y-m-d H:i:s
        return val.replace('T', ' ') + ':00';
    }

    function updateClock() {
        const el = document.getElementById('currentTime');
        const now = new Date();
        el.textContent = now.toLocaleString('id-ID', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    function showAlert(message, type = 'success') {
        const row = document.getElementById('alertRow');
        const box = document.getElementById('alertBox');
        const text = document.getElementById('alertText');
        box.className = 'alert alert-' + type;
        text.innerHTML = message;
        row.style.display = 'block';
        setTimeout(() => { row.style.display = 'none'; }, 5000);
    }

    async function refreshStaff() {
        const container = document.getElementById('staffContainer');
        container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Memuat data staff...</p></div>';

        try {
            const res = await fetch(BASE + '/staff');
            const data = await res.json();
            renderStaff(data.staff);
        } catch (e) {
            container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Gagal memuat data.</p></div>';
        }
    }

    function renderStaff(staffList) {
        const container = document.getElementById('staffContainer');
        
        if (!staffList.length) {
            container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">Tidak ada data staff aktif.</p></div>';
            return;
        }

        let html = '';
        staffList.forEach(s => {
            const statusClass = s.status_hari_ini === 'Belum Absen' ? 'status-belum' 
                               : s.status_hari_ini === 'Sudah Masuk' ? 'status-masuk' 
                               : 'status-pulang';

            const canMasuk = s.status_hari_ini === 'Belum Absen';
            const canPulang = s.status_hari_ini === 'Sudah Masuk';
            const isDone = s.status_hari_ini === 'Sudah Pulang';

            let detailHtml = '';
            if (s.daily) {
                detailHtml += `<div class="detail-row mt-2">`;
                if (s.daily.jam_masuk) detailHtml += `<div>Masuk: <span>${s.daily.jam_masuk}</span> — <span class="${s.daily.status_kehadiran === 'Terlambat' ? 'text-danger' : 'text-success'}">${s.daily.status_kehadiran}${s.daily.terlambat_menit > 0 ? ' (' + s.daily.terlambat_menit + ' mnt)' : ''}</span></div>`;
                if (s.daily.jam_pulang) detailHtml += `<div>Pulang: <span>${s.daily.jam_pulang}</span></div>`;
                if (s.daily.uang_transport > 0) detailHtml += `<div>Transport: <span class="text-warning">Rp ${Number(s.daily.uang_transport).toLocaleString('id-ID')}</span></div>`;
                detailHtml += `</div>`;
            }

            html += `
            <div class="col-xl-4 col-lg-6 col-md-6 grid-margin stretch-card">
                <div class="card sim-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="card-staff-name">${s.nama}</div>
                                <div class="card-staff-meta">${s.jabatan} — ${s.fakultas}</div>
                                <div class="card-staff-meta">NIP: ${s.nip} | Shift: ${s.shift} (${s.jam_masuk_shift.slice(0,5)} - ${s.jam_pulang_shift.slice(0,5)})</div>
                            </div>
                            <span class="status-badge ${statusClass}">${s.status_hari_ini}</span>
                        </div>
                        ${detailHtml}
                        <div class="d-flex mt-3" style="gap:8px;">
                            <button class="btn btn-scan btn-masuk btn-sm flex-fill" 
                                    onclick="doScan('${s.machine_id}', 'masuk')" 
                                    ${!canMasuk ? 'disabled' : ''}>
                                <i class="mdi mdi-login"></i> Absen Masuk
                            </button>
                            <button class="btn btn-scan btn-pulang btn-sm flex-fill" 
                                    onclick="doScan('${s.machine_id}', 'pulang')" 
                                    ${!canPulang ? 'disabled' : ''}>
                                <i class="mdi mdi-logout"></i> Absen Pulang
                            </button>
                            <button class="btn btn-scan btn-reset-single btn-sm" 
                                    onclick="resetSingle('${s.machine_id}', '${s.nama}')" 
                                    title="Reset absen ${s.nama}">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }

    async function doScan(machineId, type) {
        const simTime = getSimTime();
        const body = { machine_id: machineId };
        if (simTime) body.scan_time = simTime;

        try {
            const res = await fetch(BASE + '/scan', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify(body)
            });
            const data = await res.json();

            if (res.ok) {
                let msg = `<strong>${data.message}</strong>`;
                if (data.status) msg += ` — ${data.status}`;
                if (data.transport !== undefined) msg += ` — Uang Transport: Rp ${Number(data.transport).toLocaleString('id-ID')}`;
                showAlert(msg, 'success');
            } else {
                showAlert(`<strong>Error:</strong> ${data.message}`, 'danger');
            }
            refreshStaff();
        } catch (e) {
            showAlert('Terjadi kesalahan koneksi.', 'danger');
        }
    }

    async function resetSingle(machineId, nama) {
        if (!confirm(`Reset data absen "${nama}" hari ini?`)) return;

        try {
            const res = await fetch(BASE + '/reset', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ machine_id: machineId })
            });
            const data = await res.json();
            showAlert(`Data absen "${nama}" berhasil direset.`, 'info');
            refreshStaff();
        } catch (e) {
            showAlert('Gagal mereset data.', 'danger');
        }
    }

    async function resetAll() {
        if (!confirm('Reset SEMUA data absen hari ini? Ini tidak bisa dibatalkan.')) return;

        try {
            const res = await fetch(BASE + '/reset', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            showAlert(`Semua data absen hari ini berhasil direset. (${data.deleted_daily} daily, ${data.deleted_logs} logs)`, 'info');
            refreshStaff();
        } catch (e) {
            showAlert('Gagal mereset data.', 'danger');
        }
    }

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        setNow();
        updateClock();
        setInterval(updateClock, 1000);
        refreshStaff();
    });
</script>
@endsection
