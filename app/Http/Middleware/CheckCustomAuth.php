<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Periksa apakah data 'user' ada di session
        if (!session()->has('user')) {
            // Jika tidak ada, redirect ke halaman login
            if ($request->ajax() || $request->wantsJson()) {
                // Untuk request AJAX, kirim respons 401 Unauthorized
                return response('Unauthorized.', 401);
            }
            // Untuk request biasa, redirect ke halaman login
            return redirect()->guest(route('login'))->with('swal-error', 'Sesi Anda telah habis, silakan login kembali.');
        }

        // Jika ada, lanjutkan ke request berikutnya
        return $next($request);
    }
}