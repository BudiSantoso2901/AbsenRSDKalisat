<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
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
        /** ======================================
         * AUTH
         * ===================================== */
        $pegawai = auth()->guard('pegawai')->user();
        if (!$pegawai) {
            abort(401);
        }

        /** ======================================
         * VALIDASI REQUEST
         * ===================================== */
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit',
            'latitude'   => 'required_if:status,hadir',
            'longitude'  => 'required_if:status,hadir',
            'foto'       => 'required|image',
            'surat'      => 'nullable|file|mimes:jpg,png,pdf',
            'keterangan' => 'nullable|string',
        ]);

        /** ======================================
         * WAKTU (WIB)
         * ===================================== */
        $sekarang = Carbon::now('Asia/Jakarta');
        $hariIni  = $sekarang->toDateString();

        /** ======================================
         * AMBIL / BUAT ABSENSI HARI INI
         * ===================================== */
        $absensi = Absensi::firstOrNew([
            'id_pegawai' => $pegawai->id,
            'tanggal'    => $hariIni,
        ]);

        /** ======================================
         * IZIN / SAKIT
         * ===================================== */
        if (in_array($request->status, ['izin', 'sakit'])) {

            if ($request->hasFile('surat')) {
                $absensi->surat = $request->file('surat')
                    ->store('surat_absensi', 'public');
            }

            $absensi->status     = $request->status;
            $absensi->keterangan = $request->keterangan;
            $absensi->save();

            return response()->json([
                'message' => 'Absensi ' . $request->status . ' berhasil'
            ]);
        }

        /** ======================================
         * VALIDASI LOKASI
         * ===================================== */
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

        /** ======================================
         * VALIDASI JAM KERJA
         * ===================================== */
        $jamKerja = $pegawai->jamKerja;
        if (!$jamKerja) {
            return response()->json([
                'message' => 'Jam kerja belum ditentukan'
            ], 422);
        }

        $jamMulai = Carbon::parse(
            $hariIni . ' ' . $jamKerja->jam_mulai,
            'Asia/Jakarta'
        );

        $jamSelesai = Carbon::parse(
            $hariIni . ' ' . $jamKerja->jam_selesai,
            'Asia/Jakarta'
        );

        $toleransi = $jamKerja->toleransi_menit ?? 0;
        $jamMulaiToleransi = $jamMulai->copy()->addMinutes($toleransi);

        /** ======================================
         * SIMPAN FOTO
         * ===================================== */
        $fotoPath = $request->file('foto')
            ->store('absensi_foto', 'public');

        /** ======================================
         * ABSEN MASUK
         * ===================================== */
        if (!$absensi->waktu_masuk) {

            $isTelat = false;
            $telatMenit = 0;

            if ($sekarang->gt($jamMulaiToleransi)) {
                $isTelat = true;
                $telatMenit = $jamMulaiToleransi->diffInMinutes($sekarang);
            }

            $absensi->waktu_masuk = $sekarang;
            $absensi->foto_masuk  = $fotoPath;
            $absensi->status      = 'hadir';

            $absensi->save();

            return response()->json([
                'message'      => $isTelat
                    ? "Anda terlambat {$telatMenit} menit"
                    : 'Absen masuk berhasil',
                'telat'        => $isTelat,
                'telat_menit'  => $telatMenit,
                'badge'        => $isTelat ? $this->badgeTelat($telatMenit) : null
            ]);
        }

        /** ======================================
         * ABSEN PULANG
         * ===================================== */
        if (!$absensi->waktu_pulang) {

            if ($sekarang->lt($jamSelesai)) {
                return response()->json([
                    'message' => 'Belum waktunya absen pulang'
                ], 422);
            }

            $absensi->waktu_pulang = $sekarang;
            $absensi->foto_pulang  = $fotoPath;
            $absensi->save();

            return response()->json([
                'message' => 'Absen pulang berhasil'
            ]);
        }

        /** ======================================
         * SUDAH LENGKAP
         * ===================================== */
        return response()->json([
            'message' => 'Absensi hari ini sudah lengkap'
        ], 422);
    }

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
}
