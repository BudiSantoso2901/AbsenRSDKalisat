@extends('_layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">
                <span class="text-muted fw-light">Data Absensi /</span> Detail Absensi
            </h4>
            <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary btn-sm">
                Kembali
            </a>
        </div>

        {{-- INFO PEGAWAI --}}
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1"><strong>Nama :</strong> {{ $pegawai->name }}</p>
                <p class="mb-0"><strong>Divisi :</strong> {{ $pegawai->jabatan->nama_jabatan ?? '-' }}</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card mb-3">
            <div class="card-body d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <select name="bulan" class="form-select">
                        @foreach ($bulanList as $key => $bulan)
                            <option value="{{ $key }}" {{ $key == $bulanAktif ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>

                    <select name="tahun" class="form-select">
                        @for ($i = now()->year - 2; $i <= now()->year + 1; $i++)
                            <option value="{{ $i }}" {{ $i == $tahunAktif ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <button class="btn btn-primary">Tampilkan</button>
                </form>

                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Ekspor / Cetak
                        </button>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('absensi.export.pdf', [
                                        'pegawai' => $pegawai->id,
                                        'bulan' => $bulanAktif,
                                        'tahun' => $tahunAktif,
                                    ]) }}"
                                    target="_blank">
                                    PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card">
            <div class="card-header">
                Absensi Bulan :
                <strong>{{ $namaBulan }} {{ $tahunAktif }}</strong>

            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Foto Hadir</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Surat</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensi as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td style="min-width:180px">
                                    @php
                                        $isAdmin = auth()->guard('web')->check();
                                        $isHadir = strtolower(trim($row->status)) === 'hadir';
                                        $hasMasuk = !empty($row->waktu_masuk);
                                    @endphp

                                    @if ($isAdmin && $isHadir && $hasMasuk)
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="openEditWaktuModal(
                {{ $row->id }},
                '{{ \Carbon\Carbon::parse($row->waktu_masuk)->format('H:i') }}',
                '{{ $row->alasan_edit ?? '' }}',
                '{{ $row->edited_by_name ?? '-' }}',
                '{{ $row->edited_at ? \Carbon\Carbon::parse($row->edited_at)->format('d-m-Y H:i') : '-' }}'
            )">
                                            {{ \Carbon\Carbon::parse($row->waktu_masuk)->format('H:i') }}
                                        </button>
                                    @else
                                        <span class="text-muted">
                                            {{ $row->waktu_masuk ? \Carbon\Carbon::parse($row->waktu_masuk)->format('H:i') : '-' }}
                                        </span>
                                    @endif

                                    @if (!empty($row->tl))
                                        <span class="badge bg-warning ms-1">{{ $row->tl }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (!empty($row->foto_masuk))
                                        <img src="{{ Storage::url($row->foto_masuk) }}" class="img-thumbnail"
                                            style="width: 100px; cursor: pointer" data-bs-toggle="modal"
                                            data-bs-target="#fotoModal"
                                            onclick="showFoto('{{ Storage::url($row->foto_masuk) }}')">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $row->waktu_pulang ?? '-' }}</td>
                                <td class="text-center">
                                    @if (!empty($row->foto_pulang))
                                        <img src="{{ asset('storage/' . $row->foto_pulang) }}" class="img-thumbnail"
                                            style="width: 100px; cursor: pointer" data-bs-toggle="modal"
                                            data-bs-target="#fotoModal"
                                            onclick="showFoto('{{ asset('storage/' . $row->foto_pulang) }}')">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($row->surat))
                                        <a href="{{ asset('storage/' . $row->surat) }}" target="_blank">
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    Lat: {{ $row->latitude ?? '-' }} <br>
                                    Long: {{ $row->longitude ?? '-' }}
                                </td>

                                <td>
                                    @if ($row->status == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif ($row->status == 'izin')
                                        <span class="badge bg-warning">Izin</span>
                                    @elseif ($row->status == 'sakit')
                                        <span class="badge bg-info">Sakit</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Hadir</span>
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm btn-lokasi" data-bs-toggle="modal"
                                        data-bs-target="#modalLokasi" data-lat="{{ $row->latitude }}"
                                        data-lng="{{ $row->longitude }}" data-tanggal="{{ $row->tanggal }}">
                                        Lokasi
                                    </button>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data absensi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Modal Lokasi --}}
        <div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Lokasi Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="alert alert-warning d-none" id="lokasiKosong">
                        Pegawai belum melakukan absensi atau lokasi tidak tersedia.
                    </div>
                    <div class="modal-body">
                        <div class="mb-2 text-muted" id="infoTanggal"></div>
                        <div id="mapLokasi" style="height: 400px; border-radius: 8px;"></div>
                    </div>

                </div>
            </div>
        </div>
        {{-- MODAL EDIT WAKTU --}}
        <div class="modal fade" id="modalEditWaktu" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header py-2">
                        <h6 class="modal-title">Edit Waktu Masuk</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="edit-id">

                        <div class="mb-2">
                            <label class="form-label">Waktu Masuk</label>
                            <input type="time" class="form-control" id="edit-waktu">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Alasan Edit <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit-alasan" rows="2"></textarea>
                        </div>

                        <hr class="my-2">

                        <small class="text-muted d-block">
                            Terakhir diedit oleh: <b id="edit-by">-</b>
                        </small>
                        <small class="text-muted">
                            Waktu edit: <b id="edit-at">-</b>
                        </small>
                    </div>

                    <div class="modal-footer py-2">
                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-sm btn-success" onclick="simpanEditWaktu()">
                            Simpan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let map;
        let marker;
        let layerControl;

        // ====== TILE LAYERS ======
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

        document.querySelectorAll('.btn-lokasi').forEach(btn => {
            btn.addEventListener('click', function() {

                const lat = this.dataset.lat;
                const lng = this.dataset.lng;
                const tanggal = this.dataset.tanggal;

                document.getElementById('infoTanggal').innerText =
                    tanggal ? 'Tanggal: ' + tanggal : '';

                const infoKosong = document.getElementById('lokasiKosong');
                const mapDiv = document.getElementById('mapLokasi');

                // ====== KONDISI BELUM ABSEN / LOKASI KOSONG ======
                if (!lat || !lng) {
                    infoKosong.classList.remove('d-none');
                    mapDiv.classList.add('d-none');
                    return;
                }

                infoKosong.classList.add('d-none');
                mapDiv.classList.remove('d-none');

                setTimeout(() => {
                    if (!map) {
                        map = L.map('mapLokasi', {
                            center: [lat, lng],
                            zoom: 17,
                            layers: [osm]
                        });

                        // marker
                        marker = L.marker([lat, lng])
                            .addTo(map)
                            .bindPopup('Lokasi Absensi')
                            .openPopup();

                        // layer switcher
                        layerControl = L.control.layers({
                            "OpenStreetMap": osm,
                            "Satelit (ESRI)": esri
                        }).addTo(map);

                    } else {
                        map.setView([lat, lng], 17);
                        marker.setLatLng([lat, lng]);
                    }

                    map.invalidateSize();
                }, 300);
            });
        });

        // ====== RESET MAP SAAT MODAL DITUTUP ======
        document.getElementById('modalLokasi')
            .addEventListener('hidden.bs.modal', function() {
                if (map) {
                    map.remove();
                    map = null;
                    marker = null;
                    layerControl = null;
                }
            });
        // ====== EDIT WAKTU MASUK ======
        function openEditWaktuModal(id, waktu, alasan, editedBy, editedAt) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-waktu').value = waktu;
            document.getElementById('edit-alasan').value = alasan ?? '';

            document.getElementById('edit-by').innerText = editedBy || '-';
            document.getElementById('edit-at').innerText = editedAt || '-';

            new bootstrap.Modal(document.getElementById('modalEditWaktu')).show();
        }

        function simpanEditWaktu() {
            const id = document.getElementById('edit-id').value;
            const waktu = document.getElementById('edit-waktu').value;
            const alasan = document.getElementById('edit-alasan').value.trim();

            if (!alasan || alasan.length < 5) {
                const modalEl = document.getElementById('modalEditWaktu');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance.hide();

                modalEl.addEventListener('hidden.bs.modal', function handler() {
                    modalEl.removeEventListener('hidden.bs.modal', handler);

                    Swal.fire({
                        icon: 'warning',
                        text: 'Alasan minimal 5 karakter'
                    });
                });

                return;
            }


            // ⬅️ TUTUP MODAL DAHULU
            const modalEl = document.getElementById('modalEditWaktu');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();

            // ⏳ TUNGGU MODAL BENAR-BENAR TERTUTUP
            modalEl.addEventListener('hidden.bs.modal', function handler() {
                modalEl.removeEventListener('hidden.bs.modal', handler);

                Swal.fire({
                    title: 'Simpan perubahan?',
                    text: 'Perubahan akan tercatat di sistem',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`{{ route('absensi.inline-update', ':id') }}`.replace(':id', id), {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                waktu: waktu,
                                alasan_edit: alasan
                            })
                        })
                        .then(res => res.json())
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Waktu masuk diperbarui',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                text: 'Gagal menyimpan data'
                            });
                        });
                });
            });
        }
    </script>
@endpush
