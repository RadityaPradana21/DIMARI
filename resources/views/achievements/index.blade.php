@extends('layouts.app')
@section('title', 'Achievements')

@section('content')
<div class="achievements-page">

    <div class="ach-header">
        <h2 class="page-title">🏆 Achievements</h2>
        <p class="page-sub">Kumpulkan badge dengan menyelesaikan tantangan di DIMARI</p>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" style="margin-bottom:2rem;">
        <div class="stat-card">
            <div class="stat-num">{{ $unlockedCount }}</div>
            <div class="stat-label">Achievement Diraih</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ count($allAchievements) - $unlockedCount }}</div>
            <div class="stat-label">Belum Terbuka</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $unlockedCount > 0 ? round(($unlockedCount / count($allAchievements)) * 100) : 0 }}%</div>
            <div class="stat-label">Persentase</div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="progress-section" style="margin-bottom:2rem;">
        <div class="progress-header">
            <span>Progress Achievement</span>
            <span>{{ $unlockedCount }} / {{ count($allAchievements) }}</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill"style="width: {{count($allAchievements) > 0 ? round(($unlockedCount / count($allAchievements)) * 100) : 0 }}%;">
            </div>
        </div>
    </div>

    {{-- Grid Achievements --}}
    <style>
    .ach-card.highlight { box-shadow: 0 6px 20px rgba(0,0,0,0.25); transform: translateY(-6px); border-color: #f59e0b; }
    </style>
    <div class="ach-grid">
        @foreach($allAchievements as $ach)
            @php $earned = isset($earnedByKey[$ach['key']]); @endphp
            @php $slug = \Illuminate\Support\Str::slug($ach['key']); @endphp
            <div id="ach-{{ $slug }}" class="ach-card {{ $earned ? 'earned' : 'locked' }}">
                <div class="ach-icon-wrap">
                    <span class="ach-icon">{{ $ach['icon'] }}</span>
                    @if(!$earned)
                        <div class="ach-lock">🔒</div>
                    @endif
                </div>
                <div class="ach-name">{{ $ach['name'] }}</div>
                <div class="ach-desc">{{ $ach['desc'] }}</div>
                @if($earned)
                    @php $earnedModel = $earnedByKey[$ach['key']]; @endphp
                    <div class="ach-date">
                        ✅ {{ ($earnedModel->date_earned instanceof \Illuminate\Support\Carbon) ? $earnedModel->date_earned->format('d M Y') : \Carbon\Carbon::parse($earnedModel->date_earned)->format('d M Y') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script>
// Highlight and scroll to achievement specified by ?open=
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const open = params.get('open');
    if (open) {
        const id = 'ach-' + open.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('highlight');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    const serverAwards = @json(session('awarded', []));
    if (Array.isArray(serverAwards) && serverAwards.length > 0) {
        serverAwards.forEach(a => showToast('🏆 Achievement unlocked: ' + a, 'success', 3000));
    }
});
</script>
@endpush
