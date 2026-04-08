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
                                <th>Keterangan</th>
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
@endsection
@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let start_date = '';
        let end_date = '';
        let table;

        $(document).ready(function() {

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

            // APPLY
            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');

                $(this).val(start_date + ' s/d ' + end_date);
                table.ajax.reload();
            });

            // RESET
            $('#filterTanggal').on('cancel.daterangepicker', function() {
                $(this).val('');
                start_date = '';
                end_date = '';
                table.ajax.reload();
            });

            // 🔥 DATATABLE
            table = $('#kontenTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true, // 🔥 penting untuk mobile

                ajax: {
                    url: "{{ route('pegawai.konten.index') }}",
                    data: function(d) {
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
                    {
                        data: 'keterangan',
                        orderable: false
                    },
                    {
                        data: 'verified_by',
                        orderable: false
                    },
                    {
                        data: 'status',
                        orderable: false
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
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

            // 🔥 ALERT
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

        });
        $(document).on('click', '.btnEdit', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Perbaiki Konten',
                html: `
            <input type="text" id="link_ig" class="swal2-input" placeholder="Link Instagram">
            <input type="text" id="link_fb" class="swal2-input" placeholder="Link Facebook">
            <input type="text" id="link_tiktok" class="swal2-input" placeholder="Link TikTok">
            <input type="file" id="bukti_foto" class="swal2-file">
        `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {

                    let formData = new FormData();

                    formData.append('id', id);
                    formData.append('_token', '{{ csrf_token() }}');

                    formData.append('link_ig', $('#link_ig').val());
                    formData.append('link_fb', $('#link_fb').val());
                    formData.append('link_tiktok', $('#link_tiktok').val());

                    let file = $('#bukti_foto')[0].files[0];
                    if (file) {
                        formData.append('bukti_foto', file);
                    }

                    return $.ajax({
                        url: "{{ route('pegawai.konten.update') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).then(response => {
                        return response;
                    }).catch(() => {
                        Swal.showValidationMessage('Gagal update');
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    table.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil diperbaiki & dikirim ulang'
                    });
                }
            });
        });
    </script>
@endpush
