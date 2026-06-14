@extends('layouts.app')
@section('title', 'Edit Balasan')

@section('content')
<div class="forum-page">

    <div class="forum-header-section">
        <h2 class="page-title">✏️ Edit Balasan</h2>
        <p class="page-sub">Edit balasan kamu</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="glass-card" style="padding:1.5rem;">
        <form method="POST" action="{{ route('forum.reply.update', $reply) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Isi Balasan</label>
                <textarea name="content" class="form-input" rows="5" required
                          placeholder="Tulis balasan kamu...">{{ old('content', $reply->content) }}</textarea>
            </div>
            
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                <a href="{{ route('forum') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

</div>
