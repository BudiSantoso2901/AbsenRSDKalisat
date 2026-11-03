
@extends('mobil-cinta.layouts.app')

@section('title', 'Edit Akun')

@section('content')

<style>
    .form-card {
        max-width: 760px;
        margin: 0 auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 18px;
    }

    .form-grid .full-width {
        grid-column: 1 / -1;
    }

    .form-help {
        margin-top: 6px;
        color: #89928c;
        font-size: 11px;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #e5e9e7;
    }

    .required-mark {
        color: #dc3545;
    }

    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #dce8de;
        border-radius: 10px;
        background: #f7faf7;
    }

    .checkbox-row input {
        width: 17px;
        height: 17px;
        accent-color: #178b2a;
    }

    .checkbox-row label {
        margin: 0;
        color: #35443a;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .account-identity {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        margin-bottom: 22px;
        border: 1px solid #e0e9e1;
        border-radius: 11px;
        background: #f7faf7;
    }

    .account-identity-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        background: #e5f3e7;
        color: #178b2a;
        font-size: 22px;
    }

    .account-identity strong {
        display: block;
        font-size: 13px;
    }

    .account-identity small {
        color: #7b858c;
    }

    @media(max-width:600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-grid .full-width {
            grid-column: auto;
        }
    }
</style>

@php
    $isOwnAccount = (int)auth('mobil_cinta')->id() === (int)$account->id;
@endphp

<div class="page-heading">
    <div>
        <span class="role">
            <i class="bx bx-edit"></i>
            MASTER AKUN
        </span>

        <h1 style="margin-top:10px;">Edit Akun</h1>

        <p class="page-subtitle">
            Perbarui informasi dan pengaturan akses pengguna.
        </p>
    </div>

    <a href="{{ route('mobil-cinta.admin.akun.index') }}"
       class="btn btn-outline">
        <i class="bx bx-arrow-back"></i>
        Kembali
    </a>
</div>

<section class="card card-accent form-card">

    <h2 class="section-heading" style="margin-top:0;">
        Informasi Pengguna
    </h2>

    <div class="account-identity">
        <div class="account-identity-icon">
            <i class="bx bx-user"></i>
        </div>

        <div>
            <strong>{{ $account->nama }}</strong>
            <small>{{ '@' . $account->nama_pengguna }}</small>
        </div>
    </div>

    <form method="POST"
          action="{{ route('mobil-cinta.admin.akun.update', $account) }}">

        @csrf
        @method('PUT')

        <div class="form-grid">

            {{-- NAMA --}}

            <div class="form-group full-width">
                <label for="nama" class="form-label">
                    Nama Lengkap <span class="required-mark">*</span>
                </label>

                <input type="text"
                       id="nama"
                       name="nama"
                       class="form-control"
                       value="{{ old('nama', $account->nama) }}"
                       maxlength="255"
                       required>

                @error('nama')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- USERNAME --}}

            <div class="form-group">
                <label for="nama_pengguna" class="form-label">
                    Username <span class="required-mark">*</span>
                </label>

                <input type="text"
                       id="nama_pengguna"
                       name="nama_pengguna"
                       class="form-control"
                       value="{{ old('nama_pengguna', $account->nama_pengguna) }}"
                       maxlength="255"
                       autocomplete="username"
                       required>

                @error('nama_pengguna')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- ROLE --}}

            <div class="form-group">
                <label for="peran_id" class="form-label">
                    Peran Pengguna <span class="required-mark">*</span>
                </label>

                <select id="peran_id"
                        name="peran_id"
                        class="form-control"
                        required>

                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ (string)old('peran_id', $account->peran_id) === (string)$role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->nama_peran) }}
                        </option>
                    @endforeach

                </select>

                <div class="form-help">
                    Perubahan peran akan mengubah hak akses pengguna.
                </div>

                @error('peran_id')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- PASSWORD BARU --}}

            <div class="form-group">
                <label for="password" class="form-label">
                    Password Baru
                </label>

                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Kosongkan jika tidak diubah"
                       minlength="8"
                       autocomplete="new-password">

                <div class="form-help">
                    Isi hanya jika ingin mengganti password.
                    Minimal 8 karakter.
                </div>

                @error('password')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- KONFIRMASI PASSWORD --}}

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password Baru
                </label>

                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password baru"
                       minlength="8"
                       autocomplete="new-password">
            </div>

            {{-- STATUS AKUN --}}

            <div class="form-group full-width">

                <label class="form-label">Status Akun</label>

                @if($isOwnAccount)

                    <input type="hidden" name="status_aktif" value="1">

                    <div class="checkbox-row">
                        <input type="checkbox"
                               checked
                               disabled>

                        <label>
                            Akun Anda tetap aktif
                        </label>
                    </div>

                    <div class="form-help">
                        Admin tidak dapat menonaktifkan akun yang sedang digunakan.
                    </div>

                @else

                    <input type="hidden" name="status_aktif" value="0">

                    <div class="checkbox-row">
                        <input type="checkbox"
                               id="status_aktif"
                               name="status_aktif"
                               value="1"
                               {{ old('status_aktif', $account->status_aktif ? '1' : '0') == '1' ? 'checked' : '' }}>

                        <label for="status_aktif">
                            Akun Aktif
                        </label>
                    </div>

                    <div class="form-help">
                        Akun nonaktif tidak dapat digunakan untuk login.
                    </div>

                @endif

                @error('status_aktif')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror

            </div>

        </div>

        <div class="form-footer">

            <a href="{{ route('mobil-cinta.admin.akun.index') }}"
               class="btn btn-outline">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i>
                Simpan Perubahan
            </button>

        </div>

    </form>

</section>

@endsection
