@extends('mobil-cinta.layouts.app')

@section('title', 'Dashboard Sopir')

@push('styles')
    <style>
        .driver-dashboard {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        .driver-welcome {
            padding: 21px;
            margin-bottom: 15px;
            color: #FFFFFF;
            border-radius: 17px;
            background: linear-gradient(120deg, #078B48, #16B96A);
            box-shadow: 0 7px 20px rgba(8, 120, 60, .14);
        }

        .driver-welcome h1 {
            margin: 5px 0;
            color: #FFFFFF;
            font-size: 21px;
            line-height: 1.3;
        }

        .driver-welcome p {
            margin: 0;
            color: rgba(255, 255, 255, .88);
            font-size: 12px;
        }

        .driver-welcome small {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .5px;
            opacity: .9;
        }

        .driver-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .driver-summary-item {
            padding: 15px;
            background: #FFFFFF;
            border: 1px solid #D7EDE0;
            border-radius: 13px;
            box-shadow: 0 4px 14px rgba(8, 100, 55, .035);
        }

        .driver-summary-number {
            display: block;
            color: #078B48;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
        }

        .driver-summary-label {
            color: #62756A;
            font-size: 11px;
            font-weight: 700;
        }

        .driver-section-title {
            margin: 20px 0 12px;
            color: #18352A;
            font-size: 16px;
            font-weight: 800;
        }

        .driver-trip-card {
            padding: 16px;
            margin-bottom: 12px;
            background: #FFFFFF;
            border: 1px solid #D7EDE0;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(8, 100, 55, .045);
        }

        .driver-trip-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .driver-trip-number {
            display: block;
            color: #078B48;
            font-size: 10px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .driver-trip-destination {
            margin: 4px 0 0;
            color: #18352A;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .driver-status {
            flex-shrink: 0;
            padding: 5px 9px;
            color: #087E48;
            background: #EAF8F0;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
        }

        .driver-trip-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 11px;
            padding: 12px 0;
            border-top: 1px solid #EDF3EE;
            border-bottom: 1px solid #EDF3EE;
        }

        .driver-info-item small {
            display: block;
            color: #829087;
            font-size: 10px;
        }

        .driver-info-item strong {
            display: block;
            margin-top: 2px;
            color: #304A3A;
            font-size: 12px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .driver-rejection-note {
            margin-top: 12px;
            padding: 11px 12px;
            background: #FFF8E8;
            border: 1px solid #F0DCA8;
            border-radius: 10px;
        }

        .driver-rejection-note strong {
            display: block;
            margin-bottom: 3px;
            color: #8A5A08;
            font-size: 11px;
        }

        .driver-rejection-note p {
            margin: 0;
            color: #705721;
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .driver-action-panel {
            margin-top: 13px;
            border: 1px solid #CDE9D8;
            border-radius: 11px;
            overflow: hidden;
        }

        .driver-action-panel summary {
            padding: 12px 14px;
            color: #087E48;
            background: #F3FAF5;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            list-style: none;
        }

        .driver-action-panel summary::-webkit-details-marker {
            display: none;
        }

        .driver-action-panel summary::after {
            content: "+";
            float: right;
            font-size: 17px;
            line-height: 1;
        }

        .driver-action-panel[open] summary::after {
            content: "−";
        }

        .driver-form {
            padding: 14px;
            background: #FFFFFF;
        }

        .driver-form label {
            display: block;
            margin-bottom: 12px;
            color: #4D6254;
            font-size: 12px;
            font-weight: 700;
        }

        .driver-form input,
        .driver-form textarea {
            display: block;
            margin-top: 5px;
            font-size: 13px;
        }

        .driver-form input[readonly] {
            background: #F4F8F5;
        }

        .driver-form textarea {
            min-height: 76px;
        }

        .driver-gps-message {
            display: block;
            margin: 9px 0;
            color: #62756A;
            font-size: 11px;
        }

        .driver-form .btn {
            width: 100%;
            min-height: 43px;
        }

        .driver-form .get-gps {
            margin-top: 2px;
        }

        .driver-photo-group {
            margin-bottom: 14px;
        }

        .driver-photo-group>label {
            display: block;
            margin-bottom: 7px;
            color: #4D6254;
            font-size: 12px;
            font-weight: 700;
        }

        /* Input tetap ada untuk dikirim sebagai multipart/form-data,
                                                   tetapi pemilihan foto dilakukan melalui popup kamera. */
        .driver-camera-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .driver-photo-result {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 22px;
            margin-top: 9px;
        }

        .driver-photo-preview {
            width: 76px;
            height: 76px;
            flex: 0 0 76px;
            object-fit: cover;
            border: 1px solid #D7EDE0;
            border-radius: 10px;
        }

        .driver-photo-status {
            color: #62756A;
            font-size: 11px;
            overflow-wrap: anywhere;
        }

        .driver-empty {
            padding: 25px 18px;
            text-align: center;
            background: #FFFFFF;
            border: 1px dashed #CDE9D8;
            border-radius: 14px;
        }

        .driver-empty i {
            display: block;
            margin-bottom: 8px;
            color: #16A653;
            font-size: 34px;
        }

        .driver-empty strong {
            display: block;
            color: #304A3A;
            font-size: 13px;
        }

        .driver-empty p {
            margin: 4px 0 0;
            font-size: 11px;
        }

        .driver-profile {
            padding: 20px;
            background: #FFFFFF;
            border: 1px solid #D7EDE0;
            border-radius: 15px;
        }

        .driver-profile-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            margin-bottom: 13px;
            color: #FFFFFF;
            background: #08A653;
            border-radius: 50%;
            font-size: 25px;
        }

        .driver-profile h2 {
            margin: 0;
            font-size: 18px;
        }

        .driver-profile p {
            margin: 4px 0 18px;
            font-size: 12px;
        }

        /* POPUP KAMERA */
        .driver-camera-modal[hidden] {
            display: none !important;
        }

        .driver-camera-modal {
            position: fixed;
            inset: 0;
            z-index: 15000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(10, 25, 17, .86);
        }

        .driver-camera-dialog {
            width: 100%;
            max-width: 480px;
            padding: 16px;
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, .2);
        }

        .driver-camera-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 13px;
            color: #18352A;
        }

        .driver-camera-header strong {
            font-size: 15px;
        }

        .driver-camera-header button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0;
            color: #078B48;
            background: #EAF8F0;
            border: 0;
            border-radius: 50%;
            font-size: 21px;
            cursor: pointer;
        }

        .driver-camera-video-wrap {
            width: 100%;
            max-height: 62vh;
            aspect-ratio: 3 / 4;
            overflow: hidden;
            background: #101510;
            border-radius: 13px;
        }

        .driver-camera-video-wrap video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .driver-camera-message {
            min-height: 20px;
            margin: 12px 0;
            color: #62756A;
            font-size: 12px;
            text-align: center;
        }

        .driver-camera-actions {
            display: flex;
            gap: 10px;
        }

        .driver-camera-actions button {
            flex: 1;
            min-height: 44px;
        }

        .gps-coordinate-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin: 12px 0;
        }

        .gps-coordinate-grid label {
            display: block;
            color: #4D6254;
            font-size: 11px;
            font-weight: 700;
        }

        .gps-coordinate-grid input {
            margin-top: 5px;
            padding: 10px 8px;
            background: #F3F8F4;
            color: #087E48;
            font-size: 11px;
            font-weight: 600;
        }

        @media (max-width: 360px) {
            .gps-coordinate-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .driver-welcome {
                padding: 18px;
                border-radius: 14px;
            }

            .driver-welcome h1 {
                font-size: 19px;
            }

            .driver-trip-card {
                padding: 14px;
            }

            .driver-trip-destination {
                font-size: 15px;
            }

            .driver-camera-modal {
                align-items: flex-end;
                padding: 0;
            }

            .driver-camera-dialog {
                max-width: none;
                padding: 17px;
                padding-bottom: calc(17px + env(safe-area-inset-bottom));
                border-radius: 20px 20px 0 0;
            }

            .driver-camera-video-wrap {
                max-height: 55vh;
            }
        }
    </style>
