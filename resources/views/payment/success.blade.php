@extends('layouts.customer')

@section('content')

<div class="payment-success">

    <div style="font-size:90px">
        ✅
    </div>

    <h1>Pembayaran Berhasil</h1>

    <p>Terima kasih telah melakukan pembayaran.</p>

    <p>Pesanan Anda sedang diprosses oleh dapur.</p>

    <a href="/order-status" class="btn-order">
        Lihat Status Pesanan
    </a>

    <br><br>

    <a href="/menu" class="back">
        ← Kembali ke Menu
    </a>

</div>

@endsection