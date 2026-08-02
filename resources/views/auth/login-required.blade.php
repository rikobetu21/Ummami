@extends('layouts.app')

@section('content')

<div class="login-required">

    <div class="login-card">

        <h2>🔒 Login Diperlukan</h2>

        <p>
            Untuk melanjutkan proses checkout,
            silakan login menggunakan akun Google.
        </p>

        <a href="{{ route('google.login') }}" class="google-btn-navbar">
            <img src="{{ asset('images/google.png') }}" alt="Google">
            Login dengan Google
        </a>

        <br><br>

        <a href="{{ url('/menu') }}" class="back-menu">
            ← Kembali ke Menu
        </a>

    </div>

</div>

@endsection