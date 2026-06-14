@extends('layouts.guest')
@section('title', 'Konfirmasi Password')

@section('content')
<div style="text-align:center;margin-bottom:1.5rem;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">🛡️</div>
    <h3 style="font-family:'Orbitron',monospace;font-size:1rem;color:var(--primary);margin-bottom:0.5rem;">
        Konfirmasi Password
    </h3>
    <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.5;">
        Area ini memerlukan verifikasi password sebelum melanjutkan.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
    @csrf

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-input"
               placeholder="Masukkan password kamu"
               required autocomplete="current-password">
        @error('password')
            <div class="alert alert-error" style="margin-top:0.5rem;padding:0.5rem 0.75rem;">
                {{ $message }}
            </div>
        @enderror
    </div>

    <button type="submit" class="btn-primary w-full">
        Konfirmasi
    </button>
</form>
@endsection