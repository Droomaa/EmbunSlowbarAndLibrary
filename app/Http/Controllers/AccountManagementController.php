<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::whereIn('role', ['Admin', 'Staff'])->get();
        
        return response()->json([
            'message' => 'Berhasil mengambil data pegawai',
            'data' => $users
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Staff',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Akun ' . $request->role . ' berhasil dibuat!',
            'data' => $user
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        if (!in_array($user->role, ['Admin', 'Staff'])) {
            return response()->json(['message' => 'Akses ditolak. Anda hanya dapat menghapus akun Admin atau Staff.'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Akun berhasil dihapus!'
        ], 200);
    }
}
