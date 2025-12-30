<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login Absensi</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/images-removebg-preview.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-auth.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="{{ asset('assets/img/images-removebg-preview.png') }}" alt="" width="250">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Pendaftaran Pegawai</h4>
                        <p class="mb-4">Isikan sesuai dengan form yang ada !</p>
                        <form action="" method="POST" enctype="multipart/form-data">

                            @csrf

                            <!-- ROW 1 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama lengkap"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" name="nip" class="form-control" placeholder="NIP" maxlength="18"
                                        required>
                                </div>
                            </div>

                            <!-- ROW 2 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email aktif"
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <select name="id_jabatan" class="form-select" required>
                                        <option value="">Pilih Jabatan</option>
                                        {{-- @foreach ($jabatan as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>

                            <!-- ROW 3 -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi Kerja</label>
                                    <select name="id_lokasi" class="form-select" required>
                                        <option value="">Pilih Lokasi</option>
                                        {{-- @foreach ($lokasi as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Kerja</label>
                                    <select name="id_jam_kerja" class="form-select" required>
                                        <option value="">Pilih Jam Kerja</option>
                                        {{-- @foreach ($jamKerja as $jk)
                                        <option value="{{ $jk->id }}">{{ $jk->nama_shift }}</option>
                                        @endforeach --}}
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

                            <!-- FOTO -->
                            {{-- <div class="mb-3">
                                <label class="form-label">Foto Pegawai (Opsional)</label>
                                <input type="file" name="foto_pegawai" class="form-control" accept="image/*">
                            </div> --}}

                            <!-- INFO -->
                            <div class="alert alert-light small mb-3">
                                <i class="bx bx-info-circle"></i>
                                Akun akan aktif setelah disetujui admin
                            </div>

                            <!-- BUTTON -->
                            <button class="btn btn-primary w-100">
                                Daftar Pegawai
                            </button>

                        </form>

                        <p class="text-center mt-3">
                            <span>Sudah Punya Akun ?</span>
                            <a href="{{ route('login') }}">
                                <span>Masuk</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>

    <!-- / Content -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('/assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
