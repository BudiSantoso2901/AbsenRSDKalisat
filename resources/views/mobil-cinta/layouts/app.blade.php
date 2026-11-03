<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mobil Cinta') · RSD Kalisat</title>

    <link rel="icon" href="{{ asset('assets/img/images-removebg-preview.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">

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

            /* Alias kompatibilitas untuk CSS layout yang sudah ada */
            --background: #F7FBF8;
            --green-dark: #078B48;
            --green: #08A653;
            --green-main: #16B96A;
            --green-light: #EAF8F0;
            --green-border: #CDE9D8;
            --shadow: 0 8px 28px rgba(8, 100, 55, 0.07);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            background: var(--background);
            color: var(--ink);
            font-family: 'Public Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        /* =====================================
           HEADER
        ===================================== */

        .main-header {
            position: relative;
            background: var(--white);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 3px 15px rgba(0, 0, 0, .025);
        }

        .main-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg,
                    var(--green-dark),
                    var(--green),
                    var(--green-main));
        }

        .header-inner {
            max-width: 1180px;
            min-height: 76px;
            margin: 0 auto;
            padding: 12px 24px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: var(--ink);
        }

        .brand img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .brand-name span {
            color: var(--green-dark);
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .65px;
        }

        .header-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            display: block;
            color: var(--ink);
            font-size: 13px;
            font-weight: 800;
        }

        .user-role {
            display: inline-block;
            color: var(--green);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* =====================================
           NAVIGATION DESKTOP
        ===================================== */

        .main-nav {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px 13px;

            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            padding: 9px 14px;
            border-radius: 9px;

            color: #606b64;
            font-size: 12px;
            font-weight: 700;

            transition: .2s ease;
        }

        .nav-link:hover {
            background: var(--green-light);
            color: var(--green-dark);
        }

        .nav-link.active {
            background: var(--green-light);
            color: var(--green-dark);
            box-shadow: inset 0 -2px 0 var(--green);
        }

        .nav-link i {
            font-size: 17px;
        }

        /* =====================================
           MAIN CONTENT
        ===================================== */

        main {
            width: 100%;
            max-width: 1180px;
            margin: 34px auto;
            padding: 0 24px 45px;
        }

        .card {
            position: relative;
            padding: 28px;

            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 18px;

            box-shadow: var(--shadow);
        }

        .card-accent {
            overflow: hidden;
        }

        .card-accent::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;

            background: linear-gradient(90deg,
                    var(--green-dark),
                    var(--green),
                    var(--green-main));
        }

        h1,
        h2,
        h3,
        h4 {
            color: var(--ink);
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(22px, 3vw, 29px);
            font-weight: 800;
            line-height: 1.3;
        }

        p {
            color: var(--muted);
        }

        .page-subtitle {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 23px;
        }

        .section-heading {
            margin: 25px 0 15px;
            font-size: 17px;
            font-weight: 800;
        }

        /* =====================================
           BUTTONS
        ===================================== */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;

            padding: 10px 16px;
            border: 0;
            border-radius: 9px;

            font-size: 13px;
            font-weight: 700;
            cursor: pointer;

            transition: .2s ease;
        }

        .btn-primary {
            color: var(--white);
            background: linear-gradient(120deg,
                    var(--green-dark),
                    var(--green-main));

            box-shadow: 0 5px 13px rgba(40, 167, 69, .17);
        }

        .btn-primary:hover {
            color: var(--white);
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            color: var(--green-dark);
            background: var(--white);
            border: 1px solid var(--green-border);
        }

        .btn-outline:hover {
            background: var(--green-light);
            border-color: var(--green);
        }

        .btn-danger {
            color: var(--white);
            background: #dc3545;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            padding: 9px 14px;
            border: 1px solid var(--green-border);
            border-radius: 9px;

            background: var(--white);
            color: var(--green-dark);

            font-size: 12px;
            font-weight: 700;
            cursor: pointer;

            transition: .2s;
        }

        .logout-btn:hover {
            background: var(--green-light);
        }

        /* =====================================
           FORM
        ===================================== */

        .form-group {
            margin-bottom: 19px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;

            color: #4d5850;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control,
        input,
        select,
        textarea {
            width: 100%;
            padding: 11px 13px;

            border: 1px solid #d8e0da;
            border-radius: 9px;

            background: var(--white);
            color: var(--ink);
            font-size: 13px;

            transition: .2s;
        }

        .form-control:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, .11);
        }

        textarea {
            resize: vertical;
        }

        /* =====================================
           ALERT
        ===================================== */

        .alert {
            margin-bottom: 19px;
            padding: 13px 16px;

            border: 1px solid #d9e8dc;
            border-radius: 10px;

            background: #f3f9f4;
            color: #356b40;

            font-size: 13px;
        }

        .alert.success {
            border-color: #c8e5cd;
            background: #edf8ef;
            color: #176b2c;
        }

        .role {
            display: inline-flex;
            align-items: center;
            gap: 5px;

            padding: 6px 12px;
            border-radius: 999px;

            background: var(--green-light);
            color: var(--green-dark);

            font-size: 11px;
            font-weight: 800;
        }

        /* =====================================
           TABLE
        ===================================== */

        .table-wrap {
            width: 100%;
            margin-top: 20px;
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            padding: 13px 12px;
            border-bottom: 1px solid #edf0ed;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f5f8f6;
            color: #657168;

            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        td {
            color: #424d45;
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f7fbf7;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a {
            color: var(--green-dark);
            font-weight: 700;
        }

        /* =====================================
           STATUS BADGE
        ===================================== */

        .status {
            display: inline-flex;
            align-items: center;
            gap: 5px;

            padding: 5px 10px;
            border-radius: 999px;

            font-size: 11px;
            font-weight: 800;
        }

        .status-active {
            background: #eaf6ec;
            color: #14752a;
        }

        .status-inactive {
            background: #f0f1f2;
            color: #687078;
        }

        /* =====================================
           RESPONSIVE UMUM
        ===================================== */

        @media (max-width: 700px) {
            .header-inner {
                padding: 15px 16px;
                flex-wrap: wrap;
            }

            .header-right {
                gap: 10px;
            }

            .main-nav {
                padding: 0 16px 12px;
                gap: 4px;
            }

            .nav-link {
                padding: 8px 10px;
                font-size: 11px;
            }

            main {
                margin: 23px auto;
                padding: 0 15px 30px;
            }

            .card {
                padding: 22px 18px;
                border-radius: 15px;
            }

            .user-info {
                display: none;
            }
        }

        /* FIX TOMBOL UTAMA MOBIL CINTA */

        .btn-primary,
        a.btn-primary,
        button.btn-primary,
        input.btn-primary {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;

            background: linear-gradient(110deg,
                    #16B96A 0%,
                    #08A653 100%) !important;

            color: #FFFFFF !important;
            border: 1px solid #08A653 !important;
            border-radius: 10px !important;

            font-weight: 600 !important;
            opacity: 1 !important;
            text-decoration: none !important;

            box-shadow: 0 4px 12px rgba(8, 166, 83, 0.20) !important;
            cursor: pointer;
        }

        .btn-primary:hover,
        a.btn-primary:hover,
        button.btn-primary:hover {
            background: #078B48 !important;
            color: #FFFFFF !important;
            border-color: #078B48 !important;
        }

        .btn-primary:disabled,
        button.btn-primary:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }

        /* TOMBOL OUTLINE DAN RESET */

        .btn-outline,
        a.btn-outline,
        button.btn-outline,
        .btn-reset,
        a.btn-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            background: #FFFFFF !important;
            color: #087E48 !important;
            border: 1px solid #CDE9D8 !important;
            border-radius: 10px;

            font-weight: 600;
            text-decoration: none !important;

            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        /* EFEK HOVER RESET */

        .btn-outline:hover,
        a.btn-outline:hover,
        button.btn-outline:hover,
        .btn-reset:hover,
        a.btn-reset:hover {
            background: #EAF8F0 !important;
            color: #078B48 !important;
            border-color: #08A653 !important;
            box-shadow: 0 4px 10px rgba(8, 166, 83, 0.12);
            transform: translateY(-1px);
        }

        /* EFEK SAAT DITEKAN */

        .btn-outline:active,
        .btn-reset:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* FOCUS KEYBOARD */

        .btn-outline:focus-visible,
        .btn-reset:focus-visible {
            outline: 3px solid rgba(8, 166, 83, 0.20);
            outline-offset: 2px;
        }

        /* SWEETALERT2 - TEMA MOBIL CINTA */

        .swal2-popup {
            font-family: 'Public Sans', sans-serif !important;
            border-radius: 16px !important;
        }

        .swal2-title {
            color: #18352A !important;
            font-weight: 800 !important;
        }

        .swal2-html-container,
        .swal2-content {
            color: #62756A !important;
            font-size: 13px !important;
        }

        .swal2-confirm {
            background: #08A653 !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            box-shadow: none !important;
        }

        .swal2-confirm:hover {
            background: #078B48 !important;
        }

        .swal2-cancel {
            background: #EEF3EF !important;
            color: #53665A !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            box-shadow: none !important;
        }

        .swal2-toast {
            border: 1px solid #D7EDE0 !important;
            box-shadow: 0 8px 28px rgba(8, 100, 55, .12) !important;
        }

        /* SWEETALERT2 MOBIL CINTA */

        .swal2-container {
            z-index: 20000 !important;
        }

        .swal2-popup {
            border-radius: 16px !important;
            font-family: 'Public Sans', sans-serif !important;
        }

        .swal2-title {
            color: #18352A !important;
            font-size: 19px !important;
            font-weight: 800 !important;
        }

        .swal2-html-container {
            color: #62756A !important;
            font-size: 13px !important;
        }

        .swal2-confirm {
            background: #08A653 !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
        }

        .swal2-confirm:hover {
            background: #078B48 !important;
        }

        .swal2-cancel {
            border-radius: 8px !important;
            font-weight: 700 !important;
        }

        .swal2-toast {
            border-radius: 12px !important;
        }

        /* =====================================
           MOBILE BOTTOM NAVIGATION - SOPIR
        ===================================== */

        .mobile-driver-nav {
            display: none;
        }

        @media (max-width: 700px) {
            .is-driver-app .main-nav {
                display: none;
            }

            .is-driver-app .header-inner {
                min-height: 64px;
                padding: 9px 14px;
            }

            .is-driver-app .header-right {
                display: none;
            }

            .is-driver-app main {
                margin: 17px auto 0;
                padding: 0 13px calc(100px + env(safe-area-inset-bottom));
            }

            .is-driver-app .mobile-driver-nav {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1100;

                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                align-items: end;

                padding: 9px 5px calc(7px + env(safe-area-inset-bottom));

                background: #FFFFFF;
                border-top: 1px solid #D7EDE0;
                box-shadow: 0 -5px 20px rgba(8, 100, 55, .09);
            }

            .mobile-driver-nav a {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;

                min-width: 0;
                color: #718078;
                font-size: 10px;
                font-weight: 700;
                line-height: 1.2;
                text-decoration: none;
            }

            .mobile-driver-nav a > i {
                font-size: 22px;
                line-height: 1.2;
            }

            .mobile-driver-nav a.active {
                color: #08A653;
            }

            .mobile-driver-nav .nav-center {
                position: relative;
            }

            .mobile-driver-nav .nav-center .nav-center-icon {
                display: flex;
                align-items: center;
                justify-content: center;

                width: 54px;
                height: 54px;
                margin-top: -30px;

                color: #FFFFFF;
                background: #08A653;
                border: 4px solid #FFFFFF;
                border-radius: 50%;
                box-shadow: 0 4px 12px rgba(8, 166, 83, .25);
            }

            .mobile-driver-nav .nav-center .nav-center-icon i {
                font-size: 24px;
            }

            .mobile-driver-nav .nav-center.active {
                color: #08A653;
            }
        }

    </style>

    @stack('styles')
</head>

<body class="{{ auth('mobil_cinta')->user()?->role?->nama_peran === 'sopir' ? 'is-driver-app' : '' }}">

    @php
        $account = auth('mobil_cinta')->user();
        $roleName = $account?->role?->nama_peran;

        $dashboardRoute = match ($roleName) {
            'admin' => 'mobil-cinta.admin.dashboard',
            'koordinator' => 'mobil-cinta.koordinator.dashboard',
            'perawat' => 'mobil-cinta.perawat.dashboard',
            'sopir' => 'mobil-cinta.sopir.dashboard',
            default => null,
        };

        $historyRoute = match ($roleName) {
            'admin' => 'mobil-cinta.admin.riwayat',
            'koordinator' => 'mobil-cinta.koordinator.riwayat',
            'perawat' => 'mobil-cinta.perawat.riwayat',
            'sopir' => 'mobil-cinta.sopir.riwayat',
            default => null,
        };
    @endphp

    <header class="main-header">

        <div class="header-inner">

            <a href="{{ $dashboardRoute ? route($dashboardRoute) : route('mobil-cinta.login') }}"
                class="brand">

                <img src="{{ asset('assets/img/images-removebg-preview.png') }}" alt="Logo RSD Kalisat">

                <div>
                    <div class="brand-name">
                        <span>Mobil Cinta</span> · RSD Kalisat
                    </div>

                    <div class="brand-subtitle">
                        LAYANAN ANTAR PULANG PASIEN
                    </div>
                </div>

            </a>

            @auth('mobil_cinta')
                <div class="header-right">

                    <div class="user-info">
                        <span class="user-name">
                            {{ $account?->nama }}
                        </span>

                        <span class="user-role">
                            {{ ucfirst($roleName ?? '') }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('mobil-cinta.logout') }}">
                        @csrf

                        <button class="logout-btn" type="submit">
                            <i class="bx bx-log-out"></i>
                            Keluar
                        </button>
                    </form>

                </div>
            @endauth

        </div>

        @auth('mobil_cinta')
            <nav class="main-nav">

                @if ($dashboardRoute)
                    <a href="{{ route($dashboardRoute) }}"
                        class="nav-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">

                        <i class="bx bx-grid-alt"></i>
                        Dashboard

                    </a>
                @endif

                @if ($roleName === 'admin')
                    <a href="{{ route('mobil-cinta.admin.akun.index') }}"
                        class="nav-link {{ request()->routeIs('mobil-cinta.admin.akun.*') ? 'active' : '' }}">

                        <i class="bx bx-user-circle"></i>
                        Master Akun

                    </a>

                    <a href="{{ route('mobil-cinta.admin.kendaraan.index') }}"
                        class="nav-link {{ request()->routeIs('mobil-cinta.admin.kendaraan.*') ? 'active' : '' }}">

                        <i class="bx bx-car"></i>
                        Master Kendaraan

                    </a>
                @endif

                @if ($historyRoute)
                    <a href="{{ route($historyRoute) }}"
                        class="nav-link {{ request()->routeIs('mobil-cinta.*.riwayat') ? 'active' : '' }}">

                        <i class="bx bx-list-ul"></i>
                        Riwayat Perjalanan

                    </a>
                @endif

            </nav>
        @endauth

    </header>

    <main>

        @yield('content')

    </main>

    {{-- Bottom Navigation khusus Sopir --}}
    @if ($roleName === 'sopir')
        @php
            $driverTab = request()->routeIs('mobil-cinta.sopir.riwayat')
                ? 'riwayat'
                : request()->query('tab', 'home');
        @endphp

        <nav class="mobile-driver-nav" aria-label="Navigasi Sopir">

            <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'home']) }}"
                class="{{ $driverTab === 'home' ? 'active' : '' }}">
                <i class="bx bx-home-alt"></i>
                <span>Beranda</span>
            </a>

            <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'tugas']) }}"
                class="{{ $driverTab === 'tugas' ? 'active' : '' }}">
                <i class="bx bx-clipboard"></i>
                <span>Tugas</span>
            </a>

            <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'aktif']) }}"
                class="nav-center {{ $driverTab === 'aktif' ? 'active' : '' }}">
                <span class="nav-center-icon">
                    <i class="bx bx-navigation"></i>
                </span>
                <span>Aktif</span>
            </a>

            <a href="{{ route('mobil-cinta.sopir.riwayat') }}"
                class="{{ $driverTab === 'riwayat' ? 'active' : '' }}">
                <i class="bx bx-history"></i>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'profil']) }}"
                class="{{ $driverTab === 'profil' ? 'active' : '' }}">
                <i class="bx bx-user"></i>
                <span>Profil</span>
            </a>

        </nav>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 gagal dimuat.');
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | NOTIFIKASI SUKSES DAN ERROR
            |--------------------------------------------------------------------------
            */

            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3200,
                timerProgressBar: true
            });

            @if (session()->has('status'))
                toast.fire({
                    icon: 'success',
                    title: @json(session('status'))
                });
            @endif

            @if (session()->has('error'))
                toast.fire({
                    icon: 'error',
                    title: @json(session('error'))
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Periksa kembali data',
                    text: @json($errors->first()),
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#08A653'
                });
            @endif


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI SEMUA FORM YANG MENGUBAH DATA
            |--------------------------------------------------------------------------
            */

            document.addEventListener('submit', function(event) {

                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                // Form modal edit mempunyai konfirmasi sendiri.
                // Jangan sampai dikonfirmasi dua kali.
                if (
                    form.dataset.swalConfirm === 'false' ||
                    form.hasAttribute('data-swal-skip')
                ) {
                    return;
                }

                // Form yang sudah dikonfirmasi boleh diteruskan.
                if (form.dataset.swalConfirmed === 'true') {
                    delete form.dataset.swalConfirmed;
                    return;
                }

                const methodInput = form.querySelector('input[name="_method"]');

                const method = (
                    methodInput?.value ||
                    form.getAttribute('method') ||
                    'GET'
                ).toUpperCase();

                // Pencarian dan filter GET tidak memerlukan konfirmasi.
                if (method === 'GET') {
                    return;
                }

                event.preventDefault();

                const submitter = event.submitter;

                let actionText = 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                if (method === 'POST') {
                    actionText = 'Apakah Anda yakin ingin mengirim data ini?';
                }

                if (method === 'PUT' || method === 'PATCH') {
                    actionText = 'Apakah Anda yakin ingin menyimpan perubahan ini?';
                }

                if (method === 'DELETE') {
                    actionText = 'Apakah Anda yakin ingin menghapus data ini?';
                }

                const customText = form.dataset.swalText || actionText;

                Swal.fire({
                    icon: form.dataset.swalIcon || 'question',
                    title: form.dataset.swalTitle || 'Konfirmasi tindakan',
                    text: customText,
                    showCancelButton: true,
                    confirmButtonText: form.dataset.swalConfirmText || 'Ya, lanjutkan',
                    cancelButtonText: form.dataset.swalCancelText || 'Batal',
                    confirmButtonColor: '#08A653',
                    cancelButtonColor: '#7b8580',
                    reverseButtons: true,
                    focusCancel: true,
                    allowOutsideClick: false
                }).then(function(result) {

                    if (!result.isConfirmed) {
                        return;
                    }

                    form.dataset.swalConfirmed = 'true';

                    if (typeof form.requestSubmit === 'function') {

                        if (submitter && submitter.form === form) {
                            form.requestSubmit(submitter);
                        } else {
                            form.requestSubmit();
                        }

                        // Bersihkan flag bila validasi browser menggagalkan submit.
                        if (form.dataset.swalConfirmed === 'true') {
                            delete form.dataset.swalConfirmed;
                        }

                    } else {
                        delete form.dataset.swalConfirmed;
                        HTMLFormElement.prototype.submit.call(form);
                    }

                });

            }, true);

        });
    </script>

</body>

</html>