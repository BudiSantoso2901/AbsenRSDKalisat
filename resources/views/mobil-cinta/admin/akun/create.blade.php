
@extends('mobil-cinta.layouts.app')

@section('title', 'Tambah Akun')

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

    @media(max-width:600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-grid .full-width {
            grid-column: auto;
        }
    }
</style>

<div class="page-heading">
    <div>
        <span class="role">
            <i class="bx bx-user-plus"></i>
            MASTER AKUN
        </span>

        <h1 style="margin-top:10px;">Tambah Akun</h1>

        <p class="page-subtitle">
            Tambahkan pengguna baru ke dalam sistem Mobil Cinta.
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
        Informasi Akun
    </h2>

    <p class="page-subtitle" style="margin-bottom:23px;">
        Isi identitas pengguna dan tentukan peran aksesnya.
    </p>

    <form method="POST"
          action="{{ route('mobil-cinta.admin.akun.store') }}">

        @csrf

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
                       value="{{ old('nama') }}"
                       placeholder="Masukkan nama lengkap"
                       maxlength="255"
                       autocomplete="name"
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
                       value="{{ old('nama_pengguna') }}"
                       placeholder="Masukkan username"
                       maxlength="255"
                       autocomplete="username"
                       required>

                <div class="form-help">
                    Username harus unik dan digunakan untuk login.
                </div>

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

                    <option value="">Pilih Peran</option>

                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ (string)old('peran_id') === (string)$role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->nama_peran) }}
                        </option>
                    @endforeach

                </select>

                @error('peran_id')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- PASSWORD --}}

            <div class="form-group">
                <label for="password" class="form-label">
                    Password <span class="required-mark">*</span>
                </label>

                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Minimal 8 karakter"
                       minlength="8"
                       autocomplete="new-password"
                       required>

                <div class="form-help">
                    Password minimal 8 karakter.
                </div>

                @error('password')
                    <small style="color:#dc3545;">{{ $message }}</small>
                @enderror
            </div>

            {{-- KONFIRMASI PASSWORD --}}

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password <span class="required-mark">*</span>
                </label>

                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password"
                       minlength="8"
                       autocomplete="new-password"
                       required>
            </div>

            {{-- STATUS --}}

            <div class="form-group full-width">

                <label class="form-label">Status Akun</label>

                <input type="hidden" name="status_aktif" value="0">

                <div class="checkbox-row">
                    <input type="checkbox"
                           id="status_aktif"
                           name="status_aktif"
                           value="1"
                           {{ old('status_aktif', '1') == '1' ? 'checked' : '' }}>

                    <label for="status_aktif">
                        Aktifkan akun setelah dibuat
                    </label>
                </div>

                <div class="form-help">
                    Akun nonaktif tidak dapat digunakan untuk login.
                </div>

            </div>

        </div>

        <div class="form-footer">

            <a href="{{ route('mobil-cinta.admin.akun.index') }}"
               class="btn btn-outline">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i>
                Simpan Akun
            </button>

        </div>

    </form>

</section>

@endsection
