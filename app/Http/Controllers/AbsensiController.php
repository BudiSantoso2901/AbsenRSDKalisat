<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    //kamera
    public function kamera()
    {
        $pegawai = auth('pegawai')->user();
        $absensi = Absensi::where('id_pegawai', auth('pegawai')->id())
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal')
            ->get();
        return view('Pegawai.Kamera', [
            'pegawai'   => $pegawai,
            'lokasi'    => $pegawai->lokasi,
            'jamKerja'  => $pegawai->jamKerja,
            'absensi'   => $absensi,
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(
                Pegawai::with(['jabatan', 'jamKerja', 'lokasi'])
            )
                ->addIndexColumn()
                ->addColumn('jabatan', function ($row) {
                    return $row->jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('shift', function ($row) {
                    return $row->jamKerja->nama_jam_kerja ?? '-';
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->lokasi->nama_lokasi ?? '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                    <a href="' . route('absensi.detail', $row->id) . '"
                       class="btn btn-warning btn-sm">
                        Detail
                    </a>
                ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('Admin.Absensi');
    }
    public function detail(Request $request, $pegawaiId)
    {
        /** ======================================
         * FILTER BULAN & TAHUN
         * ===================================== */
        $bulanAktif = (int) $request->get('bulan', now()->month);
        $tahunAktif = (int) $request->get('tahun', now()->year);

        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        if (!isset($bulanList[$bulanAktif])) {
            $bulanAktif = now()->month;
        }

        /** ======================================
         * DATA PEGAWAI
         * ===================================== */
        $pegawai = Pegawai::with(['jabatan', 'jamKerja', 'lokasi'])
            ->findOrFail($pegawaiId);

        /** ======================================
         * RANGE BULAN
         * ===================================== */
        $start = Carbon::create($tahunAktif, $bulanAktif, 1);
        $end   = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        /** ======================================
         * AMBIL ABSENSI BULAN TERSEBUT
         * ===================================== */
        $absensiDb = Absensi::where('id_pegawai', $pegawaiId)
            ->whereMonth('tanggal', $bulanAktif)
            ->whereYear('tanggal', $tahunAktif)
            ->get()
            ->map(function ($item) {
                $item->menit_terlambat = (int) ($item->menit_terlambat ?? 0);

                // HITUNG TL
                if ($item->menit_terlambat > 0) {
                    if ($item->menit_terlambat <= 30) {
                        $item->tl = 'TL1';
                    } elseif ($item->menit_terlambat <= 60) {
                        $item->tl = 'TL2';
                    } elseif ($item->menit_terlambat <= 90) {
                        $item->tl = 'TL3';
                    } else {
                        $item->tl = 'TL4';
                    }
                } else {
                    $item->tl = null;
                }

                return $item;
            })
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

        /** ======================================
         * GABUNG TANGGAL (FULL BULAN)
         * ===================================== */
        $absensi = [];

        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');

            if (isset($absensiDb[$tgl])) {
                $absensi[] = $absensiDb[$tgl] ?? (object) [
                    'tanggal' => $tgl,
                    'waktu_masuk' => null,
                    'waktu_pulang' => null,
                    'status' => 'belum_hadir',
                    'menit_terlambat' => 0,
                    'tl' => null,
                    'foto_masuk' => null,
                    'foto_pulang' => null,
                    'surat' => null,
                    'lat' => null,
                    'lng' => null,
                ];
            } else {
                $absensi[] = (object) [
                    'tanggal'        => $tgl,
                    'waktu_masuk'    => null,
                    'waktu_pulang'   => null,
                    'foto_masuk'     => null,
                    'foto_pulang'    => null,
                    'latitude'       => null,
                    'longitude'      => null,
                    'surat'          => null,
                    'status'         => 'belum_hadir',
                ];
            }
        }

        /** ======================================
         * DATA BANTUAN VIEW
         * ===================================== */
        $namaBulan = $bulanList[$bulanAktif];
        $tahunList = Absensi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('Admin.Detail', compact(
            'pegawai',
            'absensi',
            'bulanAktif',
            'tahunAktif',
            'bulanList',
            'namaBulan',
            'tahunList'
        ));
    }

    public function lokasi($id)
    {
        $absensi = Absensi::findOrFail($id);

        return response()->json([
            'latitude'  => $absensi->latitude,
            'longitude' => $absensi->longitude,
            'tanggal'   => $absensi->tanggal,
        ]);
    }

    public function updateAbsen(Request $request, $id)
    {
        $absen = Absensi::findOrFail($id);

        if ($request->type === 'masuk') {
            $absen->update([
                'waktu_masuk' => now(),
                'foto_masuk' => $request->foto,
                'status' => 'hadir'
            ]);
        }

        if ($request->type === 'pulang') {
            $absen->update([
                'waktu_pulang' => now(),
                'foto_pulang' => $request->foto
            ]);
        }

        return response()->json([
            'message' => 'Absensi berhasil diperbarui'
        ]);
    }
    public function absen(Request $request)
    {
        /** ================= AUTH ================= */
        $pegawai = auth()->guard('pegawai')->user();
        if (!$pegawai) abort(401);

        /** ================= VALIDASI ================= */
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit',
            'latitude'   => 'required_if:status,hadir',
            'longitude'  => 'required_if:status,hadir',
            'foto'       => 'required_if:status,hadir|image',
            'keterangan' => 'required_if:status,izin|required_if:status,sakit|string',
            'surat'      => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $now       = Carbon::now('Asia/Jakarta');
        $today     = $now->toDateString();
        $yesterday = Carbon::yesterday('Asia/Jakarta')->toDateString();

        /** ================= JAM KERJA ================= */
        $jamKerja = $pegawai->jamKerja;
        if (!$jamKerja) {
            return response()->json(['message' => 'Jam kerja belum ditentukan'], 422);
        }

        $jamMulaiJam   = Carbon::parse($jamKerja->jam_mulai);
        $jamSelesaiJam = Carbon::parse($jamKerja->jam_selesai);

        // SHIFT MALAM
        $isShiftMalam = $jamSelesaiJam->lt($jamMulaiJam);

        /** ================= DATA ABSENSI ================= */
        $absenAktif = Absensi::where('id_pegawai', $pegawai->id)
            ->whereNotNull('waktu_masuk')
            ->whereNull('waktu_pulang')
            ->orderBy('tanggal', 'desc')
            ->first();

        /** ================= TANGGAL EFEKTIF (FIX UTAMA) ================= */
        if ($absenAktif) {
            $tanggal = $absenAktif->tanggal;
        } else {
            if ($isShiftMalam && $now->format('H:i:s') < $jamKerja->jam_selesai) {
                $tanggal = Carbon::yesterday('Asia/Jakarta')->toDateString();
            } else {
                $tanggal = Carbon::today('Asia/Jakarta')->toDateString();
            }
        }

        $jamMulai = Carbon::parse($tanggal, 'Asia/Jakarta')
            ->setTimeFromTimeString($jamKerja->jam_mulai);

        $jamSelesai = Carbon::parse($tanggal, 'Asia/Jakarta')
            ->setTimeFromTimeString($jamKerja->jam_selesai);

        if ($isShiftMalam) {
            $jamSelesai->addDay();
        }
        if ($isShiftMalam && $jamSelesai->lt($jamMulai)) {
            $jamSelesai->addDay();
        }

        $early     = $jamKerja->early_absen_menit ?? 30;
        $toleransi = $jamKerja->toleransi_menit ?? 0;

        $jamBolehMasuk = $jamMulai->copy()->subMinutes($early);
        $jamToleransi  = $jamMulai->copy()->addMinutes($toleransi);

        $absenHariIni = Absensi::where('id_pegawai', $pegawai->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        /** ================= IZIN / SAKIT ================= */
        if (in_array($request->status, ['izin', 'sakit'])) {

            if ($absenHariIni) {
                return response()->json([
                    'message' => 'Absensi hari ini sudah tercatat Hadir'
                ], 422);
            }

            Absensi::create([
                'id_pegawai' => $pegawai->id,
                'tanggal'    => $tanggal,
                'status'     => $request->status,
                'keterangan' => $request->keterangan,
                'surat'      => $request->hasFile('surat')
                    ? $request->file('surat')->store('surat_absensi', 'public')
                    : null
            ]);

            return response()->json([
                'message' => 'Absensi ' . strtoupper($request->status) . ' berhasil'
            ]);
        }

        /** ================= VALIDASI LOKASI ================= */
        $lokasi = $pegawai->lokasi;
        if (!$lokasi) {
            return response()->json(['message' => 'Lokasi kerja belum ditentukan'], 422);
        }

        $jarak = $this->hitungJarak(
            $lokasi->latitude,
            $lokasi->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($jarak > $lokasi->radius_meter) {
            return response()->json(['message' => 'Anda berada di luar area absensi'], 422);
        }

        $fotoPath = $request->file('foto')->store('absensi_foto', 'public');

        /** ================= ABSEN MASUK ================= */
        if (!$absenHariIni && !$absenAktif) {

            if (!$isShiftMalam && $now->lt($jamBolehMasuk)) {
                return response()->json(['message' => 'Belum waktunya absen masuk'], 422);
            }

            $telat = $now->gt($jamToleransi);
            $menitTelat = $telat ? $jamToleransi->diffInMinutes($now) : 0;

            Absensi::create([
                'id_pegawai'  => $pegawai->id,
                'tanggal'     => $tanggal,
                'waktu_masuk' => $now,
                'foto_masuk'  => $fotoPath,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'status'      => 'hadir',
            ]);

            return response()->json([
                'message' => $telat
                    ? "Anda terlambat {$menitTelat} menit"
                    : 'Absen masuk berhasil',
                'telat' => $telat,
                'menitTelat' => $menitTelat,
                'badge' => $telat ? $this->badgeTelat($menitTelat) : null
            ]);
        }

        /** ================= ABSEN PULANG ================= */
        if ($absenAktif) {

            if ($now->lt($jamSelesai)) {
                return response()->json([
                    'message' => 'Belum waktunya absen pulang'
                ], 422);
            }

            $absenAktif->waktu_pulang = $now;
            $absenAktif->foto_pulang = $fotoPath;
            $absenAktif->latitude    = $request->latitude;
            $absenAktif->longitude   = $request->longitude;
            $absenAktif->save();

            return response()->json([
                'message' => $isShiftMalam
                    ? 'Absen pulang shift malam berhasil'
                    : 'Absen pulang berhasil'
            ]);
        }

        return response()->json([
            'message' => 'Absensi hari ini sudah lengkap'
        ], 409);
    }
    // public function absen(Request $request)
    // {
    //     /** ======================================
    //      * AUTH
    //      * ===================================== */
    //     $pegawai = auth()->guard('pegawai')->user();
    //     if (!$pegawai) {
    //         abort(401);
    //     }

    //     /** ======================================
    //      * VALIDASI REQUEST
    //      * ===================================== */
    //     $request->validate([
    //         'status'     => 'required|in:hadir,izin,sakit',

    //         // HADIR
    //         'latitude'   => 'required_if:status,hadir',
    //         'longitude'  => 'required_if:status,hadir',
    //         'foto'       => 'required_if:status,hadir|image',

    //         // IZIN / SAKIT
    //         'keterangan' => 'required_if:status,izin|required_if:status,sakit|string',
    //         'surat'      => 'nullable|file|mimes:jpg,png,pdf|max:2048',
    //     ]);

    //     /** ======================================
    //      * WAKTU (WIB)
    //      * ===================================== */
    //     $sekarang = Carbon::now('Asia/Jakarta');             // datetime
    //     $hariIni  = Carbon::today('Asia/Jakarta');          // date (00:00:00)

    //     /** ======================================
    //      * AMBIL ABSENSI HARI INI
    //      * ===================================== */
    //     $absensi = Absensi::where('id_pegawai', $pegawai->id)
    //         ->whereDate('tanggal', $hariIni)
    //         ->first();

    //     /** ======================================
    //      * KUNCI STATUS ABSENSI
    //      * ===================================== */
    //     if ($absensi && in_array($absensi->status, ['izin', 'sakit'])) {
    //         return response()->json([
    //             'message' => 'Anda sudah absen ' . strtoupper($absensi->status) . ' hari ini'
    //         ], 422);
    //     }

    //     if (
    //         $absensi &&
    //         $absensi->status === 'hadir' &&
    //         in_array($request->status, ['izin', 'sakit'])
    //     ) {
    //         return response()->json([
    //             'message' => 'Anda sudah absen HADIR, tidak bisa izin atau sakit'
    //         ], 422);
    //     }

    //     /** ======================================
    //      * BUAT ABSENSI JIKA BELUM ADA
    //      * ===================================== */
    //     if (!$absensi) {
    //         $absensi = new Absensi();
    //         $absensi->id_pegawai = $pegawai->id;
    //         $absensi->tanggal    = $hariIni; // Carbon (AMAN)
    //     }

    //     /** ======================================
    //      * IZIN / SAKIT
    //      * ===================================== */
    //     if (in_array($request->status, ['izin', 'sakit'])) {

    //         if ($request->hasFile('surat')) {
    //             $absensi->surat = $request->file('surat')
    //                 ->store('surat_absensi', 'public');
    //         }
    //         $absensi->latitude    = null;
    //         $absensi->longitude   = null;
    //         $absensi->waktu_masuk = null;
    //         $absensi->waktu_pulang = null;
    //         $absensi->status     = $request->status;
    //         $absensi->keterangan = $request->keterangan;
    //         $absensi->save();

    //         return response()->json([
    //             'message' => 'Absensi ' . strtoupper($request->status) . ' berhasil'
    //         ]);
    //     }

    //     /** ======================================
    //      * VALIDASI LOKASI
    //      * ===================================== */
    //     $lokasi = $pegawai->lokasi;
    //     if (!$lokasi) {
    //         return response()->json([
    //             'message' => 'Lokasi kerja belum ditentukan'
    //         ], 422);
    //     }

    //     $jarak = $this->hitungJarak(
    //         $lokasi->latitude,
    //         $lokasi->longitude,
    //         $request->latitude,
    //         $request->longitude
    //     );

    //     if ($jarak > $lokasi->radius_meter) {
    //         return response()->json([
    //             'message' => 'Anda berada di luar area absensi'
    //         ], 422);
    //     }

    //     /** ======================================
    //      * VALIDASI JAM KERJA
    //      * ===================================== */
    //     $jamKerja = $pegawai->jamKerja;
    //     if (!$jamKerja) {
    //         return response()->json([
    //             'message' => 'Jam kerja belum ditentukan'
    //         ], 422);
    //     }

    //     $tanggalString = $hariIni->toDateString(); // ⬅️ FIX UTAMA

    //     $jamMulai = Carbon::createFromFormat(
    //         'Y-m-d H:i:s',
    //         $tanggalString . ' ' . $jamKerja->jam_mulai,
    //         'Asia/Jakarta'
    //     );

    //     $jamSelesai = Carbon::createFromFormat(
    //         'Y-m-d H:i:s',
    //         $tanggalString . ' ' . $jamKerja->jam_selesai,
    //         'Asia/Jakarta'
    //     );

    //     $toleransi = $jamKerja->toleransi_menit ?? 0;
    //     $jamMulaiToleransi = $jamMulai->copy()->addMinutes($toleransi);

    //     /** ======================================
    //      * SIMPAN FOTO
    //      * ===================================== */
    //     $fotoPath = $request->file('foto')
    //         ->store('absensi_foto', 'public');

    //     /** ======================================
    //      * ABSEN MASUK
    //      * ===================================== */
    //     if (!$absensi->waktu_masuk) {
    //         $jamBolehAbsen = $jamMulai->copy()->subMinutes($jamKerja->early_absen_menit ?? 0);
    //         if ($sekarang->lt($jamBolehAbsen)) {
    //             return response()->json([
    //                 'message' => 'Belum waktunya absen'
    //             ], 422);
    //         }
    //         if ($sekarang->lt($jamMulai)) {
    //             return response()->json([
    //                 'message' => 'Belum waktunya absen masuk',
    //                 'jam_mulai' => $jamMulai->format('H:i')
    //             ], 422);
    //         }

    //         $isTelat = false;
    //         $telatMenit = 0;

    //         if ($sekarang->gt($jamMulaiToleransi)) {
    //             $isTelat = true;
    //             $telatMenit = $jamMulaiToleransi->diffInMinutes($sekarang);
    //         }

    //         $absensi->waktu_masuk = $sekarang;
    //         $absensi->foto_masuk  = $fotoPath;
    //         $absensi->latitude     = $request->latitude;
    //         $absensi->longitude    = $request->longitude;
    //         $absensi->status      = 'hadir';
    //         $absensi->save();

    //         return response()->json([
    //             'message'     => $isTelat
    //                 ? "Anda terlambat {$telatMenit} menit"
    //                 : 'Absen masuk berhasil',
    //             'telat'       => $isTelat,
    //             'telat_menit' => $telatMenit,
    //             'badge'       => $isTelat ? $this->badgeTelat($telatMenit) : null
    //         ]);
    //     }

    //     /** ======================================
    //      * ABSEN PULANG
    //      * ===================================== */
    //     if (!$absensi->waktu_pulang) {

    //         if ($sekarang->lt($jamSelesai)) {
    //             return response()->json([
    //                 'message' => 'Belum waktunya absen pulang'
    //             ], 422);
    //         }

    //         $absensi->waktu_pulang = $sekarang;
    //         $absensi->foto_pulang  = $fotoPath;
    //         $absensi->save();

    //         return response()->json([
    //             'message' => 'Absen pulang berhasil'
    //         ]);
    //     }

    //     /** ======================================
    //      * SUDAH LENGKAP
    //      * ===================================== */
    //     return response()->json([
    //         'message' => 'Absensi hari ini sudah lengkap'
    //     ], 422);
    // }
    // public function absen(Request $request)
    // {
    //     /** ======================================
    //      * AUTH
    //      * ===================================== */
    //     $pegawai = auth()->guard('pegawai')->user();
    //     if (!$pegawai) {
    //         abort(401);
    //     }

    //     /** ======================================
    //      * VALIDASI REQUEST
    //      * ===================================== */
    //     $request->validate([
    //         'status'     => 'required|in:hadir,izin,sakit',
    //         // KHUSUS HADIR
    //         'latitude'   => 'required_if:status,hadir',
    //         'longitude'  => 'required_if:status,hadir',
    //         'foto'       => 'required_if:status,hadir|image',

    //         // KHUSUS IZIN / SAKIT
    //         'keterangan' => 'required_if:status,izin|required_if:status,sakit|string',
    //         'surat'      => 'nullable|file|mimes:jpg,png,pdf|max:2048',
    //     ]);

    //     /** ======================================
    //      * WAKTU (WIB)
    //      * ===================================== */
    //     $sekarang = Carbon::now('Asia/Jakarta');
    //     $hariIni  = $sekarang->toDateString();

    //     /** ======================================
    //      * AMBIL / BUAT ABSENSI HARI INI
    //      * ===================================== */
    //     $absensi = Absensi::firstOrNew([
    //         'id_pegawai' => $pegawai->id,
    //         'tanggal'    => $hariIni,
    //     ]);

    //     /** ======================================
    //      * IZIN / SAKIT
    //      * ===================================== */
    //     if (in_array($request->status, ['izin', 'sakit'])) {

    //         if ($request->hasFile('surat')) {
    //             $absensi->surat = $request->file('surat')
    //                 ->store('surat_absensi', 'public');
    //         }

    //         $absensi->status     = $request->status;
    //         $absensi->keterangan = $request->keterangan;
    //         $absensi->save();

    //         return response()->json([
    //             'message' => 'Absensi ' . $request->status . ' berhasil'
    //         ]);
    //     }

    //     /** ======================================
    //      * VALIDASI LOKASI
    //      * ===================================== */
    //     $lokasi = $pegawai->lokasi;
    //     if (!$lokasi) {
    //         return response()->json([
    //             'message' => 'Lokasi kerja belum ditentukan'
    //         ], 422);
    //     }

    //     $jarak = $this->hitungJarak(
    //         $lokasi->latitude,
    //         $lokasi->longitude,
    //         $request->latitude,
    //         $request->longitude
    //     );

    //     if ($jarak > $lokasi->radius_meter) {
    //         return response()->json([
    //             'message' => 'Anda berada di luar area absensi'
    //         ], 422);
    //     }

    //     /** ======================================
    //      * VALIDASI JAM KERJA
    //      * ===================================== */
    //     $jamKerja = $pegawai->jamKerja;
    //     if (!$jamKerja) {
    //         return response()->json([
    //             'message' => 'Jam kerja belum ditentukan'
    //         ], 422);
    //     }

    //     $jamMulai = Carbon::parse(
    //         $hariIni . ' ' . $jamKerja->jam_mulai,
    //         'Asia/Jakarta'
    //     );

    //     $jamSelesai = Carbon::parse(
    //         $hariIni . ' ' . $jamKerja->jam_selesai,
    //         'Asia/Jakarta'
    //     );

    //     $toleransi = $jamKerja->toleransi_menit ?? 0;
    //     $jamMulaiToleransi = $jamMulai->copy()->addMinutes($toleransi);

    //     /** ======================================
    //      * SIMPAN FOTO
    //      * ===================================== */
    //     $fotoPath = $request->file('foto')
    //         ->store('absensi_foto', 'public');

    //     /** ======================================
    //      * ABSEN MASUK
    //      * ===================================== */
    //     if (!$absensi->waktu_masuk) {

    //         $isTelat = false;
    //         $telatMenit = 0;

    //         if ($sekarang->gt($jamMulaiToleransi)) {
    //             $isTelat = true;
    //             $telatMenit = $jamMulaiToleransi->diffInMinutes($sekarang);
    //         }

    //         $absensi->waktu_masuk = $sekarang;
    //         $absensi->foto_masuk  = $fotoPath;
    //         $absensi->status      = 'hadir';

    //         $absensi->save();

    //         return response()->json([
    //             'message'      => $isTelat
    //                 ? "Anda terlambat {$telatMenit} menit"
    //                 : 'Absen masuk berhasil',
    //             'telat'        => $isTelat,
    //             'telat_menit'  => $telatMenit,
    //             'badge'        => $isTelat ? $this->badgeTelat($telatMenit) : null
    //         ]);
    //     }

    //     /** ======================================
    //      * ABSEN PULANG
    //      * ===================================== */
    //     if (!$absensi->waktu_pulang) {

    //         if ($sekarang->lt($jamSelesai)) {
    //             return response()->json([
    //                 'message' => 'Belum waktunya absen pulang'
    //             ], 422);
    //         }

    //         $absensi->waktu_pulang = $sekarang;
    //         $absensi->foto_pulang  = $fotoPath;
    //         $absensi->save();

    //         return response()->json([
    //             'message' => 'Absen pulang berhasil'
    //         ]);
    //     }

    //     /** ======================================
    //      * SUDAH LENGKAP
    //      * ===================================== */
    //     return response()->json([
    //         'message' => 'Absensi hari ini sudah lengkap'
    //     ], 422);
    // }

    /** ======================================
     * HITUNG JARAK (HAVERSINE)
     * ===================================== */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function badgeTelat($menit)
    {
        return match (true) {
            $menit <= 30 => 'TL1',
            $menit <= 60 => 'TL2',
            $menit <= 90 => 'TL3',
            default      => 'TL4',
        };
    }
    public function update(Request $request, $id)
    {
        // ✅ Validasi
        $request->validate([
            'waktu'       => 'required|date_format:H:i',
            'alasan_edit' => 'required|string|min:5',
        ]);

        // ✅ Pastikan admin / web guard
        if (!auth()->guard('web')->check()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Ambil absensi + relasi editor
        $absensi = Absensi::with('editor')->findOrFail($id);

        // ✅ Update data
        $absensi->update([
            'waktu_masuk' => $request->waktu,
            'alasan_edit' => $request->alasan_edit,
            'edited_by'   => auth()->id(),
            'edited_at'   => now(),
        ]);

        // 🔄 Refresh relasi editor (penting!)
        $absensi->load('editor');

        // ✅ Response lengkap (UNTUK MODAL & SWEETALERT)
        return response()->json([
            'success'        => true,
            'waktu_masuk'    => Carbon::parse($absensi->waktu_masuk)->format('H:i'),
            'alasan_edit'    => $absensi->alasan_edit,
            'edited_by'      => $absensi->editor->name ?? '-',
            'edited_at'      => $absensi->edited_at
                ? Carbon::parse($absensi->edited_at)->format('d-m-Y H:i')
                : '-',
        ]);
    }
    public function exportPdf(Request $request, Pegawai $pegawai)
    {
        $bulanAktif = (int) $request->get('bulan', now()->month);
        $tahunAktif = (int) $request->get('tahun', now()->year);

        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $pegawai->load(['jabatan', 'lokasi', 'jamKerja']);

        $start  = Carbon::create($tahunAktif, $bulanAktif, 1);
        $end    = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $absensiDb = Absensi::with('editor')
            ->where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', $bulanAktif)
            ->whereYear('tanggal', $tahunAktif)
            ->get()
            ->keyBy(fn($i) => $i->tanggal->format('Y-m-d'));

        $absensi = [];

        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');

            if (isset($absensiDb[$tgl])) {
                $absensi[] = $absensiDb[$tgl];
            } else {
                $absensi[] = (object)[
                    'tanggal'       => $tgl,
                    'waktu_masuk'   => null,
                    'waktu_pulang'  => null,
                    'status'        => 'belum_hadir',
                    'alasan_edit'   => null,
                    'edited_by'     => null,
                    'edited_at'     => null,
                    'keterangan'    => null,
                    'edited_by_name' => '-',
                ];
            }
        }
        $namaBulan = $bulanList[$bulanAktif];

        $pdf = Pdf::loadView(
            'Export.laporan-absensi',
            compact('pegawai', 'absensi', 'bulanAktif', 'tahunAktif', 'namaBulan')
        )->setPaper('A4', 'portrait');

        return $pdf->download(
            "Laporan_Absensi_{$pegawai->name}_{$namaBulan}_{$tahunAktif}.pdf"
        );
    }
}
