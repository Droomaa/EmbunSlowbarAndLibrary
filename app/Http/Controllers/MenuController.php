<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // 1. Read: Tampilkan semua menu (Bisa diakses public/pelanggan)
    public function index()
    {
        $menus = Menu::all();
        // Nambahin full URL untuk gambar biar frontend gampang nampilinnya
        foreach ($menus as $menu) {
            $menu->image_url = $menu->image ? asset('storage/' . $menu->image) : null;
        }
        return response()->json(['message' => 'Berhasil mengambil data menu', 'data' => $menus], 200);
    }

    // 2. Create: Tambah menu baru + Upload Foto (Khusus Owner/Admin)
    public function store(Request $request)
    {
        $request->validate([
            'menuName' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan gambar ke folder storage/app/public/menus
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'menuName' => $request->menuName,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Menu berhasil ditambahkan!', 'data' => $menu], 201);
    }

    // 3. Update: Ubah data menu (Khusus Owner/Admin)
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        if (!$menu) return response()->json(['message' => 'Menu tidak ditemukan'], 404);

        $request->validate([
            'menuName' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek kalau ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage kalau ada
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            // Simpan gambar baru
            $menu->image = $request->file('image')->store('menus', 'public');
        }

        $menu->menuName = $request->menuName ?? $menu->menuName;
        $menu->price = $request->price ?? $menu->price;
        $menu->description = $request->description ?? $menu->description;
        $menu->save();

        return response()->json(['message' => 'Menu berhasil diupdate!', 'data' => $menu], 200);
    }

    // 4. Delete: Hapus menu + Hapus foto (Khusus Owner/Admin)
    public function destroy($id)
    {
        $menu = Menu::find($id);
        if (!$menu) return response()->json(['message' => 'Menu tidak ditemukan'], 404);

        // Hapus file gambar dari server
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus!'], 200);
    }
}
