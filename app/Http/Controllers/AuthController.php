<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBerdasarkanRole(Auth::user()->role);
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($request->wantsJson() || $request->is('api/*')) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'user_id' => $user->id,
                    'role' => $user->role,
                ]);
            }

            $request->session()->regenerate();
            
            return $this->redirectBerdasarkanRole($user->role);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah nih!',
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout berhasil']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectBerdasarkanRole($role)
    {
        $roleAman = strtolower(trim($role));

        if ($roleAman === 'admin') return redirect('/admin/dashboard');
        if ($roleAman === 'staff') return redirect('/karyawan/dashboard');
        if ($roleAman === 'owner') return redirect('/owner/dashboard');
        
        Auth::logout();
        return redirect('/login')->with('error', 'Role akun Anda tidak dikenali sistem: ' . $role);
    }
}
