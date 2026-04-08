@extends('_layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Tables /</span> Ruangan
        </h4>

        <div class="card">
            <h5 class="card-header">List Ruangan</h5>

            <div class="table-responsive p-3">

                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah Ruangan
                </button>

                <table class="table table-bordered" id="ruanganTable">
                    <thead>
                        <tr>
                            <th width="5%">NO</th>
                            <th>NAMA RUANGAN</th>
                            <th width="15%">AKSI</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!-- 🔥 MODAL -->
    <div class="modal fade" id="modalRuangan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="formRuangan">
                    @csrf
                    <input type="hidden" id="ruangan_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" id="nama_ruangan" required>
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
        $(document).ready(function() {

            let table = $('#ruanganTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('ruangan.index') }}"
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'nama_ruangan'
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            return `
                        <button class="btn btn-warning btn-sm btnEdit"
                            data-id="${id}"
                            data-nama="${row.nama_ruangan}">
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

            // 🔥 TAMBAH
            $('#btnTambah').click(function() {
                $('#formRuangan')[0].reset();
                $('#ruangan_id').val('');
                $('#modalTitle').text('Tambah Ruangan');
                $('#modalRuangan').modal('show');
            });

            // 🔥 EDIT
            $(document).on('click', '.btnEdit', function() {
                $('#ruangan_id').val($(this).data('id'));
                $('#nama_ruangan').val($(this).data('nama'));
                $('#modalTitle').text('Edit Ruangan');
                $('#modalRuangan').modal('show');
            });

            // 🔥 SIMPAN
            $('#formRuangan').submit(function(e) {
                e.preventDefault();

                let id = $('#ruangan_id').val();
                let url = id ?
                    `/ruangan/update/${id}` :
                    `{{ route('ruangan.store') }}`;

                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_ruangan: $('#nama_ruangan').val()
                    },
                    success: function(res) {
                        $('#modalRuangan').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: id ? 'Ruangan diperbarui' : 'Ruangan ditambahkan',
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

            // 🔥 HAPUS
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data ruangan akan dihapus',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/ruangan/delete/${id}`,
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
