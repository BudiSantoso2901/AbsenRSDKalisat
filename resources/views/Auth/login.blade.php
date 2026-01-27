<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#28a745">

    <link rel="apple-touch-icon" href="/icon/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Login Presensi</title>

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


    <!-- Helpers -->
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
            background: #000;
        }

        /* VIDEO BACKGROUND */
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

        /* CARD LOGIN */
        .authentication-inner .card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .35);
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

        .app-brand img {
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, .2));
        }
    </style>
</head>

<body>

    <!-- VIDEO BACKGROUND -->
    <div class="video-bg">
        <video autoplay muted playsinline preload="auto" id="bgVideo">
            <source src="{{ asset('assets/video/rsd kalisat.mp4') }}" type="video/mp4">
        </video>
    </div>
    <div class="video-overlay"></div>

    <!-- CONTENT -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <div class="card">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-3">
                            <img src="{{ asset('assets/img/images-removebg-preview.png') }}" width="220">
                        </div>

                        <h4 class="mb-2 text-center">Selamat Datang 👋</h4>
                        <p class="mb-4 text-center text-muted">
                            Silakan login untuk melakukan presensi
                        </p>

                        <form action="{{ route('login.process') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Username / NIP</label>
                                <input type="text" name="login"
                                    class="form-control @error('login') is-invalid @enderror"
                                    placeholder="Masukkan Username atau NIP" value="{{ old('login') }}" autofocus>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="btn btn-primary w-100 mb-3">
                                Masuk
                            </button>
                        </form>

                        <p class="text-center mb-0">
                            Belum punya akun?
                            <a href="{{ route('pegawai.register.form') }}" class="fw-semibold">
                                Daftar
                            </a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('SW registered'))
                .catch(err => console.error('SW failed', err));
        }

        const video = document.getElementById('bgVideo');

        video.addEventListener('ended', function() {
            video.pause();
            video.currentTime = video.duration - 0.1; // tahan di frame akhir
        });
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('swal_error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: '{{ session('swal_error') }}',
                    confirmButtonColor: '#097612'
                });
            @endif

            @if (session('swal_warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: '{{ session('swal_warning') }}',
                    confirmButtonColor: '#f06292'
                });
            @endif

            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('swal_success') }}',
                    timer: 1500,
                    showConfirmButton: false
                });
            @endif

        });
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi Berhasil',
                    text: '{{ session('swal_success') }}',
                    confirmButtonColor: '#097612'
                });
            @endif

        });
    </script>
</body>

</html>
