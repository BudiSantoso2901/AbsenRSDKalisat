<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }

        table.data th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    @include('PDF.Kop')

    <br>

    <div style="text-align: center; font-size: 12px; font-weight: bold;">
        LAPORAN ABSENSI PEGAWAI
    </div>

    <div style="text-align: center; font-size: 10px; margin-bottom: 10px;">
        @if (!empty($filter['start_date']))
            Periode: {{ $filter['start_date'] }} s/d {{ $filter['end_date'] }}
        @endif
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                use Carbon\Carbon;

                $shifts = [
                    [
                        'nama' => 'Shift Malam',
                        'jam_mulai' => '20:00:00',
                        'jam_selesai' => '06:00:00',
                        'toleransi' => 10,
                        'early_allowed' => 120,
                    ],
                    [
                        'nama' => 'Shift Siang',
                        'jam_mulai' => '14:00:00',
                        'jam_selesai' => '19:00:00',
                        'toleransi' => 10,
                        'early_allowed' => 120,
                    ],
                    [
                        'nama' => 'Shift Pagi',
                        'jam_mulai' => '07:00:00',
                        'jam_selesai' => '13:00:00',
                        'toleransi' => 10,
                        'early_allowed' => 120,
                    ],
                ];
            @endphp

            @foreach ($data as $i => $row)
                @php
                    $badge = null;

                    if ($row->status === 'hadir' && $row->waktu_masuk) {
                        $waktuMasuk = Carbon::parse($row->waktu_masuk, 'Asia/Jakarta');
                        $tanggalMasuk = $waktuMasuk->toDateString();

                        foreach ($shifts as $shift) {
                            $jamMulai = Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                $tanggalMasuk . ' ' . $shift['jam_mulai'],
                                'Asia/Jakarta',
                            );

                            // 🔥 HANDLE SHIFT MALAM
                            if ($shift['jam_selesai'] < $shift['jam_mulai'] && $waktuMasuk->lt($jamMulai)) {
                                $jamMulai->subDay();
                            }

                            $jamSelesai = Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                $jamMulai->toDateString() . ' ' . $shift['jam_selesai'],
                                'Asia/Jakarta',
                            );

                            if ($shift['jam_selesai'] < $shift['jam_mulai']) {
                                $jamSelesai->addDay();
                            }

                            $jamMulaiEarly = $jamMulai->copy()->subMinutes($shift['early_allowed']);
                            $jamMulaiToleransi = $jamMulai->copy()->addMinutes($shift['toleransi']);

                            if (!$waktuMasuk->between($jamMulaiEarly, $jamSelesai)) {
                                continue;
                            }

                            if ($waktuMasuk->gt($jamMulaiToleransi)) {
                                $menitTelat = $jamMulaiToleransi->diffInMinutes($waktuMasuk);

                                $badge = match (true) {
                                    $menitTelat <= 30 => 'TL1',
                                    $menitTelat <= 60 => 'TL2',
                                    $menitTelat <= 90 => 'TL3',
                                    default => 'TL4',
                                };
                            }

                            break;
                        }
                    }
                @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->pegawai->name ?? '-' }}</td>

                    <td>
                        {{ Carbon::parse($row->tanggal)->locale('id')->translatedFormat('j F Y') }}
                    </td>

                    {{-- WAKTU MASUK + TL --}}
                    <td>
                        @if ($row->waktu_masuk)
                            {{ Carbon::parse($row->waktu_masuk)->format('H:i') }}

                            @if ($badge)
                                <span style="color:red; font-weight:bold; margin-left:5px;">
                                    {{ $badge }}
                                </span>
                            @endif
                        @else
                            -
                        @endif
                    </td>

                     <td>{{ $row->waktu_pulang ?? '-' }}</td>

                    <td>
                        {{ ucfirst($row->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>

    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                Kalisat, {{ date('d F Y') }}<br><br><br>
                <b>Petugas</b>
            </td>
        </tr>
    </table>

</body>

</html>
