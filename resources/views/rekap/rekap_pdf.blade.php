@php
    if (!empty($selectedFaculty)) {
        $reportTitle = 'ABSENSI SIDIK JARI UNIT KERJA ' . $selectedFaculty;
    } else {
        $reportTitle = 'ABSENSI SIDIK JARI UNIT (SEMUA FAKULTAS)';
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle }}</title>

    <style>
        @page {
            margin: 10mm 5mm 10mm 5mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7pt;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 8pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 2px 2px;
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


        @if($type == 'daily' || $type == 'weekly')
            /* Layout Harian/Mingguan (Lebih Lebar) */
            .col-no { width: 14px; }
            .col-nip { width: 60px; } /* Fixed 9 chars */
            .col-nama {
                width: 140px; /* Max 45 chars / 8 words */
                text-align: left;
                padding-left: 4px;
                font-size: 6.5pt;
                line-height: 1.15;
            }
            .col-jabatan {
                width: 120px; /* Max 65 chars */
                text-align: left;
                padding-left: 4px;
                font-size: 6.3pt;
                line-height: 1.15;
            }
        @else
            /* Layout Bulanan (Standard) */
            .col-no { width: 14px; }
            .col-nip { width: 50px; }
            .col-nama {
                width: 95px;
                text-align: left;
                padding-left: 3px;
                font-size: 6.5pt;
                line-height: 1.15;
            }
            .col-jabatan {
                width: 80px;
                text-align: center;
                font-size: 6.3pt;
                line-height: 1.15;
            }
        @endif

        .jam-cell {
            font-size: 5.5pt;
            white-space: nowrap;
        }

        .col-jml { width: 55px; }
        .col-transport { width: 60px; text-align: center; padding-right: 3px; }

        tr {
            page-break-inside: avoid;
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

    {{-- HEADER --}}
    {{-- HEADER LETTERHEAD (FORMAL BALANCED LAYOUT) --}}
    <div style="width: 100%; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
        {{-- Left Column: Logo (20%) --}}
        <div style="float: left; width: 20%; text-align: left;">
            <img src="{{ public_path('assets/images/logo_ugj.jpg') }}" width="70" style="margin-left: 20px;">
        </div>
        
        {{-- Center Column: Text (60%) - Perfectly Centered on Page --}}
        <div style="float: left; width: 60%; text-align: center;">
            <h3 style="margin: 0; font-size: 16pt; font-weight: bold; font-family: Arial, sans-serif;">UNIVERSITAS SWADAYA GUNUNG JATI</h3>
            <p style="margin: 2px 0; font-size: 10pt; font-family: Arial, sans-serif;">Jl. Pemuda No. 32 Telp. 0231-206558 Cirebon</p>
        </div>

        {{-- Right Column: Balancer (20%) - Keeps Center Column Centered --}}
        <div style="float: left; width: 20%;">
            &nbsp;
        </div>
        
        <div style="clear: both;"></div>
    </div>

    {{-- REPORT TITLE BLOCK --}}
    <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 8pt; margin-bottom: 5px;">Diprint pada: {{ \Carbon\Carbon::now()->format('d/m/Y H.i') }}</div>
        <h3 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase;">{{ $reportTitle }}</h3>
        <p style="margin: 2px 0; font-size: 9pt;">
            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
        </p>
         @if($pageIndex > 0)
            <p style="margin: 0; font-size: 8pt;">(Halaman {{ $pageIndex + 1 }})</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO</th>
                <th rowspan="2" class="col-nip">NIP</th>
                <th rowspan="2" class="col-nama">NAMA</th>
                <th rowspan="2" class="col-jabatan">JABATAN</th>

                @foreach($chunkDates as $date)
                    <th>
                        {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd') }}
                    </th>
                @endforeach

                @if($pageIndex > 0 || $type == 'daily' || $type == 'weekly')
                    <th rowspan="2" class="col-jml">JUMLAH<br>KEHADIRAN</th>
                    <th rowspan="2" class="col-transport">JUMLAH<br>TRANSPORT</th>
                @endif
            </tr>
            <tr>
                @foreach($chunkDates as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($groupedData as $data)
                <tr>
                    <td class="col-no">{{ $loop->iteration }}</td>
                    <td class="col-nip">{{ $data['nip'] ?? '-' }}</td>
                    <td class="col-nama">{{ $data['name'] ?? '-' }}</td>
                    <td class="col-jabatan">{{ $data['jabatan'] ?? '-' }}</td>

                    @foreach($chunkDates as $date)
                        <td class="jam-cell">
                            @if(isset($data['attendance'][$date]))
                                @php $atts = $data['attendance'][$date]; @endphp
                                {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }}
                                /
                                {{ $atts->jam_pulang
                                    ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i')
                                    : '-' }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach

                    @if($pageIndex > 0 || $type == 'daily' || $type == 'weekly')
                        <td class="col-jml">{{ $data['total_hadir'] }}</td>
                        <td class="col-transport">
                            {{ number_format($data['total_transport'], 0, ',', '.') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="col-no">-</td>
                    <td class="col-nip">-</td>
                    <td class="col-nama">-</td>
                    <td class="col-jabatan">-</td>

                    @foreach($chunkDates as $date)
                        <td class="jam-cell">-</td>
                    @endforeach

                    @if($pageIndex > 0 || $type == 'daily' || $type == 'weekly')
                        <td class="col-jml">-</td>
                        <td class="col-transport">-</td>
                    @endif
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($loop->last)
        <div class="notes-section" style="margin-top: 20px; font-size: 7pt; page-break-inside: avoid;">
            <strong>Catatan:</strong>
            <ol style="margin-top: 2px; padding-left: 15px;">
                <li>Jam Kerja Yang Tercatat Mesin Sidik Jari (MSJ) Pukul 06:00 s.d 18:00 di luar jam tersebut tidak tercatat kecuali anggota Satpam.</li>
                <li>MSJ hanya mencatat sesuai jam masuk unit kerja masing-masing.</li>
                <li>Jika terjadi kesalahan data agar segera melapor ke bagian kepegawaian.</li>
                <li>Bagi yang berhalangan hadir atau dinas luar agar menghubungi bagian kepegawaian.</li>
            </ol>
        </div>

        <div class="signature-section" style="margin-top: 30px; page-break-inside: avoid;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 40%; border: none; text-align: center; vertical-align: top;">
                        Mengetahui,<br>
                        Kepala Biro Adm. HKUP
                        <div style="height: 60px;"></div>
                        <span style="font-weight: bold; text-decoration: underline;">( Defi Safitri, SH., MH.)</span>
                    </td>
                    <td style="width: 20%; border: none;"></td>
                    <td style="width: 40%; border: none; text-align: center; vertical-align: top;">
                        Cirebon, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Kepala Bagian Kepegawaian
                        <div style="height: 60px;"></div>
                        <span style="font-weight: bold; text-decoration: underline;">( Devita Puspitasari, SH., MH.)</span>
                        </td>
                </tr>
            </table>
        </div>
    @endif

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@empty
    {{-- HEADER (Copied for Empty State) --}}
    <div style="width: 100%; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
        <div style="float: left; width: 20%; text-align: left;">
            <img src="{{ public_path('assets/images/logo_ugj.jpg') }}" width="70" style="margin-left: 20px;">
        </div>
        <div style="float: left; width: 60%; text-align: center;">
            <h3 style="margin: 0; font-size: 16pt; font-weight: bold; font-family: Arial, sans-serif;">UNIVERSITAS SWADAYA GUNUNG JATI</h3>
            <p style="margin: 2px 0; font-size: 10pt; font-family: Arial, sans-serif;">Jl. Pemuda No. 32 Telp. 0231-206558 Cirebon</p>
        </div>
        <div style="float: left; width: 20%;">&nbsp;</div>
        <div style="clear: both;"></div>
    </div>

    {{-- REPORT TITLE BLOCK --}}
    <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 8pt; margin-bottom: 5px;">Diprint pada: {{ \Carbon\Carbon::now()->format('d/m/Y H.i') }}</div>
        <h3 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase;">{{ $reportTitle }}</h3>
        <p style="margin: 2px 0; font-size: 9pt;">
            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
        </p>
    </div>

    <div style="text-align: center; margin-top: 50px; font-weight: bold; color: #555;">
        <p>TIDAK ADA JADWAL ABSENSI UNTUK TANGGAL INI</p>
        <p style="font-size: 8pt; font-weight: normal;">(Hari Libur / Akhir Pekan)</p>
    </div>

@endforelse

</body>
</html>