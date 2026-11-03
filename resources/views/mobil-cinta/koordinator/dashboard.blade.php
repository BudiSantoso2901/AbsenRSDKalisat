@extends('mobil-cinta.layouts.app')

@section('title', 'Dashboard Koordinator')

@section('content')

    @php
        $pendingCount = $journeys->where('status', 'diajukan')->count();
        $assignedCount = $journeys->where('status', 'ditugaskan')->count();
        $totalCount = $journeys->count();
    @endphp

    <div class="coordinator-dashboard">

        {{-- HEADER --}}
        <div class="coordinator-header">
            <div>
                <span class="coordinator-label">
                    <i class="bx bx-car"></i>
                    MOBIL CINTA
                </span>

                <h1>Dashboard Koordinator</h1>

                <p>
                    Monitoring pengajuan dan penugasan layanan antar pulang pasien.
                </p>
            </div>

            <a href="{{ route('mobil-cinta.koordinator.riwayat') }}" class="btn-history">
                <i class="bx bx-history"></i>
                Riwayat Perjalanan
            </a>
        </div>


        {{-- RINGKASAN --}}
        <div class="coordinator-summary">

            <div class="coordinator-summary-card">
                <div class="summary-icon summary-total">
                    <i class="bx bx-list-ul"></i>
                </div>

                <div>
                    <span>Total Perjalanan Aktif</span>
                    <strong>{{ number_format($totalCount) }}</strong>
                </div>
            </div>

            <div class="coordinator-summary-card">
                <div class="summary-icon summary-pending">
                    <i class="bx bx-time-five"></i>
                </div>

                <div>
                    <span>Menunggu Penugasan</span>
                    <strong>{{ number_format($pendingCount) }}</strong>
                </div>
            </div>

            <div class="coordinator-summary-card">
                <div class="summary-icon summary-assigned">
                    <i class="bx bx-user-check"></i>
                </div>

                <div>
                    <span>Sudah Ditugaskan</span>
                    <strong>{{ number_format($assignedCount) }}</strong>
                </div>
            </div>

        </div>


        {{-- DAFTAR PERMINTAAN --}}
        <section class="coordinator-card">

            <div class="coordinator-section-header">
                <div>
                    <h2>Permintaan Perjalanan</h2>
                    <p>
                        Pengajuan dari Perawat yang membutuhkan penanganan Koordinator.
                    </p>
                </div>

                <span class="total-indicator">
                    {{ $totalCount }} Data
                </span>
            </div>


            {{-- FILTER STATUS --}}
            <div class="coordinator-filter">

                <button type="button" class="coordinator-filter-btn active" data-status-filter="diajukan">
                    Menunggu
                    <span>{{ $pendingCount }}</span>
                </button>

                <button type="button" class="coordinator-filter-btn" data-status-filter="ditugaskan">
                    Ditugaskan
                    <span>{{ $assignedCount }}</span>
                </button>

            </div>


            @forelse($journeys as $journey)
                @php
                    $isPending = $journey->status === 'diajukan';

                    $date = $journey->diajukan_pada ?? $journey->created_at;

                    $statusLabels = [
                        'diajukan' => 'Menunggu Penugasan',
                        'ditugaskan' => 'Sudah Ditugaskan',
                    ];

                    $statusClasses = [
                        'diajukan' => 'status-pending',
                        'ditugaskan' => 'status-assigned',
                    ];

                    $vehicleName = $vehicles->firstWhere('id', $journey->kendaraan_id)?->nomor_polisi ?? '—';
                @endphp

                <article class="journey-coordinator-item" data-journey-status="{{ $journey->status }}">

                    {{-- BARIS UTAMA --}}
                    <div class="journey-card-top">

                        <div class="journey-card-info">

                            <div class="journey-number-label">
                                {{ $journey->nomor_perjalanan }}
                            </div>

                            <h3>{{ $journey->nama_pasien }}</h3>

                            <div class="journey-card-meta">
                                <span>
                                    <i class="bx bx-map-pin"></i>
                                    {{ $journey->tujuan }}
                                </span>

                                <span>
                                    <i class="bx bx-user"></i>
                                    {{ $journey->requester?->nama ?? '—' }}
                                </span>

                                <span>
                                    <i class="bx bx-calendar"></i>
                                    {{ $date?->format('d/m/Y H:i') ?? '—' }}
                                </span>
                            </div>

                        </div>

                        <div class="journey-card-status">
                            <span class="coordinator-status {{ $statusClasses[$journey->status] ?? '' }}">
                                <span class="status-dot"></span>
                                {{ $statusLabels[$journey->status] ?? ucfirst($journey->status) }}
                            </span>
                        </div>

                    </div>


                    {{-- RINGKASAN PENUGASAN --}}
                    @if (!$isPending)
                        <div class="assignment-info">

                            <div>
                                <span>Sopir</span>
                                <strong>{{ $journey->nama_sopir ?? 'Belum ditentukan' }}</strong>
                            </div>

                            <div>
                                <span>Kendaraan</span>
                                <strong>{{ $vehicleName }}</strong>
                            </div>

                            <div>
                                <span>Waktu Penugasan</span>
                                <strong>
                                    {{ $journey->ditugaskan_pada?->format('d/m/Y H:i') ?? '—' }}
                                </strong>
                            </div>

                        </div>
                    @endif


                    {{-- AKSI --}}
                    <div class="journey-card-actions">

                        <button type="button" class="btn-show-detail" data-toggle-coordinator-detail
                            data-target="coordinator-detail-{{ $journey->id }}" aria-expanded="false">
                            <i class="bx bx-chevron-down"></i>
                            Detail Pasien
                        </button>


                        <div class="journey-main-actions">

                            @if ($isPending)
                                <button type="button" class="btn-assign" data-open-assign
                                    data-assignment-url="{{ route('mobil-cinta.koordinator.perjalanan.assign', $journey) }}"
                                    data-driver-id="" data-vehicle-id="" data-number="{{ $journey->nomor_perjalanan }}"
                                    data-patient="{{ $journey->nama_pasien }}">
                                    <i class="bx bx-user-check"></i>
                                    Tugaskan Sopir
                                </button>
                            @endif

                            @if ($isPending)
                                <button type="button" class="btn-reject" data-reject-journey
                                    data-reject-url="{{ route('mobil-cinta.koordinator.perjalanan.reject', $journey) }}"
                                    data-number="{{ $journey->nomor_perjalanan }}"
                                    data-patient="{{ $journey->nama_pasien }}">
                                    <i class="bx bx-x-circle"></i>
                                    Tolak
                                </button>
                            @endif

                        </div>

                    </div>


                    {{-- DETAIL PASIEN --}}
                    <div class="coordinator-detail" id="coordinator-detail-{{ $journey->id }}" hidden>

                        <div class="coordinator-detail-heading">
                            <i class="bx bx-detail"></i>
                            Informasi Lengkap Pasien
                        </div>

                        <div class="coordinator-detail-grid">

                            <div class="coordinator-detail-item">
                                <span>Nama Pasien</span>
                                <strong>{{ $journey->nama_pasien ?: '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Nomor Rekam Medis</span>
                                <strong>{{ $journey->nomor_rekam_medis ?: '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Jenis Kelamin</span>
                                <strong>
                                    {{ $journey->jenis_kelamin_pasien === 'L' ? 'Laki-laki' : ($journey->jenis_kelamin_pasien === 'P' ? 'Perempuan' : '—') }}
                                </strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Ruangan</span>
                                <strong>{{ $journey->room?->nama_ruangan ?? '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Penjamin</span>
                                <strong>{{ $journey->penjamin ?: '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Diajukan Oleh</span>
                                <strong>{{ $journey->requester?->nama ?? '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Waktu Pengajuan</span>
                                <strong>{{ $date?->format('d/m/Y H:i') ?? '—' }}</strong>
                            </div>

                            <div class="coordinator-detail-item">
                                <span>Tujuan Antar</span>
                                <strong>{{ $journey->tujuan ?: '—' }}</strong>
                            </div>

                        </div>

                        <div class="coordinator-detail-note">
                            <span>Catatan Pengajuan</span>
                            <p>{{ $journey->catatan ?: 'Tidak ada catatan tambahan.' }}</p>
                        </div>

                    </div>

                </article>

            @empty

                <div class="coordinator-empty">
                    <div class="coordinator-empty-icon">
                        <i class="bx bx-clipboard"></i>
                    </div>

                    <h3>Belum Ada Permintaan Perjalanan</h3>

                    <p>
                        Pengajuan perjalanan dari Perawat akan muncul di halaman ini.
                    </p>
                </div>
            @endforelse

            <div class="coordinator-filter-empty" id="coordinatorFilterEmpty" hidden>
                Tidak ada data pada filter status yang dipilih.
            </div>

        </section>

    </div>


    {{-- =====================================================
    MODAL PENUGASAN
===================================================== --}}

    <div class="assignment-modal" id="assignmentModal" hidden>

        <div class="assignment-backdrop" data-close-assignment></div>

        <div class="assignment-dialog">

            <div class="assignment-header">
                <div>
                    <span class="assignment-label">
                        <i class="bx bx-car"></i>
                        MOBIL CINTA
                    </span>

                    <h2>Penugasan Perjalanan</h2>
                    <p>Pilih sopir dan kendaraan untuk perjalanan pasien.</p>
                </div>

                <button type="button" class="assignment-close" data-close-assignment>
                    <i class="bx bx-x"></i>
                </button>
            </div>


            <form id="assignmentForm" method="POST" action="" data-swal-title="Konfirmasi Penugasan"
                data-swal-text="Apakah Anda yakin akan menyimpan penugasan sopir dan kendaraan ini?"
                data-swal-confirm-text="Ya, tugaskan" data-swal-cancel-text="Periksa kembali">
                @csrf

                <div class="assignment-body">

                    <div class="assignment-patient" id="assignmentPatientInfo">
                        Nomor Perjalanan
                    </div>

                    <div class="assignment-form-group">
                        <label for="assignmentDriver">
                            Pilih Sopir <span>*</span>
                        </label>

                        <select name="sopir_id" id="assignmentDriver" required>
                            <option value="">Pilih sopir</option>

                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">
                                    {{ $driver->nama }}
                                </option>
                            @endforeach

                        </select>
                    </div>


                    <div class="assignment-form-group">
                        <label for="assignmentVehicle">
                            Pilih Kendaraan <span>*</span>
                        </label>

                        <select name="kendaraan_id" id="assignmentVehicle" required>
                            <option value="">Pilih nomor polisi kendaraan</option>

                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->nomor_polisi }}
                                </option>
                            @endforeach

                        </select>
                    </div>


                    <div class="assignment-info-note">
                        <i class="bx bx-info-circle"></i>
                        Pastikan sopir dan kendaraan sudah sesuai sebelum penugasan disimpan.
                    </div>

                </div>


                <div class="assignment-footer">

                    <button type="button" class="btn-assignment-cancel" data-close-assignment>
                        Batal
                    </button>

                    <button type="submit" class="btn-assignment-save">
                        <i class="bx bx-check-circle"></i>
                        Simpan Penugasan
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- FORM PENOLAKAN --}}
    <form id="rejectJourneyForm" method="POST" action="" data-swal-confirm="false" hidden>
        @csrf
        <input type="hidden" name="catatan" id="rejectJourneyNote">
    </form>


    <style>
        .coordinator-dashboard {
            width: 100%;
            color: #304b3a;
        }

        /* HEADER */

        .coordinator-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .coordinator-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 7px;
            color: #078b48;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .coordinator-header h1 {
            margin: 0 0 6px;
            color: #174b32;
            font-size: 25px;
            font-weight: 800;
        }

        .coordinator-header p {
            margin: 0;
            color: #819087;
            font-size: 12px;
        }

        .btn-history {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #dcefe1;
            border-radius: 9px;
            color: #078b48;
            font-size: 11px;
            font-weight: 750;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-history:hover {
            background: #eaf8ef;
            color: #078b48;
        }


        /* SUMMARY */

        .coordinator-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .coordinator-summary-card {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 92px;
            padding: 17px;
            background: #fff;
            border: 1px solid #e7f0e9;
            border-radius: 13px;
            box-shadow: 0 4px 14px rgba(20, 75, 45, .045);
        }

        .summary-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 11px;
            font-size: 22px;
        }

        .summary-total {
            color: #078b48;
            background: #eaf8ef;
        }

        .summary-pending {
            color: #a87518;
            background: #fff6e5;
        }

        .summary-assigned {
            color: #2477a8;
            background: #eaf5fc;
        }

        .coordinator-summary-card span {
            display: block;
            margin-bottom: 5px;
            color: #7a8a80;
            font-size: 11px;
        }

        .coordinator-summary-card strong {
            color: #174b32;
            font-size: 23px;
            font-weight: 800;
        }


        /* MAIN CARD */

        .coordinator-card {
            padding: 21px;
            background: #fff;
            border: 1px solid #e7f0e9;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(20, 75, 45, .045);
        }

        .coordinator-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 17px;
        }

        .coordinator-section-header h2 {
            margin: 0 0 5px;
            color: #174b32;
            font-size: 17px;
            font-weight: 800;
        }

        .coordinator-section-header p {
            margin: 0;
            color: #87958b;
            font-size: 11px;
        }

        .total-indicator {
            padding: 6px 10px;
            border-radius: 20px;
            background: #eaf8ef;
            color: #078b48;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }


        /* STATUS FILTER */

        .coordinator-filter {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 17px;
            padding-bottom: 14px;
            border-bottom: 1px solid #edf2ee;
        }

        .coordinator-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border: 1px solid #e1ebe4;
            border-radius: 8px;
            background: #fff;
            color: #607167;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .coordinator-filter-btn span {
            min-width: 19px;
            padding: 2px 5px;
            border-radius: 10px;
            background: #f0f4f1;
            color: #62756a;
            font-size: 9px;
            text-align: center;
        }

        .coordinator-filter-btn.active {
            border-color: #08a653;
            background: #eaf8ef;
            color: #078b48;
        }

        .coordinator-filter-btn.active span {
            background: #08a653;
            color: #fff;
        }


        /* JOURNEY ITEM */

        .journey-coordinator-item {
            margin-bottom: 13px;
            padding: 17px;
            border: 1px solid #e7f0e9;
            border-radius: 12px;
            background: #fff;
            transition: border-color .2s;
        }

        .journey-coordinator-item:last-of-type {
            margin-bottom: 0;
        }

        .journey-coordinator-item:hover {
            border-color: #cce8d5;
        }

        .journey-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .journey-card-info {
            min-width: 0;
        }

        .journey-number-label {
            margin-bottom: 5px;
            color: #078b48;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .4px;
        }

        .journey-card-info h3 {
            margin: 0 0 8px;
            color: #274c35;
            font-size: 15px;
            font-weight: 800;
        }

        .journey-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px 15px;
        }

        .journey-card-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #829087;
            font-size: 10px;
        }

        .journey-card-meta i {
            color: #08a653;
            font-size: 14px;
        }

        .journey-card-status {
            flex-shrink: 0;
        }

        .coordinator-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750;
            white-space: nowrap;
        }

        .status-pending {
            background: #fff5df;
            color: #98701e;
        }

        .status-assigned {
            background: #eaf5fc;
            color: #276b9c;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }


        /* ASSIGNMENT SUMMARY */

        .assignment-info {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 15px;
            padding: 12px;
            border-radius: 9px;
            background: #f5faf6;
        }

        .assignment-info span {
            display: block;
            margin-bottom: 4px;
            color: #87958b;
            font-size: 10px;
        }

        .assignment-info strong {
            display: block;
            color: #315540;
            font-size: 11px;
            font-weight: 750;
            overflow-wrap: anywhere;
        }


        /* ACTION BUTTONS */

        .journey-card-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            padding-top: 13px;
            border-top: 1px solid #edf2ee;
        }

        .journey-main-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-show-detail,
        .btn-assign,
        .btn-reject {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 35px;
            padding: 7px 11px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 10px;
            font-weight: 750;
            cursor: pointer;
            transition: .2s;
        }

        .btn-show-detail {
            border: 1px solid #dcefe1;
            background: #f1faf4;
            color: #078b48;
        }

        .btn-show-detail:hover {
            background: #e2f4e8;
        }

        .btn-assign {
            border: 1px solid #08a653;
            background: #08a653;
            color: #fff;
        }

        .btn-assign:hover {
            background: #078b48;
        }

        .btn-reject {
            border: 1px solid #f0d9d7;
            background: #fff5f4;
            color: #b54848;
        }

        .btn-reject:hover {
            background: #ffe9e7;
        }


        /* DETAIL */

        .coordinator-detail[hidden] {
            display: none !important;
        }

        .coordinator-detail {
            margin-top: 15px;
            padding: 17px;
            border: 1px solid #e2eee5;
            border-radius: 10px;
            background: #f8fcf9;
        }

        .coordinator-detail-heading {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            color: #078b48;
            font-size: 12px;
            font-weight: 800;
        }

        .coordinator-detail-heading i {
            font-size: 17px;
        }

        .coordinator-detail-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .coordinator-detail-item {
            padding: 11px;
            border: 1px solid #e7f0e9;
            border-radius: 8px;
            background: #fff;
            min-width: 0;
        }

        .coordinator-detail-item span {
            display: block;
            margin-bottom: 6px;
            color: #87958b;
            font-size: 10px;
        }

        .coordinator-detail-item strong {
            display: block;
            color: #355441;
            font-size: 11px;
            font-weight: 750;
            overflow-wrap: anywhere;
        }

        .coordinator-detail-note {
            margin-top: 11px;
            padding: 12px;
            border: 1px solid #e7f0e9;
            border-radius: 8px;
            background: #fff;
        }

        .coordinator-detail-note span {
            display: block;
            margin-bottom: 5px;
            color: #6c8172;
            font-size: 10px;
            font-weight: 750;
        }

        .coordinator-detail-note p {
            margin: 0;
            color: #496050;
            font-size: 11px;
            line-height: 1.6;
            white-space: pre-line;
        }


        /* EMPTY */

        .coordinator-empty {
            padding: 55px 20px;
            text-align: center;
        }

        .coordinator-empty-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 58px;
            height: 58px;
            margin: 0 auto 13px;
            border-radius: 50%;
            background: #eaf8ef;
            color: #08a653;
            font-size: 28px;
        }

        .coordinator-empty h3 {
            margin: 0 0 7px;
            color: #315540;
            font-size: 14px;
        }

        .coordinator-empty p {
            margin: 0;
            color: #8a988f;
            font-size: 11px;
        }

        .coordinator-filter-empty {
            padding: 25px;
            text-align: center;
            color: #87958b;
            font-size: 12px;
        }

        .coordinator-filter-empty[hidden] {
            display: none !important;
        }


        /* ASSIGNMENT MODAL */

        .assignment-modal[hidden] {
            display: none !important;
        }

        .assignment-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .assignment-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 37, 25, .55);
            backdrop-filter: blur(3px);
        }

        .assignment-dialog {
            position: relative;
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e5eee7;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            animation: assignmentModalIn .22s ease;
        }

        @keyframes assignmentModalIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .assignment-header {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 20px;
            border-bottom: 1px solid #edf2ee;
        }

        .assignment-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
            color: #078b48;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .assignment-header h2 {
            margin: 0 0 5px;
            color: #174b32;
            font-size: 17px;
            font-weight: 800;
        }

        .assignment-header p {
            margin: 0;
            color: #87958b;
            font-size: 11px;
        }

        .assignment-close {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #e5eee7;
            border-radius: 8px;
            background: #fff;
            color: #718078;
            font-size: 21px;
            cursor: pointer;
        }

        .assignment-close:hover {
            background: #fff1f0;
            color: #b54848;
        }

        .assignment-body {
            padding: 20px;
        }

        .assignment-patient {
            margin-bottom: 18px;
            padding: 10px;
            border-radius: 8px;
            background: #eaf8ef;
            color: #078b48;
            font-size: 11px;
            font-weight: 800;
        }

        .assignment-form-group {
            margin-bottom: 16px;
        }

        .assignment-form-group label {
            display: block;
            margin-bottom: 7px;
            color: #52665a;
            font-size: 11px;
            font-weight: 750;
        }

        .assignment-form-group label span {
            color: #d64b4b;
        }

        .assignment-form-group select {
            width: 100%;
            min-height: 43px;
            padding: 10px 12px;
            border: 1px solid #dce8df;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #354b3d;
            font-family: inherit;
            font-size: 12px;
        }

        .assignment-form-group select:focus {
            border-color: #08a653;
            box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
        }

        .assignment-info-note {
            display: flex;
            gap: 7px;
            padding: 11px;
            border: 1px solid #dcefe1;
            border-radius: 8px;
            background: #f1faf4;
            color: #587461;
            font-size: 10px;
            line-height: 1.6;
        }

        .assignment-info-note i {
            flex-shrink: 0;
            color: #078b48;
            font-size: 15px;
        }

        .assignment-footer {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            padding: 14px 20px;
            border-top: 1px solid #edf2ee;
            background: #fbfdfb;
        }

        .btn-assignment-cancel,
        .btn-assignment-save {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            min-height: 39px;
            padding: 0 15px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 750;
            cursor: pointer;
        }

        .btn-assignment-cancel {
            border: 1px solid #dce8df;
            background: #fff;
            color: #617268;
        }

        .btn-assignment-save {
            border: 1px solid #08a653;
            background: #08a653;
            color: #fff;
        }

        .btn-assignment-save:hover {
            background: #078b48;
        }


        /* RESPONSIVE */

        @media (max-width: 900px) {
            .coordinator-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .coordinator-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .coordinator-header h1 {
                font-size: 21px;
            }

            .coordinator-summary {
                grid-template-columns: 1fr;
                gap: 9px;
            }

            .coordinator-summary-card {
                min-height: 75px;
                padding: 12px;
            }

            .coordinator-card {
                padding: 14px;
            }

            .journey-coordinator-item {
                padding: 13px;
            }

            .journey-card-top {
                flex-direction: column;
            }

            .journey-card-status {
                align-self: flex-start;
            }

            .assignment-info {
                grid-template-columns: 1fr;
            }

            .journey-card-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .journey-main-actions {
                width: 100%;
            }

            .journey-main-actions button {
                flex: 1;
            }

            .coordinator-detail-grid {
                grid-template-columns: 1fr;
            }

            .coordinator-section-header h2 {
                font-size: 15px;
            }

            .assignment-footer {
                flex-direction: column-reverse;
            }

            .assignment-footer button {
                width: 100%;
            }
        }
    </style>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | DETAIL PERJALANAN
                |--------------------------------------------------------------------------
                */

                document.addEventListener('click', function(event) {

                    const button = event.target.closest('[data-toggle-coordinator-detail]');

                    if (!button) return;

                    const targetId = button.dataset.target;
                    const detail = document.getElementById(targetId);

                    if (!detail) return;

                    const opening = detail.hidden;

                    detail.hidden = !opening;

                    button.setAttribute(
                        'aria-expanded',
                        opening ? 'true' : 'false'
                    );

                    button.innerHTML = opening ?
                        '<i class="bx bx-chevron-up"></i> Tutup Detail' :
                        '<i class="bx bx-chevron-down"></i> Detail Pasien';
                });


                /*
                |--------------------------------------------------------------------------
                | FILTER STATUS
                |--------------------------------------------------------------------------
                */

                const filterButtons = document.querySelectorAll('[data-status-filter]');
                const journeyItems = document.querySelectorAll('[data-journey-status]');
                const filterEmpty = document.getElementById('coordinatorFilterEmpty');

                function applyCoordinatorFilter(selectedStatus) {

                    let visibleCount = 0;

                    filterButtons.forEach(function(button) {
                        button.classList.toggle(
                            'active',
                            button.dataset.statusFilter === selectedStatus
                        );
                    });

                    journeyItems.forEach(function(item) {

                        const matches = item.dataset.journeyStatus === selectedStatus;

                        item.hidden = !matches;

                        if (matches) {
                            visibleCount++;
                        }

                    });

                    if (filterEmpty) {
                        filterEmpty.hidden = visibleCount > 0;

                        filterEmpty.textContent =
                            selectedStatus === 'diajukan' ?
                            'Tidak ada permintaan yang menunggu penugasan.' :
                            'Tidak ada perjalanan yang sudah ditugaskan.';
                    }
                }

                filterButtons.forEach(function(button) {

                    button.addEventListener('click', function() {
                        applyCoordinatorFilter(button.dataset.statusFilter);
                    });

                });

                // Tampilan awal langsung menampilkan pengajuan Menunggu.
                applyCoordinatorFilter('diajukan');

                /*
                |--------------------------------------------------------------------------
                | MODAL PENUGASAN
                |--------------------------------------------------------------------------
                */

                const assignmentModal = document.getElementById('assignmentModal');
                const assignmentForm = document.getElementById('assignmentForm');

                const assignmentDriver = document.getElementById('assignmentDriver');
                const assignmentVehicle = document.getElementById('assignmentVehicle');
                const assignmentPatientInfo = document.getElementById('assignmentPatientInfo');

                function openAssignmentModal(button) {

                    const url = button.dataset.assignmentUrl;

                    if (!url || url.trim() === '') {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'URL penugasan tidak tersedia',
                                confirmButtonColor: '#08A653'
                            });
                        }

                        return;
                    }

                    const targetUrl = new URL(url, window.location.origin);

                    if (
                        targetUrl.origin !== window.location.origin ||
                        !targetUrl.pathname.includes('/koordinator/perjalanan/')
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'URL penugasan tidak valid',
                            confirmButtonColor: '#08A653'
                        });

                        return;
                    }

                    assignmentForm.action = targetUrl.href;

                    assignmentDriver.value = button.dataset.driverId || '';
                    assignmentVehicle.value = button.dataset.vehicleId || '';

                    assignmentPatientInfo.textContent =
                        button.dataset.number + ' · ' + button.dataset.patient;

                    assignmentModal.hidden = false;
                    document.body.style.overflow = 'hidden';
                }

                function closeAssignmentModal() {

                    assignmentModal.hidden = true;
                    document.body.style.overflow = '';
                    assignmentForm.action = '';
                }

                document.addEventListener('click', function(event) {

                    const button = event.target.closest('[data-open-assign]');

                    if (button) {
                        event.preventDefault();
                        openAssignmentModal(button);
                        return;
                    }

                    if (event.target.closest('[data-close-assignment]')) {
                        closeAssignmentModal();
                    }

                });


                /*
                |--------------------------------------------------------------------------
                | PENOLAKAN PERMINTAAN
                |--------------------------------------------------------------------------
                */

                const rejectForm = document.getElementById('rejectJourneyForm');
                const rejectNote = document.getElementById('rejectJourneyNote');

                document.addEventListener('click', function(event) {

                    const button = event.target.closest('[data-reject-journey]');

                    if (!button) return;

                    const rejectUrl = button.dataset.rejectUrl;
                    const number = button.dataset.number;
                    const patient = button.dataset.patient;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Tolak Permintaan?',
                        html: `
                <div style="font-size:12px;color:#62756A;margin-bottom:12px">
                    ${escapeHtml(number)} · ${escapeHtml(patient)}
                </div>
            `,
                        input: 'textarea',
                        inputLabel: 'Alasan Penolakan',
                        inputPlaceholder: 'Tuliskan alasan penolakan...',
                        inputAttributes: {
                            maxlength: 5000,
                            rows: 4
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tolak Permintaan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#c44747',
                        cancelButtonColor: '#7b8580',
                        reverseButtons: true,
                        allowOutsideClick: false
                    }).then(function(result) {

                        if (!result.isConfirmed) return;

                        if (!rejectUrl) {
                            Swal.fire({
                                icon: 'error',
                                title: 'URL penolakan tidak tersedia',
                                confirmButtonColor: '#08A653'
                            });

                            return;
                        }

                        rejectForm.action = rejectUrl;
                        rejectNote.value = result.value || '';

                        HTMLFormElement.prototype.submit.call(rejectForm);
                    });

                });


                function escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>"']/g, function(char) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        } [char];
                    });
                }


                /*
                |--------------------------------------------------------------------------
                | TUTUP MODAL DENGAN ESCAPE
                |--------------------------------------------------------------------------
                */

                document.addEventListener('keydown', function(event) {

                    if (event.key === 'Escape' && assignmentModal && !assignmentModal.hidden) {
                        closeAssignmentModal();
                    }

                });

            });
        </script>
    @endpush

@endsection
