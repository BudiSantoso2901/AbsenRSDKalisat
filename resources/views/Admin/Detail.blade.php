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
                    <button class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                        Ekspor / Cetak
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">PDF</a></li>
                        <li><a class="dropdown-item" href="#">Excel</a></li>
                    </ul>
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

                                <td>
                                    {{ $row->waktu_masuk ?? '-' }}

                                    @if ($row->status === 'hadir' && !empty($row->tl))
                                        <span class="badge bg-warning ms-1">{{ $row->tl }}</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if (!empty($row->foto_masuk))
                                        <img src="{{ asset('storage/' . $row->foto_masuk) }}" class="img-thumbnail"
                                            style="width: 100px; cursor: pointer" data-bs-toggle="modal"
                                            data-bs-target="#fotoModal"
                                            onclick="showFoto('{{ asset('storage/' . $row->foto_masuk) }}')">
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
                                    Lat: {{ $row->lat ?? '-' }} <br>
                                    Long: {{ $row->lng ?? '-' }}
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
                                    <button class="btn btn-warning btn-sm">
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

    </div>
@endsection
