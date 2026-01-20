@extends('_layouts.layouts')

@section('content')
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

    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
    </script>
@endpush
