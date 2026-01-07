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
            <span class="text-muted fw-light">Master /</span> Lokasi
        </h4>

        <div class="card">
            <h5 class="card-header">Data Lokasi</h5>

            <div class="card-body">
                <button class="btn btn-primary mb-3" id="btnTambah">
                    <i class="bx bx-plus"></i> Tambah Lokasi
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered" id="lokasiTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Radius</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalLokasi" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form id="formLokasi">
                    @csrf
                    <input type="hidden" id="lokasi_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lokasi</label>
                                <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" id="alamat">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="latitude" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitude" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Radius (meter)</label>
                                <input type="number" class="form-control" name="radius_meter" id="radius_meter"
                                    value="50">
                            </div>

                            <div class="col-md-12">
                                <button type="button" class="btn btn-info btn-sm mb-2" id="btnMyLocation">
                                    📍 Gunakan Lokasi Saya
                                </button>
                                <div id="map" style="height: 400px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let table;
        let map, marker, circle;

        // default koordinat
        const defaultLat = -8.133963571236817;
        const defaultLng = 113.82132126938346;

        $(document).ready(function() {

            /* ================= DATATABLE ================= */
            table = $('#lokasiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('lokasi.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lokasi'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'koordinat'
                    },
                    {
                        data: 'radius'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            /* ================= TAMBAH ================= */
            $('#btnTambah').click(function() {
                $('#formLokasi')[0].reset();
                $('#lokasi_id').val('');
                $('#modalTitle').text('Tambah Lokasi');
                $('#modalLokasi').modal('show');

                setTimeout(() => {
                    initMap(defaultLat, defaultLng, 50);
                }, 400);
            });

            /* ================= EDIT ================= */
            $(document).on('click', '.btn-edit', function() {
                $('#lokasi_id').val($(this).data('id'));
                $('#nama_lokasi').val($(this).data('nama'));
                $('#alamat').val($(this).data('alamat'));
                $('#latitude').val($(this).data('lat'));
                $('#longitude').val($(this).data('lng'));
                $('#radius_meter').val($(this).data('radius'));

                $('#modalTitle').text('Edit Lokasi');
                $('#modalLokasi').modal('show');

                setTimeout(() => {
                    initMap($(this).data('lat'), $(this).data('lng'), $(this).data('radius'));
                }, 400);
            });

            /* ================= SUBMIT ================= */
            $('#formLokasi').submit(function(e) {
                e.preventDefault();

                let id = $('#lokasi_id').val();
                let url = id ? `/lokasi/edit/${id}` : "{{ route('lokasi.store') }}";

                let formData = $(this).serializeArray();
                if (id) formData.push({
                    name: '_method',
                    value: 'PUT'
                });

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#modalLokasi').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message ?? 'Gagal menyimpan',
                            'error');
                    }
                });
            });

            /* ================= GEOLOCATION ================= */
            $('#btnMyLocation').click(function() {
                if (!navigator.geolocation) return;

                navigator.geolocation.getCurrentPosition(pos => {
                    initMap(pos.coords.latitude, pos.coords.longitude, $('#radius_meter').val());
                });
            });

            $('#radius_meter').on('input', function() {
                if (circle) circle.setRadius($(this).val());
            });
        });

        /* ================= MAP INIT ================= */
        const osm = L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }
        );

        const esri = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri — Source: Esri, Maxar, Earthstar Geographics'
            }
        );

        function initMap(lat, lng, radius) {

            if (map) map.remove();

            map = L.map('map', {
                center: [lat, lng],
                zoom: 16,
                layers: [osm] // default layer
            });

            // Base map switcher
            const baseMaps = {
                "OpenStreetMap": osm,
                "Satellite (Esri)": esri
            };

            L.control.layers(baseMaps).addTo(map);

            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            circle = L.circle([lat, lng], {
                radius: radius,
                color: 'blue',
                fillOpacity: 0.25
            }).addTo(map);

            $('#latitude').val(lat);
            $('#longitude').val(lng);

            marker.on('dragend', function(e) {
                let pos = e.target.getLatLng();
                updatePosition(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                updatePosition(e.latlng.lat, e.latlng.lng);
            });
        }

        function updatePosition(lat, lng) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);

            $('#latitude').val(lat);
            $('#longitude').val(lng);
        }

        $('#radius_meter').on('input', function() {
            if (circle) circle.setRadius($(this).val());
        });
        $(document).on('click', '.btn-delete', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus lokasi?',
                text: 'Data ini tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: `/lokasi/hapus/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false); // refresh DT
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus lokasi', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