@endpush

@section('content')
    <div class="driver-dashboard">

        @if ($tab !== 'profil')
            <section class="driver-welcome">
                <small>MOBIL CINTA · AREA SOPIR</small>
                <h1>Halo, {{ $account->nama }}</h1>
                <p>Siap menjalankan layanan antar pulang pasien hari ini?</p>
            </section>
        @endif

        @if ($tab === 'home')

            <section class="driver-summary">
                <div class="driver-summary-item">
                    <span class="driver-summary-number">{{ $assignedJourneys->count() }}</span>
                    <span class="driver-summary-label">Tugas menunggu</span>
                </div>

                <div class="driver-summary-item">
                    <span class="driver-summary-number">{{ $activeJourneys->count() }}</span>
                    <span class="driver-summary-label">Perjalanan aktif</span>
                </div>
            </section>

            <h2 class="driver-section-title">Aktivitas perjalanan</h2>

            @if ($activeJourneys->isNotEmpty())
                @php $journey = $activeJourneys->first(); @endphp

                <article class="driver-trip-card">
                    <div class="driver-trip-header">
                        <div>
                            <span class="driver-trip-number">{{ $journey->nomor_perjalanan }}</span>
                            <h3 class="driver-trip-destination">{{ $journey->tujuan }}</h3>
                        </div>
                        <span class="driver-status">Sedang berjalan</span>
                    </div>

                    <div class="driver-trip-info">
                        <div class="driver-info-item">
                            <small>Nama pasien</small>
                            <strong>{{ $journey->nama_pasien }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Kendaraan</small>
                            <strong>{{ $journey->vehicle?->nomor_polisi ?? '—' }}</strong>
                        </div>
                    </div>

                    <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'aktif']) }}" class="btn btn-primary"
                        style="width:100%; margin-top:13px;">
                        Buka perjalanan aktif
                        <i class="bx bx-right-arrow-alt"></i>
                    </a>
                </article>
            @elseif ($assignedJourneys->isNotEmpty())
                @php $journey = $assignedJourneys->first(); @endphp

                <article class="driver-trip-card">
                    <div class="driver-trip-header">
                        <div>
                            <span class="driver-trip-number">{{ $journey->nomor_perjalanan }}</span>
                            <h3 class="driver-trip-destination">{{ $journey->tujuan }}</h3>
                        </div>
                        <span class="driver-status">Menunggu</span>
                    </div>

                    <p style="font-size:12px; margin:0 0 12px;">
                        Ada penugasan baru yang perlu kamu proses.
                    </p>

                    <a href="{{ route('mobil-cinta.sopir.dashboard', ['tab' => 'tugas']) }}" class="btn btn-primary"
                        style="width:100%;">
                        Lihat penugasan
                        <i class="bx bx-right-arrow-alt"></i>
                    </a>
                </article>
            @else
                <div class="driver-empty">
                    <i class="bx bx-car"></i>
                    <strong>Belum ada perjalanan</strong>
                    <p>Penugasan baru akan tampil di halaman ini.</p>
                </div>
            @endif
        @elseif ($tab === 'tugas')
            <h2 class="driver-section-title">Penugasan saya</h2>

            @forelse ($assignedJourneys as $journey)
                <article class="driver-trip-card">
                    <div class="driver-trip-header">
                        <div>
                            <span class="driver-trip-number">{{ $journey->nomor_perjalanan }}</span>
                            <h3 class="driver-trip-destination">{{ $journey->tujuan }}</h3>
                        </div>
                        <span class="driver-status">Ditugaskan</span>
                    </div>

                    <div class="driver-trip-info">
                        <div class="driver-info-item">
                            <small>Nama pasien</small>
                            <strong>{{ $journey->nama_pasien }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>No. rekam medis</small>
                            <strong>{{ $journey->nomor_rekam_medis }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Ruangan asal</small>
                            <strong>{{ $journey->room?->nama_ruangan ?? '—' }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Kendaraan</small>
                            <strong>{{ $journey->vehicle?->nomor_polisi ?? '—' }}</strong>
                        </div>
                    </div>

                    <details class="driver-action-panel">
                        <summary><i class="bx bx-map"></i> Catat keberangkatan</summary>

                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('mobil-cinta.sopir.perjalanan.depart', $journey) }}"
                            class="gps-form driver-form">
                            @csrf

                            <label>
                                Nama sopir
                                <input value="{{ $journey->nama_sopir }}" readonly>
                            </label>

                            <label>
                                Kilometer berangkat
                                <input type="number" name="kilometer_berangkat" min="0" step="0.01"
                                    placeholder="Masukkan kilometer awal" required>
                            </label>

                            <div class="driver-photo-group">
                                <label>Foto keberangkatan</label>
                                <input type="file" name="foto_berangkat" id="foto-berangkat-{{ $journey->id }}"
                                    accept="image/jpeg" class="driver-camera-input">
                                <button type="button" class="btn btn-outline open-driver-camera"
                                    data-camera-input="foto-berangkat-{{ $journey->id }}"
                                    data-camera-title="Foto Keberangkatan">
                                    <i class="bx bx-camera"></i> Buka Kamera
                                </button>
                                <div class="driver-photo-result">
                                    <img class="driver-photo-preview" alt="Preview foto keberangkatan" hidden>
                                    <small class="driver-photo-status">Foto belum diambil</small>
                                </div>
                            </div>

                            <div class="gps-coordinate-grid">
                                <label>
                                    Latitude
                                    <input type="text" name="latitude_berangkat" placeholder="Belum mengambil GPS"
                                        readonly required>
                                </label>

                                <label>
                                    Longitude
                                    <input type="text" name="longitude_berangkat" placeholder="Belum mengambil GPS"
                                        readonly required>
                                </label>
                            </div>

                            <button type="button" class="btn btn-outline get-gps">
                                <i class="bx bx-current-location"></i> Ambil lokasi GPS
                            </button>
                            <span class="gps-message driver-gps-message" role="status">
                                Lokasi wajib direkam sebelum dikirim.
                            </span>
                            <button type="submit" class="btn btn-primary" disabled>
                                <i class="bx bx-check-circle"></i> Catat keberangkatan
                            </button>
                        </form>
                    </details>
                </article>
            @empty
                <div class="driver-empty">
                    <i class="bx bx-clipboard"></i>
                    <strong>Tidak ada tugas menunggu</strong>
                    <p>Belum ada perjalanan yang ditugaskan kepada kamu.</p>
                </div>
            @endforelse
        @elseif ($tab === 'aktif')
            <h2 class="driver-section-title">Perjalanan aktif</h2>

            @forelse ($activeJourneys as $journey)
                <article class="driver-trip-card">
                    <div class="driver-trip-header">
                        <div>
                            <span class="driver-trip-number">{{ $journey->nomor_perjalanan }}</span>
                            <h3 class="driver-trip-destination">{{ $journey->tujuan }}</h3>
                        </div>
                        <span class="driver-status">Sedang berjalan</span>
                    </div>

                    <div class="driver-trip-info">
                        <div class="driver-info-item">
                            <small>Nama pasien</small>
                            <strong>{{ $journey->nama_pasien }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>No. rekam medis</small>
                            <strong>{{ $journey->nomor_rekam_medis }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Kendaraan</small>
                            <strong>{{ $journey->vehicle?->nomor_polisi ?? '—' }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Kilometer berangkat</small>
                            <strong>{{ $journey->kilometer_berangkat ?? '—' }} km</strong>
                        </div>
                        <div class="driver-info-item">
                            <small>Waktu berangkat</small>
                            <strong>{{ $journey->waktu_berangkat?->format('d/m/Y H:i') ?? '—' }}</strong>
                        </div>
                    </div>

                    <details class="driver-action-panel">
                        <summary><i class="bx bx-flag"></i> Catat kedatangan</summary>

                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('mobil-cinta.sopir.perjalanan.arrive', $journey) }}"
                            class="gps-form driver-form">
                            @csrf

                            <label>
                                Nama sopir
                                <input value="{{ $journey->nama_sopir }}" readonly>
                            </label>

                            <label>
                                Kilometer sampai
                                <input type="number" name="kilometer_sampai" min="0" step="0.01"
                                    placeholder="Masukkan kilometer akhir" required>
                            </label>

                            <div class="driver-photo-group">
                                <label>Foto kedatangan</label>
                                <input type="file" name="foto_sampai" id="foto-sampai-{{ $journey->id }}"
                                    accept="image/jpeg" class="driver-camera-input">
                                <button type="button" class="btn btn-outline open-driver-camera"
                                    data-camera-input="foto-sampai-{{ $journey->id }}"
                                    data-camera-title="Foto Kedatangan">
                                    <i class="bx bx-camera"></i> Buka Kamera
                                </button>
                                <div class="driver-photo-result">
                                    <img class="driver-photo-preview" alt="Preview foto kedatangan" hidden>
                                    <small class="driver-photo-status">Foto belum diambil</small>
                                </div>
                            </div>

                            <div class="gps-coordinate-grid">
                                <label>
                                    Latitude
                                    <input type="text" name="latitude_sampai" placeholder="Belum mengambil GPS"
                                        readonly required>
                                </label>

                                <label>
                                    Longitude
                                    <input type="text" name="longitude_sampai" placeholder="Belum mengambil GPS"
                                        readonly required>
                                </label>
                            </div>

                            <button type="button" class="btn btn-outline get-gps">
                                <i class="bx bx-current-location"></i> Ambil lokasi GPS
                            </button>

                            <label>
                                Catatan sopir
                                <textarea name="catatan_sopir" maxlength="5000" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                            </label>

                            <span class="gps-message driver-gps-message" role="status">
                                Lokasi wajib direkam sebelum dikirim.
                            </span>
                            <button type="submit" class="btn btn-primary" disabled>
                                <i class="bx bx-check-circle"></i> Selesaikan perjalanan
                            </button>
                        </form>
                    </details>
                </article>
            @empty
                <div class="driver-empty">
                    <i class="bx bx-navigation"></i>
                    <strong>Tidak ada perjalanan aktif</strong>
                    <p>Perjalanan yang sudah dimulai akan tampil di sini.</p>
                </div>
            @endforelse
        @elseif ($tab === 'profil')
            <section class="driver-profile">
                <div class="driver-profile-avatar">
                    <i class="bx bx-user"></i>
                </div>
                <h2>{{ $account->nama }}</h2>
                <p>Akun Sopir Mobil Cinta · RSD Kalisat</p>

                <form method="POST" action="{{ route('mobil-cinta.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-log-out"></i> Keluar dari akun
                    </button>
                </form>
            </section>

        @endif

    </div>

    {{-- Popup kamera dipakai bersama untuk foto keberangkatan dan kedatangan. --}}
    <div class="driver-camera-modal" id="driverCameraModal" hidden>
        <div class="driver-camera-dialog" role="dialog" aria-modal="true" aria-labelledby="driverCameraTitle">
            <div class="driver-camera-header">
                <strong id="driverCameraTitle">Ambil Foto</strong>
                <button type="button" id="closeDriverCamera" aria-label="Tutup kamera">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="driver-camera-video-wrap">
                <video id="driverCameraVideo" autoplay muted playsinline></video>
            </div>

            <p class="driver-camera-message" id="driverCameraMessage">
                Posisikan objek di dalam kamera.
            </p>

            <div class="driver-camera-actions">
                <button type="button" class="btn btn-outline" id="cancelDriverCamera">Batal</button>
                <button type="button" class="btn btn-primary" id="captureDriverPhoto" disabled>
                    <i class="bx bx-camera"></i> Ambil Foto
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const modal = document.getElementById('driverCameraModal');
            if (!modal) return;

            const video = document.getElementById('driverCameraVideo');
            const title = document.getElementById('driverCameraTitle');
            const message = document.getElementById('driverCameraMessage');
            const captureButton = document.getElementById('captureDriverPhoto');

            let cameraStream = null;
            let targetInput = null;
            let targetForm = null;

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }

                video.pause();
                video.srcObject = null;
                captureButton.disabled = true;
            }

            function closeCamera() {
                stopCamera();
                modal.hidden = true;
            }

            async function openCamera(button) {
                targetForm = button.closest('form');
                targetInput = document.getElementById(button.dataset.cameraInput);

                if (!targetForm || !targetInput) return;

                title.textContent = button.dataset.cameraTitle || 'Ambil Foto';
                message.textContent = 'Meminta izin kamera...';
                captureButton.disabled = true;
                modal.hidden = false;

                if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
                    message.textContent =
                        'Kamera membutuhkan HTTPS atau localhost dan browser yang mendukung kamera.';
                    return;
                }

                try {
                    stopCamera();

                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            },
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 960
                            }
                        }
                    });

                    video.srcObject = cameraStream;
                    await video.play();

                    captureButton.disabled = false;
                    message.textContent = 'Posisikan objek, lalu tekan Ambil Foto.';
                } catch (error) {
                    console.error('Camera error:', error);
                    message.textContent =
                        'Kamera tidak dapat dibuka. Periksa izin kamera pada browser atau perangkat.';
                }
            }

            function canvasToBlob(canvas, quality) {
                return new Promise((resolve, reject) => {
                    canvas.toBlob(blob => {
                        if (blob) resolve(blob);
                        else reject(new Error('Foto gagal dikompres.'));
                    }, 'image/jpeg', quality);
                });
            }

            async function compressImage(sourceVideo) {
                const maxDimension = 1280;
                const targetSize = 350 * 1024;
                const originalWidth = sourceVideo.videoWidth;
                const originalHeight = sourceVideo.videoHeight;
                const scale = Math.min(1, maxDimension / Math.max(originalWidth, originalHeight));

                let width = Math.max(1, Math.round(originalWidth * scale));
                let height = Math.max(1, Math.round(originalHeight * scale));
                let bestBlob = null;

                // Coba beberapa kualitas JPEG, lalu kecilkan resolusi bila masih terlalu besar.
                while (width >= 640 && height >= 480) {
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    const context = canvas.getContext('2d', {
                        alpha: false
                    });
                    if (!context) throw new Error('Browser tidak dapat memproses foto.');

                    // Membuat hasil foto mirror secara horizontal
                    context.translate(canvas.width, 0);
                    context.scale(-1, 1);

                    context.drawImage(
                        video,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );

                    for (const quality of [0.78, 0.68, 0.58, 0.48]) {
                        const blob = await canvasToBlob(canvas, quality);
                        bestBlob = blob;

                        if (blob.size <= targetSize) {
                            return blob;
                        }
                    }

                    width = Math.floor(width * 0.85);
                    height = Math.floor(height * 0.85);
                }

                if (bestBlob) return bestBlob;
                throw new Error('Foto gagal diproses.');
            }

            captureButton.addEventListener('click', async () => {
                if (!video.videoWidth || !targetInput || !targetForm) {
                    message.textContent = 'Kamera belum siap. Silakan coba lagi.';
                    return;
                }

                captureButton.disabled = true;
                message.textContent = 'Memproses dan mengompres foto...';

                try {
                    const blob = await compressImage(video);
                    const file = new File(
                        [blob],
                        `mobil-cinta-${Date.now()}.jpg`, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }
                    );

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    targetInput.files = dataTransfer.files;

                    const preview = targetForm.querySelector('.driver-photo-preview');
                    const status = targetForm.querySelector('.driver-photo-status');

                    if (preview.dataset.objectUrl) {
                        URL.revokeObjectURL(preview.dataset.objectUrl);
                    }

                    const previewUrl = URL.createObjectURL(file);
                    preview.src = previewUrl;
                    preview.dataset.objectUrl = previewUrl;
                    preview.hidden = false;

                    status.textContent = `Foto siap dikirim · ${(file.size / 1024).toFixed(0)} KB`;
                    closeCamera();
                } catch (error) {
                    console.error('Photo compression error:', error);
                    message.textContent = error.message || 'Foto gagal diproses. Silakan ambil ulang.';
                    captureButton.disabled = false;
                }
            });

            document.querySelectorAll('.open-driver-camera').forEach(button => {
                button.addEventListener('click', () => openCamera(button));
            });

            document.getElementById('closeDriverCamera').addEventListener('click', closeCamera);
            document.getElementById('cancelDriverCamera').addEventListener('click', closeCamera);

            modal.addEventListener('click', event => {
                if (event.target === modal) closeCamera();
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && !modal.hidden) closeCamera();
            });

            // Validasi foto sebelum listener konfirmasi global SweetAlert dijalankan.
            document.addEventListener('submit', event => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement) || !form.matches('.gps-form')) return;

                const photoInput = form.querySelector('.driver-camera-input');
                if (photoInput?.files?.length) return;

                event.preventDefault();
                event.stopImmediatePropagation();

                const cameraButton = form.querySelector('.open-driver-camera');
                const status = form.querySelector('.driver-photo-status');
                if (status) status.textContent = 'Foto wajib diambil sebelum dikirim.';
                if (cameraButton) openCamera(cameraButton);
            }, true);

            // GPS tetap direkam melalui geolocation browser.
            document.querySelectorAll('.gps-form').forEach(form => {
                const button = form.querySelector('.get-gps');
                const submit = form.querySelector('[type="submit"]');
                const message = form.querySelector('.gps-message');

                const latitudeInput = form.querySelector(
                    '[name="latitude_berangkat"], [name="latitude_sampai"]'
                );

                const longitudeInput = form.querySelector(
                    '[name="longitude_berangkat"], [name="longitude_sampai"]'
                );

                if (!button || !submit || !message ||
                    !latitudeInput || !longitudeInput) {
                    console.error('Elemen GPS tidak lengkap:', form);
                    return;
                }

                button.addEventListener('click', () => {
                    submit.disabled = true;

                    latitudeInput.value = '';
                    longitudeInput.value = '';

                    message.textContent = 'Mengambil lokasi GPS...';

                    if (!navigator.geolocation) {
                        message.textContent = 'Perangkat tidak mendukung GPS.';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        ({
                            coords
                        }) => {
                            latitudeInput.value = coords.latitude.toFixed(7);
                            longitudeInput.value = coords.longitude.toFixed(7);

                            message.textContent =
                                'Lokasi berhasil direkam: ' +
                                coords.latitude.toFixed(7) + ', ' +
                                coords.longitude.toFixed(7);

                            submit.disabled = false;
                        },
                        error => {
                            console.error('GPS error:', error);

                            message.textContent =
                                'GPS gagal diperoleh. Aktifkan izin lokasi dan coba lagi.';

                            submit.disabled = true;
                        }, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );
                });
            });

            window.addEventListener('beforeunload', stopCamera);
        })();

        // Menampilkan waktu keberangkatan/kedatangan pada popup konfirmasi.
        document.addEventListener('submit', function(event) {
            const form = event.target;

            if (
                !(form instanceof HTMLFormElement) ||
                !form.matches('.gps-form')
            ) {
                return;
            }

            const isDeparture = form.querySelector(
                '[name="kilometer_berangkat"]'
            ) !== null;

            const timeNow = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hourCycle: 'h23',
                timeZone: 'Asia/Jakarta'
            }).format(new Date());

            if (isDeparture) {
                form.dataset.swalTitle = 'Konfirmasi Keberangkatan';

                form.dataset.swalText =
                    'Keberangkatan akan dicatat pada ' +
                    timeNow +
                    ' WIB. Pastikan kilometer, foto, dan lokasi GPS sudah benar.';

                form.dataset.swalConfirmText = 'Ya';

            } else {
                form.dataset.swalTitle = 'Konfirmasi Kedatangan';

                form.dataset.swalText =
                    'Kedatangan akan dicatat pada ' +
                    timeNow +
                    ' WIB. Pastikan kilometer, foto, dan lokasi GPS sudah benar.';

                form.dataset.swalConfirmText = 'Ya';
            }

        }, true);
    </script>
@endpush
