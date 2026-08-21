@extends('_layouts.layouts')

@section('title', 'Profile')


@push('styles')

<style>

/* =========================================================
   PROFILE
========================================================= */

.profile-page{
    max-width:1100px;
    margin:auto;
    padding-bottom:30px;
}


/* =========================================================
   HEADER HIJAU
========================================================= */

.profile-header{
    position:relative;
    height:235px;
    margin:-15px -12px 0;
    padding-top:20px;
    overflow:hidden;
    background:linear-gradient(
        160deg,
        #a8dc9a 0%,
        #62b95d 55%,
        #319e50 100%
    );
    border-radius:0 0 35px 35px;
}

.profile-header::before{
    content:"";
    position:absolute;
    width:230px;
    height:230px;
    right:-100px;
    top:-100px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
}

.profile-header::after{
    content:"";
    position:absolute;
    width:150px;
    height:150px;
    left:-80px;
    bottom:-100px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
}

.profile-title{
    position:relative;
    z-index:2;
    color:#fff;
    text-align:center;
    font-size:20px;
    font-weight:600;
}


/* =========================================================
   PROFILE CARD
========================================================= */

.profile-card{
    position:relative;
    z-index:3;
    margin:-50px 15px 0;
    min-height:380px;
    background:#f4f4f4;
    border-radius:14px;
    padding:0 20px 25px;
}


/* =========================================================
   FOTO PROFIL
========================================================= */

.profile-photo{
    position:relative;
    width:145px;
    height:145px;
    margin:-82px auto 0;
    z-index:5;

    border-radius:50%;
    overflow:hidden;

    background:#fff;
    border:5px solid #fff;

    box-shadow:0 4px 14px rgba(0,0,0,.12);
}

.profile-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:50%;
}


/* =========================================================
   IDENTITAS
========================================================= */

.profile-nip{
    text-align:center;
    margin-top:5px;
    color:#111;
    font-size:15px;
    font-weight:700;
}

.profile-name{
    text-align:center;
    margin-top:8px;
    margin-bottom:30px;
    color:#111;
    font-size:19px;
    font-weight:700;
}


/* =========================================================
   MENU PROFILE
========================================================= */

.profile-menu{
    width:100%;
    display:flex;
    flex-direction:column;
    gap:4px;
}

.profile-menu-item{
    width:100%;
    min-height:52px;

    display:grid;
    grid-template-columns:34px 1fr;
    align-items:center;
    column-gap:18px;

    padding:10px 0;

    border:0;
    outline:0;
    background:transparent;

    color:#111;
    text-decoration:none !important;
    text-align:left;

    font-family:inherit;
    font-size:17px;
    font-weight:500;

    cursor:pointer;

    box-sizing:border-box;
}


/* SEMUA ICON PUNYA AREA YANG SAMA */

.profile-menu-item i{
    width:34px;
    height:34px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin:0 !important;
    padding:0 !important;

    font-size:25px;

    color:#111;
}


/* SEMUA TEXT MULAI DARI GARIS YANG SAMA */

.profile-menu-item span{
    display:block;

    margin:0;
    padding:0;

    line-height:1.3;
}


/* HOVER */

.profile-menu-item:hover{
    color:#20a965;
}

.profile-menu-item:hover i{
    color:#20a965;
}

/* =========================================================
   MODAL UMUM
========================================================= */

.profile-modal .modal-content{
    border:0;
    border-radius:18px;
    overflow:hidden;
}

.profile-modal .modal-header{
    padding:18px 20px;
}

.profile-modal .modal-title{
    font-size:19px;
    font-weight:700;
    color:#52677f;
}

.profile-modal .modal-body{
    padding:20px;
}

.profile-modal .form-label{
    font-size:14px;
    font-weight:600;
    color:#52677f;
}

.profile-modal .form-control{
    min-height:45px;
    font-size:14px;
}

.profile-modal .input-group-text{
    min-width:45px;
    justify-content:center;
}


/* =========================================================
   FOTO DI PENGATURAN PROFIL
========================================================= */

.edit-avatar{
    position:relative;
    width:120px;
    height:120px;
    margin:0 auto 12px;
}

.edit-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:50%;
    border:4px solid #fff;
    box-shadow:0 3px 12px rgba(0,0,0,.15);
}

