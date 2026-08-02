<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use App\Services\DokuService;

class CheckoutController extends Controller
{

    protected DokuService $doku;

    public function __construct(DokuService $doku)
    {
        $this->doku = $doku;
    }
    public function index()
    {
        if (!Auth::check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('login.required');
        }

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

            'nama_pelanggan' => Auth::user()->name,

            'nomor_meja' => $request->nomor_meja,

            'total' => $total,

            'payment_method' => $request->payment,

            'bukti_transfer' => $buktiTransfer,

            'status' => 'pending'

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

        if ($request->payment == 'doku') {

            $result = $this->doku->createCheckout(
                $order->total,
                $order->kode_order
            );

            if (isset($result['response']['payment']['url'])) {
                return redirect($result['response']['payment']['url']);
            }

            return back()->with(
                'error',
                json_encode($result)
            );
        }

        return redirect('/order-status?id=' . $order->id);
    }
}