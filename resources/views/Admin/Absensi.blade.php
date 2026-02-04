@extends('_layouts.layouts')
@section('content')
    <style>
        /* === GLOBAL FONT === */
        body {
            font-family: 'Poppins', sans-serif;
            color: #000;
        }

        td .btn {
            margin: 2px;
            white-space: nowrap;
        }

        .swal2-container {
            z-index: 20000 !important;
        }

        /* === CARD PEGAWAI === */
        .card-pegawai {
            padding: 20px;
            border-radius: 12px;
        }

        /* Header Card */
        .card-pegawai .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: #000;
            background: #fff;
            border-bottom: 2px solid #f0f0f0;
        }

        /* Table */
        #pegawaiTable {
            font-size: 0.95rem;
            color: #000;
        }

        /* Table Head */
        #pegawaiTable thead th {
            font-size: 0.9rem;
            font-weight: 600;
            color: #000;
            background-color: #f8f9fa;
            text-transform: uppercase;
            vertical-align: middle;
        }

        /* Table Body */
        #pegawaiTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        /* Datatable Info & Pagination */
        .dataTables_info,
        .dataTables_paginate {
            font-size: 0.9rem;
            color: #000;
        }

        /* Search */
        .dataTables_filter input {
            font-size: 0.9rem;
            padding: 6px 10px;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Data /</span> Absensi
        </h4>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Data Absensi</h5>
                {{-- <button class="btn btn-primary btn-sm">
                    Ekspor Semua
                </button> --}}
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="absensiTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>ID Pengguna</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Shift</th>
                            <th>Lokasi</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('absensi.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nip'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'shift'
                    },
                    {
                        data: 'lokasi'
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
