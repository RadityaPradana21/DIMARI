@extends('layouts.guest')
@section('title', 'Register')

@section('content')
{{-- Tab Login / Register --}}
<div class="auth-tabs">
    <a href="{{ route('login') }}"
       class="auth-tab">Login</a>
    <a href="{{ route('register') }}"
       class="auth-tab active">Register</a>
</div>

<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    {{-- Global form errors --}}
    @if($errors->has('register') || $errors->any())
        <div class="alert alert-error" style="margin-bottom:0.75rem;padding:0.75rem;">
            @if($errors->has('register'))
                {{ $errors->first('register') }}
            @else
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Username --}}
    <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input type="text"
               id="username"
               name="username"
               value="{{ old('username') }}"
               class="form-input"
               placeholder="username_kamu"
               required autofocus>
        @error('username')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Full Name --}}
    <div class="form-group">
        <label class="form-label" for="full_name">Nama Lengkap</label>
        <input type="text"
               id="full_name"
               name="full_name"
               value="{{ old('full_name') }}"
               class="form-input"
               placeholder="Nama Lengkap Kamu"
               required>
        @error('full_name')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               class="form-input"
               placeholder="email@kamu.com"
               required>
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
               placeholder="Min. 8 karakter"
               required autocomplete="new-password">
        @error('password')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div class="form-group">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-input"
               placeholder="Ulangi password"
               required>
        @error('password_confirmation')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Submit --}}
    <button type="submit" class="btn-primary w-full" style="margin-top:0.5rem;">
        Daftar Sekarang
    </button>
</form>
@endsection