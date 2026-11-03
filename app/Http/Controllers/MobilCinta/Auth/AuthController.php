<?php

namespace App\Http\Controllers\MobilCinta\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login khusus Mobil Cinta.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($account = $this->activeAccount($request)) {
            return $this->dashboardRedirect($account);
        }

        return view('mobil-cinta.auth.login');
    }

    /**
     * Memproses login Mobil Cinta.
     */
    public function login(Request $request): RedirectResponse
    {
        if ($account = $this->activeAccount($request)) {
            return $this->dashboardRedirect($account);
        }

        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $authenticated = Auth::guard('mobil_cinta')->attempt([
            'nama_pengguna' => $credentials['username'],
            'password' => $credentials['password'],
            'status_aktif' => true,
        ]);

        if (! $authenticated) {
            return redirect()
                ->route('mobil-cinta.login')
                ->withErrors([
                    'username' => 'Username atau password tidak valid.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        /** @var Account $account */
        $account = Auth::guard('mobil_cinta')->user();

        $account->login_terakhir_pada = now();
        $account->save();

        return $this->dashboardRedirect($account);
    }

    /**
     * Logout hanya dari guard Mobil Cinta.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('mobil_cinta')->logout();

        $request->session()->regenerateToken();

        return redirect()->route('mobil-cinta.login');
    }

    /**
     * Memastikan akun yang sedang login masih aktif.
     */
    private function activeAccount(Request $request): ?Account
    {
        $guard = Auth::guard('mobil_cinta');
        $account = $guard->user();

        if (! $account) {
            return null;
        }

        if (! $account->status_aktif) {
            $guard->logout();
            $request->session()->regenerateToken();

            return null;
        }

        return $account;
    }

    /**
     * Mengarahkan akun sesuai role Mobil Cinta.
     */
    private function dashboardRedirect(Account $account): RedirectResponse
    {
        $route = match ($account->role?->nama_peran) {
            'admin' => 'mobil-cinta.admin.dashboard',
            'koordinator' => 'mobil-cinta.koordinator.dashboard',
            'perawat' => 'mobil-cinta.perawat.dashboard',
            'sopir' => 'mobil-cinta.sopir.dashboard',
            default => null,
        };

        abort_if($route === null, 403);

        return redirect()->route($route);
    }
}