.edit-avatar-button{
    position:absolute;
    right:0;
    bottom:0;
    width:38px;
    height:38px;
    border-radius:50%;
    background:#4caf50;
    color:#fff;
    border:3px solid #fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:18px;
}

.edit-avatar-loader{
    position:absolute;
    inset:0;
    border-radius:50%;
    background:rgba(0,0,0,.45);
    display:none;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:24px;
}

.edit-avatar.uploading .edit-avatar-loader{
    display:flex;
}

.photo-hint{
    text-align:center;
    font-size:11px;
    color:#888;
    line-height:1.5;
    margin-bottom:20px;
}


/* =========================================================
   PASSWORD
========================================================= */

.password-input{
    position:relative;
}

.password-input .toggle-eye{
    position:absolute;
    top:50%;
    right:14px;
    transform:translateY(-50%);
    color:#888;
    cursor:pointer;
    z-index:5;
    font-size:20px;
}

.strength-meter{
    height:6px;
    margin-top:8px;
    background:#e9ecef;
    border-radius:4px;
    overflow:hidden;
}

.strength-meter-bar{
    width:0;
    height:100%;
    border-radius:4px;
    transition:.3s;
}

.strength-label{
    display:block;
    margin-top:4px;
    font-size:11px;
}


/* =========================================================
   VALIDASI
========================================================= */

.field-valid{
    border-color:#4caf50!important;
}

.field-invalid{
    border-color:#dc3545!important;
}

.invalid-message{
    color:#dc3545;
    font-size:11px;
    margin-top:4px;
}


/* =========================================================
   BUTTON
========================================================= */

.btn-profile-save{
    background:#4caf50;
    color:#fff;
    border:0;
    border-radius:10px;
    padding:10px 18px;
    font-size:14px;
    font-weight:600;
}

.btn-profile-save:hover{
    background:#399d40;
    color:#fff;
}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:767px){

    .profile-page{
        width:100%;
    }

    .profile-header{
        height:235px;
        margin:-15px -12px 0;
        border-radius:0 0 32px 32px;
    }

    .profile-title{
        font-size:20px;
    }

    .profile-card{
        margin:-50px 10px 0;
        min-height:390px;
        padding:0 20px 25px;
        border-radius:13px;
    }

    .profile-photo{
        width:145px;
        height:145px;
        margin:-82px auto 0;
    }

    .profile-nip{
        font-size:15px;
    }

    .profile-name{
        font-size:19px;
        margin-bottom:28px;
    }

    .profile-menu-item{
        grid-template-columns:34px 1fr;
        column-gap:18px;
        min-height:52px;
        padding:10px 0;
        font-size:17px;
    }

    .profile-menu-item i{
        width:34px;
        height:34px
        font-size:25px;
    }

    /* =========================================
       FOOTER TIDAK TERTUTUP MOBILE TOOLBAR
    ========================================= */

    /* .content-footer,
    footer{
        margin-bottom:95px !important;
    } */

}


/* =========================================================
   DESKTOP
========================================================= */

@media(min-width:768px){

    .profile-header{
        margin-left:0;
        margin-right:0;
        border-radius:20px;
    }

    .profile-card{
        max-width:500px;
        margin:-50px auto 0;
    }

}

</style>

@endpush


@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="profile-page">


{{-- =========================================================
     HEADER
========================================================= --}}

<div class="profile-header">

    <div class="profile-title">
        Profile
    </div>

</div>


{{-- =========================================================
     PROFILE CARD
========================================================= --}}

