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
            <span class="text-muted fw-light">Tables /</span> Jabatan
        </h4>

        <div class="card">
            <h5 class="card-header">List Jabatan</h5>

            <div class="table-responsive">
                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah Jabatan
                </button>
                <table class="table table-bordered" id="jabatanTable">
                    <thead class="table table-bordered">
                        <tr>
                            <th width="5%">NO</th>
                            <th>NAMA JABATAN</th>
                            <th width="15%">AKSI</th>
                        </tr>
                    </thead>
                </table>
                {{-- modal tambah dan edit  --}}
                <div class="modal fade" id="modalPegawai" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <form id="formPegawai">
                                @csrf
                                <input type="hidden" id="pegawai_id">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalJabatan">Tambah Jabatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-12">
                                            <label class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan" name="nama_jabatan"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="btnSave">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            let table = $('#jabatanTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('jabatan.index') }}",
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'nama_jabatan'
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            return `
                        <button class="btn btn-warning btn-sm btnEdit"
                            data-id="${id}"
                            data-nama="${row.nama_jabatan}">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm btnDelete"
                            data-id="${id}">
                            Hapus
                        </button>
                    `;
                        }
                    }
                ]
            });

            /* ================= TAMBAH ================= */
            $('#btnTambah').click(function() {
                $('#formPegawai')[0].reset();
                $('#pegawai_id').val('');
                $('#modalJabatan').text('Tambah Jabatan');
                $('#modalPegawai').modal('show');
            });

            /* ================= EDIT ================= */
            $(document).on('click', '.btnEdit', function() {
                $('#pegawai_id').val($(this).data('id'));
                $('#jabatan').val($(this).data('nama'));
                $('#modalJabatan').text('Edit Jabatan');
                $('#modalPegawai').modal('show');
            });

            /* ================= SIMPAN ================= */
            $('#formPegawai').submit(function(e) {
                e.preventDefault();

                let id = $('#pegawai_id').val();
                let url = id ?
                    `/jabatan/edit/${id}` :
                    `{{ route('jabatan.store') }}`;

                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_jabatan: $('#jabatan').val()
                    },
                    success: function(res) {
                        $('#modalPegawai').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: id ? 'Jabatan berhasil diperbarui' :
                                'Jabatan berhasil ditambahkan',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON.message ?? 'Terjadi kesalahan'
                        });
                    }
                });
            });

            /* ================= HAPUS ================= */
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data jabatan akan dihapus',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/jabatan/hapus/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    timer: 1200,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
