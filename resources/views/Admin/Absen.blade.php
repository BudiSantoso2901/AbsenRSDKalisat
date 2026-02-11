@extends('_layouts.layouts')

@section('content')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #000;
        }

        .filter-box .form-control,
        .filter-box .form-select {
            font-size: 0.85rem;
        }

        #absensiTable {
            font-size: 0.9rem;
        }

        #absensiTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        #absensiTable tbody td {
            vertical-align: middle;
            padding: 10px;
        }

        .dataTables_info,
        .dataTables_paginate {
            font-size: 0.85rem;
        }

        .filter-box label {
            font-size: 0.8rem;
            color: #555;
        }

        .filter-box .form-control,
        .filter-box .form-select {
            border-radius: 8px;
        }

        .card-header h6 {
            font-weight: 600;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data /</span> Absensi Pegawai
        </h4>

        {{-- FILTER --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Filter Data Absensi</h6>
            </div>

            <div class="card-body filter-box">
                <div class="row g-3">

                    {{-- TANGGAL MULAI --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" id="start_date" class="form-control">
                    </div>

                    {{-- TANGGAL AKHIR --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Akhir</label>
                        <input type="date" id="end_date" class="form-control">
                    </div>

                    {{-- HARI --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hari</label>
                        <select id="hari" class="form-select">
                            <option value="">Semua Hari</option>
                            <option value="Monday">Senin</option>
                            <option value="Tuesday">Selasa</option>
                            <option value="Wednesday">Rabu</option>
                            <option value="Thursday">Kamis</option>
                            <option value="Friday">Jumat</option>
                            <option value="Saturday">Sabtu</option>
                            <option value="Sunday">Minggu</option>
                        </select>
                    </div>

                    {{-- BULAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select id="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- TAHUN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select id="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    {{-- PEGAWAI --}}
                    <div class="col-md-6"><label class="form-label fw-semibold">Pegawai</label>
                        <select id="pegawai_id" class="form-select select-pegawai">
                            <option value="">Semua Pegawai</option>
                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nip }} - {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- RESET BUTTON --}}
                    <div class="col-md-12 d-flex align-items-end">
                        <button id="btnResetFilter" class="btn btn-secondary w-100">
                            <i class="bx bx-reset"></i> Reset Filter
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Absensi Pegawai</h5>

                <button id="btnExportPdf" class="btn btn-danger btn-sm">
                    <i class="bx bxs-file-pdf"></i> Export PDF
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="absensiTable" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIK</th>
                            <th>Nama Pegawai</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                            <th>Diedit Oleh</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {

            let table = $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                order: [
                    [3, 'desc']
                ], // kolom TANGGAL
                ajax: {
                    url: "{{ route('admin.absensi.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.hari = $('#hari').val();
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();
                        d.pegawai_id = $('#pegawai_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nip'
                    },
                    {
                        data: 'nama_pegawai'
                    },
                    {
                        data: 'tanggal',
                        render: function(data) {
                            if (!data) return '-';

                            // Ambil hanya bagian tanggal saja (buang jamnya)
                            const onlyDate = data.split(' ')[0]; // 2026-02-09

                            const [year, month, day] = onlyDate.split('-');
                            const tanggal = new Date(year, month - 1, day);

                            return tanggal.toLocaleDateString('id-ID', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_pulang'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            if (!data) return '-';

                            let status = data.toLowerCase(); // 🔥 penting
                            let badgeClass = '';
                            let icon = '';
                            let text = data.replace('_', ' ').toUpperCase();

                            switch (status) {
                                case 'hadir':
                                    badgeClass = 'bg-label-success';
                                    icon = 'bx bx-check-circle';
                                    break;

                                case 'izin':
                                    badgeClass = 'bg-label-warning';
                                    icon = 'bx bx-time-five';
                                    break;

                                case 'sakit':
                                    badgeClass = 'bg-label-danger';
                                    icon = 'bx bx-plus-medical';
                                    break;

                                case 'belum_hadir':
                                    badgeClass = 'bg-label-secondary';
                                    icon = 'bx bx-x-circle';
                                    text = 'BELUM HADIR';
                                    break;

                                default:
                                    badgeClass = 'bg-label-dark';
                            }

                            return `
            <span class="badge ${badgeClass} rounded-pill">
                <i class="${icon} me-1"></i>
                ${text}
            </span>
        `;
                        }
                    },
                    {
                        data: 'edited_by'
                    },
                ]
            });


            // reload saat filter berubah
            $('.filter-box input, .filter-box select').on('change', function() {
                table.ajax.reload();
            });

            // EXPORT PDF (PAKAI FILTER AKTIF)
            $('#btnExportPdf').on('click', function() {

                let params = $.param({
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    hari: $('#hari').val(),
                    bulan: $('#bulan').val(),
                    tahun: $('#tahun').val(),
                    pegawai_id: $('#pegawai_id').val() // ✅ WAJIB
                });

                window.open(
                    "{{ route('absensi.exportAll.pdf') }}?" + params,
                    '_blank'
                );
            });

        });
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            const startValue = this.value;

            if (!startValue) return;

            // Kunci minimal tanggal akhir
            endDate.min = startValue;

            // Jika tanggal akhir kosong → isi otomatis
            if (!endDate.value) {
                endDate.value = startValue;
            }

            // Jika tanggal akhir < tanggal mulai → perbaiki otomatis
            if (endDate.value < startValue) {
                endDate.value = startValue;
            }
        });
        $('#btnResetFilter').on('click', function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#hari').val('');
            $('#bulan').val('');
            $('#tahun').val('');
            $('#pegawai_id').val(null).trigger('change');
            $('#end_date').attr('min', '');

            table.ajax.reload();
        });
        $('.select-pegawai').select2({
            placeholder: 'Pilih atau cari pegawai...',
            allowClear: true,
            width: '100%'
        });
    </script>
@endpush
