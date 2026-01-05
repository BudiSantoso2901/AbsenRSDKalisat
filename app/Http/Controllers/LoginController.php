<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Lokasi;
use App\Models\JamKerja;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function dashboard()
    {
        return view('_layouts.Dashboard');
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
    | LOGIN ADMIN
    |--------------------------------------------------------------------------
    | Login menggunakan NAME
    */
        $admin = User::where('name', $login)->first();

        if ($admin && Hash::check($password, $admin->password)) {
            Auth::login($admin);
            session(['role' => 'admin']);

            return redirect()->route('admin.dashboard');
        }

        /*
    |--------------------------------------------------------------------------
    | LOGIN PEGAWAI
    |--------------------------------------------------------------------------
    | Login menggunakan NIP + status approved
    */
        $pegawai = Pegawai::where('nip', $login)->first();

        if ($pegawai) {

            // cek password
            if (!Hash::check($password, $pegawai->password)) {
                return back()->withErrors([
                    'login' => 'Password salah'
                ])->withInput();
            }

            // cek status akun
            if ($pegawai->status !== 'approved') {

                $pesan = match ($pegawai->status) {
                    'pending'  => 'Akun belum disetujui oleh admin',
                    'rejected' => 'Akun ditolak, silakan hubungi admin',
                    default    => 'Status akun tidak valid'
                };

                return back()->withErrors([
                    'login' => $pesan
                ])->withInput();
            }

            // login pegawai
            Auth::login($pegawai);
            session(['role' => 'pegawai']);

            return redirect()->route('pegawai.dashboard');
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
    public function logout()
    {
        Auth::logout();
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
