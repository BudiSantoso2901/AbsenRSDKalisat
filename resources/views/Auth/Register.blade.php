<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr">

<head>

    <meta charset="utf-8" />

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0" />

    <title>Register Pegawai</title>

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('assets/img/images-removebg-preview.png') }}" />


    {{-- =====================================================
         FONTS
    ====================================================== --}}

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com" />

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin />

    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />


    {{-- =====================================================
         ICONS
    ====================================================== --}}

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />


    {{-- =====================================================
         CORE CSS
    ====================================================== --}}

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/core.css') }}" />

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/theme-default.css') }}" />

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/demo.css') }}" />

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />


    {{-- =====================================================
         HELPERS
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>

        :root{
            --rs-green:#097612;
            --rs-green-main:#28a745;
            --rs-green-light:#eaf7ec;

            --rs-pink:#f06292;
            --rs-pink-strong:#ec4f88;

            --text-dark:#31363b;
            --text-muted:#838b94;

            --border:#d8dde3;
            --white:#ffffff;
        }


        /* =====================================================
           PAGE
        ====================================================== */

        html,
        body{
            min-height:100%;
        }


        body{
            margin:0;

            min-height:100vh;

            overflow-x:hidden;

            font-family:'Public Sans',sans-serif;

            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(255,255,255,.28),
                    transparent 30%
                ),

                radial-gradient(
                    circle at 85% 85%,
                    rgba(255,255,255,.18),
                    transparent 30%
                ),

                linear-gradient(
                    145deg,
                    #086d12 0%,
                    #2b9f53 30%,
                    #d96b96 68%,
                    #ef8eb1 100%
                );
        }


        /* =====================================================
           BACKGROUND DECORATION
        ====================================================== */

        body::before{
            content:"";

            position:fixed;

            top:-120px;
            right:-110px;

            width:280px;
            height:280px;

            border-radius:50%;

            background:
                rgba(240,98,146,.18);

            pointer-events:none;
        }


        body::after{
            content:"";

            position:fixed;

            left:-90px;
            bottom:-80px;

            width:230px;
            height:230px;

            border-radius:50%;

            background:
                rgba(255,255,255,.08);

            pointer-events:none;
        }


        /* =====================================================
           WRAPPER
        ====================================================== */

        .authentication-wrapper{
            min-height:100vh;

            display:flex;

            align-items:center;
            justify-content:center;

            padding:32px 18px;
        }


        .authentication-inner{
            width:100%;

            max-width:560px;
        }


        /* =====================================================
           REGISTER CARD
        ====================================================== */

        .register-card{
            position:relative;

            overflow:hidden;

            border:0;

            border-radius:22px;

            background:#ffffff;

            box-shadow:
                0 20px 50px rgba(0,0,0,.18);

            animation:
                fadeUp .45s ease;
        }


        .register-card::before{
            content:"";

            position:absolute;

            top:0;
            left:0;
            right:0;

            height:6px;

            background:
                linear-gradient(
                    90deg,
                    var(--rs-green) 0%,
                    #4eb66e 20%,
                    var(--rs-pink) 55%,
                    var(--rs-pink-strong) 100%
                );
        }


        .register-card .card-body{
            padding:31px;
        }


        @keyframes fadeUp{

            from{
                opacity:0;
                transform:translateY(15px);
            }

            to{
                opacity:1;
                transform:translateY(0);
            }

        }


        /* =====================================================
           LOGO
        ====================================================== */

        .register-logo{
            margin-bottom:16px;

            text-align:center;
        }


        .register-logo img{
            width:190px;

            max-width:90%;

            height:auto;

            filter:
                drop-shadow(
                    0 3px 6px rgba(0,0,0,.09)
                );
        }


        /* =====================================================
           TITLE
        ====================================================== */

        .register-title{
            margin:0 0 7px;

            text-align:center;

            color:var(--text-dark);

            font-size:24px;

            font-weight:700;
        }


        .register-subtitle{
            margin:0 auto 25px;

            text-align:center;

            color:var(--text-muted);

            font-size:14px;

            line-height:1.5;
        }


        /* =====================================================
           FORM
        ====================================================== */

        .form-label{
            margin-bottom:7px;

            color:#4d555d;

            font-size:14px;

            font-weight:600;
        }


        /* =====================================================
           INPUT & SELECT
        ====================================================== */

        .form-control,
        .form-select{
            min-height:49px;

            border:
                1.5px solid
                var(--border) !important;

            border-radius:12px;

            background:#ffffff !important;

            color:#333333;

            font-size:14px;

            box-shadow:none !important;

            outline:none !important;

            box-sizing:border-box;

            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }


        .form-control{
            padding-left:14px;
            padding-right:14px;
        }


        .form-control::placeholder{
            color:#a8afb6;
        }


        .form-control:hover,
        .form-select:hover{
            border-color:
                #c6ccd3 !important;
        }


        .form-control:focus,
        .form-select:focus{
            border:
                1.5px solid
                var(--rs-pink) !important;

            box-shadow:
                0 0 0 3px
                rgba(240,98,146,.12) !important;
        }


        /* =====================================================
           INFO BOX
        ====================================================== */

        .register-info{
            display:flex;

            align-items:flex-start;

            gap:10px;

            margin-bottom:12px;

            padding:12px 14px;

            border:
                1px solid
                rgba(9,118,18,.12);

            border-radius:11px;

            background:
                rgba(9,118,18,.055);

            color:#58615d;

            font-size:12px;

            line-height:1.5;
        }


        .register-info i{
            flex-shrink:0;

            margin-top:1px;

            color:var(--rs-green-main);

            font-size:19px;
        }


        .register-info strong{
            color:#3e4842;
        }


        .register-status{
            border-color:
                rgba(240,98,146,.15);

            background:
                rgba(240,98,146,.06);
        }


        .register-status i{
            color:var(--rs-pink);
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .btn-register{
            width:100%;

            min-height:50px;

            margin-top:5px;

            display:flex;

            align-items:center;
            justify-content:center;

            gap:7px;

            border:0;

            border-radius:12px;

            background:
                linear-gradient(
                    135deg,
                    #0a7615 0%,
                    #2faa58 35%,
                    #e56e9b 72%,
                    #f06292 100%
                );

            color:#ffffff;

            font-size:15px;

            font-weight:700;

            box-shadow:
                0 8px 18px
                rgba(240,98,146,.20);

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                opacity .18s ease;
        }


        .btn-register:hover{
            color:#ffffff;

            transform:
                translateY(-1px);

            box-shadow:
                0 10px 22px
                rgba(240,98,146,.25);
        }


        .btn-register:active{
            transform:none;
        }


        /* =====================================================
           LOGIN LINK
        ====================================================== */

        .login-area{
            margin-top:13px;

            text-align:center;
        }


        .login-area p{
            margin:0;

            color:#7f8790;

            font-size:14px;

            line-height:1.4;
        }


        .login-link{
            margin-left:3px;

            color:
                var(--rs-pink-strong);

            font-weight:700;

            text-decoration:none;
        }


        .login-link:hover{
            color:
                var(--rs-green-main);

            text-decoration:none;
        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media(max-width:575px){

            .authentication-wrapper{
                padding:
                    18px 15px;
            }


            .register-card{
                border-radius:18px;
            }


            .register-card .card-body{
                padding:
                    26px 21px;
            }


            .register-logo{
                margin-bottom:14px;
            }


            .register-logo img{
                width:175px;
            }


            .register-title{
                font-size:22px;
            }


            .register-subtitle{
                margin-bottom:22px;

                font-size:13px;
            }


            .form-control,
            .form-select{
                min-height:48px;

                font-size:14px;
            }


            .btn-register{
                min-height:49px;
            }

        }


        @media(max-width:360px){

            .register-card .card-body{
                padding:
                    23px 17px;
            }


            .register-title{
                font-size:21px;
            }


            .register-logo img{
                width:160px;
            }

        }

    </style>

</head>


<body>


    <div class="container-xxl">

        <div class="authentication-wrapper authentication-basic">

            <div class="authentication-inner">


                <div class="card register-card">

                    <div class="card-body">


                        {{-- =================================================
                             LOGO
                        ================================================== --}}

                        <div class="register-logo">

                            <a href="{{ route('login') }}">

                                <img
                                    src="{{ asset('assets/img/images-removebg-preview.png') }}"
                                    alt="Logo RSD Kalisat">

                            </a>

                        </div>


                        {{-- =================================================
                             HEADER
                        ================================================== --}}

                        <h1 class="register-title">

                            Pendaftaran Pegawai

                        </h1>


                        <p class="register-subtitle">

                            Lengkapi data berikut dengan benar

                        </p>


                        {{-- =================================================
                             FORM
                        ================================================== --}}

                        <form
                            action="{{ route('pegawai.register') }}"
                            method="POST"
                            enctype="multipart/form-data">

                            @csrf


                            {{-- =============================================
                                 NAMA
                            ============================================== --}}

                            <div class="mb-3">

                                <label class="form-label">

                                    Nama Lengkap

                                </label>


                                <input
                                    type="text"

                                    name="name"

                                    class="form-control"

                                    placeholder="Nama lengkap"

                                    value="{{ old('name') }}"

                                    required>

                            </div>


                            {{-- =============================================
                                 NIP / NIK
                            ============================================== --}}

                            <div class="mb-3">

                                <label class="form-label">

                                    NIP atau NIK

                                </label>


                                <input
                                    type="text"

                                    name="nip"

                                    class="form-control"

                                    placeholder="NIP atau NIK"

                                    value="{{ old('nip') }}"

                                    required>

                            </div>


                            {{-- =============================================
                                 TANGGAL LAHIR
                            ============================================== --}}

                            <div class="mb-3">

                                <label class="form-label">

                                    Tanggal Lahir

                                </label>


                                <input
                                    type="date"

                                    name="tanggal_lahir"

                                    class="form-control"

                                    value="{{ old('tanggal_lahir') }}"

                                    required>

                            </div>


                            {{-- =============================================
                                 EMAIL + JABATAN
                            ============================================== --}}

                            <div class="row">


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Email

                                    </label>


                                    <input
                                        type="email"

                                        name="email"

                                        class="form-control"

                                        placeholder="Email aktif"

                                        value="{{ old('email') }}"

                                        required>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Jabatan

                                    </label>


                                    <select
                                        name="id_jabatan"

                                        class="form-select"

                                        required>


                                        <option value="">

                                            Pilih Jabatan

                                        </option>


                                        @foreach($jabatan as $j)

                                            <option
                                                value="{{ $j->id }}"

                                                {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>

                                                {{ $j->nama_jabatan }}

                                            </option>

                                        @endforeach


                                    </select>

                                </div>


                            </div>


                            {{-- =============================================
                                 LOKASI + JAM KERJA
                            ============================================== --}}

                            <div class="row">


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Lokasi Kerja

                                    </label>


                                    <select
                                        name="id_lokasi"

                                        class="form-select"

                                        required>


                                        <option value="">

                                            Pilih Lokasi

                                        </option>


                                        @foreach($lokasi as $l)

                                            <option
                                                value="{{ $l->id }}"

                                                {{ old('id_lokasi') == $l->id ? 'selected' : '' }}>

                                                {{ $l->nama_lokasi }}

                                            </option>

                                        @endforeach


                                    </select>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Jam Kerja

                                    </label>


                                    <select
                                        name="id_jam_kerja"

                                        class="form-select"

                                        required>


                                        <option value="">

                                            Pilih Jam Kerja

                                        </option>


                                        @foreach($jamKerja as $jk)

                                            <option
                                                value="{{ $jk->id }}"

                                                {{ old('id_jam_kerja') == $jk->id ? 'selected' : '' }}>

                                                {{ $jk->nama_jam_kerja }}
                                                ({{ $jk->jam_mulai }} - {{ $jk->jam_selesai }})

                                            </option>

                                        @endforeach


                                    </select>

                                </div>


                            </div>


                            {{-- =============================================
                                 INFO PASSWORD
                            ============================================== --}}

                            <div class="register-info">

                                <i class="bx bx-lock-alt"></i>


                                <div>

                                    Password otomatis menggunakan

                                    <strong>
                                        tanggal lahir (dd/mm/yyyy)
                                    </strong>.

                                    <br>

                                    Contoh:
                                    01-02-1998 →

                                    <strong>
                                        01021998
                                    </strong>

                                </div>

                            </div>


                            {{-- =============================================
                                 INFO STATUS
                            ============================================== --}}

                            <div class="register-info register-status">

                                <i class="bx bx-info-circle"></i>


                                <div>

                                    Akun akan aktif setelah
                                    disetujui admin.

                                </div>

                            </div>


                            {{-- =============================================
                                 BUTTON
                            ============================================== --}}

                            <button
                                type="submit"

                                class="btn-register">

                                <i class="bx bx-user-plus"></i>

                                Daftar Pegawai

                            </button>


                        </form>


                        {{-- =================================================
                             LOGIN
                        ================================================== --}}

                        <div class="login-area">

                            <p>

                                Sudah punya akun?

                                <a
                                    href="{{ route('login') }}"

                                    class="login-link">

                                    Masuk

                                </a>

                            </p>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function(){


                @if($errors->any())

                    Swal.fire({

                        icon:'error',

                        title:'Pendaftaran Gagal',

                        html:`
                            <ul style="
                                text-align:left;
                                padding-left:20px;
                                margin-bottom:0;
                            ">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>
                        `,

                        confirmButtonColor:
                            '#097612'

                    });

                @endif


            }
        );

    </script>


    {{-- =====================================================
         CORE JS
    ====================================================== --}}

    <script
        src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}">
    </script>

    <script
        src="{{ asset('assets/vendor/js/bootstrap.js') }}">
    </script>

    <script
        src="{{ asset('assets/js/main.js') }}">
    </script>


</body>

</html>