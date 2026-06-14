@extends('layouts.app')
@section('title', 'Materi')

@section('content')
<div class="materi-container">

    {{-- ── Sidebar ─────────────────────────────────────── --}}
    <aside class="materi-sidebar">
        <h3 class="sidebar-title">📚 {{ count($modules) }} Modul</h3>

        {{-- Search --}}
        <div class="sidebar-search">
            <input type="text"
                   id="moduleSearch"
                   class="sidebar-search-input"
                   placeholder="Cari modul..."
                   oninput="filterModules()"
                   autocomplete="off">
            <span class="sidebar-search-icon">🔍</span>
        </div>

        {{-- Filter --}}
        <div class="sidebar-filter">
            <button class="filter-btn active" onclick="setFilter('all', this)">Semua</button>
            <button class="filter-btn" onclick="setFilter('done', this)">✅ Selesai</button>
            <button class="filter-btn" onclick="setFilter('undone', this)">📖 Belum</button>
        </div>

        <div class="sidebar-empty" id="sidebarEmpty" style="display:none;">
            Tidak ada modul yang cocok.
        </div>

        <nav class="module-nav" id="moduleNav">
            @foreach($modules as $mod)
                @php $done = in_array($mod->id, $doneIds); @endphp
                <a href="{{ route('materi', ['id' => $mod->id]) }}"
                   class="module-nav-item {{ $mod->id === $activeModule->id ? 'active' : '' }} {{ $done ? 'completed' : '' }}"
                   data-title="{{ strtolower($mod->title) }}"
                   data-desc="{{ strtolower($mod->description ?? '') }}"
                   data-status="{{ $done ? 'done' : 'undone' }}">
                    <span class="mnav-icon">{{ $moduleIcons[$mod->id] ?? '📄' }}</span>
                    <span class="mnav-title">{{ $mod->title }}</span>
                    @if($done)
                        <span class="mnav-check">✓</span>
                    @endif
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- ── Content Area ──────────────────────────────── --}}
    <div class="materi-content">

        <div class="module-header">
            <div class="module-badge">Modul {{ $activeModule->id }}</div>
            <h2 class="module-title">{{ $activeModule->title }}</h2>
            <p class="module-desc">{{ $activeModule->description }}</p>
        </div>

        {{-- Tab: Materi / Video --}}
        <div class="tab-bar" id="materiTabBar" style="margin-bottom:1rem;">
            <button class="tab-btn active" onclick="switchMateriTab('content', this)">📄 Materi</button>
            <button class="tab-btn" onclick="switchMateriTab('video', this)">🎬 Video</button>
        </div>

        {{-- Tab: Konten Materi --}}
        <div id="materi-tab-content" class="module-body">
            {!! $moduleContent !!}
        </div>

        {{-- Tab: Video (tanpa tombol selesaikan) --}}
        <div id="materi-tab-video" style="display:none; padding:1rem 0;">
            @php $videoUrl = $activeModule->video_url ?? null; @endphp
            @php $videoTitle = $activeModule->video_title ?? null; $videoDesc = $activeModule->video_description ?? null; @endphp
            @if(!empty($videoUrl))
                @php
                    $isYT = str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be');
                    if ($isYT) {
                        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $m);
                        $embedUrl = 'https://www.youtube.com/embed/' . ($m[1] ?? '');
                    }
                @endphp
                <div class="video-meta" style="margin:0 0 0.75rem;padding:0.75rem;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                    <h3 style="margin:0 0 .25rem;font-size:1.05rem;color:var(--text-light);">{{ $videoTitle ?? 'Video Modul: ' . $activeModule->title }}</h3>
                    @if($videoDesc)
                        <p style="margin:0;color:var(--text-muted);font-size:.95rem;line-height:1.4;">{{ $videoDesc }}</p>
                    @endif
                </div>
                @if($isYT)
                    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px;">
                        <iframe src="{{ $embedUrl }}"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;"
                                frameborder="0" allowfullscreen></iframe>
                    </div>
                @else
                    <video controls style="width:100%;border-radius:12px;">
                        <source src="{{ $videoUrl }}" type="video/mp4">
                    </video>
                @endif
                <p style="font-size:.8rem;color:var(--text-muted);margin-top:.75rem;text-align:center;">
                    🎬 Video materi untuk modul ini. Kembali ke tab <strong>Materi</strong> untuk menyelesaikan modul.
                </p>
            @else
                <div class="quiz-locked-card" style="margin:0;">
                    <div class="locked-icon">🎬</div>
                    <div class="locked-title">Video Belum Tersedia</div>
                    <div class="locked-desc">Video untuk modul ini belum diunggah oleh mentor.</div>
                </div>
            @endif
        </div>

        @if(!$alreadyDone)
        {{-- Timer & Complete --}}
        <div class="module-footer">
            <div class="timer-section">
                <div class="timer-label">⏱ Baca minimal 1 menit sebelum menyelesaikan modul</div>
                <div class="timer-display" id="timerDisplay">01:00</div>
                <div class="timer-bar-track">
                    <div class="timer-bar-fill" id="timerBar"></div>
                </div>
            </div>
            <button id="completeBtn" class="btn-complete" disabled
                    onclick="completeModule({{ $activeModule->id }})">
                🔒 Tunggu Timer...
            </button>
        </div>

        @else
        {{-- Sudah selesai --}}
        @php
            $nextId     = $activeModule->id < count($modules) ? $activeModule->id + 1 : null;
            $hasNextBtn = $nextId && !in_array($nextId, $doneIds);
            $quizUrl    = route('quiz', ['module' => $activeModule->id]);
        @endphp
        <div class="module-footer">
            <div class="completed-badge">✅ Modul ini sudah kamu selesaikan minggu ini!</div>
            <div class="module-footer-actions {{ !$hasNextBtn ? 'single-action' : '' }}">
                @if($hasNextBtn)
                    <a href="{{ route('materi', ['id' => $nextId]) }}" class="btn-next">
                        Lanjut Modul {{ $nextId }} →
                    </a>
                @endif
                <a href="{{ $quizUrl }}" class="btn-quiz-nav">
                    🎯 Kerjakan Quiz Modul {{ $activeModule->id }}
                </a>
            </div>
        </div>
        @endif

    </div>{{-- /.materi-content --}}
