@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('content')
<div style="text-align:center;margin-bottom:1.5rem;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">🔑</div>
    <h3 style="font-family:'Orbitron',monospace;font-size:1rem;color:var(--primary);margin-bottom:0.5rem;">
        Reset Password
    </h3>
    <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.5;">
        Masukkan email kamu dan kami akan kirim link untuk reset password.
    </p>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="auth-form">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               class="form-input"
               placeholder="email@kamu.com"
               required autofocus>
        @error('email')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    <button type="submit" class="btn-primary w-full">
        Kirim Link Reset
    </button>

    <div style="text-align:center;margin-top:1rem;">
        <a href="{{ route('login') }}" style="font-size:0.85rem;color:var(--text-muted);">
            ← Kembali ke Login
        </a>
    </div>
</form>
@endsection