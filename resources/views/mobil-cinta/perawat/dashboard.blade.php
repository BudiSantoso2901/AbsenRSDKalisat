@extends('mobil-cinta.layouts.app')

@section('title', 'Dashboard Perawat')

@section('content')

    <div class="nurse-dashboard">

        {{-- HEADER --}}
        <div class="nurse-header">
            <div>
                <span class="nurse-header-label">
                    MOBIL CINTA
                </span>

                <h1>Dashboard Perawat</h1>

                <p>
                    Pengajuan layanan antar pulang pasien dan pemantauan perjalanan.
                </p>
            </div>
        </div>


        {{-- PESAN SUKSES --}}
        @if (session('status'))
            <div class="nurse-alert nurse-alert-success">
                <i class="bx bx-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif


        {{-- PESAN VALIDASI --}}
        @if ($errors->any())
            <div class="nurse-alert nurse-alert-error">
                <i class="bx bx-error-circle"></i>

                <div>
                    <strong>Periksa kembali data pengajuan.</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        {{-- FORM PENGAJUAN --}}
        <section class="nurse-card">

            <div class="nurse-card-heading">
                <div class="nurse-heading-icon">
                    <i class="bx bx-plus-circle"></i>
                </div>

                <div>
                    <h2>Form Pengajuan Perjalanan</h2>
                    <p>Lengkapi identitas pasien dan informasi tujuan perjalanan.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('mobil-cinta.perawat.perjalanan.store') }}" class="nurse-form">
                @csrf

                {{-- IDENTITAS PASIEN --}}
                <div class="form-section-title">
                    <i class="bx bx-user"></i>
                    <span>Identitas Pasien</span>
                </div>

                <div class="form-grid">

                    <div class="nurse-form-group">
                        <label for="ruangan_id">
                            Ruangan <span class="required">*</span>
                        </label>

                        <select name="ruangan_id" id="ruangan_id" required
                            class="@error('ruangan_id') input-error @enderror">
                            <option value="">Pilih ruangan</option>

                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ (string) old('ruangan_id') === (string) $room->id ? 'selected' : '' }}>
                                    {{ $room->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>

                        @error('ruangan_id')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="nurse-form-group">
                        <label for="nama_pasien">
                            Nama Pasien <span class="required">*</span>
                        </label>

                        <input type="text" name="nama_pasien" id="nama_pasien" value="{{ old('nama_pasien') }}"
                            placeholder="Masukkan nama lengkap pasien" maxlength="255" required
                            class="@error('nama_pasien') input-error @enderror">

                        @error('nama_pasien')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="nurse-form-group">
                        <label for="nomor_rekam_medis">
                            Nomor Rekam Medis <span class="required">*</span>
                        </label>

                        <input type="text" name="nomor_rekam_medis" id="nomor_rekam_medis"
                            value="{{ old('nomor_rekam_medis') }}" placeholder="Masukkan nomor rekam medis" maxlength="255"
                            required class="@error('nomor_rekam_medis') input-error @enderror">

                        @error('nomor_rekam_medis')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="nurse-form-group">
                        <label for="jenis_kelamin_pasien">
                            Jenis Kelamin <span class="required">*</span>
                        </label>

                        <select name="jenis_kelamin_pasien" id="jenis_kelamin_pasien" required
                            class="@error('jenis_kelamin_pasien') input-error @enderror">
                            <option value="">Pilih jenis kelamin</option>

                            <option value="L" {{ old('jenis_kelamin_pasien') === 'L' ? 'selected' : '' }}>
                                Laki-laki
                            </option>

                            <option value="P" {{ old('jenis_kelamin_pasien') === 'P' ? 'selected' : '' }}>
                                Perempuan
                            </option>
                        </select>

                        @error('jenis_kelamin_pasien')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>


                <div class="form-divider"></div>


                {{-- INFORMASI PERJALANAN --}}
                <div class="form-section-title">
                    <i class="bx bx-map"></i>
                    <span>Informasi Perjalanan</span>
                </div>

                <div class="form-grid">

                    <div class="nurse-form-group">
                        <label for="penjamin">
                            Penjamin <span class="required">*</span>
                        </label>

                        <select name="penjamin" id="penjamin" required class="@error('penjamin') input-error @enderror">
                            <option value="">Pilih penjamin</option>

                            <option value="UHC" {{ old('penjamin') === 'UHC' ? 'selected' : '' }}>
                                UHC
                            </option>

                            <option value="PBI" {{ old('penjamin') === 'PBI' ? 'selected' : '' }}>
                                PBI
                            </option>

                            <option value="NON PBI" {{ old('penjamin') === 'NON PBI' ? 'selected' : '' }}>
                                NON PBI
                            </option>
                        </select>

                        @error('penjamin')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="nurse-form-group">
                        <label for="tujuan">
                            Alamat <span class="required">*</span>
                        </label>

                        <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}"
                            placeholder="Masukkan alamat atau tujuan pasien" maxlength="255" required
                            class="@error('tujuan') input-error @enderror">

                        @error('tujuan')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="nurse-form-group form-full">
                        <label for="catatan">
                            Catatan Pengajuan <span class="required">*</span>
                        </label>

                        <textarea name="catatan" id="catatan" rows="4" maxlength="5000"
                            placeholder="Tambahkan informasi atau catatan terkait kebutuhan perjalanan pasien..." required
                            class="@error('catatan') input-error @enderror">{{ old('catatan') }}</textarea>

                        <div class="field-hint">
                            <i class="bx bx-info-circle"></i>
                            Tuliskan informasi tambahan yang diperlukan untuk penugasan perjalanan.
                        </div>

                        @error('catatan')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>


                {{-- TOMBOL --}}
                <div class="form-submit-area">

                    <button type="reset" class="btn-form-reset">
                        <i class="bx bx-refresh"></i>
                        Bersihkan
                    </button>

                    <button type="submit" class="btn-form-submit">
                        <i class="bx bx-send"></i>
                        Ajukan Perjalanan
                    </button>

                </div>

            </form>

        </section>


        {{-- PERJALANAN TERBARU --}}
        <section class="nurse-card recent-card">

            <div class="nurse-card-heading recent-heading">

                <div class="nurse-heading-icon">
                    <i class="bx bx-history"></i>
                </div>

                <div>
                    <h2>Perjalanan Terbaru</h2>
                    <p>Pantau status perjalanan yang telah diajukan.</p>
                </div>

                <a href="{{ route('mobil-cinta.perawat.riwayat') }}" class="btn-view-history">
                    Lihat Semua
                    <i class="bx bx-right-arrow-alt"></i>
                </a>

            </div>


            @if ($journeys->isNotEmpty())

                <div class="recent-journey-list">

                    @foreach ($journeys as $journey)
                        @php
                            $statusLabels = [
                                'diajukan' => 'Menunggu Penugasan',
                                'ditugaskan' => 'Sudah Ditugaskan',
                                'berangkat' => 'Dalam Perjalanan',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                                'dibatalkan' => 'Dibatalkan',
                            ];

                            $statusClasses = [
                                'diajukan' => 'journey-waiting',
                                'ditugaskan' => 'journey-assigned',
                                'berangkat' => 'journey-process',
                                'selesai' => 'journey-completed',
                                'ditolak' => 'journey-rejected',
                                'dibatalkan' => 'journey-cancelled',
                            ];

                            $date = $journey->diajukan_pada ?? $journey->created_at;
                        @endphp

                        <div class="recent-journey-item">

                            <div class="recent-journey-icon">
                                <i class="bx bx-car"></i>
                            </div>

                            <div class="recent-journey-info">

                                <strong>
                                    {{ $journey->nomor_perjalanan }}
                                </strong>

                                <span>
                                    {{ $journey->tujuan ?: 'Tujuan belum tersedia' }}
                                </span>

                                <small>
                                    <i class="bx bx-calendar"></i>
                                    {{ $date?->format('d/m/Y H:i') ?? '—' }}
                                </small>

                            </div>

                            <div class="recent-journey-status">

                                <span
                                    class="journey-status-badge {{ $statusClasses[$journey->status] ?? 'journey-default' }}">
                                    {{ $statusLabels[$journey->status] ?? ucfirst($journey->status) }}
                                </span>

                                @if ($journey->status === 'diajukan' && (int) $journey->diajukan_oleh_id === (int) auth('mobil_cinta')->id())
                                    <button type="button" class="btn-edit-recent" data-edit-journey
                                        data-update-url="{{ route('mobil-cinta.perawat.perjalanan.update', $journey) }}"
                                        data-nomor="{{ $journey->nomor_perjalanan }}"
                                        data-ruangan-id="{{ $journey->ruangan_id }}"
                                        data-nama-pasien="{{ $journey->nama_pasien }}"
                                        data-nomor-rekam-medis="{{ $journey->nomor_rekam_medis }}"
                                        data-jenis-kelamin="{{ $journey->jenis_kelamin_pasien }}"
                                        data-penjamin="{{ $journey->penjamin }}" data-tujuan="{{ $journey->tujuan }}"
                                        data-catatan="{{ $journey->catatan }}">
                                        <i class="bx bx-edit"></i>
                                        Edit
                                    </button>
                                @endif

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <div class="recent-empty">
                    <div class="recent-empty-icon">
                        <i class="bx bx-car"></i>
                    </div>

                    <strong>Belum Ada Pengajuan Perjalanan</strong>

                    <p>
                        Pengajuan perjalanan pasien yang dibuat akan tampil di sini.
                    </p>
                </div>

            @endif

        </section>

    </div>

    @include('mobil-cinta.perawat.perjalanan.modal-edit')

    <style>
        .nurse-dashboard {
            width: 100%;
            color: #304b3a;
        }

        /* HEADER */

        .nurse-header {
            margin-bottom: 22px;
        }

        .nurse-header-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 8px;
            color: #078b48;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .nurse-header h1 {
            margin: 0 0 6px;
            color: #174b32;
            font-size: 25px;
            font-weight: 800;
        }

        .nurse-header p {
            margin: 0;
            color: #819087;
            font-size: 12px;
        }


        /* ALERT */

        .nurse-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 15px;
            margin-bottom: 17px;
            border-radius: 10px;
            font-size: 12px;
        }

        .nurse-alert>i {
            font-size: 19px;
            flex-shrink: 0;
        }

        .nurse-alert-success {
            background: #eaf8ef;
            border: 1px solid #ccebd6;
            color: #087c40;
        }

        .nurse-alert-error {
            background: #fff5f4;
            border: 1px solid #f4d7d4;
            color: #a83d35;
        }

        .nurse-alert-error strong {
            display: block;
            margin-bottom: 5px;
        }

        .nurse-alert-error ul {
            margin: 0;
            padding-left: 17px;
            line-height: 1.7;
        }


        /* CARD */

        .nurse-card {
            padding: 22px;
            margin-bottom: 20px;
            background: #ffffff;
            border: 1px solid #e5eee7;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(20, 75, 45, 0.045);
        }

        .nurse-card-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 23px;
        }

        .nurse-heading-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #eaf8ef;
            color: #078b48;
            font-size: 23px;
        }

        .nurse-card-heading h2 {
            margin: 0 0 5px;
            color: #174b32;
            font-size: 16px;
            font-weight: 800;
        }

        .nurse-card-heading p {
            margin: 0;
            color: #87958b;
            font-size: 11px;
        }


        /* FORM */

        .nurse-form {
            width: 100%;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            color: #078b48;
            font-size: 12px;
            font-weight: 800;
        }

        .form-section-title i {
            font-size: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 17px;
        }

        .nurse-form-group {
            min-width: 0;
        }

        .nurse-form-group label {
            display: block;
            margin-bottom: 7px;
            color: #52665a;
            font-size: 11px;
            font-weight: 750;
        }

        .required {
            color: #d64b4b;
        }

        .nurse-form-group input,
        .nurse-form-group select,
        .nurse-form-group textarea {
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
            transition: border-color .2s, box-shadow .2s;
        }

        .nurse-form-group textarea {
            min-height: 105px;
            resize: vertical;
            line-height: 1.6;
        }

        .nurse-form-group input::placeholder,
        .nurse-form-group textarea::placeholder {
            color: #a5b0a8;
        }

        .nurse-form-group input:focus,
        .nurse-form-group select:focus,
        .nurse-form-group textarea:focus {
            border-color: #08a653;
            box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
        }

        .nurse-form-group .input-error {
            border-color: #d9534f;
            background: #fffafa;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .field-error {
            display: block;
            margin-top: 5px;
            color: #c44545;
            font-size: 10px;
        }

        .field-hint {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            margin-top: 7px;
            color: #87958b;
            font-size: 10px;
            line-height: 1.5;
        }

        .field-hint i {
            color: #08a653;
            font-size: 14px;
            flex-shrink: 0;
        }

        .form-divider {
            height: 1px;
            margin: 23px 0;
            background: #edf2ee;
        }

        .form-submit-area {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 23px;
            padding-top: 17px;
            border-top: 1px solid #edf2ee;
        }

        .btn-form-reset,
        .btn-form-submit {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            min-height: 41px;
            padding: 0 17px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 750;
            cursor: pointer;
            transition: .2s;
        }

        .btn-form-reset {
            color: #617268;
            background: #ffffff;
            border: 1px solid #dce8df;
        }

        .btn-form-reset:hover {
            background: #f3f8f4;
        }

        .btn-form-submit {
            color: #ffffff;
            background: #08a653;
            border: 1px solid #08a653;
        }

        .btn-form-submit:hover {
            background: #078b48;
            border-color: #078b48;
            box-shadow: 0 4px 10px rgba(8, 139, 72, .15);
        }

        .btn-form-reset i,
        .btn-form-submit i {
            font-size: 16px;
        }


        /* RECENT JOURNEY */

        .recent-card {
            padding-bottom: 12px;
        }

        .recent-heading {
            margin-bottom: 17px;
        }

        .btn-view-history {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: auto;
            padding: 8px 11px;
            border: 1px solid #dcefe1;
            border-radius: 7px;
            color: #078b48;
            background: #f0faf3;
            font-size: 10px;
            font-weight: 750;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-view-history:hover {
            background: #e1f4e7;
        }

        .recent-journey-list {
            display: flex;
            flex-direction: column;
        }

        .recent-journey-item {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 15px 3px;
            border-bottom: 1px solid #edf2ee;
        }

        .recent-journey-item:last-child {
            border-bottom: 0;
        }

        .recent-journey-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 41px;
            height: 41px;
            flex-shrink: 0;
            color: #078b48;
            background: #eaf8ef;
            border-radius: 10px;
            font-size: 21px;
        }

        .recent-journey-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .recent-journey-info strong {
            color: #315540;
            font-size: 12px;
            font-weight: 800;
        }

        .recent-journey-info span {
            color: #62756a;
            font-size: 11px;
            overflow-wrap: anywhere;
        }

        .recent-journey-info small {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #98a59c;
            font-size: 10px;
        }

        .recent-journey-status {
            margin-left: auto;
            flex-shrink: 0;
        }

        .journey-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750;
            white-space: nowrap;
        }

        .journey-waiting {
            color: #98701e;
            background: #fff5df;
        }

        .journey-assigned {
            color: #276b9c;
            background: #eaf5fc;
        }

        .journey-process {
            color: #2477a8;
            background: #e8f4fb;
        }

        .journey-completed {
            color: #078b48;
            background: #e8f7ed;
        }

        .journey-rejected {
            color: #b54848;
            background: #fff0f0;
        }

        .journey-cancelled {
            color: #6e7771;
            background: #eff1ef;
        }

        .journey-default {
            color: #53665a;
            background: #f0f3f0;
        }

        /* EDIT PASIEN */

        .btn-edit-recent {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 7px;
            padding: 6px 10px;
            border: 1px solid #dcefe1;
            border-radius: 7px;
            color: #078b48;
            background: #f0faf3;
            font-size: 10px;
            font-weight: 750;
            text-decoration: none;
        }

        .btn-edit-recent:hover {
            background: #e1f4e7;
        }

        .btn-edit-recent {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 7px;
            padding: 6px 10px;
            border: 1px solid #dcefe1;
            border-radius: 7px;
            color: #078b48;
            background: #f0faf3;
            font-size: 10px;
            font-weight: 750;
            cursor: pointer;
        }

        .btn-edit-recent:hover {
            background: #e1f4e7;
        }

        /* EMPTY STATE */

        .recent-empty {
            padding: 35px 15px;
            text-align: center;
        }

        .recent-empty-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 55px;
            height: 55px;
            margin: 0 auto 12px;
            color: #08a653;
            background: #eaf8ef;
            border-radius: 50%;
            font-size: 27px;
        }

        .recent-empty strong {
            display: block;
            margin-bottom: 6px;
            color: #315540;
            font-size: 13px;
        }

        .recent-empty p {
            margin: 0;
            color: #8a988f;
            font-size: 11px;
        }


        /* RESPONSIVE */

        @media (max-width: 700px) {
            .nurse-header h1 {
                font-size: 21px;
            }

            .nurse-card {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .form-full {
                grid-column: auto;
            }

            .recent-journey-item {
                flex-wrap: wrap;
            }

            .recent-journey-status {
                width: 100%;
                margin-left: 54px;
            }

            .form-submit-area {
                flex-direction: column-reverse;
            }

            .btn-form-reset,
            .btn-form-submit {
                width: 100%;
            }

            .nurse-card-heading h2 {
                font-size: 14px;
            }

            .nurse-card-heading p {
                font-size: 10px;
            }

            .btn-view-history {
                padding: 7px;
                font-size: 9px;
            }
        }
    </style>

@endsection