<div class="profile-card">


    {{-- FOTO --}}

    <div class="profile-photo">

        <img
            src="{{ $pegawai->foto_pegawai
                ? asset('storage/' . $pegawai->foto_pegawai)
                : asset('assets/img/default-avatar.png') }}"
            alt="Foto Profil">

    </div>


    {{-- NIP --}}

    <div class="profile-nip">
        {{ $pegawai->nip ?? '-' }}
    </div>


    {{-- NAMA --}}

    <div class="profile-name">
        {{ $pegawai->name }}
    </div>


    {{-- =====================================================
     MENU
====================================================== --}}

    <div class="profile-menu">


        {{-- EMAIL --}}

        <button
            type="button"
            class="profile-menu-item"
            data-bs-toggle="modal"
            data-bs-target="#emailModal">

            <i class="bx bx-envelope"></i>

            <span>Email</span>

        </button>


        {{-- PENGATURAN PROFIL --}}

        <button
            type="button"
            class="profile-menu-item"
            data-bs-toggle="modal"
            data-bs-target="#profileModal">

            <i class="bx bx-cog"></i>

            <span>Pengaturan Profil</span>

        </button>


        {{-- GANTI PASSWORD --}}

        <button
            type="button"
            class="profile-menu-item"
            data-bs-toggle="modal"
            data-bs-target="#passwordModal">

            <i class="bx bx-lock-alt"></i>

            <span>Ganti Password</span>

        </button>


        {{-- KELUAR --}}

        <a
            href="{{ route('logout') }}"
            class="profile-menu-item"
            onclick="
                event.preventDefault();
                document.getElementById('logout-form').submit();
            ">

            <i class="bx bx-log-out"></i>

            <span>Keluar</span>

        </a>


    </div>

</div>

</div>

</div>


{{-- =========================================================
     MODAL EMAIL
========================================================= --}}

<div
    class="modal fade profile-modal"
    id="emailModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">
                    Email
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bx bx-envelope"></i>

                    </span>


                    <input
                        type="text"
                        class="form-control"
                        value="{{ $pegawai->email }}"
                        readonly>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL PENGATURAN PROFIL
     FOTO + NAMA
========================================================= --}}

<div
    class="modal fade profile-modal"
    id="profileModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            {{-- HEADER --}}

            <div class="modal-header">

                <h5 class="modal-title">
                    Pengaturan Profil
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            {{-- BODY --}}

            <div class="modal-body">

                <form
                    id="profileForm"
                    action="{{ route('profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    @method('PUT')


                    {{-- FOTO --}}

                    <div class="text-center">

                        <div
                            class="edit-avatar"
                            id="avatarWrapper">

                            <img
                                id="avatarPreview"
                                src="{{ $pegawai->foto_pegawai
                                    ? asset('storage/' . $pegawai->foto_pegawai)
                                    : asset('assets/img/default-avatar.png') }}"
                                alt="Foto Profil">


                            <div class="edit-avatar-loader">

                                <i class="bx bx-loader-alt bx-spin"></i>

                            </div>


                            <label
                                for="foto_pegawai"
                                class="edit-avatar-button">

                                <i class="bx bxs-camera"></i>

                            </label>

                        </div>


                        <input
                            type="file"
                            name="foto_pegawai"
                            id="foto_pegawai"
                            accept="image/png,image/jpeg,image/jpg"
                            class="d-none">


                        <div
                            class="photo-hint"
                            id="fotoError">

                            Klik ikon kamera untuk mengganti foto.<br>
                            JPG/PNG, maksimal 2MB.

                        </div>

                    </div>


                    {{-- NAMA --}}

                    <div class="mb-3">

                        <label
                            for="name"
                            class="form-label">

                            Nama Lengkap
                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bx bx-user"></i>

                            </span>


                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="{{ old('name', $pegawai->name) }}"
                                required
                                minlength="3"
                                maxlength="255">

                        </div>


                        <div
                            id="nameError"
                            class="invalid-message">
                        </div>

                    </div>


                    {{-- BUTTON --}}

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                            Batal

                        </button>


                        <button
                            type="submit"
                            class="btn-profile-save"
                            id="profileSaveButton">

                            <span id="profileSaveText">

                                <i class="bx bx-save me-1"></i>
                                Simpan

                            </span>


                            <span
                                id="profileSaveLoading"
                                class="d-none">

                                <span class="spinner-border spinner-border-sm me-1"></span>

                                Menyimpan...

                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL GANTI PASSWORD
========================================================= --}}

