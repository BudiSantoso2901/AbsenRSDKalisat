
@extends('mobil-cinta.layouts.app')

@section('title', 'Edit Pengajuan Perjalanan')

@section('content')

<div class="edit-journey-page">

    <div class="edit-header">
        <div>
            <span class="edit-header-label">
                <i class="bx bx-edit"></i>
                MOBIL CINTA
            </span>

            <h1>Edit Pengajuan Perjalanan</h1>

            <p>Perbarui informasi pengajuan perjalanan pasien.</p>
        </div>

        <a href="{{ route('mobil-cinta.perawat.riwayat') }}" class="btn-back">
            <i class="bx bx-arrow-back"></i>
            Kembali
        </a>
    </div>


    @if($errors->any())
        <div class="edit-alert">
            <i class="bx bx-error-circle"></i>

            <div>
                <strong>Periksa kembali data pengajuan.</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    <section class="edit-card">

        <div class="edit-card-heading">
            <div class="edit-icon">
                <i class="bx bx-file"></i>
            </div>

            <div>
                <h2>Informasi Pengajuan</h2>
                <p>Nomor perjalanan: <strong>{{ $journey->nomor_perjalanan }}</strong></p>
            </div>
        </div>


        <form
            method="POST"
            action="{{ route('mobil-cinta.perawat.perjalanan.update', $journey) }}"
        >
            @csrf
            @method('PUT')


            <div class="edit-section-title">
                <i class="bx bx-user"></i>
                Identitas Pasien
            </div>


            <div class="edit-grid">

                <div class="edit-form-group">
                    <label for="ruangan_id">
                        Ruangan <span>*</span>
                    </label>

                    <select name="ruangan_id" id="ruangan_id" required>
                        <option value="">Pilih ruangan</option>

                        @foreach($rooms as $room)
                            <option
                                value="{{ $room->id }}"
                                {{ (string) old('ruangan_id', $journey->ruangan_id) === (string) $room->id ? 'selected' : '' }}
                            >
                                {{ $room->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>

                    @error('ruangan_id')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <div class="edit-form-group">
                    <label for="nama_pasien">
                        Nama Pasien <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="nama_pasien"
                        id="nama_pasien"
                        value="{{ old('nama_pasien', $journey->nama_pasien) }}"
                        maxlength="255"
                        required
                    >

                    @error('nama_pasien')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <div class="edit-form-group">
                    <label for="nomor_rekam_medis">
                        Nomor Rekam Medis <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="nomor_rekam_medis"
                        id="nomor_rekam_medis"
                        value="{{ old('nomor_rekam_medis', $journey->nomor_rekam_medis) }}"
                        maxlength="255"
                        required
                    >

                    @error('nomor_rekam_medis')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <div class="edit-form-group">
                    <label for="jenis_kelamin_pasien">
                        Jenis Kelamin <span>*</span>
                    </label>

                    <select
                        name="jenis_kelamin_pasien"
                        id="jenis_kelamin_pasien"
                        required
                    >
                        <option value="">Pilih jenis kelamin</option>

                        <option
                            value="L"
                            {{ old('jenis_kelamin_pasien', $journey->jenis_kelamin_pasien) === 'L' ? 'selected' : '' }}
                        >
                            Laki-laki
                        </option>

                        <option
                            value="P"
                            {{ old('jenis_kelamin_pasien', $journey->jenis_kelamin_pasien) === 'P' ? 'selected' : '' }}
                        >
                            Perempuan
                        </option>
                    </select>

                    @error('jenis_kelamin_pasien')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

            </div>


            <div class="edit-divider"></div>


            <div class="edit-section-title">
                <i class="bx bx-map"></i>
                Informasi Perjalanan
            </div>


            <div class="edit-grid">

                <div class="edit-form-group">
                    <label for="penjamin">
                        Penjamin <span>*</span>
                    </label>

                    <select name="penjamin" id="penjamin" required>
                        <option value="">Pilih penjamin</option>

                        <option
                            value="UHC"
                            {{ old('penjamin', $journey->penjamin) === 'UHC' ? 'selected' : '' }}
                        >
                            UHC
                        </option>

                        <option
                            value="PBI"
                            {{ old('penjamin', $journey->penjamin) === 'PBI' ? 'selected' : '' }}
                        >
                            PBI
                        </option>

                        <option
                            value="NON PBI"
                            {{ old('penjamin', $journey->penjamin) === 'NON PBI' ? 'selected' : '' }}
                        >
                            NON PBI
                        </option>
                    </select>

                    @error('penjamin')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <div class="edit-form-group">
                    <label for="tujuan">
                        Tujuan Antar <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="tujuan"
                        id="tujuan"
                        value="{{ old('tujuan', $journey->tujuan) }}"
                        maxlength="255"
                        required
                    >

                    @error('tujuan')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <div class="edit-form-group edit-form-full">
                    <label for="catatan">
                        Catatan Pengajuan <span>*</span>
                    </label>

                    <textarea
                        name="catatan"
                        id="catatan"
                        rows="4"
                        maxlength="5000"
                        required
                    >{{ old('catatan', $journey->catatan) }}</textarea>

                    @error('catatan')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

            </div>


            <div class="edit-note">
                <i class="bx bx-info-circle"></i>

                <span>
                    Nomor perjalanan dan identitas pengaju tidak dapat diubah.
                    Pengajuan hanya dapat diedit selama belum ditugaskan.
                </span>
            </div>


            <div class="edit-actions">

                <a
                    href="{{ route('mobil-cinta.perawat.riwayat') }}"
                    class="btn-edit-cancel"
                >
                    Batal
                </a>

                <button type="submit" class="btn-edit-save">
                    <i class="bx bx-save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </section>

</div>


<style>
    .edit-journey-page {
        width: 100%;
        color: #304b3a;
    }

    .edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 22px;
    }

    .edit-header-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 7px;
        color: #078b48;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .edit-header h1 {
        margin: 0 0 6px;
        color: #174b32;
        font-size: 25px;
        font-weight: 800;
    }

    .edit-header p {
        margin: 0;
        color: #819087;
        font-size: 12px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 13px;
        border: 1px solid #dce8df;
        border-radius: 8px;
        color: #52665a;
        background: #ffffff;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-back:hover {
        background: #edf8f0;
        color: #078b48;
    }

    .edit-alert {
        display: flex;
        gap: 10px;
        padding: 13px 15px;
        margin-bottom: 17px;
        border: 1px solid #f4d7d4;
        border-radius: 10px;
        color: #a83d35;
        background: #fff5f4;
        font-size: 12px;
    }

    .edit-alert > i {
        font-size: 19px;
    }

    .edit-alert ul {
        margin: 5px 0 0;
        padding-left: 18px;
        line-height: 1.7;
    }

    .edit-card {
        padding: 22px;
        background: #ffffff;
        border: 1px solid #e5eee7;
        border-radius: 15px;
        box-shadow: 0 4px 16px rgba(20, 75, 45, .045);
    }

    .edit-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 23px;
    }

    .edit-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        border-radius: 11px;
        background: #eaf8ef;
        color: #078b48;
        font-size: 23px;
    }

    .edit-card-heading h2 {
        margin: 0 0 5px;
        color: #174b32;
        font-size: 16px;
        font-weight: 800;
    }

    .edit-card-heading p {
        margin: 0;
        color: #87958b;
        font-size: 11px;
    }

    .edit-card-heading p strong {
        color: #078b48;
    }

    .edit-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
        color: #078b48;
        font-size: 12px;
        font-weight: 800;
    }

    .edit-section-title i {
        font-size: 18px;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px;
    }

    .edit-form-group {
        min-width: 0;
    }

    .edit-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #52665a;
        font-size: 11px;
        font-weight: 750;
    }

    .edit-form-group label span {
        color: #d64b4b;
    }

    .edit-form-group input,
    .edit-form-group select,
    .edit-form-group textarea {
        display: block;
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #dce8df;
        border-radius: 8px;
        outline: none;
        background: #ffffff;
        color: #354b3d;
        font-family: inherit;
        font-size: 12px;
        transition: .2s;
    }

    .edit-form-group textarea {
        min-height: 105px;
        resize: vertical;
        line-height: 1.6;
    }

    .edit-form-group input:focus,
    .edit-form-group select:focus,
    .edit-form-group textarea:focus {
        border-color: #08a653;
        box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
    }

    .edit-form-group small {
        display: block;
        margin-top: 5px;
        color: #c44545;
        font-size: 10px;
    }

    .edit-form-full {
        grid-column: 1 / -1;
    }

    .edit-divider {
        height: 1px;
        margin: 23px 0;
        background: #edf2ee;
    }

    .edit-note {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 20px;
        padding: 12px;
        border: 1px solid #dcefe1;
        border-radius: 8px;
        background: #f1faf4;
        color: #587461;
        font-size: 11px;
        line-height: 1.6;
    }

    .edit-note i {
        color: #078b48;
        font-size: 16px;
        flex-shrink: 0;
    }

    .edit-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 22px;
        padding-top: 17px;
        border-top: 1px solid #edf2ee;
    }

    .btn-edit-cancel,
    .btn-edit-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 41px;
        padding: 0 17px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-edit-cancel {
        color: #617268;
        background: #ffffff;
        border: 1px solid #dce8df;
    }

    .btn-edit-save {
        color: #ffffff;
        background: #08a653;
        border: 1px solid #08a653;
    }

    .btn-edit-save:hover {
        background: #078b48;
        border-color: #078b48;
    }

    @media (max-width: 700px) {
        .edit-header {
            align-items: flex-start;
        }

        .edit-header h1 {
            font-size: 21px;
        }

        .edit-card {
            padding: 15px;
        }

        .edit-grid {
            grid-template-columns: 1fr;
        }

        .edit-form-full {
            grid-column: auto;
        }

        .edit-actions {
            flex-direction: column-reverse;
        }

        .btn-edit-cancel,
        .btn-edit-save {
            width: 100%;
        }
    }
</style>

@endsection
