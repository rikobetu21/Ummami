<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

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
            $gambar = time() . '.' .
                $request->file('gambar')->extension();

            $request->file('gambar')->move(
                public_path('uploads'),
                $gambar
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
            $gambar = time() . '.' .
                $request->file('gambar')->extension();

            $request->file('gambar')->move(
                public_path('uploads'),
                $gambar
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
        Menu::destroy($id);

        return redirect('/admin/menu')
            ->with('success', 'Menu berhasil dihapus');
    }
}