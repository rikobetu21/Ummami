<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        // Simpan seluruh payload callback ke log
        Log::info('DOKU CALLBACK', $request->all());

        Log::info(
            'DOKU CALLBACK',
            $request->all()
        );

        return response()->json([
            'success' => true
        ]);

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice tidak ditemukan'
            ], 400);
        }

        $order = Order::where('kode_order', $invoice)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $order->status = 'diproses';
        $order->save();

        return response()->json([
            'message' => 'OK'
        ]);
    }
}