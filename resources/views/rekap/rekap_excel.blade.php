<table>
    <thead>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="font-size: 14px; font-weight: bold; text-align: center;">
                {{ $reportTitle }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="text-align: center;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($dates) + 6 }}" style="text-align: center;">
                Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr></tr> <!-- Empty row for spacing -->

        <!-- Header Tabel -->
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">NO</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">NIP</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">NAMA</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">JABATAN</th>

            @foreach($dates as $date)
                <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">
                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd') }}
                </th>
            @endforeach

            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">JUMLAH KEHADIRAN</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">JUMLAH TRANSPORT</th>
        </tr>
        <tr>
            @foreach($dates as $date)
                <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">
                    {{ \Carbon\Carbon::parse($date)->format('d') }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @forelse($groupedData as $data)
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $loop->iteration }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $data['nip'] ?? '-' }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $data['name'] ?? '-' }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $data['jabatan'] ?? '-' }}</td>

                @foreach($dates as $date)
                    <td style="text-align: center; border: 1px solid #000000;">
                        @if(isset($data['attendance'][$date]))
                            @php $atts = $data['attendance'][$date]; @endphp
                            {{ \Carbon\Carbon::parse($atts->jam_masuk)->format('H:i') }} / {{ $atts->jam_pulang ? \Carbon\Carbon::parse($atts->jam_pulang)->format('H:i') : '-' }}
                        @elseif(isset($data['leaves'][$date]))
                            {{ strtoupper($data['leaves'][$date]) }}
                        @else
                            -
                        @endif
                    </td>
                @endforeach

                <td style="text-align: center; border: 1px solid #000000;">{{ $data['total_hadir'] }}</td>
                <td style="text-align: right; border: 1px solid #000000;">{{ $data['total_transport'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($dates) + 6 }}" style="text-align: center; border: 1px solid #000000;">
                    TIDAK ADA JADWAL ABSENSI UNTUK PERIODE INI
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
