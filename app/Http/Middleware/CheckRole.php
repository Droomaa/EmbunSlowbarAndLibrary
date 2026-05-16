<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = strtolower(trim(Auth::user()->role));

        if (!in_array($userRole, $roles)) {
            abort(403, 'AKSES DITOLAK! Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
