<!DOCTYPE html>

@php
    $isPegawai = auth('pegawai')->check();
@endphp

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">

<head>

    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width,
        initial-scale=1.0,
        user-scalable=no,
        minimum-scale=1.0,
        maximum-scale=1.0" />


    <title>
        PRESENSI RSD KALISAT
    </title>


    {{-- =====================================================
    FAVICON
    ====================================================== --}}

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/images-removebg-preview.png') }}" />


    {{-- =====================================================
    FONTS
    ====================================================== --}}

    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">


    {{-- =====================================================
    ICON
    ====================================================== --}}

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />


    {{-- =====================================================
    CORE CSS
    ====================================================== --}}

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />


    {{-- =====================================================
    CUSTOM CSS
    ====================================================== --}}

    <link rel="stylesheet" href="{{ asset('css/tambah.css') }}?v={{ filemtime(public_path('css/tambah.css')) }}">


    {{-- =====================================================
    VENDOR CSS
    ====================================================== --}}

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />


    {{-- =====================================================
    DATATABLES
    ====================================================== --}}

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">


    {{-- =====================================================
    LEAFLET
    ====================================================== --}}

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


    {{-- =====================================================
    PWA
    ====================================================== --}}

    <link rel="manifest" href="/manifest.json">

    <meta name="theme-color" content="#28a745">

    <link rel="apple-touch-icon" href="/icon/icon-192.png">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="default">


    {{-- =====================================================
    SELECT2
    ====================================================== --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />


    {{-- CSS DARI MASING-MASING HALAMAN --}}
    @stack('styles')


    {{-- =====================================================
    HELPERS
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('assets/js/config.js') }}"></script>


    <style>
        /* =====================================================
           GLOBAL
        ====================================================== */

        html,
        body {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            overscroll-behavior: contain;
        }

        .card {
            border-radius: 16px;
        }

        button {
            border-radius: 12px;
        }

        .swal2-container {
            z-index: 3000 !important;
        }


        /* =====================================================
           GLOBAL TANPA HEADBAR / NAVBAR
        ====================================================== */

        .layout-wrapper {
            min-height: 100vh !important;
        }

        .layout-container {
            min-height: 100vh !important;
        }

        /*
         * Navbar atas sudah dihapus.
         * Layout halaman langsung menggunakan
         * ruang yang sebelumnya dipakai navbar.
         */
        .layout-page {
            min-height: 100vh !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .content-wrapper {
            min-height: 100vh !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        /*
         * Tetap beri sedikit ruang agar konten
         * tidak menempel langsung ke ujung layar.
         */
        .container-p-y {
            padding-top: 16px !important;
        }


        /* =====================================================
           PEGAWAI
           SIDEBAR TIDAK DIGUNAKAN
        ====================================================== */

        body.pegawai-layout .layout-container {
            width: 100% !important;
            max-width: 100% !important;
        }

        body.pegawai-layout .layout-page {
            width: 100% !important;
            max-width: 100% !important;

            padding-left: 0 !important;
            margin-left: 0 !important;
        }


        /*
         * Sneat memberikan ruang sidebar ketika
         * .layout-menu-fixed aktif.
         *
         * Khusus pegawai kita hapus ruang tersebut.
         */

        html.layout-menu-fixed body.pegawai-layout .layout-page {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }


        /* =====================================================
           CONTENT PEGAWAI
        ====================================================== */

        body.pegawai-layout .content-wrapper {
            width: 100% !important;
            max-width: 100% !important;

            padding-left: 0 !important;
            margin-left: 0 !important;
        }


        /* =====================================================
           MASTER
        ====================================================== */

        /*
         * Master tetap memakai sidebar.
         * Yang dihilangkan hanya navbar/headbar atas.
         */

        body.master-layout .content-wrapper {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media(max-width:767px) {

            .container-p-y {
                padding-top: 12px !important;
            }

            body.pegawai-layout .layout-container,
            body.pegawai-layout .layout-page,
            body.pegawai-layout .content-wrapper {
                width: 100% !important;
                max-width: 100% !important;

                padding-left: 0 !important;
                margin-left: 0 !important;
            }

        }
    </style>

</head>


<body class="{{ $isPegawai ? 'pegawai-layout' : 'master-layout' }}">


    {{-- =====================================================
    WRAPPER UTAMA
    ====================================================== --}}

    <div class="layout-wrapper layout-content-navbar">

        <div class="layout-container">


            {{-- =================================================
            SIDEBAR

            MASTER = ADA
            PEGAWAI = TIDAK ADA
            ================================================== --}}

            @if(!$isPegawai)

                @include('partials.sidebar')

            @endif


            {{-- =================================================
            PAGE
            ================================================== --}}

            <div class="layout-page">


                {{-- =============================================
                CONTENT WRAPPER

                NAVBAR / HEADBAR SUDAH DIHAPUS GLOBAL
                ============================================== --}}

                <div class="content-wrapper">


                    {{-- =========================================
                    CONTENT HALAMAN
                    ========================================== --}}

                    @yield('content')


                    {{-- =========================================
                    FOOTER
                    ========================================== --}}

                    @include('partials.footer')


                    {{-- =========================================
                    CONTENT BACKDROP
                    ========================================== --}}

                    <div class="content-backdrop fade"></div>


                </div>

            </div>

        </div>


        {{-- =================================================
        OVERLAY SIDEBAR

        CUMA DIBUTUHKAN MASTER
        ================================================== --}}

        @if(!$isPegawai)

            <div class="layout-overlay layout-menu-toggle"></div>

        @endif


    </div>


    {{-- =====================================================
    CORE JS
    ====================================================== --}}


    {{-- JQUERY --}}

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}">
    </script>


    {{-- =====================================================
    BOOTSTRAP & POPPER
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}">
    </script>

    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}">
    </script>


    {{-- =====================================================
    PERFECT SCROLLBAR
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}">
    </script>


    {{-- =====================================================
    MENU
    ====================================================== --}}

    @if(!$isPegawai)

        <script src="{{ asset('assets/vendor/js/menu.js') }}">
        </script>

    @endif


    {{-- =====================================================
    DATATABLES
    ====================================================== --}}

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js">
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js">
    </script>

    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js">
    </script>

    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js">
    </script>


    {{-- =====================================================
    SELECT2
    ====================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
    </script>


    {{-- =====================================================
    LEAFLET
    ====================================================== --}}

    <script src="https://unpkg.com/leaflet/dist/leaflet.js">
    </script>


    {{-- =====================================================
    WEBCAM
    ====================================================== --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js">
    </script>


    {{-- =====================================================
    APEX CHART
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}">
    </script>


    {{-- =====================================================
    MAIN JS
    ====================================================== --}}

    <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>


    {{-- =====================================================
    SWEETALERT
    ====================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>


    {{-- =====================================================
    SERVICE WORKER + FLASH MESSAGE
    ====================================================== --}}

    <script>

        /* =====================================================
           SERVICE WORKER
        ====================================================== */

        if ('serviceWorker' in navigator) {

            navigator
                .serviceWorker
                .register('/sw.js')

                .then(function () {

                    console.log('SW registered');

                })

                .catch(function (err) {

                    console.error(
                        'SW failed',
                        err
                    );

                });

        }


        /* =====================================================
           SWEETALERT SESSION
        ====================================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =============================================
                   SUCCESS
                ============================================== */

                @if(session('swal_success') || session('success'))

                    Swal.fire({

                        icon: 'success',

                        title: 'Berhasil',

                        text:
                            @json(
                                session('swal_success')
                                ?? session('success')
                            ),

                        confirmButtonColor: '#097612'

                    });

                @endif


                /* =============================================
                   ERROR
                ============================================== */

                @if(session('swal_error') || session('error'))

                    Swal.fire({

                        icon: 'error',

                        title: 'Gagal',

                        text:
                            @json(
                                session('swal_error')
                                ?? session('error')
                            ),

                        confirmButtonColor: '#dc3545'

                    });

                @endif


                /* =============================================
                   WARNING
                ============================================== */

                @if(session('swal_warning') || session('warning'))

                    Swal.fire({

                        icon: 'warning',

                        title: 'Perhatian',

                        text:
                            @json(
                                session('swal_warning')
                                ?? session('warning')
                            ),

                        confirmButtonColor: '#ffc107'

                    });

                @endif


                /* =============================================
                   INFO
                ============================================== */

                @if(session('swal_info') || session('info'))

                    Swal.fire({

                        icon: 'info',

                        title: 'Informasi',

                        text:
                            @json(
                                session('swal_info')
                                ?? session('info')
                            ),

                        confirmButtonColor: '#0dcaf0'

                    });

                @endif


            }
        );

    </script>


    {{-- =====================================================
    SCRIPT DARI HALAMAN
    ====================================================== --}}

    @stack('scripts')


    {{-- =====================================================
    MOBILE BOTTOM TOOLBAR

    HANYA PEGAWAI
    ====================================================== --}}

    @if($isPegawai)

        @include('partials.mobile-toolbar')

    @endif


</body>

</html>
