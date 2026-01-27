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

        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Tables /</span> Pegawai
        </h4>

        <div class="card">
            <h5 class="card-header">List Pegawai</h5>

            <div class="table-responsive">
                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah Pegawai
                </button>
                <table class="table table-bordered" id="pegawaiTable">
                    <thead class="table table-bordered">
                        <tr>
                            <th width="5%">NO</th>
                            <th>NAMA</th>
                            <th>TANGGAL LAHIR</th>
                            <th>NIP</th>
                            <th>EMAIL</th>
                            <th>JABATAN</th>
                            <th>LOKASI</th>
                            <th>JAM KERJA</th>
                            <th>STATUS</th>
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
                                    <h5 class="modal-title" id="modalPegawaiTitle">Tambah Pegawai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">NIP atau Username</label>
                                            <input type="text" class="form-control" id="nip" name="nip"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tanggal_lahir"
                                                name="tanggal_lahir">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Jabatan</label>
                                            <select class="form-select" name="id_jabatan" id="id_jabatan">
                                                @foreach ($jabatan as $jbt)
                                                    <option value="{{ $jbt->id }}">
                                                        {{ $jbt->nama_jabatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Lokasi</label>
                                            <select class="form-select" id="id_lokasi" name="id_lokasi">
                                                @foreach ($lokasis as $lokasi)
                                                    <option value="{{ $lokasi->id }}">
                                                        {{ $lokasi->nama_lokasi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Jam Kerja</label>
                                            <select class="form-select" name="id_jam_kerja" id="id_jam_kerja">
                                                <option value="">Pilih</option>
                                                @foreach ($jamKerjas as $jam)
                                                    <option value="{{ $jam->id }}">
                                                        {{ $jam->nama_jam_kerja }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 d-none" id="passwordSection">
                                            <label class="form-label">Password Baru</label>
                                            <input type="password" class="form-control" name="password"
                                                autocomplete="new-password">
                                        </div>

                                        <div class="col-md-6 d-none" id="passwordConfirmSection">
                                            <label class="form-label">Konfirmasi Password</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                autocomplete="new-password">
                                        </div>

                                        {{-- 🔘 STATUS (EDIT ONLY) --}}
                                        <div class="col-md-12 d-none" id="statusSection">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="">Pilih Status</option>
                                                <option value="approved">Approved</option>
                                                <option value="pending">Pending</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
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
        let table;

        $(document).ready(function() {

            /* ================= DATATABLE ================= */
            table = $('#pegawaiTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('pegawai.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'tanggal_lahir'
                    },
                    {
                        data: 'nip'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'lokasi'
                    },
                    {
                        data: 'jam_kerja'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        width: '160px',
                        className: 'text-center'
                    }
                ]
            });

            /* ================= TAMBAH ================= */
            $('#btnTambah').click(function() {
                $('#formPegawai')[0].reset();
                $('#pegawai_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('#modalPegawaiTitle').text('Tambah Pegawai');
                $('#passwordSection, #passwordConfirmSection, #statusSection')
                    .addClass('d-none');
                $('#modalPegawai').modal('show');
            });

            /* ================= EDIT ================= */
            $(document).on('click', '.btn-edit', function() {
                $('#pegawai_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#tanggal_lahir').val($(this).data('tanggal_lahir'));
                $('#nip').val($(this).data('nip'));
                $('#email').val($(this).data('email'));
                $('#id_jabatan').val($(this).data('id_jabatan'));
                $('#id_lokasi').val($(this).data('id_lokasi'));
                $('#id_jam_kerja').val($(this).data('id_jam_kerja'));
                $('#passwordSection, #passwordConfirmSection, #statusSection').removeClass('d-none');
                $('#status').val($(this).data('status')).trigger('change');
                $('.is-invalid').removeClass('is-invalid');
                $('#modalPegawaiTitle').text('Edit Pegawai');
                $('#modalPegawai').modal('show');
            });
            $('#modalPegawai').on('hidden.bs.modal', function() {
                $('#formPegawai')[0].reset();
                $('#passwordSection, #passwordConfirmSection, #statusSection')
                    .addClass('d-none');
            });
            /* ================= SUBMIT ================= */
            $('#formPegawai').submit(function(e) {
                e.preventDefault();

                let id = $('#pegawai_id').val();
                let url = id ?
                    "{{ url('pegawai') }}/edit/" + id :
                    "{{ route('pegawai.store') }}";

                let formData = $(this).serializeArray();

                if (id) {
                    formData.push({
                        name: '_method',
                        value: 'PUT'
                    });
                }

                Swal.fire({
                    title: 'Simpan Data?',
                    text: 'Pastikan data sudah benar',
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

                                $('#modalPegawai').modal('hide');
                                $('#formPegawai')[0].reset();

                                // 🔥 REFRESH DATATABLE TANPA RESET PAGE
                                table.ajax.reload(null, false);
                            },
                            error: function(err) {
                                $('.is-invalid').removeClass('is-invalid');

                                if (err.status === 422) {
                                    $.each(err.responseJSON.errors, function(key) {
                                        $('[name="' + key + '"]').addClass(
                                            'is-invalid');
                                    });

                                    Swal.fire(
                                        'Validasi Gagal',
                                        'Periksa kembali inputan',
                                        'warning'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error',
                                        'Terjadi kesalahan server',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        });
        // -- DELETE PEGAWAI -- //
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus data?',
                text: 'Data yang dihapus tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/pegawai/hapus/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus data', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
