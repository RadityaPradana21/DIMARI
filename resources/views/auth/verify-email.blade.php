@extends('layouts.guest')
@section('title', 'Verifikasi Email')

@section('content')
<div style="text-align:center;margin-bottom:1.5rem;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📧</div>
    <h3 style="font-family:'Orbitron',monospace;font-size:1rem;color:var(--primary);margin-bottom:0.5rem;">
        Verifikasi Email
    </h3>
    <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.5;">
        Kami sudah mengirim link verifikasi ke email kamu.
        Cek inbox atau folder spam.
    </p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success">
        Link verifikasi baru sudah dikirim ke email kamu.
    </div>
@endif

<div style="display:flex;flex-direction:column;gap:0.75rem;margin-top:1rem;">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary w-full">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-cancel w-full">
            Keluar
        </button>
    </form>
</div>
@endsection