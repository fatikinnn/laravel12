<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->session()->get('user');

        // The login controller trims the value.
        // The 'ACCESS' column is CHAR(1), so it might have trailing spaces.
        $userRole = isset($user['access']) ? trim($user['access']) : null;

        if (!$user || !$userRole) {
            return redirect('login');
        }

        // Admin gets access to everything
        if ($userRole === 'ADMIN') {
            return $next($request);
        }

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Alihkan ke dashboard dengan pesan error jika tidak punya akses
        return redirect()->route('dashboard')
            ->with('swal-error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
    }
}