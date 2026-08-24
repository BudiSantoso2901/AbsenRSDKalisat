<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="manifest" href="/manifest.json">

    <meta name="theme-color" content="#28a745">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="/icon/icon-192.png">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>Login Presensi</title>

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('assets/img/images-removebg-preview.png') }}"
    />


    {{-- =====================================================
         FONTS
    ====================================================== --}}

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    />

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    />

    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />


    {{-- =====================================================
         ICONS
    ====================================================== --}}

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/fonts/boxicons.css') }}"
    />


    {{-- =====================================================
         CORE CSS
    ====================================================== --}}

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/core.css') }}"
    />

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/theme-default.css') }}"
    />

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/demo.css') }}"
    />

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/pages/page-auth.css') }}"
    />


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

            padding:30px 18px;
        }


        .authentication-inner{
            width:100%;

            max-width:420px;
        }


        /* =====================================================
           LOGIN CARD
        ====================================================== */

        .login-card{
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


        /* GARIS GRADIENT ATAS CARD */

        .login-card::before{
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


        .login-card .card-body{
            padding:31px;
        }


        @keyframes fadeUp{

            from{
                opacity:0;

                transform:
                    translateY(15px);
            }

            to{
                opacity:1;

                transform:
                    translateY(0);
            }

        }


        /* =====================================================
           LOGO
        ====================================================== */

        .login-logo{
            text-align:center;

            margin-bottom:18px;
        }


        .login-logo img{
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

        .login-title{
            margin:0 0 7px;

            text-align:center;

            color:var(--text-dark);

            font-size:24px;

            font-weight:700;
        }


        .login-subtitle{
            max-width:300px;

            margin:
                0 auto 26px;

            text-align:center;

            color:var(--text-muted);

            font-size:14px;

            line-height:1.55;
        }


        /* =====================================================
           FORM
        ====================================================== */

        .form-group{
            margin-bottom:18px;
        }


        .form-label{
            margin-bottom:7px;

            color:#4d555d;

            font-size:14px;

            font-weight:600;
        }


        /* =====================================================
           INPUT WRAPPER
        ====================================================== */

        .login-input-wrapper{
            position:relative;
        }


        .login-input-icon{
            position:absolute;

            left:14px;
            top:50%;

            transform:
                translateY(-50%);

            display:flex;

            align-items:center;
            justify-content:center;

            color:#9299a1;

            font-size:21px;

            pointer-events:none;

            z-index:2;
        }


        /* =====================================================
           INPUT
        ====================================================== */

        .login-input{
            width:100%;

            height:50px;

            padding-left:45px;
            padding-right:14px;

            border:
                1.5px solid
                var(--border) !important;

            border-radius:12px;

            color:#333333;

            background:#ffffff !important;

            font-size:14px;

            box-shadow:none !important;

            outline:none !important;

            appearance:none;

            -webkit-appearance:none;

            -moz-appearance:none;

            box-sizing:border-box;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }


        .login-input::placeholder{
            color:#a8afb6;
        }


        .login-input:hover{
            border-color:
                #c6ccd3 !important;
        }


        .login-input:focus{
            border:
                1.5px solid
                var(--rs-pink) !important;

            background:#ffffff !important;

            box-shadow:
                0 0 0 3px
                rgba(240,98,146,.12) !important;
        }


        /* =====================================================
           PASSWORD
        ====================================================== */

        .password-field .login-input{
            padding-right:48px;
        }


        .password-toggle{
            position:absolute;

            right:10px;
            top:50%;

            transform:
                translateY(-50%);

            width:36px;
            height:36px;

            display:flex;

            align-items:center;
            justify-content:center;

            border:0;

            border-radius:8px;

            background:transparent;

            color:#858d95;

            font-size:21px;

            cursor:pointer;

            transition:.2s;
        }


        .password-toggle:hover{
            background:
                rgba(240,98,146,.10);

            color:
                var(--rs-pink);
        }


        .password-toggle:focus{
            outline:none;
        }


        /* =====================================================
           INVALID
        ====================================================== */

        .login-input.is-invalid{
            border:
                1.5px solid
                #dc3545 !important;

            box-shadow:
                none !important;
        }


        .invalid-feedback{
            display:block;

            margin-top:6px;

            font-size:12px;
        }


        /* =====================================================
           LOGIN BUTTON
        ====================================================== */

        .btn-login{
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


        .btn-login:hover{
            color:#ffffff;

            transform:
                translateY(-1px);

            box-shadow:
                0 10px 22px
                rgba(240,98,146,.25);
        }


        .btn-login:active{
            transform:none;
        }


        .btn-login:disabled{
            opacity:.75;

            cursor:not-allowed;

            transform:none;
        }


        /* =====================================================
           REGISTER
        ====================================================== */

        .register-area{
            margin-top:12px;

            padding-top:0;

            border-top:0;

            text-align:center;
        }


        .register-area p{
            margin:0;

            color:#7f8790;

            font-size:14px;

            line-height:1.4;
        }

        .app-version{
            margin-top:14px;
            color:#a0a6ad;
            font-size:10px;
            font-weight:600;
            letter-spacing:.4px;
            text-align:center;
        }

        .register-link{
            margin-left:3px;

            color:
                var(--rs-pink-strong);

            font-weight:700;

            text-decoration:none;
        }


        .register-link:hover{
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


            .login-card{
                border-radius:18px;
            }


            .login-card .card-body{
                padding:
                    26px 21px;
            }


            .login-logo{
                margin-bottom:15px;
            }


            .login-logo img{
                width:175px;
            }


            .login-title{
                font-size:22px;
            }


            .login-subtitle{
                margin-bottom:23px;

                font-size:13px;
            }


            .login-input{
                height:49px;

                font-size:14px;
            }


            .btn-login{
                min-height:49px;
            }


            .register-area{
                margin-top:11px;
            }

        }


        /* =====================================================
           VERY SMALL SCREEN
        ====================================================== */

        @media(max-width:360px){

            .login-card .card-body{
                padding:
                    23px 17px;
            }


            .login-title{
                font-size:21px;
            }


            .login-logo img{
                width:160px;
            }

        }

    </style>

</head>


<body>


    {{-- =====================================================
         LOGIN
    ====================================================== --}}

    <div class="container-xxl">

        <div class="authentication-wrapper authentication-basic">

            <div class="authentication-inner">


                <div class="card login-card">

                    <div class="card-body">


                        {{-- =================================================
                             LOGO
                        ================================================== --}}

                        <div class="login-logo">

                            <img
                                src="{{ asset('assets/img/images-removebg-preview.png') }}"
                                alt="Logo RSD Kalisat"
                            >

                        </div>


                        {{-- =================================================
                             HEADER
                        ================================================== --}}

                        <h1 class="login-title">

                            Selamat Datang 👋

                        </h1>


                        <p class="login-subtitle">

                            Masuk menggunakan Username atau NIP
                            untuk mengakses Presensi RSD Kalisat.

                        </p>


                        {{-- =================================================
                             FORM
                        ================================================== --}}

                        <form
                            id="loginForm"
                            action="{{ route('login.process') }}"
                            method="POST">

                            @csrf


                            {{-- =============================================
                                 USERNAME / NIP
                            ============================================== --}}

                            <div class="form-group">

                                <label
                                    for="login"
                                    class="form-label">

                                    Username / NIP

                                </label>


                                <div class="login-input-wrapper">

                                    <i
                                        class="bx bx-user login-input-icon">
                                    </i>


                                    <input
                                        type="text"
                                        name="login"
                                        id="login"

                                        class="
                                            login-input
                                            @error('login')
                                                is-invalid
                                            @enderror
                                        "

                                        value="{{ old('login') }}"

                                        placeholder="Masukkan Username atau NIP"

                                        autocomplete="username"

                                        required

                                        autofocus
                                    >

                                </div>


                                @error('login')

                                    <div class="invalid-feedback">

                                        <i class="bx bx-error-circle"></i>

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>


                            {{-- =============================================
                                 PASSWORD
                            ============================================== --}}

                            <div class="form-group">

                                <label
                                    for="password"
                                    class="form-label">

                                    Password

                                </label>


                                <div
                                    class="
                                        login-input-wrapper
                                        password-field
                                    ">

                                    <i
                                        class="
                                            bx
                                            bx-lock-alt
                                            login-input-icon
                                        ">
                                    </i>


                                    <input
                                        type="password"

                                        name="password"

                                        id="password"

                                        class="
                                            login-input
                                            @error('password')
                                                is-invalid
                                            @enderror
                                        "

                                        placeholder="Masukkan password"

                                        autocomplete="current-password"

                                        required
                                    >


                                    <button
                                        type="button"

                                        class="password-toggle"

                                        id="passwordToggle"

                                        aria-label="Tampilkan password">

                                        <i class="bx bx-hide"></i>

                                    </button>

                                </div>


                                @error('password')

                                    <div class="invalid-feedback">

                                        <i class="bx bx-error-circle"></i>

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>


                            {{-- =============================================
                                 BUTTON
                            ============================================== --}}

                            <button
                                type="submit"

                                class="btn-login"

                                id="loginButton">


                                <span id="loginButtonNormal">

                                    <i
                                        class="bx bx-log-in-circle">
                                    </i>

                                    Masuk

                                </span>


                                <span
                                    id="loginButtonLoading"

                                    class="d-none">


                                    <span
                                        class="
                                            spinner-border
                                            spinner-border-sm
                                            me-1
                                        ">
                                    </span>


                                    Memproses...

                                </span>

                            </button>


                        </form>


                        {{-- =================================================
                             REGISTER
                        ================================================== --}}

                        <div class="register-area">

                            <p>

                                Belum punya akun?

                                <a
                                    href="{{ route('pegawai.register.form') }}"

                                    class="register-link">

                                    Daftar Sekarang

                                </a>

                            </p>

                            <div class="app-version">
                                RSD KALISAT JEMBER V2.0
                            </div>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>


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


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function(){


                /* =================================================
                   PASSWORD SHOW / HIDE
                ================================================= */

                const passwordInput =
                    document.getElementById(
                        'password'
                    );


                const passwordToggle =
                    document.getElementById(
                        'passwordToggle'
                    );


                if(passwordToggle){

                    passwordToggle.addEventListener(
                        'click',
                        function(){

                            const icon =
                                this.querySelector('i');


                            if(
                                passwordInput.type ===
                                'password'
                            ){

                                passwordInput.type =
                                    'text';


                                icon.classList.remove(
                                    'bx-hide'
                                );


                                icon.classList.add(
                                    'bx-show'
                                );


                                this.setAttribute(
                                    'aria-label',
                                    'Sembunyikan password'
                                );

                            }else{

                                passwordInput.type =
                                    'password';


                                icon.classList.remove(
                                    'bx-show'
                                );


                                icon.classList.add(
                                    'bx-hide'
                                );


                                this.setAttribute(
                                    'aria-label',
                                    'Tampilkan password'
                                );

                            }

                        }
                    );

                }


                /* =================================================
                   LOADING BUTTON
                ================================================= */

                const loginForm =
                    document.getElementById(
                        'loginForm'
                    );


                const loginButton =
                    document.getElementById(
                        'loginButton'
                    );


                const loginButtonNormal =
                    document.getElementById(
                        'loginButtonNormal'
                    );


                const loginButtonLoading =
                    document.getElementById(
                        'loginButtonLoading'
                    );


                if(loginForm){

                    loginForm.addEventListener(
                        'submit',
                        function(){

                            loginButton.disabled =
                                true;


                            loginButtonNormal
                                .classList
                                .add(
                                    'd-none'
                                );


                            loginButtonLoading
                                .classList
                                .remove(
                                    'd-none'
                                );

                        }
                    );

                }


                /* =================================================
                   SWEET ALERT
                ================================================= */

                @if(session('swal_error'))

                    Swal.fire({

                        icon:'error',

                        title:'Login Gagal',

                        text:
                            @json(
                                session('swal_error')
                            ),

                        confirmButtonText:
                            'Coba Lagi',

                        confirmButtonColor:
                            '#097612'

                    });

                @endif


                @if(session('swal_warning'))

                    Swal.fire({

                        icon:'warning',

                        title:'Perhatian',

                        text:
                            @json(
                                session('swal_warning')
                            ),

                        confirmButtonColor:
                            '#f06292'

                    });

                @endif


                @if(session('swal_success'))

                    Swal.fire({

                        icon:'success',

                        title:'Berhasil',

                        text:
                            @json(
                                session('swal_success')
                            ),

                        confirmButtonColor:
                            '#097612'

                    });

                @endif


            }
        );


        /* =====================================================
           SERVICE WORKER
        ====================================================== */

        if(
            'serviceWorker' in navigator
        ){

            navigator.serviceWorker
                .register('/sw.js')

                .then(
                    function(){

                        console.log(
                            'SW registered'
                        );

                    }
                )

                .catch(
                    function(err){

                        console.error(
                            'SW failed',
                            err
                        );

                    }
                );

        }

    </script>


</body>

</html>