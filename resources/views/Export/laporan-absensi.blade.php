<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Bulanan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .kop-wrapper {
            width: 100%;
            border-bottom: 4px solid #000;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            border: none !important;
            /* penting agar tidak muncul garis tabel */
        }

        .kop-logo {
            width: 90px;
        }

        .kop-title {
            text-align: center;
            line-height: 1.4;
        }

        .kop-title .rs {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-title .unit {
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px;
        }

        .kop-title .alamat {
            font-size: 10px;
            margin-top: 6px;
        }

        .garis-tipis {
            border-bottom: 1px solid #000;
            margin-top: 3px;
        }

        .info {
            margin-top: 20px;
        }

        .info td {
            padding: 4px 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .center {
            text-align: center;
        }

        .ttd {
            margin-top: 50px;
            width: 100%;
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
    <div class="kop-wrapper">
        <table class="kop-table">
            <tr>
                <td width="18%" align="center">
                    <img src="{{ public_path('assets/img/logo_kop.png') }}" class="kop-logo">
                </td>
                <td width="82%" class="kop-title">
                    <div class="rs">RUMAH SAKIT DAERAH KALISAT</div>
                    <div class="unit">UNIT TEKNOLOGI INFORMASI</div>
                    <div class="alamat">
                        VR&C+898, Jl. MH. Thamrin No.31, Dusun Krajan, Ajung<br>
                        Kec. Kalisat, Kabupaten Jember, Jawa Timur 68193
                    </div>
                </td>
            </tr>
        </table>

        <!-- Garis ganda resmi -->
        <div class="garis-tipis"></div>
    </div>

    {{-- JUDUL --}}
    <h3 class="center" style="margin-top:20px">
        LAPORAN ABSENSI BULANAN
    </h3>

    {{-- INFO PEGAWAI --}}
    <table class="info">
        <tr>
            <td width="20%">Nama</td>
            <td width="30%">: {{ $pegawai->name }}</td>
            <td width="20%">NIP/NIK</td>
            <td width="30%">: {{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: {{ $pegawai->jabatan->nama_jabatan ?? '-' }}</td>
            <td>Lokasi</td>
            <td>: {{ $pegawai->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jam Kerja</td>
            <td colspan="3">
                : {{ $pegawai->jamKerja->jam_mulai ?? '-' }}
                - {{ $pegawai->jamKerja->jam_selesai ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Periode</td>
            <td colspan="3">: {{ $namaBulan }} {{ $tahunAktif }}</td>
        </tr>
    </table>

    {{-- TABEL ABSENSI --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Keterangan Izin dan Sakit</th>
                <th>Keterangan Edit</th>
                <th>Editor</th>
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
            @foreach ($absensi as $i => $row)
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

                            // Pastikan masuk ke shift ini
                            if (!$waktuMasuk->between($jamMulaiEarly, $jamSelesai)) {
                                continue;
                            }

                            // Hitung TL
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
                    <td class="center">{{ $i + 1 }}</td>

                    <td>
                        {{ Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y') }}
                    </td>

                    {{-- WAKTU MASUK + TL --}}
                    <td class="center">
                        @if ($row->waktu_masuk)
                            {{ Carbon::parse($row->waktu_masuk)->format('H:i') }}

                            @if ($badge)
                                @php
                                    $warnaTL = match ($badge) {
                                        'TL1' => 'bg-label-warning',
                                        'TL2' => 'bg-label-danger',
                                        'TL3' => 'bg-label-danger',
                                        'TL4' => 'bg-label-dark',
                                        default => 'bg-label-warning',
                                    };
                                @endphp

                                <span class="badge {{ $warnaTL }} ms-1">
                                    {{ $badge }}
                                </span>
                            @endif
                        @else
                            -
                        @endif
                    </td>

                    {{-- WAKTU PULANG --}}
                    <td class="center">
                        {{ $row->waktu_pulang ? Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                    </td>

                    {{-- STATUS --}}
                    <td class="center">
                        @php
                            $statusColor = match ($row->status) {
                                'hadir' => 'bg-label-success',
                                'izin' => 'bg-label-warning',
                                'sakit' => 'bg-label-danger',
                                'belum_hadir' => 'bg-label-secondary',
                                default => 'bg-label-dark',
                            };
                        @endphp

                        <span class="badge {{ $statusColor }}">
                            {{ strtoupper(str_replace('_', ' ', $row->status)) }}
                        </span>
                    </td>

                    <td>{{ $row->keterangan ?? '-' }}</td>
                    <td>{{ $row->alasan_edit ?? '-' }}</td>

                    <td class="center">
                        {{ $row->edited_by_name ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TTD --}}
    <table class="ttd">
        <tr>
            <td width="70%"></td>
            <td class="center">
                Jember, {{ now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br><br><br><br>
                <strong>______________________</strong><br>
                ______________________
            </td>
        </tr>
    </table>

</body>

</html>
