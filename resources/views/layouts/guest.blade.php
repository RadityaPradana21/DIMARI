<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DIMARI') — DIMARI</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="dimari-body">

<div class="auth-page">
    {{-- Background dekorasi --}}
    <div class="auth-bg">
        <div class="auth-grid"></div>
        <div class="auth-glow glow-1"></div>
        <div class="auth-glow glow-2"></div>
    </div>

    <div class="auth-container">
        <section class="auth-card auth-card-single">
            <div class="auth-intro-block">
                <div class="auth-brand">
                    <div class="brand-logo">
                        <span class="brand-di">DI</span><span class="brand-mari">MARI</span>
                    </div>
                    <div class="brand-sub">Digital Marketing Interactive</div>
                </div>

                <h1 class="auth-welcome-title">Belajar digital marketing dengan pengalaman yang interaktif.</h1>
                <p class="auth-welcome-copy">
                    Materi singkat, quiz menantang, pencapaian progres, dan reward yang membuat perjalanan belajar kamu makin seru.
                </p>

                <div class="auth-pill-row">
                    <span class="auth-pill">📚 Materi terstruktur</span>
                    <span class="auth-pill">🎯 Quiz interaktif</span>
                    <span class="auth-pill">🏆 Reward & leaderboard</span>
                </div>
            </div>

            <div class="auth-form-shell">
                @yield('content')
            </div>
        </section>
    </div>
</div>

<script src="{{ asset('js/dimari.js') }}"></script>
</body>
</html>