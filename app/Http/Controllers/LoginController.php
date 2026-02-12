<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jabatan;
use App\Models\Lokasi;
use App\Models\JamKerja;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function dashboard(Request $request)
    {
        $bulan = now()->month;
        $tahun = now()->year;
        $hariIni = Carbon::today();
        // ================= TOTAL PEGAWAI =================
        $totalPegawai = Pegawai::count();

        // ================= ABSENSI BULAN INI =================
        $hadir = Absensi::whereDate('tanggal', now())
            ->where('status', 'hadir')
            ->count();

        $izin = Absensi::whereDate('tanggal', now())
            ->where('status', 'izin')
            ->count();

        $sakit = Absensi::whereDate('tanggal', now())
            ->where('status', 'sakit')
            ->count();

        // ================= BELUM ABSEN HARI INI =================


        $sudahAbsen = Absensi::whereDate('tanggal', $hariIni)
            ->pluck('id_pegawai');

        $belumAbsen = Pegawai::whereNotIn('id', $sudahAbsen)->count();

        // ================= PIE CHART ABSENSI =================
        $chartAbsensiLabel = ['Hadir', 'Izin', 'Sakit', 'Belum Absen'];
        $chartAbsensiData  = [$hadir, $izin, $sakit, $belumAbsen];

        // ================= BAR CHART JABATAN =================
        $jabatan = Jabatan::withCount('pegawai')->get();
        $chartJabatanLabel = $jabatan->pluck('nama_jabatan');
        $chartJabatanData  = $jabatan->pluck('pegawai_count');

        // ================= BAR CHART LOKASI =================
        $lokasi = Lokasi::withCount('pegawai')->get();
        $chartLokasiLabel = $lokasi->pluck('nama_lokasi');
        $chartLokasiData  = $lokasi->pluck('pegawai_count');
        // ================= DATA TABEL ABSENSI HARI INI =================
        $tanggal = $request->tanggal
            ? Carbon::parse($request->tanggal)->toDateString()
            : now()->toDateString();
        $pegawaiHariIni = Pegawai::with(['jabatan', 'lokasi'])
            ->leftJoin('absensi', function ($join) use ($tanggal) {
                $join->on('pegawai.id', '=', 'absensi.id_pegawai')
                    ->whereDate('absensi.tanggal', $tanggal);
            })
            ->select(
                'pegawai.*',
                'absensi.status as status_absensi',
                'absensi.waktu_masuk',
                'absensi.waktu_pulang'
            )
            ->orderBy('pegawai.name')
            ->get();

        $belumAbsen = $pegawaiHariIni->filter(function ($row) {
            return is_null($row->status_absensi) || $row->status_absensi === 'belum_hadir';
        })->count();

        return view('_layouts.Dashboard', compact(
            'totalPegawai',
            'hadir',
            'izin',
            'sakit',
            'belumAbsen',
            'sudahAbsen',
            'pegawaiHariIni',
            'chartAbsensiLabel',
            'chartAbsensiData',
            'chartJabatanLabel',
            'chartJabatanData',
            'chartLokasiLabel',
            'chartLokasiData',
            'tanggal'
        ));
    }
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('pegawai')->check()) {
            return redirect()->route('pegawai.dashboard');
        }

        return view('Auth.login');
    }

    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('pegawai')->check()) {
            return redirect()->route('pegawai.dashboard');
        }

        return view('Auth.Register');
    }


    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string'
        ], [
            'login.required'    => 'Username atau NIP wajib diisi',
            'password.required' => 'Password wajib diisi'
        ]);

        $login    = trim($request->login);
        $password = $request->password;

        /*
    |------------------------------------------------
    | BERSIHKAN SESSION
    |------------------------------------------------
    */
        Auth::guard('web')->logout();
        Auth::guard('pegawai')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        /*
    |------------------------------------------------
    | LOGIN ADMIN (USERNAME)
    |------------------------------------------------
    */
        $admin = User::where('name', $login)->first();

        if ($admin && Hash::check($password, $admin->password)) {

            Auth::guard('web')->login($admin);
            session(['role' => 'admin']);

            return redirect()
                ->route('admin.dashboard')
                ->with('swal_success', 'Login admin berhasil');
        }

        /*
    |------------------------------------------------
    | LOGIN PEGAWAI (NIP)
    |------------------------------------------------
    */
        $pegawai = Pegawai::where('nip', $login)->first();

        if (!$pegawai) {
            return back()
                ->with('swal_error', 'Username atau NIP tidak terdaftar')
                ->withInput();
        }

        if (!Hash::check($password, $pegawai->password)) {
            return back()
                ->with('swal_error', 'Password salah')
                ->withInput();
        }

        if ($pegawai->status !== 'approved') {
            $pesan = match ($pegawai->status) {
                'pending'  => 'Akun belum disetujui admin',
                'rejected' => 'Akun ditolak, hubungi admin',
                default    => 'Status akun tidak valid'
            };

            return back()
                ->with('swal_warning', $pesan)
                ->withInput();
        }

        Auth::guard('pegawai')->login($pegawai);
        session(['role' => 'pegawai']);

        return redirect()
            ->route('pegawai.dashboard')
            ->with('swal_success', 'Login berhasil, selamat datang!');
    }


    // logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('pegawai')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function get_register()
    {
        if (Auth::guard('pegawai')->check()) {
            return redirect()->route('pegawai.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        $jabatan   = Jabatan::all();
        $lokasi    = Lokasi::all();
        $jamKerja  = JamKerja::all();
        return view('Auth.Register', compact(
            'jabatan',
            'lokasi',
            'jamKerja'
        ));
    }

    public function register(Request $request)
    {
        // ======================
        // VALIDASI
        // ======================
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:pegawai,nip',
            'email' => 'required|email|unique:pegawai,email',
            'id_jabatan' => 'required|exists:jabatan,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_jam_kerja' => 'required|exists:jam_kerja,id',
            'tanggal_lahir' => 'required|date',
        ]);

        $passwordPlain = \Carbon\Carbon::parse($request->tanggal_lahir)
            ->format('dmY');
        // ======================
        // UPLOAD FOTO (JIKA ADA)
        // ======================
        $fotoPath = null;

        if ($request->hasFile('foto_pegawai')) {
            $fotoPath = $request->file('foto_pegawai')
                ->store('foto_pegawai', 'public');
        }

        // ======================
        // SIMPAN PEGAWAI
        // ======================
        Pegawai::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($passwordPlain),
            'id_jabatan' => $request->id_jabatan,
            'id_lokasi' => $request->id_lokasi,
            'id_jam_kerja' => $request->id_jam_kerja,
            'foto_pegawai' => $fotoPath,
            'tanggal_lahir' => $request->tanggal_lahir,
            'status' => 'pending', // menunggu ACC admin
        ]);

        // ======================
        // REDIRECT
        // ======================
        return redirect()->route('login')
            ->with('swal_success', 'Pendaftaran berhasil. Menunggu persetujuan admin.');
    }
}
