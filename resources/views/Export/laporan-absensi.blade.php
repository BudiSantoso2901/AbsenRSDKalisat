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

        .kop {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
        }

        .kop h2,
        .kop h3 {
            margin: 0;
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
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <div class="kop">
        <h2>RUMAH SAKIT DAERAH KALISAT</h2>
        <h3>UNIT TEKNOLOGI INFORMASI</h3>
        <small>VR8C+898, Jl. MH. Thamrin No.31, Dusun Krajan, Ajung, Kec. Kalisat, Kabupaten Jember, Jawa Timur
            68193</small>
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
            <td width="20%">NIP</td>
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
            @foreach ($absensi as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                    <td class="center">{{ $row->waktu_masuk ?? '-' }}</td>
                    <td class="center">{{ $row->waktu_pulang ?? '-' }}</td>
                    <td class="center">{{ strtoupper($row->status) }}</td>
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
                Banyuwangi, {{ now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br><br><br><br>
                <strong>______________________</strong><br>
                ______________________
            </td>
        </tr>
    </table>

</body>

</html>
