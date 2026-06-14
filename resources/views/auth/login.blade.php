@extends('layouts.guest')
@section('title', 'Login')

@section('content')
{{-- Tab Login / Register --}}
<div class="auth-tabs">
    <a href="{{ route('login') }}"
       class="auth-tab active">Login</a>
    <a href="{{ route('register') }}"
       class="auth-tab">Register</a>
</div>

{{-- Session status (misal: password reset berhasil) --}}
@if (session('status'))
    <div class="alert alert-success" style="margin-bottom:1rem;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    {{-- Email --}}
    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               class="form-input"
               placeholder="email@kamu.com"
               required autofocus autocomplete="username">
        @error('email')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="••••••••"
               required autocomplete="current-password">
        @error('password')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="form-group" style="display:flex;align-items:center;gap:0.5rem;">
        <input type="checkbox" id="remember_me" name="remember"
               style="accent-color:var(--primary);width:16px;height:16px;">
        <label for="remember_me" style="font-size:0.85rem;color:var(--text-muted);cursor:pointer;">
            Ingat saya
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               style="margin-left:auto;font-size:0.8rem;color:var(--text-muted);">
                Lupa password?
            </a>
        @endif
    </div>

    {{-- Submit --}}
    <button type="submit" class="btn-primary w-full" style="margin-top:0.5rem;">
        Masuk ke DIMARI
    </button>
</form>
@endsection