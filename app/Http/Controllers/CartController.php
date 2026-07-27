<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('cart.index', compact('cart'));
    }

    public function add($id)
    {
        $menu = Menu::findOrFail($id);

        $cart = session()->get('cart', []);

        // Cek apakah stok habis
        if ($menu->stock <= 0) {
            return redirect('/')->with(
                'error',
                'Maaf, stok menu "' . $menu->nama . '" sudah habis.'
            );
        }

        if (isset($cart[$id])) {

            // Cek apakah jumlah di keranjang melebihi stok
            if ($cart[$id]['qty'] >= $menu->stock) {
                return back()->with(
                    'error',
                    'Jumlah menu "' . $menu->nama . '" melebihi stok yang tersedia.'
                );
            }

            $cart[$id]['qty']++;
        } else {

            $cart[$id] = [
                'nama' => $menu->nama,
                'harga' => $menu->harga,
                'gambar' => $menu->gambar,
                'qty' => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect('/cart')
            ->with('success', 'Menu berhasil ditambahkan ke keranjang.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        unset($cart[$id]);

        session()->put('cart', $cart);

        return redirect('/cart');
    }
}