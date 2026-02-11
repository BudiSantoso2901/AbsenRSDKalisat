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
            @foreach ($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->pegawai->name ?? '-' }}</td>
                    <td>{{ $row->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $row->waktu_masuk ?? '-' }}</td>
                    <td>{{ $row->waktu_pulang ?? '-' }}</td>
                    <td>{{ ucfirst($row->status) }}</td>
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
