<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('variants')->get()->map(function ($menu) {
            $menu->image_url = $menu->image ? asset('storage/' . $menu->image) : null;
            return $menu;
        });

        return response()->json([
            'message' => 'Berhasil mengambil data menu',
            'data' => $menus
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'menuName' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('menus', 'public')
            : null;

        $menu = Menu::create([
            'menuName' => $request->menuName,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan!',
            'data' => $menu
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $request->validate([
            'menuName' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $menu->image = $request->file('image')->store('menus', 'public');
        }

        $menu->update($request->only(['menuName', 'price', 'description']));

        return response()->json([
            'message' => 'Menu berhasil diupdate!',
            'data' => $menu
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus!'
        ], 200);
    }
}
