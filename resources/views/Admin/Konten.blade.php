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

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table.dataTable tbody tr:hover {
            background-color: #f1f5ff;
        }

        /* Filter Section Styling */
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }

        @media (max-width: 768px) {
            .btn-action-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }

            .btn-action-group .btn {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .btn-action-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }

            .btn-action-group .btn {
                width: 100%;
            }
        }

        /* 🔥 Tambahkan ini untuk memperbaiki posisi dropdown Select2 di dalam Modal */
        .select2-container--open {
            z-index: 99999 !important;
        }
    </style>
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- 🔥 HEADER TITLE -->
        <div class="header-flex mb-3">
            <div>
                <h4 class="fw-bold mb-1">Verifikasi Konten Pegawai</h4>
                <p class="text-muted mb-0 small">Menampilkan dan memverifikasi data konten dari ruangan yang Anda ampu.</p>
            </div>
        </div>

        <!-- 🔥 FILTER & EXPORT CARD -->
        <div class="card card-custom mb-4">
            <div class="card-body">
                <form id="formExportFilter" action="{{ route('admin.konten.export') }}" method="GET">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="bx bx-user me-1"></i> Nama Pegawai
                            </label>
                            <select name="id_pegawai" id="filterPegawai" class="form-select form-select-sm select2">
                                <option value="">Semua Pegawai</option>
                                @if (isset($pegawaiList))
                                    @foreach ($pegawaiList as $p)
                                        <option value="{{ $p->id }}"
                                            data-ruangans="{{ $p->absenkontens->pluck('id_ruangan')->unique()->implode(',') }}">
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- 2. FILTER RUANGAN -->
                        @if (isset($ruangans) && $ruangans->count() > 0)
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold text-dark small mb-1">
                                    <i class="bx bx-building-house me-1"></i> Filter Ruangan
                                </label>
                                <select name="id_ruangan" id="filterRuangan" class="form-select form-select-sm">
                                    <option value="">Semua Akses Ruangan</option>
                                    @foreach ($ruangans as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- 3. FILTER TANGGAL -->
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="bx bx-calendar me-1"></i> Rentang Tanggal
                            </label>
                            <input type="text" id="filterTanggal" class="form-control form-control-sm"
                                placeholder="Pilih Rentang Tanggal" readonly>
                            <input type="hidden" name="start_date" id="start_date">
                            <input type="hidden" name="end_date" id="end_date">
                        </div>

                        <!-- 4. FILTER STATUS VERIFIKASI -->
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="bx bx-check-shield me-1"></i> Status
                            </label>
                            <select name="status_verifikasi" id="filterStatus" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="pending">⏳ Pending</option>
                                <option value="valid">✅ Valid</option>
                                <option value="ditolak">❌ Ditolak</option>
                            </select>
                        </div>

                        <!-- 5. TOMBOL ACTION (RESET & EXPORT) -->
                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <button type="button" id="btnResetFilter" class="btn btn-sm btn-outline-secondary px-3"
                                title="Reset Filter">
                                <i class="bx bx-refresh me-1"></i> Reset Filter
                            </button>
                            <button type="submit" id="btnExport" class="btn btn-sm btn-success px-3 shadow-sm"
                                title="Export Laporan Excel">
                                <i class="bx bx-file me-1"></i> Export Excel
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- 🔥 TABLE DATATABLE -->
        <div class="card card-custom">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle nowrap w-100" id="kontenTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Pegawai</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Bukti</th>
                                <th>IG</th>
                                <th>FB</th>
                                <th>TikTok</th>
                                <th>Verifier</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
@endsection
@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let start_date = '';
        let end_date = '';
        let ruangan_id = '';
        let status_verifikasi = '';
        let id_pegawai = '';
        let table;

        $(document).ready(function() {

            // 🔥 1. SETUP DATE RANGE PICKER
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                opens: 'right',
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Reset'
                }
            });

            // 🔥 2. SETUP SELECT2 PEGAWAI
            $('#filterPegawai').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih / Cari Pegawai',
                allowClear: true,
                width: '100%'
            });

            const semuaPegawai = $('#filterPegawai option').clone();

            $('#filterPegawai').on('change', function() {
                id_pegawai = $(this).val() || '';
                if (table) {
                    table.ajax.reload();
                }
            });

            // 🔥 3. APPLY & RESET FILTER TANGGAL
            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);
                $(this).val(start_date + ' s/d ' + end_date);

                if (table) {
                    table.ajax.reload();
                }
            });

            $('#filterTanggal').on('cancel.daterangepicker', function() {
                resetTanggal();
            });

            function resetTanggal() {
                $('#filterTanggal').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                start_date = '';
                end_date = '';
                if (table) {
                    table.ajax.reload();
                }
            }

            // 🔥 4. FILTER RUANGAN & STATUS
            $('#filterRuangan').on('change', function() {

                ruangan_id = $(this).val() || '';
                id_pegawai = '';

                const select = $('#filterPegawai');

                // Balikkan semua pilihan pegawai
                select
                    .empty()
                    .append(semuaPegawai.clone());

                // Kalau memilih ruangan tertentu
                if (ruangan_id) {

                    select.find('option').each(function() {

                        // "Semua Pegawai" jangan dihapus
                        if (!$(this).val()) {
                            return;
                        }

                        const ruangans = String(
                            $(this).attr('data-ruangans') || ''
                        ).split(',');

                        // Hapus pegawai yang tidak pernah input di ruangan tersebut
                        if (!ruangans.includes(String(ruangan_id))) {
                            $(this).remove();
                        }
                    });
                }

                // Reset pilihan pegawai
                select
                    .val('')
                    .trigger('change.select2');

                // Reload tabel
                if (table) {
                    table.ajax.reload();
                }
            });


            $('#filterStatus').change(function() {
                status_verifikasi = $(this).val();

                if (table) {
                    table.ajax.reload();
                }
            });

            $('#filterStatus').change(function() {
                status_verifikasi = $(this).val();
                if (table) {
                    table.ajax.reload();
                }
            });

            // 🔥 5. RESET SEMUA FILTER
            $('#btnResetFilter').on('click', function() {

                id_pegawai = '';
                ruangan_id = '';
                status_verifikasi = '';

                $('#filterRuangan').val('');
                $('#filterStatus').val('');

                // Kembalikan list ke semua pegawai
                loadPegawaiByRuangan('');

                $('#filterTanggal').val('');
                $('#start_date').val('');
                $('#end_date').val('');

                start_date = '';
                end_date = '';

                if (table) {
                    table.ajax.reload();
                }
            });

            $(document).on('click', '.btnLihatBukti', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                let type = $(this).data('type');
                let container = $('#previewContainer');

                container.empty();

                if (type === 'pdf') {
                    // Menggunakan iframe agar langsung tampil di dalam modal
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

                var myModal = new bootstrap.Modal(document.getElementById('modalPreviewBukti'));
                myModal.show();
            });
            // 🔥 6. DATATABLE SETUP
            table = $('#kontenTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('admin.konten') }}",
                    data: function(d) {
                        d.id_pegawai = id_pegawai;
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.id_ruangan = ruangan_id;
                        d.status_verifikasi = status_verifikasi;
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_pegawai',
                        name: 'pegawai.name'
                    },
                    {
                        data: 'ruangan',
                        name: 'ruangan.nama_ruangan'
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'bukti',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'link_ig',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'link_fb',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'link_tiktok',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'verified_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false,
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
                    processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Memuat data...',
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

            // 🔥 7. AKSI VALIDASI KONTEN
            $(document).on('click', '.btnValid', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Validasi Konten?',
                    text: 'Pastikan konten sudah sesuai',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Valid!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('admin.konten.valid') }}", {
                            _token: '{{ csrf_token() }}',
                            id: id
                        }, function(res) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Konten telah divalidasi',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }).fail(function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ||
                                    'Gagal memvalidasi data'
                            });
                        });
                    }
                });
            });

            // 🔥 8. AKSI PENOLAKAN KONTEN
            $(document).on('click', '.btnTolak', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Tolak Konten',
                    text: 'Masukkan alasan penolakan',
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Contoh: Link tidak sesuai / bukti tidak valid...',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan penolakan wajib diisi!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('admin.konten.tolak') }}", {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            keterangan: result.value
                        }, function(res) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Konten telah ditolak',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }).fail(function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ||
                                    'Gagal menolak data'
                            });
                        });
                    }
                });
            });

        });
    </script>
@endpush
