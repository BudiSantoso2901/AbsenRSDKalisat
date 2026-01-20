<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('web')->check()) {
            abort(403);
        }

        if (Auth::guard('pegawai')->check()) {
            abort(403);
        }

        return $next($request);
    }
}
