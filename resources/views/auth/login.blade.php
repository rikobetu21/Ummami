@extends('layouts.app')

@section('content')

<div class="login-container">

    <div class="login-card">

        <h1>Login Admin</h1>

        @if(session('error'))
            <p class="error-text">
                {{ session('error') }}
            </p>
        @endif

        <form action="/admin/login" method="POST">

            @csrf

            <input
                type="email"
                name="email"
                placeholder="Email"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password"
                required>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="login-divider">
            <span>ATAU</span>
        </div>

        <a href="{{ route('admin.google') }}" class="google-login-btn">

            <img
                src="{{ asset('images/google.png') }}"
                alt="Google">

            Login menggunakan Google

        </a>

    </div>

</div>

@endsection