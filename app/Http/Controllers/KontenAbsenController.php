<?php

namespace App\Http\Controllers;

use App\Models\absenkonten;
use App\Models\ruangan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KontenAbsenController extends Controller
{
    public function view_konten_absen(Request $request)
    {
        $pegawaiId = auth()->guard('pegawai')->id();

        if ($request->ajax()) {

            $data = absenkonten::with('verifier')
                ->where('id_pegawai', $pegawaiId);

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

                // 🔥 BUKTI
                ->addColumn('bukti', function ($row) {
                    $url = asset('storage/' . $row->bukti_foto);

                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-show"></i> Lihat
                </a>';
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
                        'valid'   => '<span class="badge-status badge-valid">✅ Valid</span>',
                        'ditolak' => '<span class="badge-status badge-ditolak">❌ Ditolak</span>',
                        default   => '-',
                    };
                })
                ->addColumn('aksi', function ($row) {

                    // hanya bisa edit kalau ditolak
                    if ($row->status_verifikasi == 'ditolak') {
                        return '
            <button class="btn btn-warning btn-sm btnEdit"
                data-id="' . $row->id . '"
                data-ig="' . $row->link_ig . '"
                data-fb="' . $row->link_fb . '"
                data-tiktok="' . $row->link_tiktok . '"
            >
                ✏️ Perbaiki
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
    public function create_konten_absen()
    {
        $ruangans = ruangan::get();
        return view('Pegawai.Tambah_konten', compact('ruangans'));
    }
    public function store_konten_absen(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'bukti_foto' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            'link_fb' => 'nullable|url',
            'link_ig' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
            'id_ruangan' => 'nullable|exists:ruangans,id',
        ]);

        // Upload file
        $path = null;
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('absen_konten', $filename, 'public');
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

        // hanya boleh edit kalau ditolak
        if ($data->status_verifikasi != 'ditolak') {
            return response()->json(['error' => 'Tidak bisa diedit'], 403);
        }

        $request->validate([
            'bukti_foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'link_fb' => 'nullable|url',
            'link_ig' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);

        // upload ulang jika ada file baru
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('absen_konten', $filename, 'public');

            $data->bukti_foto = $path;
        }

        // update data
        $data->update([
            'link_fb' => $request->link_fb,
            'link_ig' => $request->link_ig,
            'link_tiktok' => $request->link_tiktok,

            // 🔥 reset ke pending lagi
            'status_verifikasi' => 'pending',
            'keterangan' => 'sudah diperbaiki, menunggu verifikasi ulang',
            'verified_by' => null,
        ]);

        return response()->json(['success' => true]);
    }
    public function view_konten_admin(Request $request)
    {
        if ($request->ajax()) {

            $data = absenkonten::with(['pegawai', 'ruangan', 'verifier']);

            // 🔥 FILTER RUANGAN
            if ($request->ruangan_id) {
                $data->where('id_ruangan', $request->ruangan_id);
            }

            // 🔥 FILTER TANGGAL
            if ($request->start_date && $request->end_date) {
                $data->whereBetween('tanggal', [
                    $request->start_date,
                    $request->end_date
                ]);
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
                    return \Carbon\Carbon::parse($row->tanggal)
                        ->translatedFormat('d M Y');
                })

                // 🔥 BUKTI FOTO
                ->addColumn('bukti', function ($row) {
                    $url = asset('storage/' . $row->bukti_foto);

                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-primary">
                    Lihat
                </a>';
                })

                // 🔥 LINK INSTAGRAM
                ->addColumn('link_ig', function ($row) {
                    return $row->link_ig
                        ? '<a href="' . $row->link_ig . '" target="_blank" class="fw-semibold text-danger">
                        <i class="bx bxl-instagram"></i>
                   </a>'
                        : '<span class="text-muted">-</span>';
                })

                ->addColumn('link_fb', function ($row) {
                    return $row->link_fb
                        ? '<a href="' . $row->link_fb . '" target="_blank" class="fw-semibold text-primary">
                        <i class="bx bxl-facebook"></i>
                   </a>'
                        : '<span class="text-muted">-</span>';
                })

                ->addColumn('link_tiktok', function ($row) {
                    return $row->link_tiktok
                        ? '<a href="' . $row->link_tiktok . '" target="_blank" class="fw-semibold text-dark">
                        <i class="bx bxl-tiktok"></i>
                   </a>'
                        : '<span class="text-muted">-</span>';
                })

                // 🔥 NAMA VERIFIER
                ->addColumn('verified_by', function ($row) {
                    return $row->verifier->name ?? '<span class="text-muted">Belum diverifikasi</span>';
                })

                // 🔥 STATUS
                ->addColumn('status', function ($row) {
                    return match ($row->status_verifikasi) {
                        'pending' => '<span class="badge bg-warning">Pending</span>',
                        'valid'   => '<span class="badge bg-success">Valid</span>',
                        'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
                    };
                })

                // 🔥 AKSI
                ->addColumn('aksi', function ($row) {

                    if ($row->status_verifikasi == 'pending') {
                        return '
                        <button class="btn btn-success btn-sm btnValid" data-id="' . $row->id . '">
                            ✔ Valid
                        </button>
                        <button class="btn btn-danger btn-sm btnTolak" data-id="' . $row->id . '">
                            ✖ Tolak
                        </button>
                    ';
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
        $ruangans = ruangan::get();

        return view('Admin.Konten', compact('ruangans'));
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
