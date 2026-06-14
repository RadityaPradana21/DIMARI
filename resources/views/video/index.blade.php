@extends('layouts.app')
@section('title', 'Video')

@section('content')
<div class="video-container">

    {{-- ── Sidebar Modul ──────────────────────────────────── --}}
    <aside class="video-sidebar">
        <h3 class="sidebar-title">🎬 Video Modul</h3>
        <nav class="module-nav" id="videoModuleNav">
            @foreach($modules as $mod)
                @php $isActive = $mod->id === $activeModule->id; @endphp
                <a href="{{ route('video', ['module' => $mod->id]) }}"
                   class="module-nav-item {{ $isActive ? 'active' : '' }} {{ empty($mod->video_url ?? null) ? 'locked' : '' }}">
                    <span class="mnav-icon">{{ empty($mod->video_url ?? null) ? '🔒' : '▶️' }}</span>
                    <span class="mnav-title">{{ $mod->title }}</span>
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- ── Area Video ───────────────────────────────────────── --}}
    <div class="video-main">

        <div class="module-header">
            <div class="module-badge">Video — Modul {{ $activeModule->id }}</div>
            <h2 class="module-title">{{ $activeModule->video_title ?? $activeModule->title }}</h2>
            @if($activeModule->video_description)
                <p class="module-desc">{{ $activeModule->video_description }}</p>
            @elseif($activeModule->description)
                <p class="module-desc">{{ $activeModule->description }}</p>
            @endif
        </div>

        @if(!empty($activeModule->video_url ?? null))
            {{-- Ada video --}}
            <div class="video-player-wrapper">
                @php
                    $url = $activeModule->video_url;
                    // Deteksi YouTube
                    $isYoutube = str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
                    if ($isYoutube) {
                        // Konversi ke embed URL
                        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
                        $videoId = $matches[1] ?? '';
                        $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                    }
                @endphp

                @if($isYoutube)
                    <div class="video-embed-responsive">
                        <iframe src="{{ $embedUrl }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="video-iframe">
                        </iframe>
                    </div>
                @else
                    <video controls class="video-player">
                        <source src="{{ $url }}" type="video/mp4">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                @endif
            </div>

            {{-- Navigasi ke materi & quiz --}}
            <div class="video-footer-actions">
                <a href="{{ route('materi', ['id' => $activeModule->id]) }}" class="btn-secondary">
                    📚 Baca Materi
                </a>
                <a href="{{ route('quiz', ['module' => $activeModule->id]) }}" class="btn-primary">
                    🎯 Kerjakan Quiz
                </a>
            </div>

        @else
            {{-- Belum ada video --}}
            <div class="quiz-locked-card">
                <div class="locked-icon">🎬</div>
                <h3>Video Belum Tersedia</h3>
                <p>
                    Video untuk modul <strong>{{ $activeModule->title }}</strong> belum diunggah.<br>
                    Coba pilih modul lain atau kembali lagi nanti.
                </p>
                <a href="{{ route('materi', ['id' => $activeModule->id]) }}" class="btn-primary">
                    📚 Baca Materi Saja
                </a>
            </div>
        @endif

    </div>{{-- /.video-main --}}
</div>{{-- /.video-container --}}
@endsection
