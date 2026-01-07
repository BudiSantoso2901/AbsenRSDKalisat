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
            <span class="text-muted fw-light">Master /</span> Jam Kerja
        </h4>

        <div class="card">
            <h5 class="card-header">List Jam Kerja</h5>

            <div class="card-body">
                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah Jam Kerja
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered" id="jamKerjaTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Jam Kerja</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Toleransi (Menit)</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalJamKerja" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="formJamKerja">
                    @csrf
                    <input type="hidden" id="jam_kerja_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Jam Kerja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Nama Jam Kerja</label>
                                <input type="text" class="form-control" name="nama_jam_kerja" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" name="jam_mulai" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" name="jam_selesai" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Toleransi (menit)</label>
                                <input type="number" class="form-control" name="toleransi_menit" min="0"
                                    value="0">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let table;

        $(document).ready(function() {

            /* ================= DATATABLE ================= */
            table = $('#jamKerjaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jam-kerja.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_jam_kerja'
                    },
                    {
                        data: 'jam_mulai'
                    },
                    {
                        data: 'jam_selesai'
                    },
                    {
                        data: 'toleransi_menit'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            /* ================= TAMBAH ================= */
            $('#btnTambah').click(function() {
                $('#formJamKerja')[0].reset();
                $('#jam_kerja_id').val('');
                $('#modalTitle').text('Tambah Jam Kerja');
                $('.is-invalid').removeClass('is-invalid');
                $('#modalJamKerja').modal('show');
            });

            /* ================= EDIT ================= */
            $(document).on('click', '.btn-edit', function() {
                $('#jam_kerja_id').val($(this).data('id'));
                $('[name="nama_jam_kerja"]').val($(this).data('nama'));
                $('[name="jam_mulai"]').val($(this).data('mulai'));
                $('[name="jam_selesai"]').val($(this).data('selesai'));
                $('[name="toleransi_menit"]').val($(this).data('toleransi'));

                $('#modalTitle').text('Edit Jam Kerja');
                $('#modalJamKerja').modal('show');
            });

            /* ================= SUBMIT ================= */
            $('#formJamKerja').submit(function(e) {
                e.preventDefault();

                let id = $('#jam_kerja_id').val();
                let url = id ?
                    "{{ url('jam-kerja/edit') }}/" + id :
                    "{{ route('jam-kerja.store') }}";

                let formData = $(this).serializeArray();

                if (id) {
                    formData.push({
                        name: '_method',
                        value: 'PUT'
                    });
                }

                Swal.fire({
                    title: 'Simpan data?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#modalJamKerja').modal('hide');
                                table.ajax.reload(null, false);
                            },
                            error: function(err) {
                                if (err.status === 422) {
                                    $.each(err.responseJSON.errors, function(key) {
                                        $('[name="' + key + '"]').addClass(
                                            'is-invalid');
                                    });

                                    Swal.fire('Validasi Gagal', 'Periksa inputan',
                                        'warning');
                                } else {
                                    Swal.fire('Error', 'Terjadi kesalahan server',
                                        'error');
                                }
                            }
                        });
                    }
                });
            });

        });

        /* ================= DELETE ================= */
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/jam-kerja/hapus/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        });
    </script>
@endpush
