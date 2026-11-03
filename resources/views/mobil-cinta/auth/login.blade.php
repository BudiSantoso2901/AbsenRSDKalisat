<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#28a745">

    <title>Login Mobil Cinta | RSD Kalisat</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/images-removebg-preview.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ink: #18352A;
            --muted: #62756A;
            --line: #D7EDE0;

            --brand: #08A653;
            --brand-dark: #078B48;
            --brand-light: #16B96A;
            --soft: #EAF8F0;

            --white: #FFFFFF;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Public Sans', sans-serif;
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, .28), transparent 30%),
                radial-gradient(circle at 85% 85%, rgba(255, 255, 255, .18), transparent 30%),
                linear-gradient(145deg, #086d12 0%, #2b9f53 30%, #d96b96 68%, #ef8eb1 100%);
        }

        body::before {
            content: "";
            position: fixed;
            top: -120px;
            right: -110px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(240, 98, 146, .18);
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            left: -90px;
            bottom: -80px;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            pointer-events: none;
        }

        .authentication-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 18px;
        }

        .authentication-inner {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .18);
            animation: fadeUp .45s ease;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--rs-green) 0%, #4eb66e 20%, var(--rs-pink) 55%, var(--rs-pink-strong) 100%);
        }

        .login-card .card-body {
            padding: 31px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 18px;
        }

        .login-logo img {
            width: 190px;
            max-width: 90%;
            height: auto;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, .09));
        }

        .login-title {
            margin: 0 0 7px;
            text-align: center;
            color: #000;
            font-size: 24px;
            font-weight: 700;
        }

        .login-subtitle {
            max-width: 300px;
            margin: 0 auto 13px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.55;
        }

        .service-label {
            display: flex;
            justify-content: center;
            margin: 0 auto 22px;
        }

        .service-label span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 15px;
            border-radius: 999px;
            border: 1px solid #f6c6d8;
            background: #fff5f9;
            color: #b82f68;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #4d555d;
            font-size: 14px;
            font-weight: 600;
        }

        .login-input-wrapper {
            position: relative;
        }

        .login-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9299a1;
            font-size: 21px;
            pointer-events: none;
            z-index: 2;
        }

        .login-input {
            width: 100%;
            height: 50px;
            padding: 0 14px 0 45px;
            border: 1.5px solid var(--border) !important;
            border-radius: 12px;
            color: #333;
            background: #fff !important;
            font-size: 14px;
            box-shadow: none !important;
            outline: none !important;
            appearance: none;
            box-sizing: border-box;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .login-input::placeholder {
            color: #a8afb6;
        }

        .login-input:hover {
            border-color: #c6ccd3 !important;
        }

        .login-input:focus {
            border: 1.5px solid var(--rs-pink) !important;
            box-shadow: 0 0 0 3px rgba(240, 98, 146, .12) !important;
        }

        .password-field .login-input {
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #858d95;
            font-size: 21px;
            cursor: pointer;
            transition: .2s;
        }

        .password-toggle:hover {
            background: rgba(240, 98, 146, .10);
            color: var(--rs-pink);
        }

        .login-input.is-invalid {
            border: 1.5px solid #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            margin-top: 6px;
            color: #dc3545;
            font-size: 12px;
        }

        .login-alert {
            padding: 12px;
            background: #fff0f3;
            border: 1px solid #ffd0dc;
            color: #a61f4d;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .btn-login {
            width: 100%;
            min-height: 50px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #0a7615 0%, #2faa58 35%, #e56e9b 72%, #f06292 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(240, 98, 146, .20);
            transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
        }

        .btn-login:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(240, 98, 146, .25);
        }

        .btn-login:disabled {
            opacity: .75;
            cursor: not-allowed;
            transform: none;
        }

        .back-area {
            margin-top: 17px;
            text-align: center;
            color: #e8088f;
            font-size: 14px;
            line-height: 1.5;
        }

        .back-link {
            color: var(--rs-pink-strong);
            font-weight: 700;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--rs-green-main);
            text-decoration: none;
        }

        .app-version {
            margin-top: 18px;
            color: #a0a6ad;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .4px;
            text-align: center;
        }

        @media(max-width:575px) {
            .authentication-wrapper {
                padding: 18px 15px;
            }

            .login-card {
                border-radius: 18px;
            }

            .login-card .card-body {
                padding: 26px 21px;
            }

            .login-logo {
                margin-bottom: 15px;
            }

            .login-logo img {
                width: 175px;
            }

            .login-title {
                font-size: 22px;
            }

            .login-subtitle {
                margin-bottom: 10px;
                font-size: 13px;
            }

            .login-input {
                height: 49px;
                font-size: 14px;
            }

            .btn-login {
                min-height: 49px;
            }
        }

        @media(max-width:360px) {
            .login-card .card-body {
                padding: 23px 17px;
            }

            .login-title {
                font-size: 21px;
            }

            .login-logo img {
                width: 160px;
            }
        }
    </style>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic">
            <div class="authentication-inner">
                <div class="card login-card">
                    <div class="card-body">
                        <div class="login-logo">
                            <img src="{{ asset('assets/img/images-removebg-preview.png') }}" alt="Logo RSD Kalisat">
                        </div>

                        <h1 class="login-title">Selamat Datang 👋</h1>
                        <p class="login-subtitle">Masuk menggunakan akun Mobil Cinta untuk mengakses layanan perjalanan
                            pasien RSD Kalisat.</p>

                        @if ($errors->any())
                            <div class="login-alert" role="alert">
                                <i class="bx bx-error-circle"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form id="mobilCintaLoginForm" action="{{ route('mobil-cinta.login.submit') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="username" class="form-label">USERNAME</label>
                                <div class="login-input-wrapper">
                                    <i class="bx bx-user login-input-icon"></i>
                                    <input type="text" name="username" id="username"
                                        class="login-input @error('username') is-invalid @enderror"
                                        value="{{ old('username') }}" placeholder="Masukkan username"
                                        autocomplete="username" maxlength="255" required autofocus>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback"><i class="bx bx-error-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">PASSWORD</label>
                                <div class="login-input-wrapper password-field">
                                    <i class="bx bx-lock-alt login-input-icon"></i>
                                    <input type="password" name="password" id="password"
                                        class="login-input @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password" autocomplete="current-password" maxlength="255"
                                        required>
                                    <button type="button" class="password-toggle" id="passwordToggle"
                                        aria-label="Tampilkan password">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback"><i class="bx bx-error-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-login" id="loginButton">
                                <span id="loginButtonNormal"><i class="bx bx-log-in-circle"></i> Masuk</span>
                                <span id="loginButtonLoading" hidden><i class="bx bx-loader-alt bx-spin"></i>
                                    Memproses...</span>
                            </button>
                        </form>

                        <div class="back-area">
                            <a href="{{ route('login') }}" class="back-link">
                                <i class="bx bx-arrow-back"></i> Kembali ke Login Absensi
                            </a>
                        </div>

                        <div class="app-version">RSD KALISAT JEMBER · MOBIL CINTA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const showing = passwordInput.type === 'password';
                    passwordInput.type = showing ? 'text' : 'password';
                    icon.classList.toggle('bx-hide', !showing);
                    icon.classList.toggle('bx-show', showing);
                    this.setAttribute('aria-label', showing ? 'Sembunyikan password' :
                        'Tampilkan password');
                });
            }

            const form = document.getElementById('mobilCintaLoginForm');
            const button = document.getElementById('loginButton');
            const normal = document.getElementById('loginButtonNormal');
            const loading = document.getElementById('loginButtonLoading');

            if (form) {
                form.addEventListener('submit', function() {
                    if (!form.checkValidity()) return;
                    button.disabled = true;
                    normal.hidden = true;
                    loading.hidden = false;
                });
            }

            @if (session('swal_error'))
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: @json(session('swal_error')),
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#097612'
                    });
                }
            @endif
        });
    </script>
</body>

</html>
