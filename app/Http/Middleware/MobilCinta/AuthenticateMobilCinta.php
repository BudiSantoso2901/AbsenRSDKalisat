<?php

namespace App\Http\Middleware\MobilCinta;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobilCinta
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('mobil_cinta');
        $account = $guard->user();

        if (!$account || !$account->status_aktif) {
            if ($account) {
                $guard->logout();
                $request->session()->regenerateToken();
            }

            return redirect()->route('mobil-cinta.login');
        }

        return $next($request);
    }
}
