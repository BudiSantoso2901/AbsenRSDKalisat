@extends('mobil-cinta.layouts.app')

@section('title', 'Riwayat Perjalanan')

@section('content')

    <div class="journey-history-page">

        {{-- HEADER --}}
        <div class="history-header">
            <div>
                <h1>Riwayat Perjalanan</h1>
                <p>
                    Monitoring data perjalanan Mobil Cinta berdasarkan
                    pengajuan dan status perjalanan.
                </p>
            </div>
        </div>


        {{-- RINGKASAN --}}
        <div class="history-summary">

            <div class="summary-card">
                <div class="summary-icon summary-icon-total">
                    <i class="bx bx-car"></i>
                </div>

                <div>
                    <span>Total Perjalanan</span>
                    <strong>{{ number_format($summary['total']) }}</strong>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon summary-icon-waiting">
                    <i class="bx bx-time-five"></i>
                </div>

                <div>
                    <span>Menunggu Penugasan</span>
                    <strong>{{ number_format($summary['diajukan']) }}</strong>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon summary-icon-process">
                    <i class="bx bx-loader-circle"></i>
                </div>

                <div>
                    <span>Dalam Proses</span>
                    <strong>{{ number_format($summary['proses']) }}</strong>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon summary-icon-complete">
                    <i class="bx bx-check-circle"></i>
                </div>

                <div>
                    <span>Selesai</span>
                    <strong>{{ number_format($summary['selesai']) }}</strong>
                </div>
            </div>

        </div>


        {{-- FILTER --}}
        <section class="history-card">

            <div class="history-section-heading">
                <div>
                    <h2>Daftar Perjalanan</h2>
                    <p>Gunakan pencarian dan filter untuk menemukan data perjalanan.</p>
                </div>
            </div>

            <form action="{{ url()->current() }}" method="GET" class="history-filter-form">

                <div class="filter-field filter-search">
                    <label for="historySearch">Pencarian</label>

                    <div class="search-input-wrap">
                        <i class="bx bx-search"></i>

                        <input type="text" id="historySearch" name="q" value="{{ $filters['q'] }}"
                            placeholder="Nomor perjalanan, pasien, RM, tujuan, atau sopir...">
                    </div>
                </div>

                <div class="filter-field date-range-field">
                    <label for="historyDateRangeDisplay">Rentang Tanggal</label>

                    <div class="date-range-input-wrap">
                        <input type="text" id="historyDateRangeDisplay" placeholder="Pilih tanggal mulai dan sampai"
                            readonly>

                        <button type="button" id="historyDateRangeToggle" class="date-range-toggle"
                            aria-label="Buka kalender">
                            <i class="bx bx-calendar"></i>
                        </button>
                    </div>

                    <div class="date-range-calendar" id="historyDateRangeCalendar" hidden>

                        <div class="calendar-header">
                            <button type="button" id="historyCalendarPrev" class="calendar-nav">
                                <i class="bx bx-chevron-left"></i>
                            </button>

                            <strong id="historyCalendarMonthTitle"></strong>

                            <button type="button" id="historyCalendarNext" class="calendar-nav">
                                <i class="bx bx-chevron-right"></i>
                            </button>
                        </div>

                        <div class="calendar-weekdays">
                            <span>Sen</span>
                            <span>Sel</span>
                            <span>Rab</span>
                            <span>Kam</span>
                            <span>Jum</span>
                            <span>Sab</span>
                            <span>Min</span>
                        </div>

                        <div class="calendar-days" id="historyCalendarDays"></div>

                        <div class="calendar-footer">
                            <span id="historyCalendarSelectionInfo">
                                Klik tanggal pertama untuk mulai.
                            </span>

                            <div class="calendar-footer-actions">
                                <button type="button" id="historyCalendarClear">
                                    Reset
                                </button>

                                <button type="button" id="historyCalendarConfirm" class="calendar-confirm" disabled>
                                    OK
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Nilai yang dikirim ke controller --}}
                    <input type="hidden" name="from" id="historyDateFrom" value="{{ $filters['from'] }}">

                    <input type="hidden" name="to" id="historyDateTo" value="{{ $filters['to'] }}">
                </div>


                <div class="filter-field">
                    <label for="historyStatus">Status Perjalanan</label>

                    <select id="historyStatus" name="status">
                        <option value="">Semua Status</option>

                        <option value="diajukan" {{ $filters['status'] === 'diajukan' ? 'selected' : '' }}>
                            Diajukan
                        </option>

                        <option value="ditugaskan" {{ $filters['status'] === 'ditugaskan' ? 'selected' : '' }}>
                            Ditugaskan
                        </option>

                        <option value="berangkat" {{ $filters['status'] === 'berangkat' ? 'selected' : '' }}>
                            Berangkat
                        </option>

                        <option value="selesai" {{ $filters['status'] === 'selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>

                        <option value="ditolak" {{ $filters['status'] === 'ditolak' ? 'selected' : '' }}>
                            Ditolak
                        </option>

                        <option value="dibatalkan" {{ $filters['status'] === 'dibatalkan' ? 'selected' : '' }}>
                            Dibatalkan
                        </option>
                    </select>
                </div>


                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="bx bx-filter-alt"></i>
                        Terapkan
                    </button>

                    <a href="{{ url()->current() }}" class="btn-reset-filter">
                        <i class="bx bx-refresh"></i>
                        Reset
                    </a>
                </div>

            </form>


            {{-- INFORMASI HASIL --}}
            <div class="history-result-info">
                <span>
                    Menampilkan
                    <strong>{{ $journeys->firstItem() ?? 0 }}–{{ $journeys->lastItem() ?? 0 }}</strong>
                    dari
                    <strong>{{ number_format($journeys->total()) }}</strong>
                    data perjalanan
                </span>
            </div>


            {{-- TABEL --}}
            <div class="history-table-wrap">

                <table class="history-table">

                    <thead>
                        <tr>
                            <th class="column-number">No.</th>
                            <th>Nomor Perjalanan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Pasien</th>
                            <th>Tujuan</th>
                            <th>Sopir</th>
                            <th>Kendaraan</th>
                            <th>Status</th>
                            <th class="column-action">Detail</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($journeys as $index => $journey)
                            @php
                                $statusLabels = [
                                    'diajukan' => 'Diajukan',
                                    'ditugaskan' => 'Ditugaskan',
                                    'berangkat' => 'Berangkat',
                                    'selesai' => 'Selesai',
                                    'ditolak' => 'Ditolak',
                                    'dibatalkan' => 'Dibatalkan',
                                ];

                                $statusClass = [
                                    'diajukan' => 'status-waiting',
                                    'ditugaskan' => 'status-assigned',
                                    'berangkat' => 'status-process',
                                    'selesai' => 'status-completed',
                                    'ditolak' => 'status-rejected',
                                    'dibatalkan' => 'status-cancelled',
                                ];

                                $tanggalPengajuan = $journey->diajukan_pada ?? $journey->created_at;

                                $namaSopir = $journey->nama_sopir ?: $journey->driver?->nama ?? '—';

                                $kilometerBerangkat = $journey->kilometer_berangkat;
                                $kilometerSampai = $journey->kilometer_sampai;

                                $jarakTempuh =
                                    $kilometerBerangkat !== null && $kilometerSampai !== null
                                        ? (float) $kilometerSampai - (float) $kilometerBerangkat
                                        : null;
                            @endphp

                            {{-- BARIS UTAMA --}}
                            <tr class="history-main-row">

                                <td class="column-number">
                                    {{ $journeys->firstItem() + $index }}
                                </td>

                                <td>
                                    <strong class="journey-number">
                                        {{ $journey->nomor_perjalanan }}
                                    </strong>
                                </td>

                                <td>
                                    <span class="date-main">
                                        {{ $tanggalPengajuan?->format('d/m/Y') ?? '—' }}
                                    </span>

                                    <small class="date-time">
                                        {{ $tanggalPengajuan?->format('H:i') ?? '' }}
                                    </small>
                                </td>

                                <td>
                                    <strong>{{ $journey->nama_pasien ?: '—' }}</strong>

                                    @if ($journey->nomor_rekam_medis)
                                        <small class="table-subtext">
                                            RM: {{ $journey->nomor_rekam_medis }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $journey->tujuan ?: '—' }}
                                </td>

                                <td>
                                    {{ $namaSopir }}
                                </td>

                                <td>
                                    {{ $journey->vehicle?->nomor_polisi ?? '—' }}
                                </td>

                                <td>
                                    <span class="status-badge {{ $statusClass[$journey->status] ?? 'status-default' }}">
                                        {{ $statusLabels[$journey->status] ?? ucfirst($journey->status) }}
                                    </span>
                                </td>

                                <td class="column-action">

                                    <div class="history-action-buttons">

                                        {{-- EDIT PENUGASAN KHUSUS KOORDINATOR --}}
                                        @if (auth('mobil_cinta')->user()?->role?->nama_peran === 'koordinator' &&
                                                in_array($journey->status, ['ditugaskan', 'ditolak'], true))
                                            <button type="button" class="btn-detail btn-coordinator-assignment"
                                                data-open-coordinator-assignment
                                                data-assignment-url="{{ route('mobil-cinta.koordinator.perjalanan.assign', $journey) }}"
                                                data-number="{{ $journey->nomor_perjalanan }}"
                                                data-patient="{{ $journey->nama_pasien }}"
                                                data-driver-id="{{ $journey->sopir_id }}"
                                                data-vehicle-id="{{ $journey->kendaraan_id }}">
                                                <i class="bx bx-edit"></i>

                                                {{ $journey->status === 'ditolak' ? 'Edit' : 'Edit' }}
                                            </button>
                                        @endif


                                        {{-- EDIT PENGAJUAN KHUSUS PERAWAT --}}
                                        @if (auth('mobil_cinta')->user()?->role?->nama_peran === 'perawat' &&
                                                (int) $journey->diajukan_oleh_id === (int) auth('mobil_cinta')->id() &&
                                                $journey->status === 'diajukan')
                                            <button type="button" class="btn-detail btn-edit-history" data-edit-journey
                                                data-update-url="{{ route('mobil-cinta.perawat.perjalanan.update', $journey) }}"
                                                data-nomor="{{ $journey->nomor_perjalanan }}"
                                                data-ruangan-id="{{ $journey->ruangan_id }}"
                                                data-nama-pasien="{{ $journey->nama_pasien }}"
                                                data-nomor-rekam-medis="{{ $journey->nomor_rekam_medis }}"
                                                data-jenis-kelamin="{{ $journey->jenis_kelamin_pasien }}"
                                                data-penjamin="{{ $journey->penjamin }}"
                                                data-tujuan="{{ $journey->tujuan }}"
                                                data-catatan="{{ $journey->catatan }}">
                                                <i class="bx bx-edit"></i>
                                                Edit
                                            </button>
                                        @endif


                                        {{-- DETAIL --}}
                                        <button type="button" class="btn-detail" data-toggle-detail
                                            data-target="journey-detail-{{ $journey->id }}" aria-expanded="false">
                                            <i class="bx bx-chevron-down"></i>
                                            Detail
                                        </button>

                                    </div>

                                </td>

                            </tr>


                            {{-- BARIS DETAIL --}}
                            <tr id="journey-detail-{{ $journey->id }}" class="history-detail-row" hidden>
                                <td colspan="9">

                                    <div class="journey-detail-content">

                                        <div class="detail-heading">
                                            <div>
                                                <h3>Detail Perjalanan</h3>
                                                <p>{{ $journey->nomor_perjalanan }}</p>
                                            </div>
                                        </div>


                                        <div class="detail-grid">

                                            <div class="detail-item">
                                                <span>Nama Pasien</span>
                                                <strong>{{ $journey->nama_pasien ?: '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Nomor Rekam Medis</span>
                                                <strong>{{ $journey->nomor_rekam_medis ?: '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Jenis Kelamin</span>
                                                <strong>{{ $journey->jenis_kelamin_pasien ?: '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Ruangan</span>
                                                <strong>{{ $journey->room?->nama_ruangan ?? '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Penjamin</span>
                                                <strong>{{ $journey->penjamin ?: '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Tujuan</span>
                                                <strong>{{ $journey->tujuan ?: '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Diajukan Oleh</span>
                                                <strong>{{ $journey->requester?->nama ?? '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Waktu Pengajuan</span>
                                                <strong>
                                                    {{ $tanggalPengajuan?->format('d/m/Y H:i') ?? '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Sopir</span>
                                                <strong>{{ $namaSopir }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Kendaraan</span>
                                                <strong>{{ $journey->vehicle?->nomor_polisi ?? '—' }}</strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Waktu Penugasan</span>
                                                <strong>
                                                    {{ $journey->ditugaskan_pada?->format('d/m/Y H:i') ?? '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Status</span>
                                                <strong>
                                                    {{ $statusLabels[$journey->status] ?? ucfirst($journey->status) }}
                                                </strong>
                                            </div>

                                        </div>


                                        <div class="detail-divider"></div>


                                        <div class="detail-heading">
                                            <div>
                                                <h3>Informasi Perjalanan</h3>
                                                <p>Data keberangkatan dan kedatangan kendaraan.</p>
                                            </div>
                                        </div>


                                        <div class="detail-grid">

                                            <div class="detail-item">
                                                <span>KM Berangkat</span>
                                                <strong>
                                                    {{ $kilometerBerangkat !== null ? number_format((float) $kilometerBerangkat, 0, ',', '.') . ' km' : '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Waktu Berangkat</span>
                                                <strong>
                                                    {{ $journey->waktu_berangkat?->format('d/m/Y H:i') ?? '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>KM Sampai</span>
                                                <strong>
                                                    {{ $kilometerSampai !== null ? number_format((float) $kilometerSampai, 0, ',', '.') . ' km' : '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Waktu Sampai</span>
                                                <strong>
                                                    {{ $journey->waktu_sampai?->format('d/m/Y H:i') ?? '—' }}
                                                </strong>
                                            </div>

                                            <div class="detail-item">
                                                <span>Total Jarak Tempuh</span>
                                                <strong class="distance-value">
                                                    {{ $jarakTempuh !== null ? number_format($jarakTempuh, 0, ',', '.') . ' km' : 'Belum tersedia' }}
                                                </strong>
                                            </div>

                                        </div>


                                        <div class="detail-note">
                                            <span>Catatan Pengajuan</span>
                                            <p>{{ $journey->catatan ?: 'Tidak ada catatan.' }}</p>
                                        </div>

                                        <div class="detail-note">
                                            <span>Catatan Sopir</span>
                                            <p>{{ $journey->catatan_sopir ?: 'Tidak ada catatan.' }}</p>
                                        </div>

                                    </div>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9">
                                    <div class="history-empty">
                                        <div class="empty-icon">
                                            <i class="bx bx-car"></i>
                                        </div>

                                        <h3>Data perjalanan tidak ditemukan</h3>

                                        <p>
                                            Belum ada perjalanan yang sesuai dengan
                                            pencarian atau filter yang dipilih.
                                        </p>

                                        <a href="{{ url()->current() }}" class="btn-reset-filter">
                                            Tampilkan Semua Data
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if ($journeys->hasPages())
                <div class="history-pagination">
                    {{ $journeys->links() }}
                </div>
            @endif

        </section>

    </div>


    <style>
        .journey-history-page {
            width: 100%;
            color: #263b31;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .history-header h1 {
            margin: 0 0 7px;
            font-size: 25px;
            font-weight: 750;
            color: #174b32;
        }

        .history-header p {
            margin: 0;
            color: #77877d;
            font-size: 13px;
        }

        .history-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .history-action-buttons {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            white-space: nowrap;
        }

        .history-table th.column-action,
        .history-table td.column-action {
            width: 190px;
            min-width: 190px;
        }

        .history-action-buttons .btn-detail {
            flex: 1 1 0;
            min-width: 0;
            height: 36px;
            padding: 6px 8px;
            margin: 0;
            white-space: nowrap;
            font-size: 10px;
        }

        .history-action-buttons .btn-detail i {
            font-size: 14px;
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 94px;
            padding: 17px;
            background: #ffffff;
            border: 1px solid #e7f0e9;
            border-radius: 13px;
            box-shadow: 0 4px 14px rgba(20, 75, 45, 0.045);
        }

        .summary-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 23px;
        }

        .summary-icon-total {
            color: #078b48;
            background: #e9f8ef;
        }

        .summary-icon-waiting {
            color: #a87518;
            background: #fff6e5;
        }

        .summary-icon-process {
            color: #2477a8;
            background: #eaf5fc;
        }

        .summary-icon-complete {
            color: #078b48;
            background: #e9f8ef;
        }

        .summary-card span {
            display: block;
            margin-bottom: 5px;
            color: #7a8a80;
            font-size: 12px;
        }

        .summary-card strong {
            display: block;
            color: #174b32;
            font-size: 23px;
            font-weight: 750;
        }

        .history-card {
            padding: 21px;
            background: #ffffff;
            border: 1px solid #e7f0e9;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(20, 75, 45, 0.045);
        }

        .history-section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .history-section-heading h2 {
            margin: 0 0 5px;
            color: #174b32;
            font-size: 17px;
            font-weight: 750;
        }

        .history-section-heading p {
            margin: 0;
            color: #86948b;
            font-size: 12px;
        }

        .history-filter-form {
            display: grid;
            grid-template-columns:
                minmax(220px, 2fr) minmax(250px, 1.2fr) minmax(180px, 1fr);
            gap: 13px;
            align-items: end;
            padding: 16px;
            background: #f7fbf8;
            border: 1px solid #e7f1e9;
            border-radius: 12px;
        }

        .date-range-field {
            position: relative;
            min-width: 0;
        }

        .date-range-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .date-range-input-wrap input {
            padding-right: 43px;
            background: #ffffff;
            cursor: pointer;
        }

        .date-range-toggle {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            background: #EAF8F0;
            color: #078B48;
            font-size: 19px;
            cursor: pointer;
            transition: .2s ease;
        }

        .date-range-toggle:hover {
            background: #08A653;
            color: #FFFFFF;
        }

        .date-range-calendar {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            z-index: 3000;
            width: 310px;
            padding: 15px;
            border: 1px solid #D7EDE0;
            border-radius: 14px;
            background: #FFFFFF;
            box-shadow: 0 12px 32px rgba(8, 100, 55, .16);
        }

        .date-range-calendar[hidden] {
            display: none !important;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .calendar-header strong {
            color: #18352A;
            font-size: 14px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .calendar-nav {
            width: 31px;
            height: 31px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #D7EDE0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #078B48;
            font-size: 20px;
            cursor: pointer;
        }

        .calendar-nav:hover {
            background: #EAF8F0;
        }

        .calendar-weekdays,
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
            text-align: center;
        }

        .calendar-weekdays {
            margin-bottom: 7px;
        }

        .calendar-weekdays span {
            color: #819087;
            font-size: 10px;
            font-weight: 700;
        }

        .calendar-day {
            width: 100%;
            height: 34px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #35483B;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: .15s ease;
        }

        .calendar-day:hover {
            background: #EAF8F0;
            color: #078B48;
        }

        .calendar-day.is-start,
        .calendar-day.is-end {
            background: #08A653;
            color: #FFFFFF;
            font-weight: 800;
        }

        .calendar-day.is-range {
            background: #EAF8F0;
            color: #078B48;
            border-radius: 0;
        }

        .calendar-day.is-start.is-end {
            border-radius: 8px;
        }

        .calendar-day.is-today:not(.is-start):not(.is-end) {
            border: 1px solid #08A653;
        }

        .calendar-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
            margin-top: 13px;
            padding-top: 11px;
            border-top: 1px solid #EAF1EC;
        }

        .calendar-footer span {
            color: #718078;
            font-size: 10px;
            line-height: 1.5;
        }

        .calendar-footer-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-shrink: 0;
        }

        .calendar-footer button {
            padding: 6px 10px;
            border: 1px solid #D7EDE0;
            border-radius: 7px;
            background: #FFFFFF;
            color: #8b070b;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .calendar-footer button:hover {
            background: #EAF8F0;
        }

        .calendar-footer .calendar-confirm {
            background: #08A653;
            color: #FFFFFF;
            border-color: #08A653;
            font-weight: 800;
        }

        .calendar-footer .calendar-confirm:hover {
            background: #078B48;
            border-color: #078B48;
        }

        .calendar-footer .calendar-confirm:disabled {
            background: #E6EEE9;
            color: #9AA7A0;
            border-color: #DCE7DF;
            cursor: not-allowed;
        }

        @media (max-width: 1100px) {
            .history-filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-search {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 700px) {
            .history-filter-form {
                grid-template-columns: 1fr;
            }

            .filter-search {
                grid-column: auto;
            }

            .date-range-calendar {
                width: min(310px, calc(100vw - 40px));
            }
        }

        .filter-field label {
            display: block;
            margin-bottom: 7px;
            color: #52665a;
            font-size: 11px;
            font-weight: 700;
        }

        .filter-field input,
        .filter-field select {
            width: 100%;
            height: 40px;
            padding: 0 11px;
            border: 1px solid #dce8df;
            border-radius: 8px;
            outline: none;
            background: #ffffff;
            color: #344b3c;
            font-family: inherit;
            font-size: 12px;
            transition: border-color .2s, box-shadow .2s;
        }

        .filter-field input:focus,
        .filter-field select:focus {
            border-color: #08a653;
            box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input-wrap i {
            position: absolute;
            top: 50%;
            left: 11px;
            transform: translateY(-50%);
            color: #8a9c90;
            font-size: 17px;
            pointer-events: none;
        }

        .search-input-wrap input {
            padding-left: 34px;
        }

        .filter-actions {
            display: flex;
            gap: 7px;
            grid-column: 1 / -1;
            justify-content: flex-end;
        }

        .btn-filter,
        .btn-reset-filter {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            min-height: 37px;
            padding: 0 14px;
            border: 0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }

        .btn-filter {
            background: #08a653;
            color: #ffffff;
        }

        .btn-filter:hover {
            background: #078b48;
        }

        .btn-reset-filter {
            background: #ffffff;
            border: 1px solid #dce8df;
            color: #53695b;
        }

        .btn-reset-filter:hover {
            background: #edf8f0;
            color: #078b48;
        }

        .history-result-info {
            display: flex;
            justify-content: flex-end;
            margin: 18px 0 12px;
            color: #819086;
            font-size: 11px;
        }

        .history-result-info strong {
            color: #315d43;
        }

        .history-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e8efe9;
            border-radius: 10px;
        }

        .history-table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            font-size: 12px;
        }

        .history-table thead {
            background: #f0f8f2;
        }

        .history-table th {
            padding: 13px 12px;
            color: #42634e;
            font-size: 10px;
            font-weight: 750;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid #e1ece4;
        }

        .history-table td {
            padding: 13px 12px;
            color: #52665a;
            vertical-align: middle;
            border-bottom: 1px solid #edf2ee;
        }

        .history-main-row:hover {
            background: #fbfdfb;
        }

        .history-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .column-number {
            width: 42px;
            text-align: center !important;
        }

        .column-action {
            text-align: center !important;
        }

        .journey-number {
            color: #078b48;
            font-weight: 750;
            white-space: nowrap;
        }

        .date-main {
            display: block;
            white-space: nowrap;
        }

        .date-time,
        .table-subtext {
            display: block;
            margin-top: 4px;
            color: #98a59c;
            font-size: 10px;
        }

        .history-table td strong:not(.journey-number) {
            color: #3d5546;
            font-weight: 650;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750;
            white-space: nowrap;
        }

        .status-waiting {
            color: #98701e;
            background: #fff5df;
        }

        .status-assigned {
            color: #276b9c;
            background: #eaf5fc;
        }

        .status-process {
            color: #2477a8;
            background: #e8f4fb;
        }

        .status-completed {
            color: #078b48;
            background: #e8f7ed;
        }

        .status-rejected {
            color: #b54848;
            background: #fff0f0;
        }

        .status-cancelled {
            color: #6e7771;
            background: #eff1ef;
        }

        .status-default {
            color: #53665a;
            background: #f0f3f0;
        }

        .btn-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 6px 9px;
            color: #078b48;
            background: #edf8f0;
            border: 1px solid #dcefe1;
            border-radius: 7px;
            font-family: inherit;
            font-size: 10px;
            font-weight: 750;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-detail:hover {
            background: #dff3e5;
        }

        .btn-coordinator-assignment {
            color: #078b48;
            background: #eaf8ef;
            border-color: #ccebd6;
        }

        .btn-coordinator-assignment:hover {
            background: #d8f2e1;
            border-color: #08a653;
        }

        .btn-detail i {
            font-size: 15px;
        }

        .history-detail-row>td {
            padding: 0;
            background: #f8fcf9;
            border-bottom: 1px solid #e3eee6;
        }

        .journey-detail-content {
            padding: 21px;
        }

        .detail-heading {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .detail-heading h3 {
            margin: 0 0 5px;
            color: #174b32;
            font-size: 14px;
            font-weight: 750;
        }

        .detail-heading p {
            margin: 0;
            color: #87958b;
            font-size: 11px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .detail-item {
            min-height: 62px;
            padding: 11px;
            background: #ffffff;
            border: 1px solid #e7f0e9;
            border-radius: 8px;
        }

        .detail-item span {
            display: block;
            margin-bottom: 7px;
            color: #87958b;
            font-size: 10px;
        }

        .detail-item strong {
            display: block;
            color: #355441;
            font-size: 12px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .detail-item .distance-value {
            color: #078b48;
        }

        .detail-divider {
            height: 1px;
            margin: 20px 0;
            background: #e4eee7;
        }

        .detail-note {
            margin-top: 13px;
            padding: 12px;
            background: #ffffff;
            border: 1px solid #e7f0e9;
            border-radius: 8px;
        }

        .detail-note span {
            display: block;
            margin-bottom: 6px;
            color: #6c8172;
            font-size: 10px;
            font-weight: 750;
        }

        .detail-note p {
            margin: 0;
            color: #496050;
            font-size: 12px;
            line-height: 1.6;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .history-empty {
            padding: 55px 20px;
            text-align: center;
        }

        .empty-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            color: #08a653;
            background: #eaf8ef;
            border-radius: 50%;
            font-size: 28px;
        }

        .history-empty h3 {
            margin: 0 0 7px;
            color: #315540;
            font-size: 15px;
        }

        .history-empty p {
            margin: 0 0 17px;
            color: #8a988f;
            font-size: 12px;
        }

        .history-pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .history-pagination nav {
            display: flex;
            align-items: center;
        }

        .history-pagination nav a,
        .history-pagination nav span {
            font-size: 12px;
        }

        @media (max-width: 1100px) {
            .history-filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-search {
                grid-column: 1 / -1;
            }

            .detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .history-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px;
            }

            .summary-card {
                min-height: 78px;
                padding: 12px;
                gap: 9px;
            }

            .summary-icon {
                width: 38px;
                height: 38px;
                font-size: 19px;
            }

            .summary-card strong {
                font-size: 19px;
            }

            .summary-card span {
                font-size: 10px;
            }

            .history-card {
                padding: 13px;
            }

            .history-filter-form {
                grid-template-columns: 1fr;
            }

            .filter-search {
                grid-column: auto;
            }

            .filter-actions {
                grid-column: auto;
                justify-content: stretch;
            }

            .filter-actions>* {
                flex: 1;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .history-header h1 {
                font-size: 21px;
            }
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | DATE RANGE PICKER
                |--------------------------------------------------------------------------
                */

                const form = document.querySelector('.history-filter-form');

                const display = document.getElementById('historyDateRangeDisplay');
                const toggle = document.getElementById('historyDateRangeToggle');

                const calendar = document.getElementById('historyDateRangeCalendar');
                const monthTitle = document.getElementById('historyCalendarMonthTitle');
                const daysContainer = document.getElementById('historyCalendarDays');

                const prevButton = document.getElementById('historyCalendarPrev');
                const nextButton = document.getElementById('historyCalendarNext');

                const clearButton = document.getElementById('historyCalendarClear');
                const confirmButton = document.getElementById('historyCalendarConfirm');
                const selectionInfo = document.getElementById('historyCalendarSelectionInfo');

                const fromInput = document.getElementById('historyDateFrom');
                const toInput = document.getElementById('historyDateTo');

                if (
                    form && display && toggle && calendar &&
                    monthTitle && daysContainer &&
                    prevButton && nextButton &&
                    clearButton && confirmButton &&
                    selectionInfo && fromInput && toInput
                ) {
                    let startDate = fromInput.value || '';
                    let endDate = toInput.value || '';

                    let selectionConfirmed = Boolean(startDate && endDate);

                    let selectedMonth = startDate ?
                        parseDate(startDate) :
                        new Date();

                    selectedMonth = new Date(
                        selectedMonth.getFullYear(),
                        selectedMonth.getMonth(),
                        1
                    );

                    const monthFormatter = new Intl.DateTimeFormat('id-ID', {
                        month: 'long',
                        year: 'numeric'
                    });

                    function parseDate(value) {
                        const parts = value.split('-').map(Number);

                        return new Date(
                            parts[0],
                            parts[1] - 1,
                            parts[2]
                        );
                    }

                    function formatISO(date) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');

                        return `${year}-${month}-${day}`;
                    }

                    function formatDisplay(value) {
                        if (!value) return '';

                        return new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        }).format(parseDate(value));
                    }

                    function updateDisplay() {
                        if (startDate && endDate) {
                            display.value =
                                formatDisplay(startDate) + ' - ' + formatDisplay(endDate);
                        } else if (startDate) {
                            display.value =
                                formatDisplay(startDate) + ' - Pilih tanggal akhir';
                        } else {
                            display.value = '';
                        }
                    }

                    function updateSelectionInfo() {
                        if (!startDate) {
                            selectionInfo.textContent =
                                'Klik tanggal pertama untuk tanggal mulai.';

                            confirmButton.disabled = true;

                        } else if (!endDate) {
                            selectionInfo.textContent =
                                'Mulai ' + formatDisplay(startDate) +
                                '. Pilih tanggal akhir.';

                            confirmButton.disabled = true;

                        } else {
                            selectionInfo.textContent = selectionConfirmed ?
                                'Rentang tanggal sudah dikonfirmasi.' :
                                'Tanggal sudah dipilih. Klik OK untuk konfirmasi.';

                            confirmButton.disabled = false;
                        }
                    }

                    function renderCalendar() {
                        const year = selectedMonth.getFullYear();
                        const month = selectedMonth.getMonth();

                        monthTitle.textContent = monthFormatter.format(selectedMonth);
                        daysContainer.innerHTML = '';

                        const firstDay = new Date(year, month, 1);
                        const offset = (firstDay.getDay() + 6) % 7;
                        const daysInMonth = new Date(year, month + 1, 0).getDate();

                        for (let i = 0; i < offset; i++) {
                            daysContainer.appendChild(document.createElement('span'));
                        }

                        const today = formatISO(new Date());

                        for (let day = 1; day <= daysInMonth; day++) {
                            const date = new Date(year, month, day);
                            const iso = formatISO(date);

                            const button = document.createElement('button');

                            button.type = 'button';
                            button.className = 'calendar-day';
                            button.textContent = day;

                            if (iso === today) {
                                button.classList.add('is-today');
                            }

                            if (iso === startDate) {
                                button.classList.add('is-start');
                            }

                            if (iso === endDate) {
                                button.classList.add('is-end');
                            }

                            if (
                                startDate &&
                                endDate &&
                                iso > startDate &&
                                iso < endDate
                            ) {
                                button.classList.add('is-range');
                            }

                            button.addEventListener('click', function(event) {
                                event.preventDefault();
                                event.stopPropagation();

                                selectDate(iso);
                            });

                            daysContainer.appendChild(button);
                        }

                        updateSelectionInfo();
                    }

                    function selectDate(date) {
                        if (!startDate || endDate) {
                            // Klik pertama: pilih tanggal awal.
                            startDate = date;
                            endDate = '';
                            selectionConfirmed = false;

                        } else if (date < startDate) {
                            // Tanggal yang lebih awal menjadi tanggal awal baru.
                            startDate = date;
                            endDate = '';
                            selectionConfirmed = false;

                        } else {
                            // Klik kedua: pilih tanggal akhir.
                            endDate = date;
                            selectionConfirmed = false;
                        }

                        fromInput.value = startDate;
                        toInput.value = endDate;

                        updateDisplay();
                        renderCalendar();

                        // Kalender tetap terbuka sampai tombol OK ditekan.
                    }

                    function openCalendar() {
                        if (calendar.hidden) {
                            calendar.hidden = false;
                            renderCalendar();
                        } else {
                            calendar.hidden = true;
                        }
                    }

                    toggle.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        openCalendar();
                    });

                    display.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        openCalendar();
                    });

                    prevButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        selectedMonth.setMonth(selectedMonth.getMonth() - 1);
                        renderCalendar();
                    });

                    nextButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        selectedMonth.setMonth(selectedMonth.getMonth() + 1);
                        renderCalendar();
                    });

                    clearButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        startDate = '';
                        endDate = '';
                        selectionConfirmed = false;

                        fromInput.value = '';
                        toInput.value = '';
                        display.value = '';

                        updateSelectionInfo();
                        renderCalendar();
                    });

                    confirmButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        if (!startDate || !endDate) return;

                        if (endDate < startDate) {
                            alert('Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
                            return;
                        }

                        fromInput.value = startDate;
                        toInput.value = endDate;

                        selectionConfirmed = true;

                        updateDisplay();
                        updateSelectionInfo();

                        calendar.hidden = true;
                    });

                    form.addEventListener('submit', function(event) {
                        // Rentang tanggal boleh dikosongkan.
                        // Namun, bila baru memilih satu tanggal, wajib dilengkapi dahulu.
                        const partialRange =
                            (startDate && !endDate) ||
                            (!startDate && endDate);

                        const unconfirmedRange =
                            startDate && endDate && !selectionConfirmed;

                        if (partialRange || unconfirmedRange) {
                            event.preventDefault();

                            alert('Pilih tanggal awal dan tanggal akhir, lalu tekan OK.');

                            calendar.hidden = false;
                            renderCalendar();

                            return;
                        }

                        fromInput.value = startDate;
                        toInput.value = endDate;
                    });

                    document.addEventListener('click', function(event) {
                        if (!event.target.closest('.date-range-field')) {
                            calendar.hidden = true;
                        }
                    });

                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            calendar.hidden = true;
                        }
                    });

                    updateDisplay();
                    updateSelectionInfo();
                }


                /*
                |--------------------------------------------------------------------------
                | BUKA / TUTUP DETAIL PERJALANAN
                |--------------------------------------------------------------------------
                */

                document.addEventListener('click', function(event) {
                    const button = event.target.closest('[data-toggle-detail]');

                    if (!button) return;

                    const targetId = button.getAttribute('data-target');
                    const detailRow = document.getElementById(targetId);

                    if (!detailRow) return;

                    const isOpening = detailRow.hidden;

                    detailRow.hidden = !isOpening;

                    button.setAttribute(
                        'aria-expanded',
                        isOpening ? 'true' : 'false'
                    );

                    button.innerHTML = isOpening ?
                        '<i class="bx bx-chevron-up"></i> Tutup' :
                        '<i class="bx bx-chevron-down"></i> Detail';
                });

            });
        </script>
    @endpush

    @if (auth('mobil_cinta')->user()?->role?->nama_peran === 'perawat')
        @include('mobil-cinta.perawat.perjalanan.modal-edit')
    @endif

    @if (auth('mobil_cinta')->user()?->role?->nama_peran === 'koordinator')
        @include('mobil-cinta.koordinator.perjalanan.modal-penugasan')
    @endif

@endsection
