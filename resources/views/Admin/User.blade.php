@extends('_layouts.layouts')

@push('styles')
    <style>
        .swal2-container {
            z-index: 2000 !important;
        }

        /* Penyesuaian Z-Index Select2 di dalam Bootstrap Modal */
        .select2-container--bootstrap-5 {
            z-index: 1060 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Tables /</span> User Admin
        </h4>

        <div class="card">
            <h5 class="card-header">List User Admin</h5>

            <div class="table-responsive p-3">

                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah User
                </button>

                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th width="5%">NO</th>
                            <th>NAMA</th>
                            <th>EMAIL</th>
                            <th>RUANGAN HAK AKSES</th>
                            <th width="20%">AKSI</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!-- 🔥 MODAL -->
    <div class="modal fade" id="modalUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="formUser">
                    @csrf
                    <input type="hidden" id="user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control mb-2" id="name" required
                            placeholder="Isikan Nama Admin">

                        <label class="form-label">Email</label>
                        <input type="email" class="form-control mb-2" id="email" required
                            placeholder="Isikan Email Admin">

                        <!-- 🔥 MULTIPLE SELECT2 UNTUK RUANGAN -->
                        <div class="mb-2">
                            <label class="form-label">Akses Ruangan Verifikasi</label>
                            <select class="form-select" id="ruangan_ids" name="ruangan_ids[]" multiple="multiple"
                                data-placeholder="Cari dan pilih ruangan...">
                            </select>
                            <div class="form-text">Bisa memilih lebih dari satu ruangan. Ketik untuk mencari.</div>
                        </div>

                        <label class="form-label">Password</label>
                        <input type="password" class="form-control mb-2" id="password"
                            placeholder="Kosongkan jika tidak ingin mengubah password">

                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control mb-2" id="password_confirmation"
                            placeholder="Kosongkan jika tidak ingin mengubah password">

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

            const routes = {
                index: "{{ route('user.index') }}",
                store: "{{ route('user.store') }}",
                show: "{{ route('user.show', ':id') }}",
                update: "{{ route('user.update', ':id') }}",
                delete: "{{ route('user.destroy', ':id') }}",
                ruanganList: "{{ route('ruangan.index') }}" // Route penyedia data master ruangan
            };

            // 1. Inisialisasi Select2
            $('#ruangan_ids').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalUser'),
                width: '100%',
                allowClear: true
            });

            // 2. Fetch Master Ruangan ke Dropdown Select2
            function loadMasterRuangan() {
                $.ajax({
                    url: routes.ruanganList,
                    type: 'GET',
                    success: function(res) {
                        let data = res.data || res;
                        let select = $('#ruangan_ids');
                        select.empty();

                        data.forEach(function(item) {
                            let option = new Option(item.nama_ruangan, item.id, false, false);
                            select.append(option);
                        });

                        select.trigger('change');
                    },
                    error: function() {
                        console.error('Gagal memuat master ruangan');
                    }
                });
            }

            loadMasterRuangan();

            // 3. Setup DataTables
            let table = $('#userTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: routes.index
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'ruangans',
                        render: function(ruangans) {
                            if (!ruangans || ruangans.length === 0) {
                                return '<span class="badge bg-label-secondary">Tidak ada akses</span>';
                            }
                            return ruangans.map(r =>
                                `<span class="badge bg-label-primary me-1 mb-1">${r.nama_ruangan}</span>`
                                ).join('');
                        }
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                                <button class="btn btn-warning btn-sm btnEdit" data-id="${id}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm btnDelete" data-id="${id}">
                                    Hapus
                                </button>
                            `;
                        }
                    }
                ]
            });

            // 4. Tambah User
            $('#btnTambah').click(function() {
                $('#formUser')[0].reset();
                $('#user_id').val('');
                $('#ruangan_ids').val(null).trigger('change'); // Reset Select2
                $('#modalTitle').text('Tambah User');
                $('#modalUser').modal('show');
            });

            // 5. Edit User
            $(document).on('click', '.btnEdit', function() {
                let id = $(this).data('id');
                let urlShow = routes.show.replace(':id', id);

                $.ajax({
                    url: urlShow,
                    type: 'GET',
                    success: function(user) {
                        $('#user_id').val(user.id);
                        $('#name').val(user.name);
                        $('#email').val(user.email);
                        $('#password').val('');
                        $('#password_confirmation').val('');

                        // Set nilai Terpilih pada Multiple Select2
                        let selectedIds = user.ruangans ? user.ruangans.map(r => r.id) : [];
                        $('#ruangan_ids').val(selectedIds).trigger('change');

                        $('#modalTitle').text('Edit User');
                        $('#modalUser').modal('show');
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Tidak dapat mengambil data user', 'error');
                    }
                });
            });

            // 6. Simpan (Create / Update)
            $('#formUser').submit(function(e) {
                e.preventDefault();

                let id = $('#user_id').val();
                let isEdit = !!id;

                $('#modalUser').modal('hide');

                setTimeout(() => {
                    Swal.fire({
                        title: 'Masukkan Kode Akses',
                        input: 'password',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        allowOutsideClick: false,
                        focusConfirm: false,
                        didOpen: () => {
                            const input = document.querySelector('.swal2-input');
                            if (input) input.focus();
                        },
                        inputValidator: (value) => {
                            if (!value) return 'Kode wajib diisi!';
                        }
                    }).then((result) => {

                        if (!result.isConfirmed) {
                            $('#modalUser').modal('show');
                            return;
                        }

                        let url = isEdit ?
                            routes.update.replace(':id', id) :
                            routes.store;

                        let method = isEdit ? 'PUT' : 'POST';

                        let payload = {
                            _token: "{{ csrf_token() }}",
                            name: $('#name').val(),
                            email: $('#email').val(),
                            kode_akses: result.value,
                            ruangan_ids: $('#ruangan_ids').val() ||
                            [] // Array ID ruangan dari Select2
                        };

                        let password = $('#password').val();
                        if (password) {
                            payload.password = password;
                            payload.password_confirmation = $('#password_confirmation')
                            .val();
                        }

                        $.ajax({
                            url: url,
                            type: method,
                            data: payload,
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: isEdit ? 'User diperbarui' :
                                        'User ditambahkan',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ??
                                        'Terjadi kesalahan'
                                });
                            }
                        });

                    });

                }, 300);
            });

            // 7. Hapus
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Masukkan Kode Hapus',
                    input: 'password',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    inputValidator: (value) => {
                        if (!value) return 'Kode wajib diisi!';
                    }
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    let url = routes.delete.replace(':id', id);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_akses: result.value
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus',
                                timer: 1200,
                                showConfirmButton: false
                            });

                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ?? 'Kode salah'
                            });
                        }
                    });

                });
            });

        });
    </script>
@endpush
