<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('user')) {
            // Jika tidak ada data 'user' di session, redirect ke halaman login
            return redirect()->route('login');
        }

        return $next($request);
    }
}