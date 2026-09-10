@extends('_layouts.layouts')

@section('content')
    <style>
        .upload-card {
            border-radius: 16px;
        }

        .upload-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .upload-filter-row {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .upload-filter {
            width: auto !important;
            border-radius: 9px;
            flex: 0 0 auto;
        }

        .upload-filter-week {
            width: 115px !important;
        }

        .upload-filter-month {
            width: 130px !important;
        }

        .upload-filter-year {
            width: 95px !important;
        }

        .upload-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .upload-summary-item {
            border: 1px solid #edf0f2;
            border-radius: 12px;
            padding: 14px 16px;
            background: #fafbfc;
        }

        .upload-summary-label {
            display: block;
            font-size: 12px;
            color: #89919a;
            margin-bottom: 3px;
        }

        .upload-main-value {
            display: block;
            font-size: 24px;
            color: #343a40;
            line-height: 1.2;
        }

        .upload-month-value {
            display: block;
            margin-top: 5px;
            font-size: 11px;
            color: #89919a;
        }

        .upload-month-value b {
            color: #566a7f;
        }

        .upload-loading {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #89919a;
        }

        .upload-chart-scroll {
            max-height: 600px;
            overflow-y: auto;
            overflow-x: auto;
        }

        #uploadLollipopWrap {
            min-width: 720px;
            height: 420px;
        }

        .upload-toggle {
            border-radius: 8px;
            font-size: 11px;
            padding: 5px 10px;
        }

        @media (max-width: 768px) {
            .upload-summary {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }

            .upload-summary-item {
                padding: 10px 8px;
            }

            .upload-summary-label {
                font-size: 9px;
            }

            .upload-main-value {
                font-size: 18px;
            }

            .upload-month-value {
                font-size: 9px;
            }

            .upload-header {
                flex-direction: column;
            }

            .upload-filter-row {
                width: 100%;
                margin-left: 0;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 2px;
            }

            .upload-filter-week {
                width: 105px !important;
            }

            .upload-filter-month {
                width: 120px !important;
            }

            .upload-filter-year {
                width: 90px !important;
            }

            .upload-filter {
                font-size: 11px;
            }

            .upload-chart-scroll {
                max-height: 500px;
            }

            #uploadLollipopWrap {
                min-width: 680px;
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ================= ROW 1 : WELCOME + STATISTIK ================= --}}
        <div class="row g-4 mb-4">

            {{-- Welcome --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h5 class="card-title text-primary">
                                Selamat Datang, {{ auth()->user()->name ?? 'Admin' }} 👋
                            </h5>
                            <p class="mb-3">
                                Dashboard monitoring absensi pegawai.
                            </p>
                        </div>

                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="120"
                            class="d-none d-md-block" alt="welcome">
                    </div>
                </div>
            </div>

            {{-- Total Pegawai --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mb-2">
                            <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" class="rounded">
                        </div>
                        <span class="fw-semibold">Total Pegawai</span>
                        <h3 class="mt-2 mb-0 text-primary">{{ $totalPegawai }}</h3>
                        <small class="text-muted">Keseluruhan</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= ROW 2 : STAT ABSENSI ================= --}}
        <div class="row g-4 mb-4">

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6>Hadir</h6>
                        <h3 class="text-success">{{ $hadir }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6>Izin</h6>
                        <h3 class="text-warning">{{ $izin }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6>Sakit</h6>
                        <h3 class="text-danger">{{ $sakit }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6>Belum Absen</h6>
                        <h3 class="text-secondary">{{ $belumAbsen }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= ROW 3 : CHART ================= --}}
        <div class="row g-4">

            {{-- Pie Chart Absensi --}}
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Status Absensi</h5>
                        <canvas id="chartAbsensi"></canvas>
                    </div>
                </div>
            </div>

            {{-- Bar Chart Jabatan --}}
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pegawai per Jabatan</h5>
                        <canvas id="chartJabatan"></canvas>
                    </div>
                </div>
            </div>

            {{-- Bar Chart Lokasi --}}
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pegawai per Lokasi</h5>
                        <canvas id="chartLokasi"></canvas>
                    </div>
                </div>
            </div>

        </div>

        @php
            $bulanUpload = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $nowUpload = now('Asia/Jakarta');

            $mingguUpload = match (true) {
                $nowUpload->day <= 7 => 1,
                $nowUpload->day <= 14 => 2,
                $nowUpload->day <= 21 => 3,
                default => 4,
            };
        @endphp


        {{-- ================= AKTIVITAS UPLOAD KONTEN ================= --}}
        <div class="card border-0 shadow-sm mt-4 upload-card">

            <div class="card-body">

                {{-- HEADER + FILTER --}}
                <div class="upload-header mb-3">

                    <div>
                        <h5 class="mb-1 fw-bold">
                            Aktivitas Upload Konten Mingguan
                        </h5>

                        <small class="text-muted">
                            Distribusi jumlah upload konten setiap ruangan
                        </small>
                    </div>


                    <div class="upload-filter-row">

                        {{-- MINGGU --}}
                        <select id="uploadMinggu" class="form-select form-select-sm upload-filter upload-filter-week">

                            <option value="1" {{ $mingguUpload == 1 ? 'selected' : '' }}>
                                Minggu 1
                            </option>

                            <option value="2" {{ $mingguUpload == 2 ? 'selected' : '' }}>
                                Minggu 2
                            </option>

                            <option value="3" {{ $mingguUpload == 3 ? 'selected' : '' }}>
                                Minggu 3
                            </option>

                            <option value="4" {{ $mingguUpload == 4 ? 'selected' : '' }}>
                                Minggu 4
                            </option>

                        </select>


                        {{-- BULAN --}}
                        <select id="uploadBulan" class="form-select form-select-sm upload-filter upload-filter-month">

                            @foreach ($bulanUpload as $nomor => $nama)
                                <option value="{{ $nomor }}" {{ $nomor == $nowUpload->month ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach

                        </select>


                        {{-- TAHUN --}}
                        <select id="uploadTahun" class="form-select form-select-sm upload-filter upload-filter-year">

                            @for ($tahun = $nowUpload->year; $tahun >= $nowUpload->year - 2; $tahun--)
                                <option value="{{ $tahun }}">
                                    {{ $tahun }}
                                </option>
                            @endfor

                        </select>

                    </div>

                </div>


                {{-- SUMMARY --}}
                <div class="upload-summary mb-3">

                    <div class="upload-summary-item">

                        <span class="upload-summary-label">
                            Total Upload
                        </span>

                        <strong id="uploadTotalMinggu" class="upload-main-value">
                            -
                        </strong>

                        <span class="upload-month-value">
                            Bulan terpilih:
                            <b id="uploadTotalBulan">-</b>
                        </span>

                    </div>


                    <div class="upload-summary-item">

                        <span class="upload-summary-label">
                            Ruangan Aktif
                        </span>

                        <strong id="uploadAktifMinggu" class="upload-main-value">
                            -
                        </strong>

                        <span class="upload-month-value">
                            Bulan terpilih:
                            <b id="uploadAktifBulan">-</b>
                        </span>

                    </div>


                    <div class="upload-summary-item">

                        <span class="upload-summary-label">
                            Belum Upload
                        </span>

                        <strong id="uploadBelumMinggu" class="upload-main-value">
                            -
                        </strong>

                        <span class="upload-month-value">
                            Bulan terpilih:
                            <b id="uploadBelumBulan">-</b>
                        </span>

                    </div>

                </div>


                {{-- PERIODE + BUTTON --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">

                    <small id="uploadPeriode" class="fw-semibold text-muted">
                        Memuat periode...
                    </small>

                    <button type="button" id="btnToggleUpload" class="btn btn-sm btn-outline-secondary upload-toggle">

                        Lihat Semua

                    </button>

                </div>


                {{-- LOADING --}}
                <div id="uploadLoading" class="upload-loading">

                    <div class="spinner-border spinner-border-sm"></div>

                    <span>
                        Memuat aktivitas upload...
                    </span>

                </div>


                {{-- CHART --}}
                <div id="uploadChartScroll" class="upload-chart-scroll d-none">

                    <div id="uploadLollipopWrap">

                        <canvas id="chartUploadKonten"></canvas>

                    </div>

                </div>

            </div>

        </div>

        {{-- card pegawai absensi hari ini  --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Absensi Pegawai
                    ({{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }})
                </h5>

                <div class="d-flex gap-2">

                    {{-- INPUT PILIH TANGGAL --}}
                    <form method="GET">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control"
                            onchange="this.form.submit()">
                    </form>

                    {{-- FILTER STATUS --}}
                    <select id="filterAbsensi" class="form-select">
                        <option value="all">Semua</option>
                        <option value="hadir">Sudah Absen</option>
                        <option value="belum">Belum Absen</option>
                    </select>

                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle" id="datatableAbsensi">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Lokasi</th>
                            <th>Status Hari Ini</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
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

                        @foreach ($pegawaiHariIni as $row)
                            @php
                                $badge = null;
                                $status = $row->status_absensi ?? 'belum_hadir';

                                if ($status === 'hadir' && $row->waktu_masuk) {
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

                                        // Pastikan masuk shift ini
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

                            <tr data-status="{{ $status }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->nip }}</td>
                                <td>{{ $row->jabatan->nama_jabatan ?? '-' }}</td>
                                <td>{{ $row->lokasi->nama_lokasi ?? '-' }}</td>

                                {{-- STATUS --}}
                                <td class="text-center">
                                    @if ($status === 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif ($status === 'izin')
                                        <span class="badge bg-warning text-dark">Izin</span>
                                    @elseif ($status === 'sakit')
                                        <span class="badge bg-info">Sakit</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- WAKTU MASUK + TL --}}
                                <td class="text-center">
                                    @if ($row->waktu_masuk)
                                        {{ Carbon::parse($row->waktu_masuk)->format('H:i') }}

                                        @if ($badge)
                                            @php
                                                $warnaTL = match ($badge) {
                                                    'TL1' => 'bg-warning',
                                                    'TL2' => 'bg-danger',
                                                    'TL3' => 'bg-danger',
                                                    'TL4' => 'bg-dark',
                                                    default => 'bg-warning',
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
                                <td class="text-center">
                                    {{ $row->waktu_pulang ? Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // =====================================================
        // LOLLIPOP AKTIVITAS UPLOAD KONTEN
        // =====================================================

        const uploadBulan = document.getElementById('uploadBulan');
        const uploadTahun = document.getElementById('uploadTahun');
        const uploadMinggu = document.getElementById('uploadMinggu');

        const uploadLoading = document.getElementById('uploadLoading');
        const uploadChartScroll = document.getElementById('uploadChartScroll');
        const uploadChartWrap = document.getElementById('uploadLollipopWrap');

        const btnToggleUpload = document.getElementById('btnToggleUpload');

        let uploadChart = null;
        let uploadResult = null;
        let tampilSemuaUpload = false;


        /*
         * Plugin custom:
         * garis horizontal + titik + angka
         */
        const lollipopPlugin = {

            id: 'lollipopPlugin',

            beforeDatasetsDraw(chart) {

                const {
                    ctx,
                    scales: {
                        x
                    }
                } = chart;

                const meta = chart.getDatasetMeta(0);

                ctx.save();

                ctx.strokeStyle = 'rgba(32, 201, 151, 0.35)';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';

                meta.data.forEach((point, index) => {

                    const value =
                        Number(chart.data.datasets[0].data[index].x || 0);

                    const startX =
                        x.getPixelForValue(0);

                    const endX =
                        x.getPixelForValue(value);

                    ctx.beginPath();
                    ctx.moveTo(startX, point.y);
                    ctx.lineTo(endX, point.y);
                    ctx.stroke();

                });

                ctx.restore();
            },


            afterDatasetsDraw(chart) {

                const {
                    ctx
                } = chart;

                const meta =
                    chart.getDatasetMeta(0);

                ctx.save();

                ctx.fillStyle = '#566a7f';
                ctx.font = '600 11px sans-serif';
                ctx.textBaseline = 'middle';

                meta.data.forEach((point, index) => {

                    const value =
                        chart.data.datasets[0]
                        .data[index]
                        .x;

                    ctx.fillText(
                        value,
                        point.x + 9,
                        point.y
                    );

                });

                ctx.restore();
            }
        };


        async function loadUploadChart() {

            uploadLoading.classList.remove('d-none');
            uploadChartScroll.classList.add('d-none');

            const params = new URLSearchParams({
                bulan: uploadBulan.value,
                tahun: uploadTahun.value,
                minggu: uploadMinggu.value
            });

            try {

                const response = await fetch(
                    `{{ route('admin.konten.distribusi') }}?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Gagal mengambil data upload');
                }

                const result = await response.json();

                uploadResult = result;

                updateUploadSummary(result);
                updateWeekLabels(result);

                uploadLoading.classList.add('d-none');
                uploadChartScroll.classList.remove('d-none');

                renderUploadChart(result);

            } catch (error) {

                console.error(
                    'UPLOAD CHART ERROR:',
                    error
                );

                uploadLoading.innerHTML = `
            <span class="text-danger">
                Gagal memuat aktivitas upload.
            </span>
        `;
            }
        }


        function updateUploadSummary(result) {

            document.getElementById('uploadTotalMinggu')
                .textContent =
                result.mingguan.total_upload ?? 0;

            document.getElementById('uploadAktifMinggu')
                .textContent =
                result.mingguan.ruangan_aktif ?? 0;

            document.getElementById('uploadBelumMinggu')
                .textContent =
                result.mingguan.belum_upload ?? 0;


            document.getElementById('uploadTotalBulan')
                .textContent =
                result.bulanan.total_upload ?? 0;

            document.getElementById('uploadAktifBulan')
                .textContent =
                result.bulanan.ruangan_aktif ?? 0;

            document.getElementById('uploadBelumBulan')
                .textContent =
                result.bulanan.belum_upload ?? 0;


            document.getElementById('uploadPeriode')
                .textContent =
                `${result.minggu_terpilih.label} • ` +
                `${result.minggu_terpilih.range} ${result.tahun}`;
        }


        /*
         * Update tulisan dropdown:
         * Minggu 2 → Minggu 2 (8-14 Sep)
         */
        function updateWeekLabels(result) {

            result.weeks.forEach(week => {

                const option =
                    uploadMinggu.querySelector(
                        `option[value="${week.key}"]`
                    );

                if (option) {

                    option.textContent =
                        `${week.label}`;
                }
            });
        }


        function renderUploadChart(result) {

            if (uploadChart) {

                uploadChart.destroy();
                uploadChart = null;
            }


            const dataTampil = tampilSemuaUpload ?
                result.data :
                result.data.slice(0, 15);


            /*
             * Tinggi mengikuti jumlah ruangan.
             */
            uploadChartWrap.style.height =
                Math.max(
                    420,
                    dataTampil.length * 34 + 60
                ) + 'px';


            const chartData =
                dataTampil.map(item => ({

                    x: Number(item.total_minggu ?? 0),

                    y: item.ruangan,

                    totalBulan: Number(item.total_bulan ?? 0)
                }));


            uploadChart = new Chart(
                document.getElementById('chartUploadKonten'), {

                    type: 'scatter',

                    data: {

                        datasets: [{
                            data: chartData,

                            pointRadius: 6,
                            pointHoverRadius: 8,

                            pointBackgroundColor: '#20c997',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }]
                    },


                    plugins: [
                        lollipopPlugin
                    ],


                    options: {

                        responsive: true,
                        maintainAspectRatio: false,

                        animation: false,

                        layout: {
                            padding: {
                                right: 35
                            }
                        },

                        scales: {

                            x: {

                                beginAtZero: true,

                                ticks: {
                                    precision: 0
                                },

                                title: {
                                    display: true,
                                    text: 'Jumlah Upload'
                                },

                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },


                            y: {

                                type: 'category',

                                labels: dataTampil.map(
                                    item => item.ruangan
                                ),

                                offset: true,

                                ticks: {

                                    autoSkip: false,

                                    font: {
                                        size: 11
                                    }
                                },

                                grid: {
                                    display: false
                                }
                            }
                        },


                        plugins: {

                            legend: {
                                display: false
                            },


                            tooltip: {

                                callbacks: {

                                    title(items) {

                                        return items[0]
                                            .raw
                                            .y;
                                    },


                                    label(context) {

                                        return `${context.raw.x} upload ` +
                                            `• ${result.minggu_terpilih.range} ${result.tahun}`;
                                    },


                                    afterLabel(context) {

                                        return `Bulan terpilih: ` +
                                            `${context.raw.totalBulan} upload`;
                                    }
                                }
                            }
                        }
                    }
                }
            );
        }


        /*
         * TOP 15 / SEMUA
         */
        btnToggleUpload.addEventListener(
            'click',
            function() {

                tampilSemuaUpload = !tampilSemuaUpload;

                this.textContent =
                    tampilSemuaUpload ?
                    'Tampilkan Top 15' :
                    'Lihat Semua';

                if (uploadResult) {
                    renderUploadChart(uploadResult);
                }
            }
        );


        /*
         * Filter
         */
        function reloadUploadChart() {

            tampilSemuaUpload = false;

            btnToggleUpload.textContent =
                'Lihat Semua';

            loadUploadChart();
        }


        uploadBulan.addEventListener(
            'change',
            reloadUploadChart
        );

        uploadTahun.addEventListener(
            'change',
            reloadUploadChart
        );

        uploadMinggu.addEventListener(
            'change',
            reloadUploadChart
        );


        /*
         * Initial load
         */
        loadUploadChart();

        // PIE ABSENSI
        new Chart(document.getElementById('chartAbsensi'), {
            type: 'pie',
            data: {
                labels: @json($chartAbsensiLabel),
                datasets: [{
                    data: @json($chartAbsensiData),
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d']
                }]
            }
        });

        // BAR JABATAN
        new Chart(document.getElementById('chartJabatan'), {
            type: 'bar',
            data: {
                labels: @json($chartJabatanLabel),
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: @json($chartJabatanData),
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // BAR LOKASI
        new Chart(document.getElementById('chartLokasi'), {
            type: 'bar',
            data: {
                labels: @json($chartLokasiLabel),
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: @json($chartLokasiData),
                    backgroundColor: '#20c997'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        $(document).ready(function() {

            let table = $('#datatableAbsensi').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [1, 'asc']
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        next: "Next",
                        previous: "Prev"
                    },
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });
        document.getElementById('filterAbsensi').addEventListener('change', function() {
            let value = this.value;
            let rows = document.querySelectorAll('#datatableAbsensi tbody tr');

            rows.forEach(row => {
                let status = row.getAttribute('data-status');

                if (value === 'all') {
                    row.style.display = '';
                } else if (value === 'hadir') {
                    row.style.display = (status === 'hadir') ? '' : 'none';
                } else if (value === 'belum') {
                    row.style.display = (status === 'belum_hadir' || !status) ? '' : 'none';
                }
            });
        });
    </script>
@endpush
