<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::all()->map(function ($menu) {
            // 🌟 KUNCI 1: Ubah ke Array dulu agar properti tambahan tidak dibuang oleh JSON
            $data = $menu->toArray();
            
            $data['image_url'] = $menu->image ? asset('storage/' . $menu->image) : null;
            
            // 🌟 KUNCI 2: Ambil data resep dan paksa masuk ke dalam array $data
            try {
                $rawIngredients = DB::table('menu_ingredients')->where('menu_id', $menu->id)->get();
                $mappedIngredients = [];
                
                foreach ($rawIngredients as $ing) {
                    $invId = $ing->inventory_id ?? $ing->bahan_id;
                    $qty = $ing->quantity_needed ?? $ing->qty ?? 0;

                    $inv = DB::table('inventories')->where('id', $invId)->first();
                    $invName = $inv ? ($inv->item_name ?? $inv->name ?? 'Bahan Baku') : 'Bahan Terhapus';

                    $mappedIngredients[] = [
                        'inventory_id' => $invId,
                        'inventory_name' => $invName,
                        'quantity_needed' => $qty
                    ];
                }
                $data['ingredients'] = $mappedIngredients;
            } catch (\Exception $e) {
                $data['ingredients'] = [];
            }
            
            return $data;
        });

        return response()->json([
            'message' => 'Berhasil mengambil data menu',
            'data' => $menus,
            'menus' => $menus,
            'metrics' => [
                'total_catalog' => count($menus),
                'stock_alerts' => DB::table('inventories')->where('quantity', '<=', 10)->count(),
                'popular_name' => '-',
                'popular_sold' => 0
            ]
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'menuName' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('menus', 'public') : null;

            $menu = Menu::create([
                'menuName' => $request->menuName,
                'price' => $request->price,
                'description' => $request->description,
                'image' => $imagePath,
                'category' => $request->category ?? 'Coffee',
                'status' => $request->status ?? 'Available'
            ]);

            if ($request->has('ingredients')) {
                $ingredients = json_decode($request->ingredients, true);
                if (is_array($ingredients) && count($ingredients) > 0) {
                    try {
                        foreach ($ingredients as $ing) {
                            DB::table('menu_ingredients')->insert([
                                'menu_id' => $menu->id,
                                'inventory_id' => $ing['inventory_id'],
                                'quantity_needed' => $ing['quantity_needed'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['error' => 'Gagal menyimpan resep!'], 500);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Menu berhasil ditambahkan!', 'data' => $menu], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $menu = Menu::find($id);
        if (!$menu) return response()->json(['message' => 'Menu tidak ditemukan'], 404);

        DB::beginTransaction();
        try {
            // 🌟 KUNCI 3: Kumpulkan semua data yang mau diupdate ke dalam 1 wadah
            $updateData = [
                'menuName' => $request->menuName ?? $menu->menuName,
                'price' => $request->price ?? $menu->price,
                'description' => $request->description ?? $menu->description,
                'category' => $request->category ?? $menu->category,
                'status' => $request->status ?? $menu->status,
            ];

            // Jika ada gambar baru, hapus yang lama, lalu MASUKKAN PATH BARU ke wadah
            if ($request->hasFile('image')) {
                if ($menu->image) Storage::disk('public')->delete($menu->image);
                $updateData['image'] = $request->file('image')->store('menus', 'public');
            }

            // Eksekusi update wadah
            $menu->update($updateData);

            // Update Resep
            if ($request->has('ingredients')) {
                $ingredients = json_decode($request->ingredients, true);
                if (is_array($ingredients)) {
                    try {
                        DB::table('menu_ingredients')->where('menu_id', $menu->id)->delete();
                        
                        foreach ($ingredients as $ing) {
                            DB::table('menu_ingredients')->insert([
                                'menu_id' => $menu->id,
                                'inventory_id' => $ing['inventory_id'],
                                'quantity_needed' => $ing['quantity_needed'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['error' => 'Gagal merubah resep!'], 500);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Menu berhasil diupdate!', 'data' => $menu], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $menu = Menu::find($id);
        if (!$menu) return response()->json(['message' => 'Menu tidak ditemukan'], 404);

        try {
            DB::table('menu_ingredients')->where('menu_id', $menu->id)->delete();
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $menu->delete();
            return response()->json(['message' => 'Menu berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
