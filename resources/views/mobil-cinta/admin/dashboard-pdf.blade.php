<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Perjalanan Mobil Cinta</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            color: #24382d;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 20px;
        }

        .print-actions button {
            padding: 9px 15px;
            border: 0;
            border-radius: 6px;
            background: #08A653;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        .report-header {
            padding-bottom: 15px;
            margin-bottom: 18px;
            border-bottom: 3px solid #08A653;
        }

        .report-header h1 {
            margin: 0 0 5px;
            color: #078B48;
            font-size: 21px;
        }

        .report-header p {
            margin: 3px 0;
            color: #62756A;
        }

        .report-period {
            margin-top: 12px;
            font-weight: bold;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            padding: 13px;
            border: 1px solid #D7EDE0;
            border-top: 3px solid #16B96A;
            border-radius: 8px;
            background: #F3FAF5;
        }

        .summary-card strong {
            display: block;
            color: #078B48;
            font-size: 19px;
        }

        .summary-card span {
            color: #62756A;
        }

        h2 {
            margin: 20px 0 10px;
            color: #18352A;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 7px;
            border: 1px solid #DCE8E0;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #EAF8F0;
            color: #18352A;
            font-weight: bold;
        }

        .status-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .report-footer {
            margin-top: 22px;
            padding-top: 10px;
            border-top: 1px solid #D7EDE0;
            color: #718078;
            font-size: 10px;
        }

        .no-data {
            padding: 15px;
            text-align: center;
            color: #718078;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="print-actions">
        <button type="button" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    {{-- HEADER --}}

    <div class="report-header">

        <h1>LAPORAN PERJALANAN MOBIL CINTA</h1>

        <p>Rumah Sakit Daerah Kalisat</p>

        <p>Layanan Antar Pulang Pasien</p>

        <div class="report-period">
            Periode:
            {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
        </div>

    </div>

    {{-- RINGKASAN --}}

    <div class="summary">

        <div class="summary-card">
            <strong>
                {{ number_format($totalJourneys, 0, ',', '.') }}
            </strong>
            <span>Jumlah Perjalanan</span>
        </div>

        <div class="summary-card">
            <strong>
                {{ number_format($totalKilometers, 2, ',', '.') }} KM
            </strong>
            <span>Total Kilometer</span>
        </div>

        <div class="summary-card">
            <strong>
                {{ number_format($inProgress, 0, ',', '.') }}
            </strong>
            <span>Sedang Berjalan</span>
        </div>

    </div>

    {{-- REKAP STATUS --}}

    <h2>Komposisi Status Perjalanan</h2>

    <table class="status-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Jumlah Perjalanan</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($statusData as $item)
                <tr>
                    <td>{{ $item['label'] }}</td>
                    <td>{{ number_format($item['total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- REKAP TANGGAL --}}

    <h2>Jumlah Perjalanan per Tanggal</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Perjalanan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($dailyData as $item)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $item['total'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="no-data">
                        Belum ada data perjalanan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- DETAIL PERJALANAN --}}

    <h2>Detail Perjalanan</h2>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nomor Perjalanan</th>
                <th>Tanggal</th>
                <th>Nama Pasien</th>
                <th>Tujuan</th>
                <th>Sopir</th>
                <th>Nomor Polisi</th>
                <th>Status</th>
                <th>Jarak (KM)</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($journeys as $journey)

                @php
                    $tanggalPengajuan = $journey->diajukan_pada
                        ?? $journey->created_at;

                    $jarak = (
                        $journey->kilometer_berangkat !== null &&
                        $journey->kilometer_sampai !== null &&
                        (float) $journey->kilometer_sampai >=
                            (float) $journey->kilometer_berangkat
                    )
                        ? (float) $journey->kilometer_sampai -
                            (float) $journey->kilometer_berangkat
                        : null;
                @endphp

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $journey->nomor_perjalanan }}</td>

                    <td>
                        {{ $tanggalPengajuan
                            ? \Carbon\Carbon::parse($tanggalPengajuan)->format('d/m/Y H:i')
                            : '-' }}
                    </td>

                    <td>{{ $journey->nama_pasien }}</td>

                    <td>{{ $journey->tujuan }}</td>

                    <td>{{ $journey->driver?->nama ?? '-' }}</td>

                    <td>{{ $journey->vehicle?->nomor_polisi ?? '-' }}</td>

                    <td>
                        {{ $statusLabels[$journey->status]
                            ?? ucfirst($journey->status) }}
                    </td>

                    <td>
                        {{ $jarak !== null
                            ? number_format($jarak, 2, ',', '.')
                            : '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9" class="no-data">
                        Tidak ada perjalanan pada periode ini.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    <div class="report-footer">
        Dicetak dari Sistem Mobil Cinta RSD Kalisat.
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>
</html>