<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PegawaiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Session pegawai sudah habis / belum login
        if (!Auth::guard('pegawai')->check()) {

            // Untuk request AJAX / fetch
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali.',
                    'redirect' => route('login')
                ], 401);
            }

            // Untuk akses halaman biasa
            return redirect()
                ->route('login')
                ->with(
                    'swal_warning',
                    'Sesi Anda telah berakhir. Silakan login kembali.'
                );
        }

        return $next($request);
    }
}