<div
    class="modal fade profile-modal"
    id="passwordModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            {{-- HEADER --}}

            <div class="modal-header">

                <h5 class="modal-title">
                    Ganti Password
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            {{-- BODY --}}

            <div class="modal-body">

                <form
                    id="passwordForm"
                    action="{{ route('profile.update') }}"
                    method="POST">

                    @csrf

                    @method('PUT')


                    {{-- NAMA TERSEMBUNYI
                         Agar data profile tetap lengkap
                         jika backend membutuhkan name
                    --}}

                    <input
                        type="hidden"
                        name="name"
                        value="{{ $pegawai->name }}">


                    {{-- PASSWORD BARU --}}

                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label">

                            Password Baru

                        </label>


                        <div class="password-input">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Minimal 6 karakter"
                                minlength="6"
                                required>


                            <i
                                class="bx bx-hide toggle-eye"
                                data-target="password">
                            </i>

                        </div>


                        <div class="strength-meter">

                            <div
                                class="strength-meter-bar"
                                id="strengthBar">
                            </div>

                        </div>


                        <span
                            class="strength-label"
                            id="strengthLabel">
                        </span>

                    </div>


                    {{-- KONFIRMASI PASSWORD --}}

                    <div class="mb-2">

                        <label
                            for="password_confirmation"
                            class="form-label">

                            Konfirmasi Password

                        </label>


                        <div class="password-input">

                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                                required>


                            <i
                                class="bx bx-hide toggle-eye"
                                data-target="password_confirmation">
                            </i>

                        </div>


                        <div
                            id="confirmError"
                            class="invalid-message">
                        </div>

                    </div>


                    {{-- BUTTON --}}

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                            Batal

                        </button>


                        <button
                            type="submit"
                            class="btn-profile-save"
                            id="passwordSaveButton">

                            <span id="passwordSaveText">

                                <i class="bx bx-save me-1"></i>
                                Simpan

                            </span>


                            <span
                                id="passwordSaveLoading"
                                class="d-none">

                                <span class="spinner-border spinner-border-sm me-1"></span>

                                Menyimpan...

                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     LOGOUT
========================================================= --}}

<form
    id="logout-form"
    action="{{ route('logout') }}"
    method="POST"
    class="d-none">

    @csrf

