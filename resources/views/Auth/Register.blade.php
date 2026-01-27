<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Pegawai</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/images-removebg-preview.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ===== CUSTOM STYLE ===== -->
    <style>
        :root {
            --rs-green: #097612;
            --rs-pink: #f06292;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg,
                    var(--rs-green),
                    var(--rs-pink));
        }

        .video-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            overflow: hidden;
        }

        .video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* GRADIENT OVERLAY */
        .video-overlay {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: linear-gradient(135deg,
                    rgba(9, 118, 18, 0.75),
                    rgba(240, 98, 146, 0.65));
        }

        .authentication-inner .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .35);
            animation: fadeUp .8s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg,
                    var(--rs-green),
                    var(--rs-pink));
            border: none;
        }

        .btn-primary:hover {
            opacity: .9;
        }

        .form-label {
            font-weight: 500;
        }

        .app-brand img {
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, .2));
        }
    </style>
</head>

<body>
    <div class="video-bg">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('assets/video/rsd kalisat.mp4') }}" type="video/mp4">
        </video>
    </div>
    <div class="video-overlay"></div>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <!-- REGISTER CARD -->
                <div class="card">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-3"><a href="{{ route('login') }}"> <img
                                    src="{{ asset('assets/img/images-removebg-preview.png') }}" width="220"></a>
                        </div>

                        <h4 class="mb-1 text-center">Pendaftaran Pegawai</h4>
                        <p class="mb-4 text-center text-muted">
                            Lengkapi data berikut dengan benar
                        </p>

                        <form action="{{ route('pegawai.register') }}" method="POST">
                            @csrf

                            <!-- ROW 1 -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama lengkap"
                                        value="{{ old('name') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" name="nip" class="form-control" placeholder="NIP"
                                        value="{{ old('nip') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control"
                                        value="{{ old('tanggal_lahir') }}" placeholder="Tanggal Lahir" required>
                                </div>

                            </div>

                            <!-- ROW 2 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email aktif"
                                        value="{{ old('email') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <select name="id_jabatan" class="form-select" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatan as $j)
                                            <option value="{{ $j->id }}"
                                                {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>
                                                {{ $j->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- ROW 3 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi Kerja</label>
                                    <select name="id_lokasi" class="form-select" required>
                                        <option value="">Pilih Lokasi</option>
                                        @foreach ($lokasi as $l)
                                            <option value="{{ $l->id }}"
                                                {{ old('id_lokasi') == $l->id ? 'selected' : '' }}>
                                                {{ $l->nama_lokasi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Kerja</label>
                                    <select name="id_jam_kerja" class="form-select" required>
                                        <option value="">Pilih Jam Kerja</option>
                                        @foreach ($jamKerja as $jk)
                                            <option value="{{ $jk->id }}"
                                                {{ old('id_jam_kerja') == $jk->id ? 'selected' : '' }}>
                                                {{ $jk->nama_jam_kerja }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- ROW 4 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="••••••••" required>
                                </div>
                            </div>

                            <!-- INFO -->
                            <div class="alert alert-light d-flex align-items-center gap-2 small mb-3">
                                <i class="bx bx-info-circle"></i>
                                Akun akan aktif setelah disetujui admin
                            </div>

                            <!-- BUTTON -->
                            <button class="btn btn-primary w-100">
                                Daftar Pegawai
                            </button>
                        </form>

                        <p class="text-center mt-3 mb-0">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="fw-semibold">
                                Masuk
                            </a>
                        </p>

                    </div>
                </div>
                <!-- /REGISTER CARD -->

            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Pendaftaran Gagal',
                    html: `
                <ul style="text-align:left; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
                    confirmButtonColor: '#097612'
                });
            @endif

        });
    </script>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
