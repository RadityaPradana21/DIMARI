@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
<div style="text-align:center;margin-bottom:1.5rem;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">🔒</div>
    <h3 style="font-family:'Orbitron',monospace;font-size:1rem;color:var(--primary);">
        Password Baru
    </h3>
</div>

<form method="POST" action="{{ route('password.store') }}" class="auth-form">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email', $request->email) }}"
               class="form-input"
               required autofocus>
        @error('email')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password Baru</label>
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

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-input"
               placeholder="Ulangi password baru"
               required>
    </div>

    <button type="submit" class="btn-primary w-full">
        Reset Password
    </button>
</form>
@endsection