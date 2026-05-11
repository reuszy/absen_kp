@php
    if (!empty($selectedFaculty)) {
        $reportTitle = 'ABSENSI SIDIK JARI UNIT KERJA ' . $selectedFaculty;
    } else {
        $reportTitle = 'ABSENSI SIDIK JARI UNIT (SEMUA FAKULTAS)';
    }

    // Hitung lebar kolom tanggal secara dinamis
    // A4 landscape usable width ~277mm, dikurangi kolom tetap
    $fixedWidth = ($type == 'daily' || $type == 'weekly')
        ? (10 + 25 + 65 + 50 + 22 + 25) // no+nip+nama+jabatan+jml+transport
        : (10 + 22 + 45 + 40 + 22 + 25);  // versi bulanan lebih ramping

    $dateColWidth = max(18, round((277 - $fixedWidth) / max(count($dates), 1)));
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            margin: 10mm 8mm 10mm 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 6pt;
            word-wrap: break-word;
            white-space: normal;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* ── Lebar Kolom (Persentase) ── */
        .col-no       { width: 3%; }
        .col-nip      { width: {{ $type == 'daily' || $type == 'weekly' ? '4%' : '2%' }}; }
        .col-nama     {
            width: {{ $type == 'daily' || $type == 'weekly' ? '8%' : '4%' }};
            text-align: left;
            padding-left: 4px;
            font-size: 6.5pt;
            line-height: 1.2;
        }
        .col-jabatan  {
            width: {{ $type == 'daily' || $type == 'weekly' ? '10%' : '5%' }};
            text-align: left;
            padding-left: 3px;
            font-size: 6.3pt;
            line-height: 1.2;
        }
        .col-jml      { width: 8%; }
        .col-transport{ width: 10%; text-align: right; padding-right: 3px; }

        /* ── Sel Jam ── */
        .jam-cell {
            font-size: 5.8pt;
            white-space: nowrap;
        }

        .jam-terlambat {
            font-size: 5.8pt;
            white-space: nowrap;
            color: #cc0000;
        }

        .leave-cell {
            font-size: 5.5pt;
            font-weight: bold;
            color: #555;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@php
    $dateChunks = collect($dates)->chunk(12);
@endphp

@forelse($dateChunks as $pageIndex => $chunkDates)

    {{-- ── KOP SURAT ── --}}
    <div style="width: 100%; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
        <div style="float: left; width: 18%; text-align: left;">
            <img src="{{ public_path('assets/images/logo_ugj.jpg') }}" width="65" style="margin-left: 10px;">
        </div>
        <div style="float: left; width: 64%; text-align: center;">
            <h3 style="margin: 0; font-size: 15pt; font-weight: bold; font-family: Arial, sans-serif;">
                UNIVERSITAS SWADAYA GUNUNG JATI
            </h3>
            <p style="margin: 2px 0; font-size: 9pt; font-family: Arial, sans-serif;">
                Jl. Pemuda No. 32 Telp. 0231-206558 Cirebon
            </p>
        </div>
        <div style="float: left; width: 18%;">&nbsp;</div>
        <div style="clear: both;"></div>
    </div>

    {{-- ── JUDUL LAPORAN ── --}}
    <div style="text-align: center; margin-bottom: 12px;">
        <div style="font-size: 7.5pt; margin-bottom: 4px; color: #555;">
            Diprint pada: {{ \Carbon\Carbon::now()->format('d/m/Y H.i') }}
        </div>
        <h3 style="margin: 0; font-size: 10.5pt; font-weight: bold; text-transform: uppercase;">
            {{ $reportTitle }}
        </h3>
        <p style="margin: 3px 0; font-size: 8.5pt;">
            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }}
            s.d
            {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
        </p>
        @if($pageIndex > 0)
            <p style="margin: 0; font-size: 7.5pt; color: #555;">(Halaman {{ $pageIndex + 1 }})</p>
        @endif
    </div>

    {{-- ── TABEL ── --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">No.</th>
                <th rowspan="2" class="col-nip">NIP</th>
                <th rowspan="2" class="col-nama">NAMA</th>
                <th rowspan="2" class="col-jabatan">JABATAN</th>

                @foreach($chunkDates as $date)
                    <th style="width: {{ $dateColWidth }}mm; font-size: 5.5pt;">
                        {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('ddd') }}
                    </th>
                @endforeach

                @if($pageIndex > 0 || $type == 'daily' || $type == 'weekly')
                    <th rowspan="2" class="col-jml">JUMLAH<br>HADIR</th>
                    <th rowspan="2" class="col-transport">JUMLAH<br>TRANSPORT</th>
                @endif
            </tr>
            <tr>
                @foreach($chunkDates as $date)
                    <th style="width: {{ $dateColWidth }}mm; font-size: 5.5pt;">
                        {{ \Carbon\Carbon::parse($date)->format('d') }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($groupedData as $data)
                <tr>
                    <td class="col-no" style="text-align: center;">{{ $loop->iteration }}</td>
                    <td class="col-nip">{{ $data['nip'] ?? '-' }}</td>
                    <td class="col-nama">{{ $data['name'] ?? '-' }}</td>
                    <td class="col-jabatan">{{ $data['jabatan'] ?? '-' }}</td>

                    @foreach($chunkDates as $date)
                        @if(isset($data['attendance'][$date]))
                            @php $atts = $data['attendance'][$date]; @endphp
                            <td class="{{ $atts->status_kehadiran == 'Terlambat' ? 'jam-terlambat' : 'jam-cell' }}">
                                {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }}
                                /
                                {{ $atts->jam_pulang ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i') : '-' }}
                            </td>
                        @elseif(isset($data['leaves'][$date]))
                            <td class="leave-cell">
                                {{ strtoupper($data['leaves'][$date]) }}
                            </td>
                        @else
                            <td class="jam-cell" style="color: #bbb;">-</td>
                        @endif
                    @endforeach

                    @if($pageIndex > 0 || $type == 'daily' || $type == 'weekly')
                        <td class="col-jml" style="text-align: center;">
                            {{ $data['total_hadir'] }}
                        </td>
                        <td class="col-transport">
                            {{ number_format($data['total_transport'], 0, ',', '.') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + count($chunkDates) + ($pageIndex > 0 || $type == 'daily' || $type == 'weekly' ? 2 : 0) }}"
                        style="text-align: center; padding: 10px; color: #888;">
                        Tidak ada data kehadiran.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── CATATAN & TTD (halaman terakhir saja) ── --}}
    @if($loop->last)
        <div style="margin-top: 16px; font-size: 7pt;">
            <strong>Catatan:</strong>
            <ol style="margin-top: 3px; padding-left: 14px; line-height: 1.6;">
                <li>Jam Kerja Yang Tercatat Mesin Sidik Jari (MSJ) Pukul 06:00 s.d 18:00; di luar jam tersebut tidak tercatat kecuali anggota Satpam.</li>
                <li>MSJ hanya mencatat sesuai jam masuk unit kerja masing-masing.</li>
                <li>Jika terjadi kesalahan data agar segera melapor ke bagian kepegawaian.</li>
                <li>Bagi yang berhalangan hadir atau dinas luar agar menghubungi bagian kepegawaian.</li>
            </ol>
        </div>

        <div style="margin-top: 28px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 38%; border: none; text-align: center; vertical-align: top; font-size: 8pt;">
                        Mengetahui,<br>
                        Kepala Biro Adm. HKUP
                        <div style="height: 55px;"></div>
                        <span style="font-weight: bold; text-decoration: underline;">( Defi Safitri, SH., MH. )</span>
                    </td>
                    <td style="width: 24%; border: none;"></td>
                    <td style="width: 38%; border: none; text-align: center; vertical-align: top; font-size: 8pt;">
                        Cirebon, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Kepala Bagian Kepegawaian
                        <div style="height: 55px;"></div>
                        <span style="font-weight: bold; text-decoration: underline;">( Devita Puspitasari, SH., MH. )</span>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@empty
    {{-- ── KOP (empty state) ── --}}
    <div style="width: 100%; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
        <div style="float: left; width: 18%; text-align: left;">
            <img src="{{ public_path('assets/images/logo_ugj.jpg') }}" width="65" style="margin-left: 10px;">
        </div>
        <div style="float: left; width: 64%; text-align: center;">
            <h3 style="margin: 0; font-size: 15pt; font-weight: bold; font-family: Arial, sans-serif;">UNIVERSITAS SWADAYA GUNUNG JATI</h3>
            <p style="margin: 2px 0; font-size: 9pt; font-family: Arial, sans-serif;">Jl. Pemuda No. 32 Telp. 0231-206558 Cirebon</p>
        </div>
        <div style="float: left; width: 18%;">&nbsp;</div>
        <div style="clear: both;"></div>
    </div>

    <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 7.5pt; margin-bottom: 4px;">Diprint pada: {{ \Carbon\Carbon::now()->format('d/m/Y H.i') }}</div>
        <h3 style="margin: 0; font-size: 10.5pt; font-weight: bold; text-transform: uppercase;">{{ $reportTitle }}</h3>
        <p style="margin: 2px 0; font-size: 8.5pt;">
            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }}
            s.d
            {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
        </p>
    </div>

    <div style="text-align: center; margin-top: 50px; color: #666;">
        <p style="font-weight: bold;">TIDAK ADA JADWAL ABSENSI UNTUK TANGGAL INI</p>
        <p style="font-size: 8pt;">(Hari Libur / Akhir Pekan)</p>
    </div>
@endforelse

</body>
</html>