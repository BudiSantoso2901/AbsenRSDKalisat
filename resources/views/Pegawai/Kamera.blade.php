@extends('_layouts.layouts')

@section('content')
    <style>
        /* Container kamera */
        #my_camera {
            width: 270px;
            height: 270px;
            margin: auto;
            padding: 6px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f06292, #2ecc71);
            box-shadow:
                0 8px 20px rgba(9, 118, 18, 0.35),
                inset 0 0 0 1px rgba(255, 255, 255, 0.3);
        }

        /* Video kamera */
        #my_camera video {
            width: 100% !important;
            height: 100% !important;
            border-radius: 12px;
            object-fit: cover;
            background: #000;
            transform: scaleX(-1) !important;
        }

        /* Hasil foto */
        #results img {
            border-radius: 12px;
            transform: scaleX(1) !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Kamera /</span> Ambil Gambar & Lokasi
        </h4>

        <div class="row g-4">

            {{-- ================= CARD KAMERA (BARIS 1) ================= --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">
                        Kamera
                    </div>

                    <div class="card-body">
                        <small class="text-muted d-block mb-1">
                            Jangan lupa absen sebelum meninggalkan lokasi
                        </small>
                        {{-- Waktu --}}
                        <div class="mb-3">
                            <i class="bx bx-time"></i>
                            <small id="currentTime">--:--:--</small>
                        </div>

                        {{-- Kamera --}}
                        <div id="my_camera" class="mb-3"></div>

                        {{-- Hasil --}}
                        <div id="results" class="mb-3"></div>

                        <div class="text-center">
                            <button class="btn btn-success px-4 py-2" onclick="take_snapshot()">
                                <i class="bx bx-camera"></i> Ambil Foto
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= CARD MAP (BARIS 2) ================= --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">
                        Lokasi Pengguna
                    </div>

                    <div class="card-body">
                        <div class="mb-2">
                            <small>
                                Latitude: <b id="lat">-</b><br>
                                Longitude: <b id="lng">-</b>
                            </small>
                        </div>

                        <div id="map" style="height: 350px; width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <caption class="ms-4">
                                List of Projects
                            </caption>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu Datang</th>
                                    <th>Waktu Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 Januari 2026</td>
                                    <td>Albert Cook</td>
                                    <td>test</td>
                                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        /* ================= WAKTU REALTIME ================= */
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const timeString = now.toLocaleTimeString('id-ID');

            let waktu;
            let icon;

            if (hours >= 4 && hours < 11) {
                waktu = 'Pagi';
                icon = '🌅';
            } else if (hours >= 11 && hours < 15) {
                waktu = 'Siang';
                icon = '☀️';
            } else if (hours >= 15 && hours < 18) {
                waktu = 'Sore';
                icon = '🌤';
            } else {
                waktu = 'Malam';
                icon = '🌙';
            }

            document.getElementById('currentTime').innerText =
                `${icon} ${timeString} - ${waktu}`;
        }

        setInterval(updateTime, 1000);
        updateTime()

        /* ================= WEBCAM ================= */
        function getCameraSize() {
            if (window.innerWidth <= 768) {
                // Mobile
                return {
                    width: 260,
                    height: 260
                };
            } else {
                // Desktop
                return {
                    width: 420,
                    height: 420
                };
            }
        }

        const camSize = getCameraSize();
        Webcam.set({
            width: camSize.width,
            height: camSize.height,
            image_format: 'jpeg',
            jpeg_quality: 85,
            constraints: {
                facingMode: {
                    ideal: "environment"
                } // kamera belakang HP
            }
        });

        Webcam.attach('#my_camera');

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                document.getElementById('results').innerHTML = `
                                <img src="${data_uri}" class="img-fluid rounded mt-2">
                            `;
            });
        }

        /* ================= MAP ================= */
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

        const map = L.map('map', {
            center: [-8.219233, 114.369141],
            zoom: 15,
            layers: [esri] // default satelit
        });

        L.control.layers({
            "OpenStreetMap": osm,
            "Satelit": esri
        }).addTo(map);

        let marker;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('lat').innerText = lat.toFixed(6);
                    document.getElementById('lng').innerText = lng.toFixed(6);

                    map.setView([lat, lng], 17);

                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup('Lokasi Anda Saat Ini')
                        .openPopup();
                },
                function(error) {
                    alert('Gagal mengambil lokasi. Aktifkan GPS.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        }
    </script>
@endpush
