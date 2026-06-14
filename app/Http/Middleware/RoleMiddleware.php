<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Silakan login terlebih dahulu.'], 401);
        }

        // Cek apakah role user ada di dalam daftar role yang diizinkan untuk route ini
        $user = Auth::user();
        
        $userRole = strtolower(trim($user->role));
        $allowedRoles = array_map(function($role) {
            return strtolower(trim($role));
        }, $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json(['message' => 'Forbidden. Anda tidak memiliki akses ke resource ini.'], 403);
        }

        return $next($request);
    }
}
