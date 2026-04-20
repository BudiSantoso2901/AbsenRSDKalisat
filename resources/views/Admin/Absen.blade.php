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
                        <label class="form-label fw-semibold">Range Tanggal</label>
                        <input type="text" id="tanggal_range" class="form-control" placeholder="Pilih range tanggal">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <select id="jabatan_id" class="form-select">
                            <option value="">Semua Jabatan</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <select id="lokasi_id" class="form-select">
                            <option value="">Semua Lokasi</option>
                            @foreach ($lokasi as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jam Kerja</label>
                        <select id="jam_kerja_id" class="form-select">
                            <option value="">Semua Jam Kerja</option>
                            @foreach ($jamKerja as $jk)
                                <option value="{{ $jk->id }}">{{ $jk->nama_jam_kerja }}</option>
                            @endforeach
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
                    <div class="col-md-6"><label class="form-label fw-semibold">Jenis Absensi</label>
                        <select id="jenis_absen" class="form-control">
                            <option value="">Semua</option>
                            <option value="apel">Apel</option>
                            <option value="jumat_sehat">Jumat Sehat</option>
                            <option value="normal">Normal</option>
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
                            <th>Ketarangan</th>
                            <th>Diedit Oleh</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // 🔥 GLOBAL VARIABLE
        var table;
        let start_date = '';
        let end_date = '';

        $(document).ready(function() {

            // =========================
            // INIT SELECT2
            // =========================
            $('.select-pegawai').select2({
                placeholder: 'Pilih atau cari pegawai...',
                allowClear: true,
                width: '100%'
            });

            // =========================
            // INIT DATE RANGE
            // =========================
            $('#tanggal_range').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false
            });

            $('#tanggal_range').on('apply.daterangepicker', function(ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');

                $(this).val(start_date + ' s/d ' + end_date);

                table.ajax.reload();
            });

            $('#tanggal_range').on('cancel.daterangepicker', function() {
                $(this).val('');
                start_date = '';
                end_date = '';

                table.ajax.reload();
            });

            // =========================
            // INIT DATATABLE
            // =========================
            table = $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                order: [
                    [3, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.absensi.index') }}",
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.pegawai_id = $('#pegawai_id').val();
                        d.jabatan_id = $('#jabatan_id').val();
                        d.lokasi_id = $('#lokasi_id').val();
                        d.jam_kerja_id = $('#jam_kerja_id').val();
                        d.jenis_absen = $('#jenis_absen').val();

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

                            const onlyDate = data.split(' ')[0];
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
                        data: 'jam_masuk'
                    },
                    {
                        data: 'jam_pulang'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            if (!data) return '-';

                            let status = data.toLowerCase();
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
                        </span>`;
                        }
                    },
                    {
                        data: 'keterangan',
                        render: function(data) {
                            if (!data) return 'Normal';

                            if (data === 'apel') {
                                return '<span class="badge bg-primary">APEL</span>';
                            }

                            if (data === 'jumat_sehat') {
                                return '<span class="badge bg-success">JUMAT SEHAT</span>';
                            }

                            return data;
                        }
                    },
                    {
                        data: 'edited_by'
                    }
                ]
            });

            // =========================
            // FILTER CHANGE
            // =========================
            $('.filter-box select').on('change', function() {
                table.ajax.reload();
            });

            // =========================
            // EXPORT PDF
            // =========================
            $('#btnExportPdf').on('click', function() {

                let params = $.param({
                    start_date: start_date,
                    end_date: end_date,
                    pegawai_id: $('#pegawai_id').val(),
                    jabatan_id: $('#jabatan_id').val(),
                    lokasi_id: $('#lokasi_id').val(),
                    jam_kerja_id: $('#jam_kerja_id').val(),
                    jenis_absen: $('#jenis_absen').val(),
                });

                window.open("{{ route('absensi.exportAll.pdf') }}?" + params, '_blank');
            });

            // =========================
            // RESET FILTER
            // =========================
            $('#btnResetFilter').on('click', function() {

                start_date = '';
                end_date = '';

                $('#tanggal_range').val('');
                $('#pegawai_id').val(null).trigger('change');
                $('#jabatan_id').val('');
                $('#lokasi_id').val('');
                $('#jam_kerja_id').val('');

                table.ajax.reload();
            });

        });
    </script>
@endpush
