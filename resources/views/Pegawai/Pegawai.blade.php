@extends('_layouts.layouts')
@section('content')
    <style>
        /* === GLOBAL FONT === */
        body {
            font-family: 'Poppins', sans-serif;
            color: #000;
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
            <span class="text-muted fw-light">Tables /</span> Pegawai
        </h4>

        <div class="card">
            <h5 class="card-header">List Pegawai</h5>

            <div class="table-responsive text-nowrap">
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
                                            <label class="form-label">NIP</label>
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
                                                @foreach ($jamKerjas as $jam)
                                                    <option value="{{ $jam->id }}">
                                                        {{ $jam->nama_jam_kerja }}
                                                    </option>
                                                @endforeach
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
                        searchable: false
                    }
                ]
            });

            /* ================= TAMBAH ================= */
            $('#btnTambah').click(function() {
                $('#formPegawai')[0].reset();
                $('#pegawai_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('#modalPegawaiTitle').text('Tambah Pegawai');
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

                $('#modalPegawaiTitle').text('Edit Pegawai');
                $('#modalPegawai').modal('show');
            });

            /* ================= SUBMIT ================= */
            $('#formPegawai').submit(function(e) {
                e.preventDefault();

                let id = $('#pegawai_id').val();
                let url = id ?
                    "{{ url('pegawai/edit') }}/" + id :
                    "{{ route('pegawai.store') }}";

                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.fire('Sukses', res.message, 'success');
                        $('#modalPegawai').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        $('.is-invalid').removeClass('is-invalid');

                        if (err.status === 422) {
                            $.each(err.responseJSON.errors, function(key) {
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        }

                        Swal.fire('Error', 'Periksa input', 'error');
                    }
                });
            });

        });
    </script>
@endpush
