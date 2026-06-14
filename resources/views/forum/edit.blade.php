@extends('layouts.app')
@section('title', 'Edit Topik Forum')

@section('content')
<div class="forum-page">

    <div class="forum-header-section">
        <h2 class="page-title">✏️ Edit Topik</h2>
        <p class="page-sub">Edit diskusi kamu</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="glass-card" style="padding:1.5rem;">
        <form method="POST" action="{{ route('forum.update', $forum) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Judul Topik</label>
                <input type="text" name="title" class="form-input" required
                       value="{{ old('title', $forum->title) }}"
                       placeholder="Tulis judul diskusi kamu...">
            </div>
            
            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Kategori</label>
                <div class="custom-select-wrap">
                    <button type="button" class="custom-select-btn" id="catDropBtn"
                            onclick="toggleCatDrop()">
                        <span id="catDropLabel">{{ old('category', $forum->category ?? 'General') }}</span>
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" id="catDropArrow">
                            <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="custom-select-menu hidden" id="catDropMenu">
                        @foreach(\App\Http\Controllers\ForumController::CATEGORIES as $cat)
                            <div class="custom-select-item" onclick="selectCat('{{ $cat }}')">
                                {{ $cat }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="category" id="catInput" value="{{ old('category', $forum->category ?? 'General') }}">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Isi Diskusi</label>
                <textarea name="content" class="form-input" rows="5" required
                          placeholder="Ceritakan apa yang ingin kamu diskusikan...">{{ old('content', $forum->content) }}</textarea>
            </div>
            
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                <a href="{{ route('forum') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

</div>

<style>
.custom-select-wrap { position: relative; }
.custom-select-btn {
    width: 100%;
    padding: 0.6rem 0.75rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    color: var(--text-light, #e5e7eb);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
}
.custom-select-btn.open { border-color: var(--primary, #8b5cf6); }
.custom-select-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 0.25rem;
    background: var(--bg2, #1f2937);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 10;
}
.custom-select-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.15s;
}
.custom-select-item:hover { background: rgba(255,255,255,0.05); }
.custom-select-item.selected { background: rgba(139,92,246,0.2); }
.hidden { display: none; }
.rotated { transform: rotate(180deg); }
</style>

@push('scripts')
<script>
function toggleCatDrop() {
    const menu = document.getElementById('catDropMenu');
    const arrow = document.getElementById('catDropArrow');
    const btn = document.getElementById('catDropBtn');
    const hidden = menu.classList.toggle('hidden');
    arrow.classList.toggle('rotated', !hidden);
    btn.classList.toggle('open', !hidden);
}
function selectCat(cat) {
    document.getElementById('catDropLabel').textContent = cat;
    document.getElementById('catInput').value = cat;
    document.getElementById('catDropMenu').classList.add('hidden');
    document.getElementById('catDropArrow').classList.remove('rotated');
    document.getElementById('catDropBtn').classList.remove('open');
    document.querySelectorAll('.custom-select-item').forEach(el => {
        el.classList.toggle('selected', el.textContent.trim() === cat);
    });
}
document.addEventListener('click', e => {
    if (!e.target.closest('.custom-select-wrap')) {
        document.getElementById('catDropMenu')?.classList.add('hidden');
        document.getElementById('catDropArrow')?.classList.remove('rotated');
        document.getElementById('catDropBtn')?.classList.remove('open');
    }
});
</script>
@endpush
