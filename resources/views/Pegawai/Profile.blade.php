@extends('_layouts.layouts') {{-- ganti sesuai nama layout Anda, mis. layouts.app / layouts.master --}}

@section('title', 'Profil Saya')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        :root {
            --primary: #097612;
            --primary-light: #0fa81c;
            --primary-soft: rgba(9, 118, 18, .08);
            --danger: #dc3545;
            --warning: #ffc107;
            --radius: 18px;
        }

        .profile-hero {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 2.5rem 1.5rem 5.5rem;
            color: #fff;
            margin-bottom: -4rem;
        }

        .profile-hero::before,
        .profile-hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .profile-hero::before {
            width: 220px;
            height: 220px;
            top: -80px;
            right: -60px;
        }

        .profile-hero::after {
            width: 140px;
            height: 140px;
            bottom: -60px;
            left: 10%;
        }

        .profile-hero h4,
        .profile-hero p {
            position: relative;
            z-index: 2;
        }

        .profile-card {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 .5rem 2rem rgba(0, 0, 0, .08);
        }

        .avatar-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto;
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 .35rem 1rem rgba(0, 0, 0, .18);
            transition: transform .35s ease, filter .35s ease;
        }

        .avatar-wrapper:hover img {
            transform: scale(1.05);
            filter: brightness(.92);
        }

        .avatar-edit {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
            transition: transform .2s ease, background .2s ease;
            box-shadow: 0 .2rem .5rem rgba(0, 0, 0, .25);
        }

        .avatar-edit:hover {
            transform: scale(1.12) rotate(-8deg);
            background: var(--primary-light);
        }

        .avatar-edit i {
            font-size: 1.1rem;
        }

        .avatar-loader {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: rgba(0, 0, 0, .45);
            display: none;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
        }

        .avatar-wrapper.uploading .avatar-loader {
            display: flex;
        }

        .dropzone-hint {
            text-align: center;
            font-size: .8rem;
            color: #8a93a3;
            margin-top: .75rem;
        }

        .form-floating-icon {
            position: relative;
        }

        .form-floating-icon .toggle-eye {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8a93a3;
            z-index: 5;
            transition: color .2s ease;
        }

        .form-floating-icon .toggle-eye:hover {
            color: var(--primary);
        }

        .strength-meter {
            height: 6px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: .5rem;
        }

        .strength-meter-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width .35s ease, background-color .35s ease;
        }

        .strength-label {
            font-size: .75rem;
            margin-top: .25rem;
            display: block;
            font-weight: 500;
        }

        .section-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .section-title i {
            color: var(--primary);
            font-size: 1.25rem;
        }

        .toggle-password-section {
            border: 1px dashed #d9dee3;
            border-radius: 14px;
            padding: .9rem 1.1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background .2s ease, border-color .2s ease;
        }

        .toggle-password-section:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
        }

        .toggle-password-section i.chev {
            transition: transform .3s ease;
        }

        .toggle-password-section.open i.chev {
            transform: rotate(180deg);
        }

        #passwordFields {
            max-height: 0;
            overflow: hidden;
            transition: max-height .4s ease, opacity .3s ease, margin-top .3s ease;
            opacity: 0;
        }

        #passwordFields.open {
            max-height: 600px;
            opacity: 1;
            margin-top: 1rem;
        }

        .btn-save {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: .75rem 1.75rem;
            font-weight: 600;
            transition: transform .15s ease, box-shadow .15s ease, background .2s ease;
            box-shadow: 0 .3rem .8rem rgba(9, 118, 18, .25);
        }

        .btn-save:hover:not(:disabled) {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.2rem rgba(9, 118, 18, .35);
        }

        .btn-save:disabled {
            opacity: .75;
            cursor: not-allowed;
        }

        .info-pill {
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: 999px;
            padding: .3rem .85rem;
            font-size: .78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .field-valid {
            border-color: var(--primary) !important;
        }

        .field-invalid {
            border-color: var(--danger) !important;
        }

        .shake {
            animation: shakeErr .4s;
        }

        @keyframes shakeErr {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-6px);
            }

            40%,
            80% {
                transform: translateX(6px);
            }
        }

        .crop-modal .cropper-canvas-wrapper {
            max-height: 60vh;
        }

        @media (max-width: 576px) {
            .profile-hero {
                padding: 2rem 1rem 5rem;
            }

            .avatar-wrapper {
                width: 116px;
                height: 116px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO --}}
        <div class="profile-hero animate__animated animate__fadeIn">
            <h4 class="mb-1 fw-bold"><i class='bx bxs-user-circle me-2'></i>Profil Saya</h4>
            <p class="mb-0 opacity-75">Kelola informasi akun dan keamanan Anda</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card profile-card animate__animated animate__fadeInUp">
                    <div class="card-body p-4 p-md-5">

                        <form id="profileForm" action="{{ route('profile.update') }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            {{-- ================= AVATAR ================= --}}
                            <div class="text-center mb-4">
                                <div class="avatar-wrapper" id="avatarWrapper">
                                    <img id="avatarPreview"
                                        src="{{ $pegawai->foto_pegawai ? asset('storage/' . $pegawai->foto_pegawai) : asset('assets/img/default-avatar.png') }}"
                                        alt="Foto Profil">
                                    <div class="avatar-loader">
                                        <i class='bx bx-loader-alt bx-spin'></i>
                                    </div>
                                    <label class="avatar-edit" for="foto_pegawai" title="Ganti foto">
                                        <i class='bx bxs-camera'></i>
                                    </label>
                                </div>
                                <input type="file" name="foto_pegawai" id="foto_pegawai"
                                    accept="image/png, image/jpeg, image/jpg" class="d-none">
                                <p class="dropzone-hint">Klik ikon kamera atau <b>drag & drop</b> gambar ke foto.<br>
                                    Format JPG/PNG, maks 2MB.</p>
                                <div class="invalid-feedback d-block text-center" id="fotoError"
                                    style="display:none !important;"></div>
                            </div>

                            {{-- ================= DATA DIRI ================= --}}
                            <div class="mb-4">
                                <div class="section-title"><i class='bx bxs-id-card'></i>Data Diri</div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class='bx bx-user'></i></span>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name', $pegawai->name) }}" placeholder="Masukkan nama lengkap"
                                            required minlength="3" maxlength="255">
                                    </div>
                                    <div class="invalid-feedback" id="nameError"></div>
                                </div>

                                @if (isset($pegawai->email))
                                    <div class="mb-1">
                                        <span class="info-pill"><i class='bx bx-envelope'></i>{{ $pegawai->email }}</span>
                                    </div>
                                @endif
                            </div>

                            <hr class="my-4">

                            {{-- ================= GANTI PASSWORD (COLLAPSIBLE) ================= --}}
                            <div class="mb-3">
                                <div class="toggle-password-section" id="togglePasswordSection">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class='bx bxs-lock-alt fs-5' style="color:var(--primary)"></i>
                                        <span class="fw-semibold">Ubah Kata Sandi</span>
                                        <small class="text-muted d-none d-sm-inline">(opsional)</small>
                                    </div>
                                    <i class='bx bx-chevron-down chev fs-4'></i>
                                </div>

                                <div id="passwordFields">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Kata Sandi Baru</label>
                                        <div class="form-floating-icon">
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="Minimal 6 karakter" minlength="6">
                                            <i class='bx bx-hide toggle-eye' data-target="password"></i>
                                        </div>
                                        <div class="strength-meter">
                                            <div class="strength-meter-bar" id="strengthBar"></div>
                                        </div>
                                        <span class="strength-label" id="strengthLabel"></span>
                                    </div>

                                    <div class="mb-2">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                        <div class="form-floating-icon">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" placeholder="Ulangi kata sandi baru">
                                            <i class='bx bx-hide toggle-eye' data-target="password_confirmation"></i>
                                        </div>
                                        <div class="invalid-feedback" id="confirmError"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary rounded-3" id="btnReset">
                                    <i class='bx bx-reset me-1'></i>Reset
                                </button>
                                <button type="submit" class="btn btn-save text-white" id="btnSave">
                                    <span id="btnSaveText"><i class='bx bx-save me-1'></i>Simpan Perubahan</span>
                                    <span id="btnSaveLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                                    </span>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ============================================================
             * 1. AVATAR: preview instan + drag & drop + validasi client-side
             * ============================================================ */
            const fotoInput = document.getElementById('foto_pegawai');
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarWrapper = document.getElementById('avatarWrapper');
            const fotoError = document.getElementById('fotoError');
            const defaultAvatarSrc = avatarPreview.src;

            function handleFotoFile(file) {
                if (!file) return;

                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                fotoError.style.display = 'none !important';
                fotoError.textContent = '';
                avatarWrapper.classList.remove('shake');

                if (!validTypes.includes(file.type)) {
                    showFotoError('Format file harus JPG atau PNG.');
                    return;
                }
                if (file.size > maxSize) {
                    showFotoError('Ukuran file maksimal 2MB.');
                    return;
                }

                avatarWrapper.classList.add('uploading');
                const reader = new FileReader();
                reader.onload = function(e) {
                    setTimeout(() => {
                        avatarPreview.src = e.target.result;
                        avatarWrapper.classList.remove('uploading');
                        avatarWrapper.classList.add('animate__animated', 'animate__pulse');
                        setTimeout(() => avatarWrapper.classList.remove('animate__animated',
                            'animate__pulse'), 600);
                    }, 350); // sedikit delay biar animasi loading terasa halus
                };
                reader.readAsDataURL(file);
            }

            function showFotoError(msg) {
                fotoError.textContent = msg;
                fotoError.style.removeProperty('display');
                avatarWrapper.classList.add('shake');
                fotoInput.value = '';
                setTimeout(() => avatarWrapper.classList.remove('shake'), 400);
            }

            fotoInput.addEventListener('change', function() {
                handleFotoFile(this.files[0]);
            });

            // Drag & drop di area avatar
            ['dragenter', 'dragover'].forEach(evt => {
                avatarWrapper.addEventListener(evt, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    avatarWrapper.style.outline = '3px dashed var(--primary)';
                    avatarWrapper.style.outlineOffset = '4px';
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                avatarWrapper.addEventListener(evt, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    avatarWrapper.style.outline = 'none';
                });
            });
            avatarWrapper.addEventListener('drop', function(e) {
                const file = e.dataTransfer.files[0];
                if (file) {
                    fotoInput.files = e.dataTransfer.files;
                    handleFotoFile(file);
                }
            });

            /* ============================================================
             * 2. COLLAPSIBLE: bagian ganti password
             * ============================================================ */
            const toggleSection = document.getElementById('togglePasswordSection');
            const passwordFields = document.getElementById('passwordFields');

            toggleSection.addEventListener('click', function() {
                toggleSection.classList.toggle('open');
                passwordFields.classList.toggle('open');
            });

            /* ============================================================
             * 3. TOGGLE SHOW/HIDE PASSWORD
             * ============================================================ */
            document.querySelectorAll('.toggle-eye').forEach(icon => {
                icon.addEventListener('click', function() {
                    const targetInput = document.getElementById(this.dataset.target);
                    const isPassword = targetInput.type === 'password';
                    targetInput.type = isPassword ? 'text' : 'password';
                    this.classList.toggle('bx-hide');
                    this.classList.toggle('bx-show');
                });
            });

            /* ============================================================
             * 4. PASSWORD STRENGTH METER
             * ============================================================ */
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('strengthBar');
            const strengthLabel = document.getElementById('strengthLabel');

            function evaluateStrength(val) {
                let score = 0;
                if (val.length >= 6) score++;
                if (val.length >= 10) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                return score; // 0 - 5
            }

            passwordInput.addEventListener('input', function() {
                const val = this.value;
                const score = evaluateStrength(val);
                const percent = (score / 5) * 100;

                let color = '#dc3545',
                    label = 'Sangat Lemah';
                if (val.length === 0) {
                    strengthBar.style.width = '0%';
                    strengthLabel.textContent = '';
                    return;
                } else if (score <= 1) {
                    color = '#dc3545';
                    label = 'Lemah';
                } else if (score <= 2) {
                    color = '#ffc107';
                    label = 'Cukup';
                } else if (score <= 3) {
                    color = '#0dcaf0';
                    label = 'Baik';
                } else {
                    color = '#097612';
                    label = 'Kuat';
                }

                strengthBar.style.width = percent + '%';
                strengthBar.style.backgroundColor = color;
                strengthLabel.textContent = label;
                strengthLabel.style.color = color;

                checkConfirmMatch();
            });

            /* ============================================================
             * 5. VALIDASI KONFIRMASI PASSWORD REAL-TIME
             * ============================================================ */
            const confirmInput = document.getElementById('password_confirmation');
            const confirmError = document.getElementById('confirmError');

            function checkConfirmMatch() {
                if (confirmInput.value.length === 0) {
                    confirmInput.classList.remove('field-valid', 'field-invalid');
                    confirmError.textContent = '';
                    return true;
                }
                if (passwordInput.value !== confirmInput.value) {
                    confirmInput.classList.add('field-invalid');
                    confirmInput.classList.remove('field-valid');
                    confirmError.textContent = 'Konfirmasi kata sandi tidak sama.';
                    return false;
                } else {
                    confirmInput.classList.add('field-valid');
                    confirmInput.classList.remove('field-invalid');
                    confirmError.textContent = '';
                    return true;
                }
            }
            confirmInput.addEventListener('input', checkConfirmMatch);

            /* ============================================================
             * 6. VALIDASI NAMA REAL-TIME
             * ============================================================ */
            const nameInput = document.getElementById('name');
            const nameError = document.getElementById('nameError');

            function checkName() {
                const val = nameInput.value.trim();
                if (val.length < 3) {
                    nameInput.classList.add('field-invalid');
                    nameInput.classList.remove('field-valid');
                    nameError.textContent = 'Nama minimal 3 karakter.';
                    return false;
                }
                nameInput.classList.add('field-valid');
                nameInput.classList.remove('field-invalid');
                nameError.textContent = '';
                return true;
            }
            nameInput.addEventListener('input', checkName);

            /* ============================================================
             * 7. SUBMIT: validasi akhir + loading state tombol
             * ============================================================ */
            const form = document.getElementById('profileForm');
            const btnSave = document.getElementById('btnSave');
            const btnSaveText = document.getElementById('btnSaveText');
            const btnSaveLoading = document.getElementById('btnSaveLoading');

            form.addEventListener('submit', function(e) {
                let valid = true;

                if (!checkName()) valid = false;

                if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
                    valid = false;
                    passwordInput.classList.add('field-invalid');
                }

                if (passwordInput.value.length > 0 && !checkConfirmMatch()) {
                    valid = false;
                    // otomatis buka bagian password kalau ada error di dalamnya
                    toggleSection.classList.add('open');
                    passwordFields.classList.add('open');
                }

                if (!valid) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Periksa kembali form',
                            text: 'Beberapa data belum valid, silakan periksa kembali.',
                            confirmButtonColor: '#097612'
                        });
                    }
                    return;
                }

                // Loading state
                btnSave.disabled = true;
                btnSaveText.classList.add('d-none');
                btnSaveLoading.classList.remove('d-none');
            });

            /* ============================================================
             * 8. RESET: kembalikan avatar ke foto lama saat klik reset
             * ============================================================ */
            document.getElementById('btnReset').addEventListener('click', function() {
                setTimeout(() => {
                    avatarPreview.src = defaultAvatarSrc;
                    strengthBar.style.width = '0%';
                    strengthLabel.textContent = '';
                    [nameInput, confirmInput, passwordInput].forEach(el => el.classList.remove(
                        'field-valid', 'field-invalid'));
                    nameError.textContent = '';
                    confirmError.textContent = '';
                    passwordFields.classList.remove('open');
                    toggleSection.classList.remove('open');
                }, 10);
            });

        });
    </script>
@endpush
