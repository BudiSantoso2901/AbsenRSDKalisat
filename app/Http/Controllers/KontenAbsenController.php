<?php

namespace App\Http\Controllers;

use App\Models\absenkonten;
use App\Models\ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Exports\AbsenKontenExport;
use App\Models\Pegawai;
use Maatwebsite\Excel\Facades\Excel;

class KontenAbsenController extends Controller
{
    public function view_konten_absen(Request $request)
    {
        $pegawaiId = auth()->guard('pegawai')->id();

        if ($request->ajax()) {

            $data = absenkonten::with('verifier')
                ->where('id_pegawai', $pegawaiId)
                ->latest('created_at');

            // FILTER TANGGAL
            if ($request->start_date && $request->end_date) {
                $data->whereBetween('tanggal', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            return DataTables::of($data)
                ->addIndexColumn()

                // 🔥 TANGGAL
                ->editColumn('tanggal', function ($row) {
                    return \Carbon\Carbon::parse($row->tanggal)
                        ->locale('id')
                        ->translatedFormat('l, d F Y');
                })

                ->addColumn('bukti', function ($row) {
                    if (!$row->bukti_foto) {
                        return '<span class="text-muted">-</span>';
                    }

                    $url = route('pegawai.konten.bukti', $row->id);
                    $extension = strtolower(pathinfo($row->bukti_foto, PATHINFO_EXTENSION));

                    if ($extension === 'pdf') {
                        return '<button type="button" class="btn btn-sm btn-outline-danger btnLihatBukti"
                    data-url="' . $url . '" data-type="pdf">
                    <i class="bx bxs-file-pdf"></i> Lihat PDF
                </button>';
                    }

                    return '<button type="button" class="btn btn-sm btn-outline-primary btnLihatBukti"
                data-url="' . $url . '" data-type="image">
                <img src="' . $url . '" alt="bukti" style="width:28px;height:28px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:4px;">
                Lihat Foto
            </button>';
                })

                // 🔥 KETERANGAN (FIX)
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan
                        ? $row->keterangan
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 IG
                ->addColumn('link_ig', function ($row) {
                    return $row->link_ig
                        ? '<a href="' . $row->link_ig . '" target="_blank" class="fw-semibold text-danger">
                        <i class="bx bxl-instagram"></i> Instagram
                    </a>'
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 FB
                ->addColumn('link_fb', function ($row) {
                    return $row->link_fb
                        ? '<a href="' . $row->link_fb . '" target="_blank" class="fw-semibold text-primary">
                        <i class="bx bxl-facebook"></i> Facebook
                    </a>'
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 TIKTOK
                ->addColumn('link_tiktok', function ($row) {
                    return $row->link_tiktok
                        ? '<a href="' . $row->link_tiktok . '" target="_blank" class="fw-semibold text-dark">
                        <i class="bx bxl-tiktok"></i> TikTok
                    </a>'
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 VERIFIER (FIX)
                ->addColumn('verified_by', function ($row) {
                    return $row->verifier->name
                        ?? '<span class="text-muted">Belum diverifikasi</span>';
                })

                // 🔥 STATUS
                ->addColumn('status', function ($row) {
                    return match ($row->status_verifikasi) {
                        'pending' => '<span class="badge-status badge-pending">⏳ Pending</span>',
                        'valid' => '<span class="badge-status badge-valid">✅ Valid</span>',
                        'ditolak' => '<span class="badge-status badge-ditolak">❌ Ditolak</span>',
                        default => '-',
                    };
                })
                ->addColumn('aksi', function ($row) {
                    $status = trim(strtolower($row->status_verifikasi));

                    if (in_array($status, ['ditolak', 'pending'])) {
                        $ig = e($row->link_ig ?? '');
                        $fb = e($row->link_fb ?? '');
                        $tiktok = e($row->link_tiktok ?? '');
                        $ket = e($row->keterangan ?? '');

                        return '
            <button class="btn btn-warning btn-sm btnEdit"
                data-id="' . $row->id . '"
                data-ig="' . $ig . '"
                data-fb="' . $fb . '"
                data-tiktok="' . $tiktok . '"
                data-status="' . $status . '"
                data-keterangan="' . $ket . '"
                data-keterangan="' . $ket . '"
                data-bukti-foto="' . e($row->bukti_foto) . '"
            >
                ✏️ EDIT
            </button>
        ';
                    }

                    return '<span class="text-muted">-</span>';
                })

                ->rawColumns([
                    'bukti',
                    'link_ig',
                    'link_fb',
                    'link_tiktok',
                    'status',
                    'verified_by',
                    'keterangan',
                    'aksi'
                ])
                ->make(true);
        }

        return view('Pegawai.konten');
    }
    public function viewBukti($id)
    {
        $konten = absenkonten::with('ruangan')->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $isPegawai = auth()->guard('pegawai')->check();
        $pegawaiId = auth()->guard('pegawai')->id();

        // Validasi hak akses:
        // 1. Jika yang login pegawai, pastikan itu miliknya sendiri
        if ($isPegawai && $konten->id_pegawai != $pegawaiId) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Jika yang login admin (web guard), pastikan admin memiliki akses ke ruangan konten tersebut
        if (!$isPegawai && $user) {
            $userRuanganIds = $user->ruangans()->pluck('ruangans.id')->toArray();
            if (!in_array($konten->id_ruangan, $userRuanganIds)) {
                abort(403, 'Unauthorized action.');
            }
        }

        if (!$konten->bukti_foto) {
            abort(404, 'File tidak ditemukan');
        }

        $path = storage_path('app/public/' . $konten->bukti_foto);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
    public function create_konten_absen()
    {
        $ruangans = ruangan::get();
        return view('Pegawai.Tambah_konten', compact('ruangans'));
    }
    public function store_konten_absen(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'bukti_foto' => 'required|file|mimes:jpeg,jpg,png,webp,gif,svg,pdf|max:10240',
            'link_fb' => 'nullable|url',
            'link_ig' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
            'id_ruangan' => 'nullable|exists:ruangans,id',
        ]);

        $path = null;

        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = storage_path('app/public/absen_konten/' . $filename);

            if (!file_exists(storage_path('app/public/absen_konten'))) {
                mkdir(storage_path('app/public/absen_konten'), 0755, true);
            }

            // JPG/PNG dari Android atau hasil konversi HEIC iPhone
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($file->getRealPath());

                if ($img->width() > 1200) {
                    $img->scale(width: 1200);
                }

                $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webpPath = storage_path('app/public/absen_konten/' . $webpFilename);

                $img->toWebp(75)->save($webpPath);
                clearstatcache(true, $webpPath);

                if (file_exists($webpPath) && filesize($webpPath) < $file->getSize()) {
                    $path = 'absen_konten/' . $webpFilename;
                } else {
                    if (file_exists($webpPath)) {
                        unlink($webpPath);
                    }

                    $path = $file->storeAs('absen_konten', $filename, 'public');
                }

            } elseif (in_array($extension, ['gif', 'webp'])) {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($file->getRealPath());

                $img->scale(width: 1200);
                $img->save($destinationPath, quality: 75);

                $path = 'absen_konten/' . $filename;

            } else {
                // PDF / SVG
                $path = $file->storeAs('absen_konten', $filename, 'public');
            }
        }

        absenkonten::create([
            'id_pegawai' => auth()->guard('pegawai')->id(),
            'tanggal' => $request->tanggal,
            'bukti_foto' => $path,
            'link_fb' => $request->link_fb,
            'link_ig' => $request->link_ig,
            'link_tiktok' => $request->link_tiktok,
            'status_verifikasi' => 'pending',
            'id_ruangan' => $request->id_ruangan,
        ]);

        return redirect()->route('pegawai.konten.index')
            ->with('success', 'Konten absen berhasil ditambahkan!');
    }

    public function update_konten(Request $request)
    {
        $data = absenkonten::findOrFail($request->id);

        if ($data->status_verifikasi === 'valid') {
            return response()->json([
                'message' => 'Konten yang sudah VALID tidak dapat diedit.'
            ], 403);
        }

        $request->validate([
            'bukti_foto' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:10240',
            'link_fb' => 'nullable|url',
            'link_ig' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);

        if ($request->hasFile('bukti_foto')) {

            if (
                $data->bukti_foto &&
                Storage::disk('public')->exists($data->bukti_foto)
            ) {
                Storage::disk('public')->delete($data->bukti_foto);
            }

            $file = $request->file('bukti_foto');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = storage_path('app/public/absen_konten/' . $filename);

            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($file->getRealPath());

                if ($img->width() > 1200) {
                    $img->scale(width: 1200);
                }

                $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webpPath = storage_path('app/public/absen_konten/' . $webpFilename);

                $img->toWebp(75)->save($webpPath);
                clearstatcache(true, $webpPath);

                if (
                    file_exists($webpPath) &&
                    filesize($webpPath) < $file->getSize()
                ) {
                    $data->bukti_foto = 'absen_konten/' . $webpFilename;

                } else {

                    if (file_exists($webpPath)) {
                        unlink($webpPath);
                    }

                    $data->bukti_foto = $file->storeAs(
                        'absen_konten',
                        $filename,
                        'public'
                    );
                }

            } elseif (in_array($extension, ['gif', 'webp'])) {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($file->getRealPath());

                $img->scale(width: 1200);
                $img->save($destinationPath, quality: 75);

                $data->bukti_foto = 'absen_konten/' . $filename;

            } else {
                $data->bukti_foto = $file->storeAs(
                    'absen_konten',
                    $filename,
                    'public'
                );
            }
        }

        $data->update([
            'link_fb' => $request->link_fb,
            'link_ig' => $request->link_ig,
            'link_tiktok' => $request->link_tiktok,
            'status_verifikasi' => 'pending',
            'keterangan' => 'sudah diperbaiki, menunggu verifikasi ulang',
            'verified_by' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function export_konten_admin(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userRuanganIds = $user->ruangans()->pluck('ruangans.id')->toArray();

        $fileName = 'Laporan_Absen_Konten_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new AbsenKontenExport($request, $userRuanganIds), $fileName);
    }
    public function view_konten_admin(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userRuanganIds = $user->ruangans()->pluck('ruangans.id')->toArray();

        if ($request->ajax()) {

            $data = absenkonten::with(['pegawai', 'ruangan', 'verifier'])
                ->whereIn('id_ruangan', $userRuanganIds);

            // Filter Spesifik Pegawai dari Dropdown
            if ($request->filled('id_pegawai')) {
                $data->where('id_pegawai', $request->id_pegawai);
            }

            // Filter Tanggal
            if ($request->start_date && $request->end_date) {
                $data->whereBetween('tanggal', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Filter Spesifik Ruangan
            if ($request->filled('id_ruangan')) {
                $data->where('id_ruangan', $request->id_ruangan);
            }

            // Filter Status Verifikasi
            if ($request->filled('status_verifikasi')) {
                $data->where('status_verifikasi', $request->status_verifikasi);
            }

            $data->latest();

            return DataTables::of($data)
                ->addIndexColumn()

                // 🔥 NAMA PEGAWAI
                ->addColumn('nama_pegawai', function ($row) {
                    return $row->pegawai->name ?? '-';
                })

                // 🔥 NAMA RUANGAN
                ->addColumn('ruangan', function ($row) {
                    return $row->ruangan->nama_ruangan ?? '-';
                })

                // 🔥 TANGGAL
                ->editColumn('tanggal', function ($row) {
                    return \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y');
                })

                ->addColumn('bukti', function ($row) {
                    if (!$row->bukti_foto) {
                        return '<span class="text-muted">-</span>';
                    }

                    $url = route('pegawai.konten.bukti', $row->id);
                    $extension = strtolower(pathinfo($row->bukti_foto, PATHINFO_EXTENSION));

                    if ($extension === 'pdf') {
                        return '<button type="button" class="btn btn-sm btn-outline-danger btnLihatBukti"
                    data-url="' . $url . '" data-type="pdf">
                    <i class="bx bxs-file-pdf"></i> Lihat PDF
                </button>';
                    }

                    return '<button type="button" class="btn btn-sm btn-outline-primary btnLihatBukti"
                data-url="' . $url . '" data-type="image">
                <img src="' . $url . '" alt="bukti" style="width:28px;height:28px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:4px;">
                Lihat Foto
            </button>';
                })

                // 🔥 LINK SOSMED
                ->addColumn('link_ig', function ($row) {
                    return $row->link_ig
                        ? '<a href="' . $row->link_ig . '" target="_blank" class="fw-semibold text-danger"><i class="bx bxl-instagram"></i></a>'
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('link_fb', function ($row) {
                    return $row->link_fb
                        ? '<a href="' . $row->link_fb . '" target="_blank" class="fw-semibold text-primary"><i class="bx bxl-facebook"></i></a>'
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('link_tiktok', function ($row) {
                    return $row->link_tiktok
                        ? '<a href="' . $row->link_tiktok . '" target="_blank" class="fw-semibold text-dark"><i class="bx bxl-tiktok"></i></a>'
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 VERIFIER
                ->addColumn('verified_by', function ($row) {
                    return $row->verifier->name ?? '<span class="text-muted">Belum diverifikasi</span>';
                })

                // 🔥 STATUS
                ->addColumn('status', function ($row) {
                    $status = trim(strtolower($row->status_verifikasi));
                    return match ($status) {
                        'pending' => '<span class="badge bg-warning">Pending</span>',
                        'valid' => '<span class="badge bg-success">Valid</span>',
                        'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
                        default => '<span class="badge bg-secondary">-</span>'
                    };
                })

                // 🔥 AKSI
                ->addColumn('aksi', function ($row) {
                    $status = trim(strtolower($row->status_verifikasi));
                    if ($status === 'pending') {
                        return '
                        <button class="btn btn-success btn-sm btnValid" data-id="' . $row->id . '">✔ Valid</button>
                        <button class="btn btn-danger btn-sm btnTolak" data-id="' . $row->id . '">✖ Tolak</button>';
                    }
                    return '<span class="text-muted">Selesai</span>';
                })

                ->rawColumns([
                    'bukti',
                    'link_ig',
                    'link_fb',
                    'link_tiktok',
                    'status',
                    'aksi',
                    'verified_by'
                ])
                ->make(true);
        }
        $ruangans = $user->ruangans;
        $pegawaiList = Pegawai::whereHas('absenkontens', function ($q) use ($userRuanganIds) {
            $q->whereIn('id_ruangan', $userRuanganIds);
        })
            ->with([
                'absenkontens' => function ($q) use ($userRuanganIds) {
                    $q->whereIn('id_ruangan', $userRuanganIds);
                }
            ])
            ->orderBy('name', 'asc')
            ->get();
        return view('Admin.Konten', compact(
            'ruangans',
            'pegawaiList'
        ));
    }

    public function valid(Request $request)
    {
        $data = absenkonten::findOrFail($request->id);
        $data->update([
            'status_verifikasi' => 'valid',
            'verified_by' => auth()->id(),
            'keterangan' => 'Konten sudah sesuai'
        ]);

        return response()->json(['success' => true]);
    }

    public function tolak(Request $request)
    {
        $data = absenkonten::findOrFail($request->id);
        $data->update([
            'status_verifikasi' => 'ditolak',
            'verified_by' => auth()->id(),
            'keterangan' => $request->keterangan
        ]);

        return response()->json(['success' => true]);
    }
}
