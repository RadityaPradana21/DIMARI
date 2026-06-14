<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DIMARI') — DIMARI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* Confirm modal - dimari native */
    #dimariConfirmModal .modal-box { text-align: center; }

    /* Review: skipped state */
    .review-item.skipped {
        border-left: 3px solid #f59e0b;
        background: rgba(245,158,11,0.05);
    }

    /* Radio button hijau di kelola soal */
    .radio-correct { accent-color: #22c55e !important; }

    /* Forum styles */
    .forum-list { display: flex; flex-direction: column; gap: .5rem; }

    /* Achievement */
    .achievements-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px,1fr)); gap:1rem; }
    .achievement-card { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:12px; padding:1.25rem 1rem; text-align:center; }
    .ach-icon { font-size:2.2rem; margin-bottom:.4rem; }
    .ach-name { font-size:.8rem; font-weight:600; color:var(--text-light,#e5e7eb); margin-bottom:.3rem; }
    .ach-date { font-size:.7rem; color:var(--text-muted); }
    </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-optimizations.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="dimari-body">

{{-- Modal Logout --}}
<div id="logoutModal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-icon">⚡</div>
        <h3 class="modal-title">Keluar dari DIMARI?</h3>
        <p class="modal-desc">Progress minggu ini akan tetap tersimpan. Kamu bisa login lagi kapan saja.</p>
        <div class="modal-actions">
            <button onclick="closeLogoutModal()" class="btn-cancel">Batal</button>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-confirm">Ya, Keluar</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
                @csrf
            </form>
        </div>
    </div>
</div>

{{-- Navbar --}}
<nav class="dimari-nav">
    <a href="{{ route('dashboard') }}" class="nav-brand">
        <span class="brand-di">DI</span><span class="brand-mari">MARI</span>
    </a>
    <button type="button" class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Buka menu navigasi" aria-expanded="false">
        ☰
    </button>
    <div id="mobileMenu" class="nav-links">
        @if(auth()->user()->role === 'user')
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
            <a href="{{ route('materi') }}"
               class="nav-link {{ request()->routeIs('materi*') ? 'active' : '' }}">📚 Materi</a>
            <a href="{{ route('quiz') }}"
               class="nav-link {{ request()->routeIs('quiz*') ? 'active' : '' }}">🎯 Quiz</a>
            <a href="{{ route('forum') }}"
               class="nav-link {{ request()->routeIs('forum*') ? 'active' : '' }}">💬 Forum</a>
            <a href="{{ route('achievements') }}"
               class="nav-link {{ request()->routeIs('achievements*') ? 'active' : '' }}">🏆 Achievements</a>
            <a href="{{ route('profile') }}"
               class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}">👤 Profile</a>
        @elseif(auth()->user()->role === 'mentor')
            <a href="{{ route('mentor.index') }}" class="nav-link {{ request()->routeIs('mentor*') ? 'active' : '' }}">Mentor Panel</a>
            <a href="{{ route('forum') }}"
               class="nav-link {{ request()->routeIs('forum*') ? 'active' : '' }}">💬 Forum</a>
        @elseif(auth()->user()->role === 'admin')
            <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin*') ? 'active' : '' }}">Admin Panel</a>
            <a href="{{ route('forum') }}"
               class="nav-link {{ request()->routeIs('forum*') ? 'active' : '' }}">💬 Forum</a>
        @endif
    </div>
    <div class="nav-user">
        <span class="user-badge">{{ auth()->user()->username }}</span>
        <span class="role-badge role-{{ auth()->user()->role }}">
            {{ strtoupper(auth()->user()->role) }}
        </span>
        <button onclick="openLogoutModal()" class="btn-logout">⏻</button>
    </div>
</nav>

{{-- Toast Container --}}
<div id="toastContainer" class="toast-container"></div>

<main class="dimari-main">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="dimari-footer">
    <p>© {{ date('Y') }} DIMARI — Digital Marketing Learning Platform</p>
</footer>

<!-- DIMARI Confirm Modal (global) -->
<div id="dimariConfirmModal" class="modal-overlay hidden">
    <div class="modal-box" style="max-width:420px; text-align:center;">
        <div class="modal-icon">⚠️</div>
        <h3 class="modal-title" id="dimariConfirmTitle">Konfirmasi</h3>
        <p class="modal-desc" id="dimariConfirmMsg"></p>
        <div class="modal-actions">
            <button onclick="cancelDimariConfirm()" class="btn-cancel">Batal</button>
            <button onclick="executeDimariConfirm()" id="dimariConfirmBtn" class="btn-confirm" style="background:var(--error, #ef4444);">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/dimari.js') }}"></script>
<script>
// ── GLOBAL DIMARI CONFIRM (mengganti browser confirm()) ────────
let _dimariPendingForm = null;
let _dimariPendingCallback = null;

function dimariConfirm(e, msg, btnLabel) {
    e.preventDefault();
    _dimariPendingForm = e.target.closest('form');
    _dimariPendingCallback = null;
    document.getElementById('dimariConfirmMsg').innerHTML = msg || 'Yakin melanjutkan?';
    document.getElementById('dimariConfirmBtn').textContent = btnLabel || 'Ya, Lanjutkan';
    document.getElementById('dimariConfirmModal').classList.remove('hidden');
    return false;
}

function dimariConfirmCb(msg, callback) {
    _dimariPendingForm = null;
    _dimariPendingCallback = callback;
    document.getElementById('dimariConfirmMsg').innerHTML = msg;
    document.getElementById('dimariConfirmBtn').textContent = 'Ya';
    document.getElementById('dimariConfirmModal').classList.remove('hidden');
}

function cancelDimariConfirm() {
    document.getElementById('dimariConfirmModal').classList.add('hidden');
    _dimariPendingForm = null;
    _dimariPendingCallback = null;
}

function executeDimariConfirm() {
    document.getElementById('dimariConfirmModal').classList.add('hidden');
    if (_dimariPendingForm) { _dimariPendingForm.submit(); _dimariPendingForm = null; }
    if (_dimariPendingCallback) { _dimariPendingCallback(); _dimariPendingCallback = null; }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cancelDimariConfirm();
});
</script>
<script src="{{ asset('js/analytics-tracker.js') }}"></script>
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[method="GET"] input[type="search"]').forEach(function (input) {
        let timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const form = input.closest('form');
                if (!form) return;
                // reset page param if exists
                const pageInput = form.querySelector('input[name="page"]');
                if (pageInput) pageInput.value = 1;
                form.submit();
            }, 450);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                clearTimeout(timer);
                input.closest('form')?.submit();
            }
        });
    });
});
</script>
</body>
</html>