</form>


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){


    /* =====================================================
       PENGATURAN PROFIL
       FOTO
    ===================================================== */

    const fotoInput =
        document.getElementById('foto_pegawai');

    const avatarPreview =
        document.getElementById('avatarPreview');

    const avatarWrapper =
        document.getElementById('avatarWrapper');

    const fotoError =
        document.getElementById('fotoError');


    if(fotoInput){

        fotoInput.addEventListener(
            'change',
            function(){

                const file =
                    this.files[0];

                if(!file) return;


                const validTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/jpg'
                ];

                const maxSize =
                    2 * 1024 * 1024;


                if(!validTypes.includes(file.type)){

                    fotoError.innerHTML =
                        '<span class="text-danger">Format file harus JPG atau PNG.</span>';

                    this.value = '';

                    return;

                }


                if(file.size > maxSize){

                    fotoError.innerHTML =
                        '<span class="text-danger">Ukuran file maksimal 2MB.</span>';

                    this.value = '';

                    return;

                }


                avatarWrapper.classList.add(
                    'uploading'
                );


                const reader =
                    new FileReader();


                reader.onload =
                    function(e){

                        avatarPreview.src =
                            e.target.result;

                        avatarWrapper.classList.remove(
                            'uploading'
                        );

                        fotoError.innerHTML =
                            'Foto siap disimpan.<br>JPG/PNG, maksimal 2MB.';

                    };


                reader.readAsDataURL(file);

            }
        );

    }


    /* =====================================================
       VALIDASI NAMA
    ===================================================== */

    const nameInput =
        document.getElementById('name');

    const nameError =
        document.getElementById('nameError');


    const profileForm =
        document.getElementById('profileForm');


    if(profileForm){

        profileForm.addEventListener(
            'submit',
            function(e){

                const name =
                    nameInput.value.trim();


                if(name.length < 3){

                    e.preventDefault();

                    nameInput.classList.add(
                        'field-invalid'
                    );

                    nameError.textContent =
                        'Nama minimal 3 karakter.';

                    return;

                }


                nameInput.classList.remove(
                    'field-invalid'
                );


                document
                    .getElementById('profileSaveButton')
                    .disabled = true;


                document
                    .getElementById('profileSaveText')
                    .classList.add('d-none');


                document
                    .getElementById('profileSaveLoading')
                    .classList.remove('d-none');

            }
        );

    }


    /* =====================================================
       PASSWORD SHOW / HIDE
    ===================================================== */

    document
        .querySelectorAll('.toggle-eye')
        .forEach(function(icon){

            icon.addEventListener(
                'click',
                function(){

                    const target =
                        document.getElementById(
                            this.dataset.target
                        );


                    if(target.type === 'password'){

                        target.type =
                            'text';

                        this.classList.remove(
                            'bx-hide'
                        );

                        this.classList.add(
                            'bx-show'
                        );

                    }else{

                        target.type =
                            'password';

                        this.classList.remove(
                            'bx-show'
                        );

                        this.classList.add(
                            'bx-hide'
                        );

                    }

                }
            );

        });


    /* =====================================================
       PASSWORD STRENGTH
    ===================================================== */

    const passwordInput =
        document.getElementById('password');

    const strengthBar =
        document.getElementById('strengthBar');

    const strengthLabel =
        document.getElementById('strengthLabel');


    function evaluateStrength(value){

        let score = 0;

        if(value.length >= 6)
            score++;

        if(value.length >= 10)
            score++;

        if(/[A-Z]/.test(value))
            score++;

        if(/[0-9]/.test(value))
            score++;

        if(/[^A-Za-z0-9]/.test(value))
            score++;

        return score;

    }


    if(passwordInput){

        passwordInput.addEventListener(
            'input',
            function(){

                const value =
                    this.value;

                const score =
                    evaluateStrength(value);


                if(value.length === 0){

                    strengthBar.style.width =
                        '0%';

                    strengthLabel.textContent =
                        '';

                    return;

                }


                let color =
                    '#dc3545';

                let label =
                    'Lemah';


                if(score <= 1){

                    color =
                        '#dc3545';

                    label =
                        'Lemah';

                }else if(score <= 2){

                    color =
                        '#ffc107';

                    label =
                        'Cukup';

                }else if(score <= 3){

                    color =
                        '#0dcaf0';

                    label =
                        'Baik';

                }else{

                    color =
                        '#4caf50';

                    label =
                        'Kuat';

                }


                strengthBar.style.width =
                    ((score / 5) * 100) + '%';

                strengthBar.style.backgroundColor =
                    color;

                strengthLabel.textContent =
                    label;

                strengthLabel.style.color =
                    color;


                checkPasswordMatch();

            }
        );

    }


    /* =====================================================
       KONFIRMASI PASSWORD
    ===================================================== */

    const confirmInput =
        document.getElementById(
            'password_confirmation'
        );

    const confirmError =
        document.getElementById(
            'confirmError'
        );


    function checkPasswordMatch(){

        if(!confirmInput.value){

            confirmInput.classList.remove(
                'field-valid',
                'field-invalid'
            );

            confirmError.textContent =
                '';

            return true;

        }


        if(
            passwordInput.value !==
            confirmInput.value
        ){

            confirmInput.classList.add(
                'field-invalid'
            );

            confirmInput.classList.remove(
                'field-valid'
            );

            confirmError.textContent =
                'Konfirmasi password tidak sama.';

            return false;

        }


        confirmInput.classList.add(
            'field-valid'
        );

        confirmInput.classList.remove(
            'field-invalid'
        );

        confirmError.textContent =
            '';

        return true;

    }


    if(confirmInput){

        confirmInput.addEventListener(
            'input',
            checkPasswordMatch
        );

    }


    /* =====================================================
       SUBMIT PASSWORD
    ===================================================== */

    const passwordForm =
        document.getElementById(
            'passwordForm'
        );


    if(passwordForm){

        passwordForm.addEventListener(
            'submit',
            function(e){

                let valid = true;


                if(
                    passwordInput.value.length < 6
                ){

                    passwordInput.classList.add(
                        'field-invalid'
                    );

                    valid = false;

                }


                if(!checkPasswordMatch()){

                    valid = false;

                }


                if(!valid){

                    e.preventDefault();

                    return;

                }


                document
                    .getElementById(
                        'passwordSaveButton'
                    )
                    .disabled = true;


                document
                    .getElementById(
                        'passwordSaveText'
                    )
                    .classList.add(
                        'd-none'
                    );


                document
                    .getElementById(
                        'passwordSaveLoading'
                    )
                    .classList.remove(
                        'd-none'
                    );

            }
        );

    }


});

</script>

@endpush

@endsection