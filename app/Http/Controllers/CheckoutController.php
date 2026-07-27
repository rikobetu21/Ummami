<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'nomor_meja' => 'required'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        foreach ($cart as $id => $item) {
            $menu = Menu::find($id);

            if (!$menu) {
                return back()->with('error', 'Menu tidak ditemukan.');
            }

            if ($menu->stock < $item['qty']) {
                return back()->with(
                    'error',
                    'Stok menu "' . $menu->nama . '" tidak mencukupi.'
                );
            }
        }

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['harga'] * $item['qty'];
        }

        $buktiTransfer = null;

        if ($request->hasFile('bukti_transfer')) {
            $buktiTransfer = $request
                ->file('bukti_transfer')
                ->store('bukti-transfer', 'public');
        }

        $order = Order::create([

            'kode_order' => 'ORD-' . time(),

            'nama_pelanggan' => $request->nama_pelanggan,

            'nomor_meja' => $request->nomor_meja,

            'total' => $total,

            'payment_method' => $request->payment,

            'bukti_transfer' => $buktiTransfer,

            'status' =>
                $request->payment == 'qris'
                ? 'menunggu_pembayaran'
                : 'pending'

        ]);

        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $id,
                'qty' => $item['qty'],
                'subtotal' => $item['harga'] * $item['qty']
            ]);

            $menu = Menu::find($id);

            if ($menu) {
                $menu->stock -= $item['qty'];
                $menu->terjual += $item['qty'];
                $menu->save();
            }
        }

        session()->forget('cart');

        return redirect('/order-status?id=' . $order->id);
    }

    public function qris(Request $request)
    {
        return view(
            'checkout.qris',
            [
                'nama_pelanggan' =>
                    $request->nama_pelanggan,

                'nomor_meja' =>
                    $request->nomor_meja
            ]
        );
    }
}