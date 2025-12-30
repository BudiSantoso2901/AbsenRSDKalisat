<?php

namespace App\Http\Controllers;

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
            'login' => 'required',
            'password' => 'required'
        ]);

        $login = $request->login;
        $password = $request->password;

        /**
         * ======================
         * LOGIN ADMIN (USERS)
         * ======================
         * Login pakai NAME
         */
        $admin = User::where('name', $login)->first();

        if ($admin && Hash::check($password, $admin->password)) {
            Auth::login($admin);
            session(['role' => 'admin']);

            return redirect()->route('admin.dashboard');
        }

        /**
         * ======================
         * LOGIN PEGAWAI
         * ======================
         * Login pakai NIP
         */
        $pegawai = Pegawai::where('nip', $login)->first();

        if ($pegawai && Hash::check($password, $pegawai->password)) {
            Auth::login($pegawai);
            session(['role' => 'pegawai']);

            return redirect()->route('pegawai.dashboard');
        }

        /**
         * ======================
         * GAGAL LOGIN
         * ======================
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
}
