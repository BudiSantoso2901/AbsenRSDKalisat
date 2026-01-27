@extends('_layouts.layouts')

@section('content')
    <style>
        .profile-card {
            border-radius: 16px;
            overflow: hidden;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .profile-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, .15);
        }

        /* HEADER */
        .profile-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            padding: 30px 20px 50px;
            color: #fff;
            position: relative;
        }

        .avatar-wrapper {
            width: 90px;
            height: 90px;
            margin: 0 auto;
            background: #fff;
            border-radius: 50%;
            padding: 4px;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* INFO ITEM */
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            font-size: 22px;
            color: #28a745;
            background: rgba(40, 167, 69, .1);
            padding: 10px;
            border-radius: 12px;
        }

        .info-item small {
            color: #888;
            font-size: 12px;
        }

        .info-item p {
            margin: 0;
            font-weight: 600;
        }

        .btn-absen {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px;
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
            transition: all 0.25s ease;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ================= ROW 1 : WELCOME + HADIR ================= --}}
        <div class="row g-4 mb-4">

            {{-- Welcome --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">

                        <!-- TEKS -->
                        <div class="text-center text-md-start mb-3 mb-md-0">
                            <h5 class="card-title text-success mb-2">
                                Selamat Datang, {{ $pegawai->name }} 👋
                            </h5>
                            <p class="mb-3">
                                Semoga harimu menyenangkan.
                                <span class="fw-bold text-success">Jangan lupa absensi hari ini.</span>
                            </p>

                            <!-- BUTTON CENTER -->
                            <div class="d-flex justify-content-center justify-content-md-start">
                                <a href="{{ route('pegawai.kamera') }}" class="btn btn-absen px-4">
                                    Absen Sekarang
                                </a>
                            </div>
                        </div>

                        <!-- GAMBAR -->
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                            class="d-none d-md-block" height="120" alt="welcome">
                    </div>
                </div>
            </div>


            {{-- Ringkasan Hadir --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mb-2">
                            <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" class="rounded">
                        </div>
                        <span class="fw-semibold d-block">Hadir</span>
                        <h3 class="text-success my-2">{{ $hadir }}</h3>
                        <small class="text-muted">Bulan ini</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= ROW 2 : INFO PEGAWAI + CHART ================= --}}
        <div class="row g-4">

            {{-- Informasi Pegawai --}}
            <div class="col-lg-4 col-md-12">
                <div class="card profile-card h-100 shadow-sm">
                    <!-- HEADER -->
                    <div class="profile-header text-center">
                        <div class="avatar-wrapper">
                            <img src="{{ asset('assets/img/icon.png') }}" class="profile-avatar" alt="Foto Pegawai">
                        </div>
                        <h5 class="mt-3 mb-0">{{ $pegawai->name }}</h5>
                        <small class="text-shadow">
                            {{ $pegawai->jabatan->nama_jabatan ?? 'Pegawai' }}
                        </small>
                    </div>

                    <!-- BODY -->
                    <div class="card-body">
                        <div class="info-item">
                            <i class="bx bx-envelope"></i>
                            <div>
                                <small>Email</small>
                                <p>{{ $pegawai->email }}</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bx bx-id-card"></i>
                            <div>
                                <small>NIP</small>
                                <p>{{ $pegawai->nip ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bx bx-map"></i>
                            <div>
                                <small>Lokasi</small>
                                <p>{{ $pegawai->lokasi->nama_lokasi ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bx bx-time-five"></i>
                            <div>
                                <small>Jam Kerja</small>
                                <p>{{ $pegawai->jamKerja->nama_jam_kerja ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Absensi --}}
            <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Rekap Absensi Bulanan</h5>
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
        const ctx = document.getElementById('absensiChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                        label: 'Hadir',
                        data: @json($chartHadir),
                        backgroundColor: '#28a745'
                    },
                    {
                        label: 'Izin',
                        data: @json($chartIzin),
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Sakit',
                        data: @json($chartSakit),
                        backgroundColor: '#17a2b8'
                    }

                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
@endpush
