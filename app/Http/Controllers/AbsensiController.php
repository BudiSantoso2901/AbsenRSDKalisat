<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jabatan;
use App\Models\JamKerja;
use App\Models\Lokasi;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AbsensiController extends Controller
{
    //kamera
    public function kamera()
    {
        $pegawai = auth('pegawai')->user();

        $absensi = Absensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal')
            ->get();

        // Ambil semua shift untuk fallback
        $shifts = JamKerja::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_jam_kerja,
                'jam_mulai' => $item->jam_mulai,
                'jam_selesai' => $item->jam_selesai,
                'toleransi' => $item->toleransi_menit ?? 0,
                'early_allowed' => $item->early_allowed ?? 0,
            ];
        });

        return view('Pegawai.Kamera', [
            'pegawai'   => $pegawai,
            'lokasi'    => $pegawai->lokasi,
            'jamKerja'  => $pegawai->jamKerja,
            'absensi'   => $absensi,
            'shifts'    => $shifts, // ✅ kirim ke blade
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
    public function absen_get(Request $request)
    {
        if ($request->ajax()) {

            $query = Absensi::with([
                'pegawai.jabatan',
                'pegawai.lokasi',
                'pegawai.jamKerja',
                'editor'
            ]);

            /** ===============================
             * FILTER RANGE TANGGAL (WAJIB ADA)
             * =============================== */
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay(),
                ]);
            }

            /** ===============================
             * FILTER PEGAWAI
             * =============================== */
            if ($request->filled('pegawai_id')) {
                $query->where('id_pegawai', $request->pegawai_id);
            }

            /** ===============================
             * FILTER JABATAN
             * =============================== */
            if ($request->filled('jabatan_id')) {
                $query->whereHas('pegawai', function ($q) use ($request) {
                    $q->where('id_jabatan', $request->jabatan_id);
                });
            }

            /** ===============================
             * FILTER LOKASI
             * =============================== */
            if ($request->filled('lokasi_id')) {
                $query->whereHas('pegawai', function ($q) use ($request) {
                    $q->where('id_lokasi', $request->lokasi_id);
                });
            }

            /** ===============================
             * FILTER JAM KERJA
             * =============================== */
            if ($request->filled('jam_kerja_id')) {
                $query->whereHas('pegawai', function ($q) use ($request) {
                    $q->where('id_jam_kerja', $request->jam_kerja_id);
                });
            }

            if ($request->jenis_absen) {

                if ($request->jenis_absen == 'apel') {
                    $query->where('keterangan', 'apel');
                } elseif ($request->jenis_absen == 'jumat_sehat') {
                    $query->where('keterangan', 'jumat_sehat');
                } elseif ($request->jenis_absen == 'normal') {
                    $query->where(function ($q) {
                        $q->whereNull('keterangan')
                            ->orWhere('keterangan', '');
                    });
                }
            }

            return datatables()->eloquent($query->orderBy('tanggal', 'desc'))
                ->addIndexColumn()

                ->addColumn('nip', fn($row) => $row->pegawai->nip ?? '-')
                ->addColumn('nama_pegawai', fn($row) => $row->pegawai->name ?? '-')

                ->addColumn('jabatan', fn($row) => $row->pegawai->jabatan->nama_jabatan ?? '-')
                ->addColumn('lokasi', fn($row) => $row->pegawai->lokasi->nama_lokasi ?? '-')
                ->addColumn('jam_kerja', fn($row) => $row->pegawai->jamKerja->nama_jam_kerja ?? '-')

                ->addColumn('tanggal', fn($row) => $row->tanggal)

                /** ===============================
                 * JAM MASUK + TL BADGE (TETAP)
                 * =============================== */
                ->addColumn('jam_masuk', function ($row) {

                    if (!$row->waktu_masuk || $row->status !== 'hadir') {
                        return '-';
                    }

                    $waktuMasuk = Carbon::parse($row->waktu_masuk, 'Asia/Jakarta');
                    $jam = $waktuMasuk->format('H:i');

                    return $jam;
                })

                ->rawColumns(['jam_masuk'])

                ->addColumn(
                    'jam_pulang',
                    fn($row) =>
                    $row->waktu_pulang
                        ? Carbon::parse($row->waktu_pulang)->format('H:i')
                        : '-'
                )

                ->addColumn('status', fn($row) => ucfirst($row->status))

                /** 🔥 TAMBAHAN: JENIS ABSEN */
                ->addColumn('jenis_absen', function ($row) {

                    if (!$row->keterangan) {
                        return '<span class="badge bg-label-primary">Normal</span>';
                    }

                    return match ($row->keterangan) {
                        'apel' => '<span class="badge bg-label-info">Apel</span>',
                        'jumat_sehat' => '<span class="badge bg-label-success">Jumat Sehat</span>',
                        default => '<span class="badge bg-label-secondary">' .
                            ucwords(str_replace('_', ' ', $row->keterangan)) .
                            '</span>',
                    };
                })

                ->addColumn('edited_by', fn($row) => $row->editor->name ?? '-')

                ->rawColumns(['jam_masuk', 'jenis_absen'])

                ->make(true);
        }

        /** ===============================
         * VIEW
         * =============================== */
        $pegawai   = Pegawai::orderBy('name')->get();
        $jabatan   = Jabatan::orderBy('nama_jabatan')->get();
        $lokasi    = Lokasi::orderBy('nama_lokasi')->get();
        $jamKerja  = JamKerja::orderBy('nama_jam_kerja')->get();

        return view('Admin.Absen', compact(
            'pegawai',
            'jabatan',
            'lokasi',
            'jamKerja'
        ));
    }

    public function export_Pdf(Request $request)
    {
        $query = Absensi::with('pegawai');

        // RANGE TANGGAL
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // BULAN
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // TAHUN
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // HARI
        if ($request->filled('hari')) {
            $query->whereRaw('DAYNAME(tanggal) = ?', [$request->hari]);
        }

        /**
         * =========================
         * FILTER PEGAWAI (SAMA!)
         * =========================
         */
        if ($request->filled('pegawai_id')) {
            $query->where('id_pegawai', $request->pegawai_id);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        $pdf = Pdf::loadView('PDF.laporan', [
            'data'   => $data,
            'filter' => $request->all()
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Absensi_Pegawai.pdf');
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
        $start  = Carbon::create($tahunAktif, $bulanAktif, 1);
        $end    = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        /** ======================================
         * AMBIL ABSENSI BULAN TERSEBUT
         * ===================================== */
        $absensiDb = Absensi::with('shift') // supaya bisa tampil nama shift
            ->where('id_pegawai', $pegawaiId)
            ->whereMonth('tanggal', $bulanAktif)
            ->whereYear('tanggal', $tahunAktif)
            ->orderBy('tanggal')
            ->orderBy('shift_id')
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
            ->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

        /** ======================================
         * GABUNG TANGGAL (FULL BULAN)
         * ===================================== */
        $absensi = [];

        foreach ($period as $date) {

            $tgl = $date->format('Y-m-d');

            if (isset($absensiDb[$tgl])) {

                // Kalau ada 2 shift dalam 1 hari
                foreach ($absensiDb[$tgl] as $item) {
                    $absensi[] = $item;
                }
            } else {

                // Kalau tidak ada absensi sama sekali
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
                    'menit_terlambat' => 0,
                    'tl'             => null,
                    'shift'          => null,
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

        $shifts = JamKerja::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_jam_kerja,
            ];
        });

        return view('Admin.Detail', compact(
            'pegawai',
            'absensi',
            'bulanAktif',
            'tahunAktif',
            'bulanList',
            'namaBulan',
            'tahunList',
            'shifts'
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
        $shiftId = $pegawai->id_jam_kerja;
        if (!$jamKerja) {
            return response()->json(['message' => 'Jam kerja belum ditentukan'], 422);
        }

        $jamMulaiJam   = Carbon::parse($jamKerja->jam_mulai);
        $jamSelesaiJam = Carbon::parse($jamKerja->jam_selesai);

        // SHIFT MALAM
        $isShiftMalam = $jamSelesaiJam->lt($jamMulaiJam);

        /** ================= DATA ABSENSI ================= */
        $absenAktif = Absensi::where('id_pegawai', $pegawai->id)
            ->where('shift_id', $shiftId)
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

        $early     = $jamKerja->early_absen_menit ?? 120;
        $toleransi = $jamKerja->toleransi_menit ?? 0;

        $jamBolehMasuk = $jamMulai->copy()->subMinutes($early);
        $jamToleransi  = $jamMulai->copy()->addMinutes($toleransi);

        $absenHariIni = Absensi::where('id_pegawai', $pegawai->id)
            ->whereDate('tanggal', $tanggal)
            ->where('shift_id', $shiftId)
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
            $jamBolehMasuk = $jamMulai->copy()->subMinutes($early);
            if ($now->lt($jamBolehMasuk)) {
                return response()->json(['message' => 'Belum waktunya absen masuk'], 422);
            }

            $telat = $now->gt($jamToleransi);
            $menitTelat = $telat ? $jamToleransi->diffInMinutes($now) : 0;

            Absensi::create([
                'id_pegawai'  => $pegawai->id,
                'tanggal'     => $tanggal,
                'shift_id'    => $shiftId,
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
    public function absenKegiatan(Request $request)
    {
        /** ================= AUTH ================= */
        $pegawai = auth()->guard('pegawai')->user();
        if (!$pegawai) abort(401);

        /** ================= VALIDASI ================= */
        $request->validate([
            'latitude'  => 'required',
            'longitude' => 'required',
            'foto'      => 'required|image',
        ]);

        $now   = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $hari  = $now->format('l');

        /** ================= SETTING KEGIATAN ================= */
        $configKegiatan = [
            'Monday' => [
                'label' => 'Apel',
                'mulai' => '07:00',
                'selesai' => '10:00',
            ],
            'Friday' => [
                'label' => 'Jumat Sehat',
                'mulai' => '06:30',
                'selesai' => '10:00',
            ]
        ];

        // cek apakah hari ini ada kegiatan
        if (!isset($configKegiatan[$hari])) {
            return response()->json([
                'message' => 'Hari ini tidak ada kegiatan absensi khusus'
            ], 422);
        }

        $kegiatan = $configKegiatan[$hari];
        $label = $kegiatan['label'];

        $jamMulai   = Carbon::parse("$today {$kegiatan['mulai']}");
        $jamSelesai = Carbon::parse("$today {$kegiatan['selesai']}");

        /** ================= VALIDASI JAM ================= */
        if ($now->lt($jamMulai) || $now->gt($jamSelesai)) {
            return response()->json([
                'message' => "Di luar jam $label"
            ], 422);
        }

        /** ================= CEK SUDAH ABSEN ================= */
        $sudah = Absensi::where('id_pegawai', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->where('keterangan', strtolower(str_replace(' ', '_', $label)))
            ->exists();

        if ($sudah) {
            return response()->json([
                'message' => "Anda sudah absen $label hari ini"
            ], 422);
        }

        /** ================= VALIDASI LOKASI ================= */
        $lokasi = $pegawai->lokasi;
        if (!$lokasi) {
            return response()->json([
                'message' => 'Lokasi kerja belum ditentukan'
            ], 422);
        }

        $jarak = $this->hitungJarak(
            $lokasi->latitude,
            $lokasi->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($jarak > $lokasi->radius_meter) {
            return response()->json([
                'message' => 'Anda berada di luar area absensi'
            ], 422);
        }

        /** ================= SIMPAN ================= */
        $fotoPath = $request->file('foto')->store('absensi_foto', 'public');

        $keterangan = strtolower(str_replace(' ', '_', $label));

        Absensi::create([
            'id_pegawai'  => $pegawai->id,
            'tanggal'     => $today,
            'waktu_masuk' => $now,
            'foto_masuk'  => $fotoPath,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'status'      => 'hadir',
            'keterangan'  => $keterangan,
            'shift_id'    => null
        ]);

        return response()->json([
            'message' => "Absen $label berhasil"
        ]);
    }
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
        /** ================= VALIDASI ================= */
        $request->validate([
            'waktu_masuk'  => 'nullable|date_format:Y-m-d\TH:i',
            'waktu_pulang' => 'nullable|date_format:Y-m-d\TH:i',
            'alasan_edit'  => 'required|string|min:5',
            'shift_id'     => 'nullable|exists:jam_kerja,id',
        ]);

        /** ================= AUTH ADMIN ================= */
        if (!auth()->guard('web')->check()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        /** ================= AMBIL DATA ================= */
        $absensi = Absensi::with(['editor', 'pegawai'])->findOrFail($id);

        /** ================= PREPARE DATA ================= */
        $dataUpdate = [
            'alasan_edit' => $request->alasan_edit,
            'edited_by'   => auth()->id(),
            'edited_at'   => now(),
        ];

        // ⛳ HANDLE SHIFT
        if ($request->filled('shift_id')) {
            $dataUpdate['shift_id'] = $request->shift_id;
        }

        /** ================= WAKTU MASUK ================= */
        if ($request->filled('waktu_masuk')) {
            $dataUpdate['waktu_masuk'] = Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->waktu_masuk,
                'Asia/Jakarta'
            )->format('Y-m-d H:i:s');
        }

        /** ================= WAKTU PULANG ================= */
        if ($request->filled('waktu_pulang')) {
            $dataUpdate['waktu_pulang'] = Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->waktu_pulang,
                'Asia/Jakarta'
            )->format('Y-m-d H:i:s');
        }

        /** ================= UPDATE ABSENSI ================= */
        $absensi->update($dataUpdate);

        /** ================= SYNC KE PEGAWAI ================= */
        if ($request->filled('shift_id')) {
            $absensi->pegawai->update([
                'id_jam_kerja' => $request->shift_id
            ]);
        }

        /** ================= REFRESH ================= */
        $absensi->load(['editor', 'shift']);

        /** ================= RESPONSE ================= */
        return response()->json([
            'success'       => true,
            'message'       => 'Data absensi & shift pegawai berhasil diperbarui',
            'waktu_masuk'   => $absensi->waktu_masuk
                ? Carbon::parse($absensi->waktu_masuk)->format('H:i')
                : '-',
            'waktu_pulang'  => $absensi->waktu_pulang
                ? Carbon::parse($absensi->waktu_pulang)->format('H:i')
                : '-',
            'alasan_edit'   => $absensi->alasan_edit,
            'edited_by'     => $absensi->editor->name ?? '-',
            'edited_at'     => $absensi->edited_at
                ? Carbon::parse($absensi->edited_at)->format('d-m-Y H:i')
                : '-',
            'shift_id'      => $absensi->shift_id,
            'shift_nama'    => $absensi->shift
                ? $absensi->shift->nama_jam_kerja
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
