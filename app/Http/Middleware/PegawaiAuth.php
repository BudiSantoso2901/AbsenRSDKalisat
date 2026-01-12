<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiAuth
{
    public function handle(Request $request, Closure $next)
    {
        // cek login pegawai (guard pegawai)
        if (!Auth::guard('pegawai')->check()) {
            abort(401);
        }

        return $next($request);
    }
}
