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
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-box {
            min-width: 200px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- 🔥 HEADER -->
        <div class="header-flex mb-3">
            <div>
                <h4 class="fw-bold mb-1">Verifikasi Konten Pegawai</h4>
                <p class="text-muted mb-0 small">Menampilkan data konten dari ruangan yang Anda ampu.</p>
            </div>

            <div class="d-flex gap-2 flex-wrap align-items-end">
                <!-- OPTIONAL: FILTER RUANGAN KHUSUS RUANGAN YANG DIAMPU USER (JIKALAU USER MEMILIKI > 1 RUANGAN) -->
                @if(isset($ruangans) && $ruangans->count() > 1)
                    <div class="filter-box">
                        <label class="form-label fw-semibold mb-1">Filter Ruangan Anda</label>
                        <select id="filterRuangan" class="form-select form-select-sm">
                            <option value="">Semua Akses Ruangan</option>
                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- FILTER TANGGAL -->
                <div class="filter-box">
                    <label class="form-label fw-semibold mb-1">Filter Tanggal</label>
                    <input type="text" id="filterTanggal" class="form-control form-control-sm" placeholder="Pilih rentang tanggal">
                </div>
            </div>
        </div>

        <!-- 🔥 TABLE -->
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
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let start_date = '';
        let end_date = '';
        let ruangan_id = '';
        let table;

        $(document).ready(function() {

            // 🔥 DATE RANGE
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Reset'
                }
            });

            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');
                $(this).val(start_date + ' s/d ' + end_date);
                table.ajax.reload();
            });

            $('#filterTanggal').on('cancel.daterangepicker', function() {
                $(this).val('');
                start_date = '';
                end_date = '';
                table.ajax.reload();
            });

            // 🔥 FILTER RUANGAN (Optional jika user mengampu > 1 ruangan)
            $('#filterRuangan').change(function() {
                ruangan_id = $(this).val();
                table.ajax.reload();
            });

            // 🔥 DATATABLE
            table = $('#kontenTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('admin.konten') }}",
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.ruangan_id = ruangan_id; // Tetap opsional dikirimkan jika difilter
                    }
                },

                columns: [
                    {
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
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // 🔥 AKSI VALID
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
                                text: 'Konten sudah divalidasi',
                                timer: 1500,
                                showConfirmButton: false
                            });

                        });
                    }
                });
            });


            // 🔥 AKSI TOLAK (SweetAlert input)
            $(document).on('click', '.btnTolak', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Tolak Konten',
                    text: 'Masukkan alasan penolakan',
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Contoh: Link tidak sesuai / bukti tidak valid...',
                    inputAttributes: {
                        'aria-label': 'Masukkan keterangan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',

                    inputValidator: (value) => {
                        if (!value) {
                            return 'Keterangan wajib diisi!';
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
                                text: 'Konten berhasil ditolak',
                                timer: 1500,
                                showConfirmButton: false
                            });

                        });
                    }
                });
            });

        });
    </script>
@endpush
