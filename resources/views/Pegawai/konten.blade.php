@extends('_layouts.layouts')

@section('content')
    <style>
        .card-custom {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-valid {
            background: #d4edda;
            color: #155724;
        }

        .badge-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-absen {
            background-color: #28a745;
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* 🔥 Responsive header */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-box {
            min-width: 200px;
        }

        /* 🔥 Table mobile fix */
        .table-responsive {
            overflow-x: auto;
        }

        table.dataTable tbody tr:hover {
            background-color: #f1f5ff;
        }

        @media (max-width: 768px) {
            .header-flex {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-absen {
                width: 100%;
            }

            .filter-box {
                width: 100%;
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- 🔥 HEADER -->
        <div class="header-flex mb-3">
            <h4 class="fw-bold mb-0">Absen Konten Pegawai</h4>

            <div class="filter-box">
                <label class="form-label fw-semibold">Filter Tanggal</label>
                <input type="text" id="filterTanggal" class="form-control" placeholder="Pilih tanggal">
            </div>

            <a href="{{ route('pegawai.konten.create') }}" class="btn btn-absen">
                <i class="bx bx-plus"></i> Tambah Konten
            </a>
        </div>

        <!-- 🔥 TABLE -->
        <div class="card card-custom">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle nowrap w-100" id="kontenTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Bukti</th>
                                <th>Instagram</th>
                                <th>Facebook</th>
                                <th>TikTok</th>
                                {{-- <th>Keterangan</th> --}}
                                <th>Verifikasi Oleh</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- MODAL PREVIEW BUKTI -->
    <div class="modal fade" id="modalPreviewBukti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">📎 Preview Bukti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="min-height: 70vh;">
                    <div id="previewContainer" style="width:100%; height:70vh;"></div>
                </div>
                <div class="modal-footer">
                    <a id="btnBukaTabBaru" href="#" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-link-external"></i> Buka di Tab Baru
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!--  MODAL EDIT / PERBAIKI KONTEN -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formUpdateKonten" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalTitleEdit">✏️ Edit Absen Konten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- 🔥 ALASAN PENOLAKAN DARI FIELD 'keterangan' (TAMPIL KHUSUS STATUS DITOLAK) -->
                        <div class="alert alert-danger d-flex align-items-center mb-3 d-none" id="boxAlasanDitolak"
                            role="alert">
                            <i class="bx bx-error-circle me-2 fs-3"></i>
                            <div>
                                <strong>Alasan Ditolak:</strong><br>
                                <span id="textAlasanDitolak">-</span>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Link Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bxl-instagram text-danger fs-5"></i></span>
                                <input type="url" name="link_ig" id="edit_link_ig" class="form-control"
                                    placeholder="https://instagram.com/...">
                            </div>
                        </div>

                        <!-- Facebook -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Link Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bxl-facebook text-primary fs-5"></i></span>
                                <input type="url" name="link_fb" id="edit_link_fb" class="form-control"
                                    placeholder="https://facebook.com/...">
                            </div>
                        </div>

                        <!-- TikTok -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Link TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bxl-tiktok text-dark fs-5"></i></span>
                                <input type="url" name="link_tiktok" id="edit_link_tiktok" class="form-control"
                                    placeholder="https://tiktok.com/...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                GANTI BUKTI FOTO/PDF (OPSIONAL)
                            </label>

                            <div class="input-group">
                                <button type="button" class="btn btn-outline-primary" id="btnChooseFile">
                                    Choose File
                                </button>

                                <input type="text" id="nama_file_display" class="form-control"
                                    placeholder="Tidak ada file dipilih" readonly>
                            </div>

                            <input type="file" name="bukti_foto" id="edit_bukti_foto" class="d-none" accept="image/*,.pdf">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-semibold" id="btnSimpanUpdate">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let start_date = '';
        let end_date = '';
        let table;

        $(document).ready(function () {
            $(document).on('click', '.btnLihatBukti', function () {
                let url = $(this).data('url');
                let type = $(this).data('type');
                let container = $('#previewContainer');

                container.empty();

                if (type === 'pdf') {
                    container.html(
                        '<iframe src="' + url +
                        '" style="width:100%; height:70vh; border:none;"></iframe>'
                    );
                } else {
                    container.html(
                        '<div class="d-flex justify-content-center align-items-center h-100">' +
                        '<img src="' + url +
                        '" style="max-width:100%; max-height:70vh; object-fit:contain;">' +
                        '</div>'
                    );
                }

                $('#btnBukaTabBaru').attr('href', url);
                $('#modalPreviewBukti').modal('show');
            });

            // 🔥 BERSIHKAN PREVIEW SAAT MODAL DITUTUP (hemat memori & hentikan load PDF)
            $('#modalPreviewBukti').on('hidden.bs.modal', function () {
                $('#previewContainer').empty();
                $('#btnBukaTabBaru').attr('href', '#');
            });

            // 🔥 DATE RANGE
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                opens: 'right',
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Reset'
                }
            });

            // APPLY FILTER TANGGAL
            $('#filterTanggal').on('apply.daterangepicker', function (ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');

                $(this).val(start_date + ' s/d ' + end_date);
                table.ajax.reload();
            });

            // RESET FILTER TANGGAL
            $('#filterTanggal').on('cancel.daterangepicker', function () {
                $(this).val('');
                start_date = '';
                end_date = '';
                table.ajax.reload();
            });

            // 🔥 DATATABLE SETUP
            table = $('#kontenTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,

                ajax: {
                    url: "{{ route('pegawai.konten.index') }}",
                    data: function (d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },

                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'bukti',
                    orderable: false
                },
                {
                    data: 'link_ig',
                    orderable: false
                },
                {
                    data: 'link_fb',
                    orderable: false
                },
                {
                    data: 'link_tiktok',
                    orderable: false
                },
                // {
                //     data: 'keterangan',
                //     orderable: false
                // },
                {
                    data: 'verified_by',
                    orderable: false
                },
                {
                    data: 'status',
                    orderable: false,
                    responsivePriority: 2
                },
                {
                    data: 'aksi',
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1
                }
                ],

                language: {
                    processing: "Memproses...",
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        next: "→",
                        previous: "←"
                    }
                }
            });

            // 🔥 SESSION SWEETALERT NOTIFICATION
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}'
                });
            @endif
            $(document).on('click', '.btnEdit', function () {

                let btn = $(this);

                let id = btn.data('id');
                let ig = btn.data('ig') || '';
                let fb = btn.data('fb') || '';
                let tiktok = btn.data('tiktok') || '';
                let status = $.trim(btn.data('status') || '').toLowerCase();
                let bukti = btn.attr('data-bukti-foto') || '';
                let keterangan = btn.data('keterangan') || 'Tidak ada alasan khusus';

                $('#edit_id').val(id);
                $('#edit_link_ig').val(ig);
                $('#edit_link_fb').val(fb);
                $('#edit_link_tiktok').val(tiktok);

                $('#edit_bukti_foto').val('');

                if (bukti) {
                    let namaFile = bukti.split('/').pop();
                    $('#nama_file_display').val(namaFile);
                } else {
                    $('#nama_file_display').val('Tidak ada file');
                }

                if (status === 'ditolak') {
                    $('#modalTitleEdit').html('✏️ Perbaiki Konten Ditolak');

                    $('#boxAlasanDitolak')
                        .removeClass('d-none')
                        .addClass('d-flex');

                    $('#textAlasanDitolak').html(keterangan);
                } else {
                    $('#modalTitleEdit').html('✏️ Edit Absen Konten');

                    $('#boxAlasanDitolak')
                        .addClass('d-none')
                        .removeClass('d-flex');
                }

                $('#modalEdit').modal('show');
            });

            $(document).on('click', '#btnChooseFile', function () {
                $('#edit_bukti_foto').click();
            });

            $(document).on('change', '#edit_bukti_foto', function () {

                if (this.files && this.files.length > 0) {
                    let namaFileBaru = this.files[0].name;
                    $('#nama_file_display').val(namaFileBaru);
                }
            });
            $('#formUpdateKonten').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);
                let btn = $('#btnSimpanUpdate');

                btn.prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ route('pegawai.konten.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#modalEdit').modal('hide');
                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Gagal memperbarui data';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('💾 Simpan Perubahan');
                    }
                });
            });

        });
    </script>
@endpush
