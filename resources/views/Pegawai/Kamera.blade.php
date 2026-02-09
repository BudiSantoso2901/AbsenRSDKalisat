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

        .btn-absen {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px;
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
            transition: all 0.25s ease;
        }

        .btn-absen:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(40, 167, 69, 0.55);
        }

        .btn-absen:active {
            transform: scale(0.98);
        }

        .alert-lokasi {
            background: linear-gradient(135deg, #f06292, #2ecc71);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.15);
        }

        .alert-lokasi strong {
            color: #fff;
        }

        .alert-lokasi i {
            font-size: 1.1rem;
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
                            <div class="alert alert-lokasi mt-3">
                                <strong>Lokasi:</strong> {{ $lokasi->nama_lokasi }} <br>
                                <strong>Radius:</strong> {{ $lokasi->radius_meter }} meter <br>
                                <strong>Jam Kerja:</strong>
                                {{ $jamKerja->jam_mulai }} - {{ $jamKerja->jam_selesai }}
                                <br>
                                <strong>
                                    <i class="bx bx-time"></i>
                                    <small id="currentTime">--:--:--</small>
                                </strong>
                            </div>
                        </div>
                        <form id="form-absensi" enctype="multipart/form-data">
                            @csrf

                            {{-- STATUS --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Absensi</label>
                                <select class="form-select" name="status" id="status_absen">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </div>

                            {{-- ================= HADIR ================= --}}
                            <div id="section-hadir">

                                <div id="my_camera" class="mb-3"></div>
                                {{-- <input type="hidden" name="foto" id="fotoBase64"> --}}
                                <input type="hidden" name="latitude" id="inputLat">
                                <input type="hidden" name="longitude" id="inputLng">

                            </div>

                            {{-- ================= IZIN / SAKIT ================= --}}
                            <div id="section-izin" style="display:none">

                                <div class="mb-3">
                                    <label class="form-label">Keterangan <strong>*</strong></label>
                                    <textarea class="form-control" name="keterangan" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Upload Surat <strong>*</strong></label>
                                    <input type="file" name="surat" class="form-control">
                                </div>

                            </div>
                            <button class="btn btn-absen w-100 mt-2" type="submit">
                                <i class="bx bx-camera"></i> Absen
                            </button>
                        </form>
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
                                Data Absensi Bulan Ini
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
                                @php
                                    $shifts = [
                                        [
                                            'nama' => 'Shift Pagi',
                                            'jam_mulai' => '07:00:00',
                                            'jam_selesai' => '14:00:00',
                                            'toleransi' => 10, // TL mulai dihitung setelah jam mulai + 10 menit
                                            'early_allowed' => 120, // datang maksimal 30 menit lebih awal
                                        ],
                                        [
                                            'nama' => 'Shift Siang',
                                            'jam_mulai' => '14:00:00',
                                            'jam_selesai' => '20:00:00',
                                            'toleransi' => 10,
                                            'early_allowed' => 120,
                                        ],
                                        [
                                            'nama' => 'Shift Malam',
                                            'jam_mulai' => '20:00:00',
                                            'jam_selesai' => '07:00:00',
                                            'toleransi' => 10,
                                            'early_allowed' => 120,
                                        ],
                                    ];
                                @endphp

                                @foreach ($absensi as $row)
                                    @php
                                        $badge = null;

                                        if ($row->status === 'hadir' && $row->waktu_masuk) {
                                            $waktuMasuk = \Carbon\Carbon::parse($row->waktu_masuk, 'Asia/Jakarta');
                                            $tanggalMasuk = $waktuMasuk->toDateString();

                                            foreach ($shifts as $shift) {
                                                $jamMulai = \Carbon\Carbon::createFromFormat(
                                                    'Y-m-d H:i:s',
                                                    $tanggalMasuk . ' ' . $shift['jam_mulai'],
                                                    'Asia/Jakarta',
                                                );

                                                // hitung jam selesai shift
                                                if ($shift['jam_selesai'] < $shift['jam_mulai']) {
                                                    $jamSelesai = \Carbon\Carbon::createFromFormat(
                                                        'Y-m-d H:i:s',
                                                        $tanggalMasuk . ' ' . $shift['jam_selesai'],
                                                        'Asia/Jakarta',
                                                    )->addDay();
                                                } else {
                                                    $jamSelesai = \Carbon\Carbon::createFromFormat(
                                                        'Y-m-d H:i:s',
                                                        $tanggalMasuk . ' ' . $shift['jam_selesai'],
                                                        'Asia/Jakarta',
                                                    );
                                                }

                                                // batas hadir lebih awal
                                                $jamMulaiEarly = $jamMulai->copy()->subMinutes($shift['early_allowed']);
                                                // batas telat
                                                $jamMulaiToleransi = $jamMulai->copy()->addMinutes($shift['toleransi']);

                                                if ($waktuMasuk->between($jamMulaiEarly, $jamMulaiToleransi)) {
                                                    // hadir on time / early diperbolehkan → TL0
                                                    $badge = null; // atau bisa isi 'TL0'
                                                } elseif ($waktuMasuk->gt($jamMulaiToleransi)) {
                                                    // hadir telat → hitung TL1–TL4
                                                    $menitTelat = $jamMulaiToleransi->diffInMinutes($waktuMasuk);
                                                    $badge = match (true) {
                                                        $menitTelat <= 30 => 'TL1',
                                                        $menitTelat <= 60 => 'TL2',
                                                        $menitTelat <= 90 => 'TL3',
                                                        default => 'TL4',
                                                    };
                                                }

                                                // cek apakah masuk di shift ini
                                                if ($waktuMasuk->between($jamMulaiEarly, $jamSelesai)) {
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                        </td>

                                        {{-- Waktu hadir --}}
                                        <td>
                                            {{ $row->waktu_masuk ? \Carbon\Carbon::parse($row->waktu_masuk)->format('H:i') : '-' }}
                                            @if ($badge)
                                                <span class="badge bg-warning ms-1">{{ $badge }}</span>
                                            @endif
                                        </td>

                                        {{-- Waktu pulang --}}
                                        <td>
                                            {{ $row->waktu_pulang
                                                ? \Carbon\Carbon::parse($row->waktu_pulang)->locale('id')->translatedFormat('l, j F Y h.i A')
                                                : '-' }}
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <span class="badge bg-success">{{ strtoupper($row->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
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
        /* ================= Tampilan  ================= */
        const statusSelect = document.getElementById('status_absen');
        const sectionHadir = document.getElementById('section-hadir');
        const sectionIzin = document.getElementById('section-izin');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'hadir') {
                sectionHadir.style.display = 'block';
                sectionIzin.style.display = 'none';
            } else {
                sectionHadir.style.display = 'none';
                sectionIzin.style.display = 'block';
            }
        });

        /* ================= FORM SUBMIT ================= */
        document.getElementById('form-absensi').addEventListener('submit', function(e) {
            e.preventDefault();

            const status = statusSelect.value;
            const formData = new FormData(this);

            // ================= VALIDASI IZIN / SAKIT =================
            if (status !== 'hadir') {
                formData.delete('foto');
                const keteranganField = this.querySelector('textarea[name="keterangan"]');
                const keterangan = keteranganField ? keteranganField.value.trim() : '';

                if (!keterangan.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Keterangan wajib diisi untuk izin atau sakit'
                    });
                    return;
                }

                kirim(formData);
                return;
            }

            // ================= VALIDASI HADIR =================
            if (typeof Webcam === 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera tidak aktif',
                    text: 'Webcam belum siap'
                });
                return;
            }
            formData.delete('keterangan');
            formData.delete('surat');
            Webcam.snap(function(data_uri) {
                formData.append('foto', dataURItoBlob(data_uri), 'absen.jpg');
                kirim(formData);
            });
        });

        /* ================= AJAX ================= */
        function kirim(formData) {
            fetch("{{ route('absensi.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    return {
                        status: res.status,
                        data
                    };
                })
                .then(({
                    status,
                    data
                }) => {

                    if (status === 422) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: data.message
                        });
                        return;
                    }

                    if (data.telat === true) {
                        Swal.fire({
                            icon: 'warning',
                            title: '⚠️ Anda Terlambat',
                            html: `
                        <b>${data.menitTelat} menit</b><br>
                        Badge: <b>${data.badge}</b>
                    `
                        }).then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Absen masuk berhasil (terlambat)'
                            }).then(() => location.reload());
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message
                    }).then(() => location.reload());
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan server'
                    });
                });
        }

        /* ================= HELPER ================= */
        function dataURItoBlob(dataURI) {
            const byteString = atob(dataURI.split(',')[1]);
            const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
            return new Blob([ab], {
                type: mimeString
            });
        }

        /* ================= WAKTU REALTIME ================= */
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const timeString = now.toLocaleTimeString('id-ID');

            let waktu;
            let icon;

            if (hours >= 4 && hours < 11) {
                waktu = 'Pagi';
                icon = '🌅 ';
            } else if (hours >= 11 && hours < 15) {
                waktu = 'Siang';
                icon = '☀️ ';
            } else if (hours >= 15 && hours < 18) {
                waktu = 'Sore';
                icon = '🌤 ';
            } else {
                waktu = 'Malam';
                icon = '🌙 ';
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
                facingMode: "user" // kamera depan
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
        const lokasiPegawai = {
            lat: {{ $lokasi->latitude }},
            lng: {{ $lokasi->longitude }},
            radius: {{ $lokasi->radius_meter }},
            nama: "{{ $lokasi->nama_lokasi }}"
        };
        const osm = L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }
        );

        const esri = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri'
            }
        );

        // ================= MAP INIT =================
        const map = L.map('map', {
            center: [lokasiPegawai.lat, lokasiPegawai.lng],
            zoom: 17,
            layers: [esri]
        });

        L.control.layers({
            "OpenStreetMap": osm,
            "Satelit": esri
        }).addTo(map);

        // ================= MARKER LOKASI ABSENSI =================
        const lokasiMarker = L.marker([lokasiPegawai.lat, lokasiPegawai.lng])
            .addTo(map)
            .bindPopup(`Lokasi Absensi<br><b>${lokasiPegawai.nama}</b>`)
            .openPopup();

        // ================= RADIUS ABSENSI =================
        const radiusCircle = L.circle(
            [lokasiPegawai.lat, lokasiPegawai.lng], {
                radius: lokasiPegawai.radius,
                color: 'green',
                fillColor: '#4CAF50',
                fillOpacity: 0.2
            }
        ).addTo(map);

        let marker;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('lat').innerText = lat.toFixed(6);
                    document.getElementById('lng').innerText = lng.toFixed(6);
                    document.getElementById('inputLat').value = lat;
                    document.getElementById('inputLng').value = lng;
                    map.setView([lat, lng], 17);

                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup('Lokasi Anda Saat Ini')
                        .openPopup();
                },
                function(error) {
                    alert('Gagal mengambil lokasi. Aktifkan GPS.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    </script>
@endpush
