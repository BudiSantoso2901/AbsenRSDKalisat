@extends('_layouts.layouts')

@section('content')
    <style>
        .card-form {
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            border-radius: 10px;
        }

        .preview-img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
        }

        .upload-box {
            border: 2px dashed #dcdcdc;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-box:hover {
            border-color: #696cff;
            background: #f9f9ff;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        /* efek focus modern */
        .input-group:focus-within {
            box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.2);
            border-radius: 10px;
        }

        .btn-kirim {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px;
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
            transition: all 0.25s ease;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <div class="container-xxl container-p-y">

        <div class="mb-4">
            <h4 class="fw-bold">📸 Tambah Absen Konten</h4>
            <small class="text-muted">Upload bukti & link sosial media kamu</small>
        </div>

        <div class="card card-form">
            <div class="card-body">

                <form action="{{ route('pegawai.konten.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ruangan (Opsional)</label>
                        <select name="id_ruangan" class="form-select">
                            <option value="" selected>-- Pilih Ruangan --</option>
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</ option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Upload --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Bukti</label>

                        <div class="upload-box" onclick="document.getElementById('bukti_foto').click()">
                            <i class="bx bx-cloud-upload fs-1 text-primary"></i>
                            <p class="mb-1 mt-2">Klik untuk upload</p>
                            <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                        </div>

                        <input type="file" name="bukti_foto" id="bukti_foto" hidden required>

                        <img id="preview" class="preview-img">
                    </div>

                    {{-- Instagram --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fab fa-instagram text-danger"></i>
                            </span>
                            <input type="url" name="link_ig" class="form-control"
                                placeholder="https://instagram.com/...">
                        </div>
                    </div>

                    {{-- Facebook --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fab fa-facebook text-primary"></i>
                            </span>
                            <input type="url" name="link_fb" class="form-control"
                                placeholder="https://facebook.com/...">
                        </div>
                    </div>

                    {{-- TikTok --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">TikTok</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fab fa-tiktok"></i>
                            </span>
                            <input type="url" name="link_tiktok" class="form-control"
                                placeholder="https://tiktok.com/...">
                        </div>
                    </div>
                    {{-- Ruangan --}}


                    {{-- BUTTON --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pegawai.konten.index') }}" class="btn btn-outline-secondary">
                            ⬅ Kembali
                        </a>

                        <button type="submit" class="btn btn-kirim">
                            💾 Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Preview gambar
        document.getElementById('bukti_foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');

            if (file) {
                if (file.type.startsWith('image/')) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            }
        });
    </script>
@endpush
