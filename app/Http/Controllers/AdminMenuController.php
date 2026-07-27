<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store(Request $request)
    {
        $gambar = null;

        $request->validate([
            'nama' => 'required|max:255',
            'harga' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'kategori' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {

            $gambar = 'menu/' . time() . '_' .
                $request->file('gambar')->getClientOriginalName();

            Storage::disk('s3')->put(
                $gambar,
                file_get_contents($request->file('gambar'))
            );
        }

        Menu::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stock' => $request->stock,
            'gambar' => $gambar,
            'rating' => $request->rating,
            'terjual' => 0,
            'kategori' => $request->kategori
        ]);

        return redirect('/admin/menu')
            ->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);

        return view(
            'admin.menu.edit',
            compact('menu')
        );
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $gambar = $menu->gambar;

        $request->validate([
            'nama' => 'required|max:255',
            'harga' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'kategori' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {

            if ($menu->gambar) {
                Storage::disk('s3')->delete($menu->gambar);
            }

            $gambar = 'menu/' . time() . '_' .
                $request->file('gambar')->getClientOriginalName();

            Storage::disk('s3')->put(
                $gambar,
                file_get_contents($request->file('gambar'))
            );
        }

        $menu->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stock' => $request->stock,
            'gambar' => $gambar,
            'rating' => $request->rating,
            'kategori' => $request->kategori
        ]);

        return redirect('/admin/menu')
            ->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->gambar) {
            Storage::disk('s3')->delete($menu->gambar);
        }

        $menu->delete();

        return redirect('/admin/menu')
            ->with('success', 'Menu berhasil dihapus');
    }
}