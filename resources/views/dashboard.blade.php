@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Welcome Banner --}}
    <div class="welcome-banner">
        <div>
            <h2 class="welcome-title gradient-text">
                Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->username }}! 👋
            </h2>
            <p class="welcome-sub">Lanjutkan perjalanan belajar digital marketing kamu minggu ini.</p>
        </div>
        <div class="week-score-badge">
            <div class="score-num">{{ $weekScore ?? 0 }}</div>
            <div class="score-label">Skor Minggu Ini</div>
        </div>
    </div>

    {{-- Progress Modul --}}
    <div class="progress-section">
        <div class="progress-header">
            <span class="progress-label">Progress Modul Minggu Ini</span>
            <span class="progress-count">{{ $completedCount ?? 0 }} / {{ $totalModules ?? 7 }}</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill"
                 style="width: {{ $totalModules > 0 ? number_format((($completedCount ?? 0) / $totalModules) * 100, 2) : 0 }}%;">
            </div>
        </div>
        <div class="progress-steps">
            @foreach($modules ?? [] as $mod)
                <div class="step-dot {{ in_array($mod->id, $doneIds ?? []) ? 'done' : '' }}"
                     title="Modul {{ $mod->id }}">{{ $mod->id }}</div>
            @endforeach
        </div>
    </div>



    {{-- ── BERANDA ─────────────────────────────────── --}}
    <div>
        <div class="dashboard-grid">
            {{-- Modul List --}}
            <div class="card-section">
                <h3 class="section-title">📚 Modul Minggu Ini</h3>
                <div class="module-list">
                    @forelse($modules ?? [] as $mod)
                        @php $done = in_array($mod->id, $doneIds ?? []); @endphp
                        <div class="module-item {{ $done ? 'done' : '' }}">
                            <div class="module-info">
                                <span class="module-num">M{{ $mod->id }}</span>
                                <span class="module-name">{{ $mod->title }}</span>
                            </div>
                            @if($done)
                                <span class="badge-done">✅ Selesai</span>
                            @else
                                <a href="{{ route('materi', ['id' => $mod->id]) }}" class="badge-pending">Baca →</a>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state">Belum ada modul tersedia.</div>
                    @endforelse
                </div>
            </div>

            {{-- Leaderboard --}}
            <div class="card-section">
                <h3 class="section-title">🏆 Leaderboard Minggu Ini</h3>
                <p style="font-size:.85rem;color:var(--text-muted);margin-top:.25rem;">Leaderboard reset setiap <strong>Senin 00:00</strong>. <br>Top 1–10 menerima voucher mingguan sesuai posisi.</p>
                <div class="leaderboard-list">
                    @forelse($leaderboard ?? [] as $i => $entry)
                        <div class="leader-row {{ $entry->user_id === auth()->id() ? 'is-me' : '' }}">
                            <span class="leader-rank">
                                @if($i === 0) 🥇
                                @elseif($i === 1) 🥈
                                @elseif($i === 2) 🥉
                                @else {{ $i + 1 }}
                                @endif
                            </span>
                            <span class="leader-name">
                                {{ $entry->username }}
                                @if($entry->user_id === auth()->id()) <em style="color:var(--primary)">(Kamu)</em> @endif
                            </span>
                            <span class="leader-score">{{ $entry->total_score }}</span>
                            <span class="leader-reward" style="margin-left:1rem;color:var(--text-muted);font-size:.9rem;">
                                {{ $entry->reward_label ?? '-' }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada data leaderboard.</div>
                    @endforelse
                </div>
                <div style="margin-top:.75rem;font-size:.85rem;color:var(--text-muted);">
                    <strong>Ketentuan Reward:</strong>
                    <div style="margin-top:.35rem;">
                        🥇 Top 1 : Voucher Rp100.000<br> 
                        🥈 Top 2 : Voucher Rp50.000<br>
                        🥉 Top 3 : Voucher Rp25.000<br>
                        🏅 Top 4 s/d 10 : Voucher Rp10.000<br>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="quick-actions">
            <a href="{{ route('materi') }}" class="action-card">
                <div class="action-icon">📖</div>
                <div class="action-label">Baca Materi</div>
            </a>
            <a href="{{ route('quiz') }}" class="action-card">
                <div class="action-icon">🎯</div>
                <div class="action-label">Kerjakan Quiz</div>
            </a>
            <a href="{{ route('profile') }}" class="action-card">
                <div class="action-icon">👤</div>
                <div class="action-label">Profil Saya</div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serverNotes = @json($notifications ?? []);
    if (Array.isArray(serverNotes) && serverNotes.length > 0) {
        serverNotes.forEach(n => {
            try {
                showToast(n.title + ': ' + (n.body || ''), 'success', 5000);
            } catch (e) { console.warn(e); }
        });
    }
});
</script>
@endpush


