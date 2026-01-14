<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Lokasi;
use App\Models\JamKerja;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class PegawaiController extends Controller
{
    /**
     * PAGE LIST
     */
    public function index(Request $request)
    {
        // AJAX DATATABLE
        if ($request->ajax()) {
            $data = Pegawai::with(['jabatan', 'lokasi', 'jamKerja'])
                ->select('pegawai.*');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('jabatan', function ($row) {
                    return $row->jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->lokasi->nama_lokasi ?? '-';
                })
                ->addColumn('jam_kerja', function ($row) {
                    return $row->jamKerja->nama_jam_kerja ?? '-';
                })
                ->editColumn('tanggal_lahir', function ($row) {
                    return Carbon::parse($row->tanggal_lahir)
                        ->translatedFormat('j F Y');
                })
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'pending' => '<span class="badge bg-warning">Perlu Persetujuan</span>',
                        'approved' => '<span class="badge bg-success">Disetujui</span>',
                        'rejected' => '<span class="badge bg-danger">Ditolak</span>',
                    };
                })


                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-warning btn-edit"
                            data-id="' . $row->id . '"
                            data-name="' . $row->name . '"
                            data-tanggal_lahir="' . $row->tanggal_lahir . '"
                            data-nip="' . $row->nip . '"
                            data-email="' . $row->email . '"
                            data-id_jabatan="' . $row->id_jabatan . '"
                            data-id_lokasi="' . $row->id_lokasi . '"
                            data-id_jam_kerja="' . $row->id_jam_kerja . '"
                            data-status="' . $row->status . '">
                            Edit
                        </button>
                         <button class="btn btn-sm btn-danger btn-delete"
                            data-id="' . $row->id . '">
                            Hapus
                        </button>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('pegawai.pegawai', [
            'jabatan'   => Jabatan::all(),
            'lokasis'   => Lokasi::all(),
            'jamKerjas' => JamKerja::all(),
        ]);
    }

    /**
     * STORE (TAMBAH)
     */
    public function store(Request $request)
    {
        $validated = $this->validatePegawai($request);

        // format tanggal lahir ke ddmmyyyy
        $tgl = Carbon::parse($request->tanggal_lahir)->format('dmY');

        // generate password
        $validated['password'] = Hash::make($tgl);

        $validated['status'] = 'pending';

        Pegawai::create($validated);

        return response()->json([
            'message' => 'Pegawai berhasil ditambahkan'
        ]);
    }

    /**
     * UPDATE (EDIT)
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $this->validatePegawai($request, $id);
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $pegawai->update($validated);

        return response()->json([
            'message' => 'Pegawai berhasil diperbarui'
        ]);
    }
    /**
     * VALIDATION (DIPAKAI STORE & UPDATE)
     */
    private function validatePegawai(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'nip' => 'required|string|max:18|unique:pegawai,nip,' . $id,
            'tanggal_lahir' => 'required|date',
            'email' => 'required|email|unique:pegawai,email,' . $id,
            'id_jabatan' => 'required|exists:jabatan,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_jam_kerja' => 'required|exists:jam_kerja,id',
            // password tidak wajib (auto)
            'password' => $id ? 'nullable|min:6|confirmed' : 'nullable',

            'status' => 'nullable|in:pending,approved,rejected',
        ], [
            'name.required' => 'Nama wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'id_jabatan.required' => 'Jabatan wajib dipilih',
            'id_lokasi.required' => 'Lokasi wajib dipilih',
            'id_jam_kerja.required' => 'Jam kerja wajib dipilih',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);
    }

    public function destroy($id)
    {
        Pegawai::findOrFail($id)->delete();

        return response()->json([
            'success' => 'Data berhasil dihapus'
        ]);
    }
    public function dashboard_pegawai()
    {
        $pegawai = Auth::guard('pegawai')->user();

        $bulan = now()->month;
        $tahun = now()->year;

        /** ================= RINGKASAN BULANAN ================= */
        $hadir = DB::table('absensi')
            ->where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')
            ->count();

        $izin = DB::table('absensi')
            ->where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'izin')
            ->count();

        $sakit = DB::table('absensi')
            ->where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'sakit')
            ->count();

        /** ================= DATA CHART MINGGUAN ================= */
        $chartDB = DB::table('absensi')
            ->select(
                DB::raw('WEEK(tanggal, 1) as minggu'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy(
                DB::raw('WEEK(tanggal, 1)'),
                'status'
            )
            ->get();

        /** ================= INIT 4 MINGGU ================= */
        $chartHadir = $chartIzin = $chartSakit = [0, 0, 0, 0];

        foreach ($chartDB as $row) {
            $index = ((int) $row->minggu % 4); // 0–3

            if ($row->status === 'hadir') {
                $chartHadir[$index] = $row->total;
            }

            if ($row->status === 'izin') {
                $chartIzin[$index] = $row->total;
            }

            if ($row->status === 'sakit') {
                $chartSakit[$index] = $row->total;
            }
        }

        return view('_layouts.Dashboard_pegawai', compact(
            'pegawai',
            'hadir',
            'izin',
            'sakit',
            'chartHadir',
            'chartIzin',
            'chartSakit'
        ));
    }
}
