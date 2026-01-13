@extends('_layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ================= ROW 1 : WELCOME + HADIR ================= --}}
        <div class="row g-4 mb-4">

            {{-- Welcome --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h5 class="card-title text-primary">
                                Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }} 👋
                            </h5>
                            <p class="mb-3">
                                Semoga harimu menyenangkan.
                                <span class="fw-bold text-success">Jangan lupa absensi hari ini.</span>
                            </p>

                            <a href="{{ route('pegawai.kamera') }}" class="btn btn-outline-success btn-sm">
                                Absen Sekarang
                            </a>
                        </div>

                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="120"
                            class="d-none d-md-block" alt="welcome">
                    </div>
                </div>
            </div>

            {{-- Hadir --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mb-2">
                            <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" class="rounded">
                        </div>
                        <span class="fw-semibold">Hadir</span>
                        <h3 class="mt-2 mb-0 text-success">20</h3>
                        <small class="text-muted">Bulan ini</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= ROW 2 : INFO PEGAWAI + CHART ================= --}}
        <div class="row g-4">

            {{-- Basic Info Pegawai --}}
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Pegawai</h5>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Nama :</strong> {{ auth()->user()->name ?? '-' }}
                            </li>
                            <li class="mb-2">
                                <strong>Jabatan :</strong> {{ auth()->user()->jabatan->nama ?? '-' }}
                            </li>
                            <li class="mb-2">
                                <strong>Lokasi :</strong> {{ auth()->user()->lokasi->nama ?? '-' }}
                            </li>
                            <li class="mb-2">
                                <strong>Jam Kerja :</strong> {{ auth()->user()->jamKerja->nama ?? '-' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Chart Absensi --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Rekap Absensi Bulanan</h5>
                        <canvas id="absensiChart" height="120"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('absensiChart').getContext('2d');
        const absensiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                        label: 'Hadir',
                        data: [5, 4, 6, 5],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Tidak Hadir',
                        data: [2, 3, 1, 2],
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
