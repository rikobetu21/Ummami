@extends('layouts.customer')

@section('content')

    <div class="container">

        <div class="checkout-card">

            <h1>Checkout</h1>

            <form id="checkoutForm" action="/checkout/store" method="POST">

                @csrf

                <div class="form-group">

                    <label>Nama Pelanggan</label>

                    <input type="text" name="nama_pelanggan" required>

                </div>

                <div class="form-group">

                    <label>Nomor Meja</label>

                    <input type="number" name="nomor_meja" placeholder="Masukkan nomor meja" required>

                </div>

                <h3>Metode Pembayaran</h3>

                <div>

                    <input type="radio" name="payment" value="cash" checked>

                    Cash

                </div>

                <div>

                    <input type="radio" name="payment" value="doku">

                    DOKU Online Payment

                </div>

                <br>

                <button type="submit" class="btn-order">

                    Buat Pesanan

                </button>

            </form>

        </div>

    </div>

@endsection