</div>{{-- /.materi-container --}}
@endsection

@push('scripts')
<script>
function switchMateriTab(name, btn) {
    document.querySelectorAll('#materiTabBar .tab-btn')
        .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    document.getElementById('materi-tab-content').style.display =
        name === 'content' ? '' : 'none';

    document.getElementById('materi-tab-video').style.display =
        name === 'video' ? '' : 'none';

    // Sembunyikan footer saat video dibuka
    const footer = document.querySelector('.module-footer');

    if (footer) {
        footer.style.display =
            name === 'video' ? 'none' : '';
    }
}

// ── Timer ─────────────────────────────────────────────────
@if(!$alreadyDone)
(function() {
    const TOTAL = 60;
    let remaining = TOTAL;
    const display = document.getElementById('timerDisplay');
    const bar     = document.getElementById('timerBar');
    const btn     = document.getElementById('completeBtn');

    const tick = setInterval(() => {
        remaining--;
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        display.textContent = m + ':' + s;
        bar.style.width = ((TOTAL - remaining) / TOTAL * 100) + '%';

        if (remaining <= 0) {
            clearInterval(tick);
            display.textContent = '✓ Siap!';
            btn.disabled        = false;
            btn.textContent     = '✅ Selesaikan Modul';
            btn.classList.add('ready');
        }
    }, 1000);
})();
@endif

function completeModule(moduleId) {
    const btn = document.getElementById('completeBtn');
    btn.disabled    = true;
    btn.textContent = 'Menyimpan...';

    fetch('{{ route("materi.complete") }}', {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ module_id: moduleId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // If backend returned awarded achievements, show notifications and open achievements page
            if (data.awarded && Array.isArray(data.awarded) && data.awarded.length > 0) {
                data.awarded.forEach(a => {
                    showToast('🏆 Achievement unlocked: ' + a.name, 'success', 2500);
                });
                // Open achievements page and highlight the first unlocked achievement
                setTimeout(() => {
                    const url = '{{ route("achievements") }}' + '?open=' + encodeURIComponent(data.awarded[0].name);
                    window.location.href = url;
                }, 1000);
            } else {
                showToast('🎉 Modul berhasil diselesaikan!', 'success');
                setTimeout(() => location.reload(), 1200);
            }
        } else {
            showToast(data.message || 'Gagal menyimpan.', 'error');
            btn.disabled    = false;
            btn.textContent = '✅ Selesaikan Modul';
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan koneksi.', 'error');
        btn.disabled = false;
    });
}

// ── Sidebar Search & Filter ───────────────────────────────
let activeFilter = 'all';

function filterModules() {
    const query   = document.getElementById('moduleSearch').value.toLowerCase().trim();
    const items   = document.querySelectorAll('#moduleNav .module-nav-item');
    const empty   = document.getElementById('sidebarEmpty');
    let   visible = 0;

    items.forEach(item => {
        const matchSearch = !query
            || item.dataset.title.includes(query)
            || item.dataset.desc.includes(query);
        const matchFilter = activeFilter === 'all' || item.dataset.status === activeFilter;

        item.style.display = (matchSearch && matchFilter) ? '' : 'none';
        if (matchSearch && matchFilter) visible++;
    });

    empty.style.display = visible === 0 ? 'block' : 'none';
}

function setFilter(filter, btn) {
    activeFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterModules();
}
</script>
@endpush