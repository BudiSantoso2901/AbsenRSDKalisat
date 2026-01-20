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
    public function dashboard()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // ================= TOTAL PEGAWAI =================
        $totalPegawai = Pegawai::count();

        // ================= ABSENSI BULAN INI =================
        $hadir = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')
            ->count();

        $izin = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'izin')
            ->count();

        $sakit = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'sakit')
            ->count();

        // ================= BELUM ABSEN HARI INI =================
        $hariIni = Carbon::today();

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

        return view('_layouts.Dashboard', compact(
            'totalPegawai',
            'hadir',
            'izin',
            'sakit',
            'belumAbsen',
            'chartAbsensiLabel',
            'chartAbsensiData',
            'chartJabatanLabel',
            'chartJabatanData',
            'chartLokasiLabel',
            'chartLokasiData'
        ));
    }
    public function showLogin()
    {
        return view('Auth.login');
    }
    public function showRegister()
    {
        return view('Auth.Register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string'
        ]);

        $login    = $request->login;
        $password = $request->password;

        /*
    |--------------------------------------------------------------------------
    | BERSIHKAN SESSION SEBELUM LOGIN
    |--------------------------------------------------------------------------
    */
        Auth::guard('web')->logout();
        Auth::guard('pegawai')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        /*
    |--------------------------------------------------------------------------
    | LOGIN PEGAWAI (NIP = ANGKA)
    |--------------------------------------------------------------------------
    */
        if (ctype_digit($login)) {

            $pegawai = Pegawai::where('nip', $login)->first();

            if (!$pegawai) {
                return back()->withErrors([
                    'login' => 'NIP tidak terdaftar'
                ])->withInput();
            }

            if (!Hash::check($password, $pegawai->password)) {
                return back()->withErrors([
                    'login' => 'Password salah'
                ])->withInput();
            }

            if ($pegawai->status !== 'approved') {
                $pesan = match ($pegawai->status) {
                    'pending'  => 'Akun belum disetujui admin',
                    'rejected' => 'Akun ditolak, hubungi admin',
                    default    => 'Status akun tidak valid'
                };

                return back()->withErrors([
                    'login' => $pesan
                ])->withInput();
            }

            Auth::guard('pegawai')->login($pegawai);
            session(['role' => 'pegawai']);

            return redirect()->route('pegawai.dashboard');
        }

        /*
    |--------------------------------------------------------------------------
    | LOGIN ADMIN (USERNAME)
    |--------------------------------------------------------------------------
    */
        $admin = User::where('name', $login)->first();

        if ($admin && Hash::check($password, $admin->password)) {

            Auth::guard('web')->login($admin);
            session(['role' => 'admin']);

            return redirect()->route('admin.dashboard');
        }

        /*
    |--------------------------------------------------------------------------
    | LOGIN GAGAL
    |--------------------------------------------------------------------------
    */
        return back()->withErrors([
            'login' => 'Username / NIP atau password salah'
        ])->withInput();
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
        $lokasi = Lokasi::all();
        $jamKerja = JamKerja::all();
        $jabatan = Jabatan::all();

        return view('auth.register', compact([
            'lokasi',
            'jamKerja',
            'jabatan'
        ]));
    }
    public function register(Request $request)
    {
        // ======================
        // VALIDASI
        // ======================
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:18|unique:pegawai,nip',
            'email' => 'required|email|unique:pegawai,email',
            'password' => 'required|min:6|confirmed',
            'id_jabatan' => 'required|exists:jabatan,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_jam_kerja' => 'required|exists:jam_kerja,id',
        ]);

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
            'password' => Hash::make($request->password),
            'id_jabatan' => $request->id_jabatan,
            'id_lokasi' => $request->id_lokasi,
            'id_jam_kerja' => $request->id_jam_kerja,
            'foto_pegawai' => $fotoPath,
            'status' => 'pending', // menunggu ACC admin
        ]);

        // ======================
        // REDIRECT
        // ======================
        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil. Menunggu persetujuan admin.');
    }
}
