<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // belum login
        if (!Auth::check()) {
            abort(401);
        }

        // login tapi bukan admin
        if (session('role') !== 'admin') {
            abort(401);
        }

        return $next($request);
    }
}
