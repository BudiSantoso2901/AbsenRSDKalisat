@extends('mobil-cinta.layouts.app')

@section('title', 'Laporan Perjalanan')

@section('content')

    <style>
        .report-heading {
            margin-bottom: 18px;
        }

        .report-heading h1 {
            margin: 0 0 5px;
            font-size: 25px;
        }

        .report-heading p {
            margin: 0;
            color: #718078;
            font-size: 12px;
        }

        /* FILTER TANGGAL */

        .report-filter {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 12px;
            margin: 22px 0;
        }

        .report-filter .form-group {
            width: 190px;
            margin: 0;
        }

        .report-filter .form-label {
            margin-bottom: 7px;
        }

        .report-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        /* KPI */

        .report-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .report-summary-card {
            padding: 19px;
            border: 1px solid #d7eafa;
            border-top: 3px solid #16B96A;
            border-radius: 14px;
            background: linear-gradient(120deg, #F1FAF5, #F7FCF9);
        }

        .report-summary-card strong {
            display: block;
            color: #18352A;
            font-size: 26px;
            line-height: 1.3;
            font-weight: 800;
        }

        .report-summary-card span {
            display: block;
            margin-top: 5px;
            color: #718078;
            font-size: 12px;
        }

        /* CHART GRID */

        .report-chart-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(300px, 1fr);
            gap: 17px;
        }

        .report-chart-card {
            min-width: 0;
            padding: 20px;
            border: 1px solid #d7eafa;
            border-radius: 15px;
            background: linear-gradient(135deg, #F5FBF7, #FFFFFF);
        }

        .report-chart-heading {
            margin-bottom: 20px;
        }

        .report-chart-heading h2 {
            margin: 0;
            color: #18352A;
            font-size: 16px;
            font-weight: 800;
        }

        .report-chart-heading p {
            margin: 4px 0 0;
            color: #819087;
            font-size: 11px;
        }

        /* DAILY BAR CHART */

        .daily-chart-scroll {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 7px;
        }

        .daily-chart {
            display: flex;
            align-items: flex-end;
            gap: 13px;
            min-width: max-content;
            height: 275px;
            padding: 8px 5px 0;
        }

        .daily-chart-item {
            width: 47px;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
        }

        .daily-chart-count {
            min-height: 17px;
            color: #62756A;
            font-size: 11px;
            font-weight: 700;
        }

        .daily-chart-track {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
            border-radius: 9px 9px 0 0;
            background: #EAF5EF;
        }

        .daily-chart-bar {
            width: 100%;
            min-height: 0;
            border-radius: 8px 8px 0 0;
            background: linear-gradient(180deg,
                    #16B96A,
                    #078B48);
            transition: height .3s ease;
        }

        .daily-chart-label {
            color: #718078;
            font-size: 10px;
            white-space: nowrap;
        }

        /* DONUT STATUS */

        .status-chart-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .status-donut {
            width: 205px;
            height: 205px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #EDF5EF;
            position: relative;
        }

        .status-donut::before {
            content: "";
            position: absolute;
            width: 135px;
            height: 135px;
            border-radius: 50%;
            background: #FFFFFF;
        }

        .status-donut-center {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .status-donut-center strong {
            display: block;
            color: #18352A;
            font-size: 26px;
            font-weight: 800;
        }

        .status-donut-center span {
            display: block;
            color: #718078;
            font-size: 11px;
        }

        .status-legend {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .status-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #718078;
            font-size: 12px;
        }

        .status-legend-name {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-legend-dot {
            width: 11px;
            height: 11px;
            flex-shrink: 0;
            border-radius: 4px;
        }

        .status-legend-item strong {
            color: #18352A;
            font-size: 11px;
        }

        /* PENGGUNAAN KENDARAAN */

        .vehicle-chart-card {
            margin-top: 17px;
        }

        .vehicle-usage-list {
            display: flex;
            flex-direction: column;
            gap: 17px;
        }

        .vehicle-usage-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 7px;
        }

        .vehicle-usage-heading strong {
            color: #18352A;
            font-size: 12px;
        }

        .vehicle-usage-heading span {
            color: #718078;
            font-size: 11px;
        }

        .vehicle-usage-track {
            height: 10px;
            overflow: hidden;
            border-radius: 20px;
            background: #EAF5EF;
        }

        .vehicle-usage-bar {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, #16B96A, #078B48);
        }

        /* EMPTY STATE */

        .report-empty {
            padding: 32px 10px;
            text-align: center;
            color: #819087;
        }

        .report-empty i {
            display: block;
            margin-bottom: 8px;
            color: #16B96A;
            font-size: 34px;
        }

        .report-empty strong {
            display: block;
            color: #53665A;
            font-size: 12px;
        }

        .report-empty p {
            margin: 4px 0 0;
            font-size: 11px;
        }

        @media (max-width: 900px) {
            .report-chart-grid {
                grid-template-columns: 1fr;
            }

            .status-chart-content {
                flex-direction: row;
                align-items: center;
                justify-content: center;
            }

            .status-legend {
                max-width: 300px;
            }
        }

        @media (max-width: 650px) {
            .report-summary {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .report-filter .form-group {
                width: calc(50% - 6px);
            }

            .report-actions {
                width: 100%;
            }

            .report-actions .btn {
                flex: 1;
            }

            .report-chart-card {
                padding: 16px;
            }

            .status-chart-content {
                flex-direction: column;
            }
        }

        /* =====================================
                                           DATE RANGE PICKER
                                        ===================================== */

        .date-range-field {
            position: relative;
            width: 285px !important;
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

        .calendar-footer button {
            padding: 6px 10px;

            border: 1px solid #D7EDE0;
            border-radius: 7px;

            background: #FFFFFF;
            color: #8b0707;

            font-size: 10px;
            font-weight: 700;

            cursor: pointer;
        }

        .calendar-footer button:hover {
            background: #EAF8F0;
        }

        .calendar-footer-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-shrink: 0;
        }

        .calendar-footer .calendar-confirm {
            background: #08A653;
            color: #FFFFFF;
            border-color: #08A653;
            font-weight: 800;
        }

        .calendar-footer .calendar-confirm:hover {
            background: #078B48;
            color: #FFFFFF;
            border-color: #078B48;
        }

        .calendar-footer .calendar-confirm:disabled {
            background: #E6EEE9;
            color: #9AA7A0;
            border-color: #DCE7DF;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        @media (max-width: 650px) {
            .date-range-field {
                width: 100% !important;
            }

            .date-range-calendar {
                width: min(310px, calc(100vw - 40px));
            }
        }
    </style>

    {{-- JUDUL LAPORAN --}}

    <div class="report-heading">
        <h1>Laporan Perjalanan</h1>

        <p>
            Monitoring dan rekapitulasi layanan antar pulang pasien Mobil Cinta.
        </p>
    </div>

    {{-- FILTER TANGGAL DAN EXPORT --}}

    <form method="GET" action="{{ route('mobil-cinta.admin.dashboard') }}" class="report-filter" id="reportFilter">

        <div class="form-group date-range-field">

            <label class="form-label" for="dateRangeDisplay">
                Rentang Tanggal
            </label>

            <div class="date-range-input-wrap">

                <input type="text" id="dateRangeDisplay" class="form-control" readonly
                    placeholder="Pilih tanggal mulai dan sampai"
                    value="{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}">

                <button type="button" id="dateRangeToggle" class="date-range-toggle" aria-label="Buka kalender">
                    <i class="bx bx-calendar"></i>
                </button>

            </div>

            {{-- KALENDER --}}

            <div class="date-range-calendar" id="dateRangeCalendar" hidden>

                <div class="calendar-header">

                    <button type="button" id="calendarPrev" class="calendar-nav">
                        <i class="bx bx-chevron-left"></i>
                    </button>

                    <strong id="calendarMonthTitle"></strong>

                    <button type="button" id="calendarNext" class="calendar-nav">
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

                <div class="calendar-days" id="calendarDays"></div>

                <div class="calendar-footer">

                    <span id="calendarSelectionInfo">
                        Klik tanggal pertama untuk mulai.
                    </span>

                    <div class="calendar-footer-actions">

                        <button type="button" id="calendarClear">
                            Reset
                        </button>

                        <button type="button" id="calendarConfirm" class="calendar-confirm" disabled>
                            OK
                        </button>

                    </div>

                </div>

            </div>

            {{-- VALUE YANG DIKIRIM KE CONTROLLER --}}

            <input type="hidden" name="from" id="dateFrom" value="{{ $from }}">

            <input type="hidden" name="to" id="dateTo" value="{{ $to }}">

        </div>

        <div class="report-actions">

            <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i>
                Tampilkan Laporan
            </button>

            <a href="{{ route('mobil-cinta.admin.dashboard.export.excel', [
                'from' => $from,
                'to' => $to,
            ]) }}"
                class="btn btn-outline">
                <i class="bx bx-file"></i>
                Ekspor Excel
            </a>

            <a href="{{ route('mobil-cinta.admin.dashboard.export.pdf', [
                'from' => $from,
                'to' => $to,
            ]) }}"
                class="btn btn-outline" target="_blank" rel="noopener">
                <i class="bx bxs-file-pdf"></i>
                Simpan PDF
            </a>

        </div>

    </form>

    {{-- RINGKASAN --}}

    <div class="report-summary">

        <div class="report-summary-card">
            <strong>
                {{ number_format($totalJourneys, 0, ',', '.') }}
            </strong>
            <span>Jumlah perjalanan</span>
        </div>

        <div class="report-summary-card">
            <strong>
                {{ number_format($totalKilometers, 2, ',', '.') }}
            </strong>
            <span>Total kilometer (KM)</span>
        </div>

        <div class="report-summary-card">
            <strong>
                {{ number_format($inProgress, 0, ',', '.') }}
            </strong>
            <span>Sedang berjalan</span>
        </div>

    </div>

    {{-- GRAFIK PER TANGGAL DAN STATUS --}}

    <div class="report-chart-grid">

        {{-- GRAFIK HARIAN --}}

        <section class="report-chart-card">

            <div class="report-chart-heading">
                <h2>Jumlah perjalanan per tanggal</h2>

                <p>
                    Jumlah pengajuan berdasarkan tanggal.
                </p>
            </div>

            @if (count($dailyData) > 0)

                <div class="daily-chart-scroll">

                    <div class="daily-chart">

                        @foreach ($dailyData as $item)
                            <div class="daily-chart-item">

                                <span class="daily-chart-count">
                                    {{ $item['total'] }}
                                </span>

                                <div class="daily-chart-track">

                                    <div class="daily-chart-bar"
                                        style="height: {{ ($item['total'] / $maxDailyJourneys) * 100 }}%;"></div>

                                </div>

                                <span class="daily-chart-label">
                                    {{ $item['label'] }}
                                </span>

                            </div>
                        @endforeach

                    </div>

                </div>
            @else
                <div class="report-empty">
                    <i class="bx bx-bar-chart-alt-2"></i>
                    <strong>Belum ada data perjalanan</strong>
                    <p>Grafik akan tampil setelah terdapat transaksi pada periode ini.</p>
                </div>

            @endif

        </section>

        {{-- KOMPOSISI STATUS --}}

        <section class="report-chart-card">

            <div class="report-chart-heading">
                <h2>Komposisi status perjalanan</h2>

                <p>
                    Distribusi seluruh status dalam periode laporan.
                </p>
            </div>

            <div class="status-chart-content">

                <div class="status-donut" style="background: {{ $statusDonutBackground }};">
                    <div class="status-donut-center">
                        <strong>
                            {{ number_format($totalJourneys, 0, ',', '.') }}
                        </strong>

                        <span>Perjalanan</span>
                    </div>
                </div>

                <div class="status-legend">

                    @foreach ($statusData as $item)
                        <div class="status-legend-item">

                            <span class="status-legend-name">

                                <span class="status-legend-dot" style="background: {{ $item['color'] }};"></span>

                                {{ $item['label'] }}

                            </span>

                            <strong>
                                {{ $item['total'] }}
                            </strong>

                        </div>
                    @endforeach

                </div>

            </div>

        </section>

    </div>

    {{-- PENGGUNAAN KENDARAAN: FITUR YANG SUDAH ADA TETAP DIPERTAHANKAN --}}

    <section class="report-chart-card vehicle-chart-card">

        <div class="report-chart-heading">
            <h2>Penggunaan kendaraan</h2>

            <p>
                Lima kendaraan dengan jumlah perjalanan terbanyak pada periode laporan.
                Total kendaraan terdaftar: {{ number_format($totalVehicles, 0, ',', '.') }}.
            </p>
        </div>

        @if (count($vehicleUsage) > 0)

            <div class="vehicle-usage-list">

                @foreach ($vehicleUsage as $vehicle)
                    <div>

                        <div class="vehicle-usage-heading">
                            <strong>{{ $vehicle['nomor_polisi'] }}</strong>

                            <span>
                                {{ number_format($vehicle['total'], 0, ',', '.') }}
                                perjalanan
                            </span>
                        </div>

                        <div class="vehicle-usage-track">

                            <div class="vehicle-usage-bar"
                                style="width: {{ ($vehicle['total'] / $maxVehicleUsage) * 100 }}%;"></div>

                        </div>

                    </div>
                @endforeach

            </div>
        @else
            <div class="report-empty">
                <i class="bx bx-car"></i>
                <strong>Belum ada penggunaan kendaraan</strong>
                <p>Grafik penggunaan akan tampil setelah kendaraan digunakan.</p>
            </div>

        @endif

    </section>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('reportFilter');

    const display = document.getElementById('dateRangeDisplay');
    const toggle = document.getElementById('dateRangeToggle');

    const calendar = document.getElementById('dateRangeCalendar');
    const monthTitle = document.getElementById('calendarMonthTitle');
    const daysContainer = document.getElementById('calendarDays');

    const prevButton = document.getElementById('calendarPrev');
    const nextButton = document.getElementById('calendarNext');

    const clearButton = document.getElementById('calendarClear');
    const confirmButton = document.getElementById('calendarConfirm');
    const selectionInfo = document.getElementById('calendarSelectionInfo');

    const fromInput = document.getElementById('dateFrom');
    const toInput = document.getElementById('dateTo');

    if (
        !form || !display || !toggle || !calendar ||
        !fromInput || !toInput || !confirmButton
    ) {
        console.error('Komponen date range picker tidak lengkap.');
        return;
    }

    // DEKLARASI VARIABEL HARUS SEBELUM DIGUNAKAN.
    let startDate = fromInput.value || '';
    let endDate = toInput.value || '';

    let selectionConfirmed = Boolean(startDate && endDate);

    let selectedMonth = startDate
        ? parseDate(startDate)
        : new Date();

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
            selectionInfo.textContent = selectionConfirmed
                ? 'Rentang tanggal sudah dikonfirmasi.'
                : 'Tanggal sudah dipilih. Klik OK untuk konfirmasi.';

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

            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                selectDate(iso);
            });

            daysContainer.appendChild(button);
        }

        updateSelectionInfo();
    }

    // PEMILIHAN TANGGAL PERTAMA DAN KEDUA.
    function selectDate(date) {

        if (!startDate || endDate) {

            // Klik pertama: tanggal mulai.
            startDate = date;
            endDate = '';
            selectionConfirmed = false;

        } else if (date < startDate) {

            // Tanggal lebih awal menjadi tanggal mulai baru.
            startDate = date;
            endDate = '';
            selectionConfirmed = false;

        } else {

            // Klik kedua: tanggal akhir.
            endDate = date;
            selectionConfirmed = false;
        }

        fromInput.value = startDate;
        toInput.value = endDate;

        updateDisplay();
        renderCalendar();

        // Jangan tutup kalender di sini.
    }

    function openCalendar() {
        if (calendar.hidden) {
            calendar.hidden = false;
            renderCalendar();
        } else {
            calendar.hidden = true;
        }
    }

    toggle.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        openCalendar();
    });

    display.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        openCalendar();
    });

    prevButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        selectedMonth.setMonth(selectedMonth.getMonth() - 1);
        renderCalendar();
    });

    nextButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        selectedMonth.setMonth(selectedMonth.getMonth() + 1);
        renderCalendar();
    });

    // RESET PILIHAN TANGGAL.
    clearButton.addEventListener('click', function (event) {
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

    // KONFIRMASI TANGGAL.
    confirmButton.addEventListener('click', function (event) {
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

    // VALIDASI SEBELUM MENAMPILKAN LAPORAN.
    form.addEventListener('submit', function (event) {

        if (!startDate || !endDate || !selectionConfirmed) {
            event.preventDefault();

            alert('Pilih tanggal mulai, tanggal akhir, lalu tekan OK.');

            calendar.hidden = false;
            renderCalendar();

            return;
        }

        fromInput.value = startDate;
        toInput.value = endDate;
    });

    // TUTUP SAAT KLIK DI LUAR KALENDER.
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.date-range-field')) {
            calendar.hidden = true;
        }
    });

    // TUTUP DENGAN ESCAPE.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            calendar.hidden = true;
        }
    });

    updateDisplay();
    updateSelectionInfo();

});
</script>
@endpush
