@extends('layouts.app')
@section('title', 'Forum Diskusi')

@section('content')
<div class="forum-page">

    <div class="forum-header-section">
        <h2 class="page-title">💬 Forum Diskusi</h2>
        <p class="page-sub">Tempat berdiskusi, bertanya, dan berbagi pengalaman belajar</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Buat Topik --}}
    <div class="glass-card" style="padding:1.5rem; margin-bottom:1.5rem;">
        <h3 class="section-title" style="margin-bottom:1rem;">➕ Buat Topik Baru</h3>
        <form method="POST" action="{{ route('forum.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group" style="flex:2">
                    <label class="form-label">Judul Topik</label>
                    <input type="text" name="title" class="form-input" required
                           placeholder="Tulis judul diskusi kamu...">
                </div>
                {{-- #4: Dropdown kategori dengan desain konsisten (bukan select HTML default) --}}
                <div class="form-group" style="position:relative;">
                    <label class="form-label">Kategori</label>
                    <div class="custom-select-wrap">
                        <button type="button" class="custom-select-btn" id="catDropBtn"
                                onclick="toggleCatDrop()">
                            <span id="catDropLabel">General</span>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" id="catDropArrow">
                                <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="custom-select-menu hidden" id="catDropMenu">
                            @foreach($categories as $cat)
                                <div class="custom-select-item" onclick="selectCat('{{ $cat }}')">
                                    {{ $cat }}
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="category" id="catInput" value="General">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Isi Diskusi</label>
                <textarea name="content" class="form-input" rows="3" required
                          placeholder="Ceritakan apa yang ingin kamu diskusikan..."></textarea>
            </div>
            <button type="submit" class="btn-primary">💬 Posting Topik</button>
        </form>
    </div>

    {{-- #3: Filter Multi-Pilih (toggle per kategori) --}}
    <div class="forum-filter-bar">
        <span class="filter-label">Filter:</span>
        <div class="filter-chips" id="filterChips">
            @foreach($categories as $cat)
                @php $isActive = in_array($cat, $activeCats); @endphp
                <button type="button"
                        class="filter-chip {{ $isActive ? 'active' : '' }}"
                        data-cat="{{ $cat }}"
                        onclick="toggleCatFilter('{{ $cat }}', this)">
                    {{ $cat }}
                    @if($isActive)<span class="chip-x">✕</span>@endif
                </button>
            @endforeach
            @if(!empty($activeCats))
                <button type="button" class="filter-chip clear-btn" onclick="clearAllFilters()">
                    Reset ✕
                </button>
            @endif
        </div>
        <form method="GET" action="{{ route('forum') }}" id="filterForm" style="display:none;">
            @foreach($activeCats as $cat)
                <input type="hidden" name="cats[]" value="{{ $cat }}">
            @endforeach
        </form>
    </div>

    {{-- Daftar Topik --}}
    <div class="forum-topics">
        @forelse($forums as $forum)
            <div class="forum-card" id="forum-{{ $forum->id }}">
                <div class="forum-card-top">
                    <div class="forum-meta">
                        <span class="forum-cat-badge">{{ $forum->category ?? 'General' }}</span>
                        <span class="forum-author">
                            👤 <strong>{{ $forum->author->username ?? 'Anonim' }}</strong>
                            <span class="role-badge role-{{ $forum->author->role ?? 'user' }}" style="font-size: .65rem; margin-left: 0.25rem; padding: 0.1rem 0.4rem;">
                                {{ strtoupper($forum->author->role ?? 'user') }}
                            </span>
                        </span>
                        <span class="text-muted" style="font-size:.78rem;">
                            {{ $forum->created_at->diffForHumans() }}
                        </span>
                        @can('update', $forum)
                        <a href="{{ route('forum.edit', $forum) }}" class="btn-edit" style="font-size:.75rem; margin-left:0.5rem;">✏️</a>
                        @endcan
                        @can('delete', $forum)
                        <form method="POST" action="{{ route('forum.destroy', $forum) }}" style="display:inline;" onsubmit="return dimariConfirm(event, 'Hapus topik ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" style="font-size:.75rem; margin-left:0.25rem;">🗑</button>
                        </form>
                        @endcan
                    </div>
                    <span class="forum-reply-badge">💬 {{ $forum->replies->count() }}</span>
                </div>

                <h4 class="forum-title">{{ $forum->title }}</h4>
                <p class="forum-excerpt">{{ Str::limit($forum->content, 180) }}</p>

                <button class="btn-edit" style="font-size:.8rem; margin-top:.5rem;"
                        onclick="toggleThread({{ $forum->id }})">
                    💬 Lihat Diskusi ({{ $forum->replies->count() }})
                </button>

                {{-- Thread --}}
                <div id="thread-{{ $forum->id }}" class="forum-thread" style="display:none;">
                    <div class="forum-full-body">{!! nl2br(e($forum->content)) !!}</div>

                    @foreach($forum->replies as $reply)
                        <div class="forum-reply {{ $reply->is_mentor_reply ? 'reply-mentor' : '' }}">
                            <div class="reply-meta">
                                <strong>{{ $reply->user->username ?? 'User' }}</strong>
                                <span class="role-badge role-{{ $reply->user->role ?? 'user' }}" style="font-size: .65rem; margin-left: 0.25rem; padding: 0.1rem 0.4rem;">
                                    {{ strtoupper($reply->user->role ?? 'user') }}
                                </span>
                                <span class="text-muted" style="font-size:.72rem;">
                                    · {{ $reply->created_at->diffForHumans() }}
                                </span>
                                @can('update', $reply)
                                <a href="{{ route('forum.reply.edit', $reply) }}" class="btn-edit" style="font-size:.7rem; margin-left:0.5rem;">✏️</a>
                                @endcan
                                @can('delete', $reply)
                                <form method="POST" action="{{ route('forum.reply.destroy', $reply) }}" style="display:inline;" onsubmit="return dimariConfirm(event, 'Hapus balasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" style="font-size:.7rem; margin-left:0.25rem;">🗑</button>
                                </form>
                                @endcan
                            </div>
                            <div class="reply-content">{{ $reply->content }}</div>
                        </div>
                    @endforeach

                    <form method="POST" action="{{ route('forum.reply', $forum->id) }}"
                          class="reply-form">
                        @csrf
                        <input type="text" name="content" class="form-input"
                               placeholder="Tulis balasan kamu..." required
                               style="flex:1; padding:.5rem .75rem; font-size:.85rem;">
                        <button type="submit" class="btn-primary"
                                style="padding:.5rem 1rem; white-space:nowrap;">Kirim</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size:2rem; margin-bottom:.5rem;">💬</div>
                @if(!empty($activeCats))
                    Tidak ada topik untuk kategori yang dipilih.
                    <br><a href="{{ route('forum') }}" class="btn-edit" style="margin-top:.5rem; display:inline-block;">Lihat Semua</a>
                @else
                    Belum ada topik diskusi. Jadilah yang pertama memulai!
                @endif
            </div>
        @endforelse
    </div>

    <div style="margin-top:1.5rem;">{{ $forums->links() }}</div>

</div>

<style>

</style>
@endsection

@push('scripts')
<script>
// Toggle thread
function toggleThread(id) {
    const el = document.getElementById('thread-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// Custom dropdown kategori
function toggleCatDrop() {
    const menu  = document.getElementById('catDropMenu');
    const arrow = document.getElementById('catDropArrow');
    const btn   = document.getElementById('catDropBtn');
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
    // tandai selected
    document.querySelectorAll('.custom-select-item').forEach(el => {
        el.classList.toggle('selected', el.textContent.trim() === cat);
    });
}
// Tutup dropdown klik luar
document.addEventListener('click', e => {
    if (!e.target.closest('.custom-select-wrap')) {
        document.getElementById('catDropMenu')?.classList.add('hidden');
        document.getElementById('catDropArrow')?.classList.remove('rotated');
        document.getElementById('catDropBtn')?.classList.remove('open');
    }
});

// Multi-filter toggle
let activeCats = @json($activeCats);

function toggleCatFilter(cat, btn) {
    const idx = activeCats.indexOf(cat);
    if (idx === -1) {
        activeCats.push(cat);
        btn.classList.add('active');
        if (!btn.querySelector('.chip-x')) {
            const x = document.createElement('span');
            x.className = 'chip-x'; x.textContent = '✕';
            btn.appendChild(x);
        }
    } else {
        activeCats.splice(idx, 1);
        btn.classList.remove('active');
        btn.querySelector('.chip-x')?.remove();
    }
    submitFilter();
}

function clearAllFilters() {
    activeCats = [];
    submitFilter();
}

function submitFilter() {
    const form = document.getElementById('filterForm');
    // Hapus input lama
    form.querySelectorAll('input[name="cats[]"]').forEach(el => el.remove());
    // Tambah input baru
    activeCats.forEach(cat => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'cats[]'; inp.value = cat;
        form.appendChild(inp);
    });
    form.submit();
}
</script>
@endpush
