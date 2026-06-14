@extends('layouts.app')
@section('title', 'Quiz')

@section('content')
<div class="quiz-container">

    {{-- ── Header & Module Selector ─────────────────────── --}}
    <div class="quiz-header">
        <h2 class="quiz-title">🎯 Quiz Digital Marketing</h2>
        <div class="module-selector">
            @foreach($modules as $mod)
                @php
                    $modId      = (int)$mod->id;
                    $isUnlocked = in_array($modId, $completedIds);
                    $modResult  = \App\Models\QuizResult::where('user_id', auth()->id())
                                    ->where('module_id', $modId)
                                    ->where('week_start', \App\Helpers\WeekHelper::thisWeekMonday())
                                    ->first();
                    $isDone     = (bool)$modResult;
                    $isActive   = $modId === (int)$activeModule->id;
                    $tabClass   = 'mod-tab';
                    $tabClass  .= $isActive    ? ' active' : '';
                    $tabClass  .= $isDone      ? ' done'   : '';
                    $tabClass  .= !$isUnlocked ? ' locked' : '';
                @endphp
                @if($isUnlocked)
                    <a href="{{ route('quiz', ['module' => $modId]) }}" class="{{ $tabClass }}">
                        M{{ $modId }}
                        @if($isDone)
                            <span class="tab-score">{{ $modResult->score }}</span>
                        @endif
                    </a>
                @else
                    <span class="{{ $tabClass }}">🔒 M{{ $modId }}</span>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ── LOCKED: Materi belum diselesaikan ─────────────── --}}
    @if(!$hasCompleted)
        <div class="quiz-locked-card">
            <div class="locked-icon">🔒</div>
            <div class="locked-title">Quiz Terkunci</div>
            <div class="locked-desc">
                Selesaikan materi
                <strong>Modul {{ $activeModule->id }}: {{ $activeModule->title }}</strong>
                terlebih dahulu untuk membuka quiz ini.
            </div>
            <div class="locked-hint">
                📖 Baca dan selesaikan seluruh materi modul, lalu kembali ke halaman ini.
            </div>
            <div class="locked-action" style="margin-top:1.5rem;">
                <a href="{{ route('materi', ['id' => $activeModule->id]) }}" class="btn-primary">
                    📚 Buka Materi Modul {{ $activeModule->id }}
                </a>
            </div>
        </div>

    {{-- ── RESULT: Sudah mengerjakan quiz ─────────────────── --}}
    @elseif($hasQuiz && !$reviewMode)
        <div class="quiz-result-card">
            <div class="result-icon">
                {{ $quizResult->score == 100 ? '🏆' : ($quizResult->score >= 70 ? '👍' : '📚') }}
            </div>
            <div class="result-score">{{ $quizResult->score }}<span>/100</span></div>
            <div class="result-label">Skor kamu untuk Modul {{ $activeModule->id }} minggu ini</div>
            <p class="result-note">Kamu hanya bisa mengerjakan quiz ini <strong>1x per minggu</strong>. Reset setiap Senin pukul 00.00.</p>
            @if($quizResult->score == 100)
                <div class="perfect-badge">🌟 PERFECT SCORE — Namamu ada di leaderboard!</div>
            @endif
            <div style="margin-top:1.5rem;">
                <a href="{{ route('quiz', ['module' => $activeModule->id, 'review' => 1]) }}" class="btn-review">
                    📋 Lihat Review Jawaban
                </a>
            </div>
        </div>

    {{-- ── REVIEW MODE ─────────────────────────────────────── --}}
    @elseif($hasQuiz && $reviewMode)
        <div class="review-header">
            <h3>📋 Review Jawaban — Modul {{ $activeModule->id }}</h3>
            <div class="review-score">Skor: {{ $quizResult->score }}/100</div>
            <a href="{{ route('quiz', ['module' => $activeModule->id]) }}" class="btn-back">← Kembali</a>
        </div>

        @php $savedAnswers = $quizResult->answers_json ?? []; @endphp

        @if($reviewQuestions->isEmpty())
            <div class="empty-state">Data review tidak tersedia. Jawaban sudah dihapus dari sesi.</div>
        @else
            <div class="review-questions">
                @foreach($reviewQuestions as $qi => $q)
                    @php
                        $chosen     = isset($savedAnswers[$q->id]) ? $savedAnswers[$q->id] : null;
                        $correctOpt = $q->options->firstWhere('is_correct', 1);
                        $chosenOpt  = $chosen ? $q->options->firstWhere('id', $chosen) : null;
                        $isRight    = $chosenOpt && $chosenOpt->is_correct == 1;
                        $notAnswered = !$chosen;
                    @endphp
                    <div class="review-item {{ $notAnswered ? 'skipped' : ($isRight ? 'correct' : 'wrong') }}">
                        <div class="review-qnum">
                            {{ $qi + 1 }}.
                            @if($notAnswered) <span style="color:#f59e0b">⏭ Tidak Dijawab</span>
                            @elseif($isRight) ✅
                            @else ❌
                            @endif
                        </div>
                        <div class="review-qtext">{{ $q->question_text }}</div>
                        <div class="options-grid-2">
                            @foreach($q->options as $opt)
                                @php
                                    $cls = 'review-opt';
                                    if ($opt->is_correct == 1) $cls .= ' opt-correct';
                                    if ($opt->id == $chosen && !$isRight) $cls .= ' opt-wrong';
                                @endphp
                                <div class="{{ $cls }}">{{ $opt->option_text }}</div>
                            @endforeach
                        </div>
                        @if($notAnswered)
                            <div class="correct-note">✅ Jawaban benar: {{ $correctOpt->option_text ?? '-' }}</div>
                        @elseif(!$isRight)
                            <div class="correct-note">✅ Jawaban benar: {{ $correctOpt->option_text ?? '-' }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    {{-- ── QUIZ AKTIF: Form soal dengan sistem nyawa ───────── --}}
    @else
        <div class="quiz-info-bar">
            <span>📝 Modul {{ $activeModule->id }}: {{ $activeModule->title }}</span>
            <span class="quiz-warning">⚠️ Hanya 1x per minggu! Tidak bisa diulang.</span>
        </div>

        @if(empty($questions))
            <div class="empty-state">Belum ada soal untuk modul ini. Hubungi mentor!</div>
        @else
            {{-- HUD Nyawa --}}
            <div class="lives-hud">
                <div class="lives-label">NYAWA</div>
                <div class="lives-hearts" id="livesHearts">
                    <span class="heart" data-i="1">❤️</span>
                    <span class="heart" data-i="2">❤️</span>
                    <span class="heart" data-i="3">❤️</span>
                    <span class="heart" data-i="4">❤️</span>
                    <span class="heart" data-i="5">❤️</span>
                </div>
                <div class="lives-count"><span id="livesCount">5</span> / 5</div>
            </div>

            {{-- HUD Poin (Realtime) --}}
            <div class="score-hud">
                <div class="score-label">POINTS</div>
                <div class="score-value" id="scoreValue">0</div>
            </div>

            <style>
            .score-hud {
                position: relative;
                display: inline-flex;
                flex-direction: column;
                align-items: center;
                margin-left: 1rem;
                padding: 0.35rem 0.8rem;
                border: 1px solid rgba(0, 204, 255, 0.15);
                border-radius: 8px;
                background: rgba(10, 10, 21, 0.6);
                box-shadow: 0 0 10px rgba(0, 204, 255, 0.05);
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .score-hud.pop-active {
                border-color: rgba(0, 240, 255, 0.6);
                box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);
            }
            .score-label {
                font-size: .7rem;
                color: var(--text-muted);
                letter-spacing: 0.5px;
            }
            .score-value {
                font-size: 1.15rem;
                font-weight: 800;
                margin-top: .15rem;
                color: #00f0ff; /* Solid light blue */
                display: inline-block;
                transition: transform 0.15s ease;
            }

            .score-value.color-shift-active {
                background: linear-gradient(225deg, #00f0ff 0%, #0066ff 25%, #8b5cf6 50%, #00f0ff 75%, #0066ff 100%);
                background-size: 400% 400%;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shiftNeon 1.5s linear forwards;
            }
            
            @keyframes shiftNeon {
                0% { background-position: 100% 100%; }
                20% { background-position: 66% 66%; } /* 0.3s fast */
                40% { background-position: 33% 33%; } /* 0.3s fast */
                100% { background-position: 0% 0%; } /* 0.9s slower */
            }
            
            .score-value.pop-bounce {
                animation: scorePop 0.15s ease-out;
            }
            
            @keyframes scorePop {
                0% { transform: scale(1); }
                50% { transform: scale(1.4); }
                100% { transform: scale(1); }
            }

            /* +10 burst */
            .points-burst {
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translate(8px, -50%);
                background: linear-gradient(45deg, #00f0ff, #0066ff);
                color: #fff;
                padding: 0.15rem 0.55rem;
                border-radius: 6px;
                font-weight: 800;
                font-size: 0.95rem;
                pointer-events: none;
                box-shadow: 0 0 12px rgba(0, 204, 255, 0.4);
                opacity: 0;
                animation: burst 1200ms cubic-bezier(.22,.9,.35,1) forwards;
            }
            @keyframes burst {
                0% { transform: translate(8px, 0) scale(1); opacity: 1 }
                30% { transform: translate(14px, -8px) scale(1.3); opacity: 1 }
                100% { transform: translate(18px, -28px) scale(1); opacity: 0 }
            }

            /* Heart / nyawa animations */
            .heart {
                display: inline-block;
                transition: transform .28s ease, opacity .28s ease;
                font-size: 1.05rem;
            }
            .heart-lost {
                animation: heartPop 700ms forwards;
            }
            @keyframes heartPop {
                0% { transform: scale(1); opacity: 1 }
                40% { transform: scale(1.45) rotate(-8deg); opacity: 1 }
                100% { transform: scale(0.18) rotate(-28deg); opacity: 0.15 }
            }

            /* Option correct feedback */
            .opt-correct-feedback {
                box-shadow: 0 8px 30px rgba(34, 197, 94, 0.12);
                border: 1px solid rgba(34, 197, 94, 0.35);
            }
            .opt-wrong-feedback {
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.06);
                border: 1px solid rgba(239, 68, 68, 0.12);
            }
            </style>

            <form id="quizForm">
                <div id="quizSlides">
                    @foreach($questions as $qi => $q)
                        @php $qData = is_array($q) ? (object)$q : $q; @endphp
                        <div class="question-block quiz-slide {{ $qi === 0 ? 'active' : '' }}"
                             id="slide-{{ $qi }}"
                             data-index="{{ $qi }}"
                             data-qid="{{ $qData->id ?? $q['id'] }}">

                            <div class="q-number">Soal {{ $qi + 1 }} dari {{ count($questions) }}</div>
                            <div class="q-text">{{ $qData->question_text ?? $q['question_text'] }}</div>

                            <div class="options-grid-2">
                                @php $opts = is_array($q) ? ($q['options'] ?? []) : ($q->options ?? []); @endphp
                                @foreach($opts as $opt)
                                    @php $optArr = is_array($opt) ? $opt : $opt->toArray(); @endphp
                                    <button type="button"
                                            class="option-btn"
                                            data-qid="{{ $qData->id ?? $q['id'] }}"
                                            data-oid="{{ $optArr['id'] }}"
                                            data-correct="{{ $optArr['is_correct'] }}"
                                            onclick="selectOption(this)">
                                        {{ $optArr['option_text'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Nav Bar --}}
                <div class="quiz-nav-bar">
                    <div id="progressInfo" class="quiz-progress">
                        0 / {{ count($questions) }} soal dijawab
                    </div>
                    <button type="button" id="btnNext" class="btn-nav" onclick="navigate()" disabled>
                        Next →
                    </button>
                    <button type="button" id="submitBtn" class="btn-submit"
                            style="display:none" onclick="submitQuiz()">
                        🚀 Kumpulkan Jawaban
                    </button>
                </div>
            </form>

            {{-- Game Over Overlay --}}
            <div class="gameover-overlay" id="gameOverScreen" style="display:none;">
                <div class="gameover-box">
                    <div class="gameover-icon">💔</div>
                    <div class="gameover-title">GAME OVER</div>
                    <div class="gameover-desc">Nyawa kamu habis! Skor dihitung dari soal yang benar dibagi total 10 soal.</div>
                    <div class="gameover-score" id="gameOverScore">0 / {{ count($questions) }} soal terjawab</div>
                    <button class="btn-primary" onclick="forceSubmit()">📤 Simpan & Lihat Hasil</button>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
const answers  = {};
const total    = {{ count($questions ?? []) }};
const moduleId = {{ (int)$activeModule->id }};

let current     = 0;
let lives       = 5;
let answered    = 0;
let gameOver    = false;
let waitingNext = false;
let points      = 0;

const slides = document.querySelectorAll('.quiz-slide');


function showSlide(index) {
    slides.forEach(s => s.classList.remove('active'));
    slides[index]?.classList.add('active');
    current = index;

    const isLast = current === total - 1;
    const nextBtn   = document.getElementById('btnNext');
    const submitBtn = document.getElementById('submitBtn');

    if (nextBtn)   { nextBtn.style.display   = isLast ? 'none'         : 'inline-block'; nextBtn.disabled = true; }
    if (submitBtn) { submitBtn.style.display = isLast ? 'inline-block' : 'none'; }
}

function navigate() {
    if (!waitingNext) return;
    waitingNext = false;
    if (current < total - 1) showSlide(current + 1);
}

function selectOption(btn) {
    if (gameOver) return;

    const slide = slides[current];
    if (!slide || slide.dataset.answered) return;

    const qid     = btn.dataset.qid;
    const oid     = btn.dataset.oid;
    const correct = btn.dataset.correct === '1';

    slide.dataset.answered = '1';
    const allBtns = slide.querySelectorAll('.option-btn');
    allBtns.forEach(b => { b.disabled = true; b.classList.remove('selected'); });

    answers[qid] = oid;
    answered++;

    // Mark slide as answered
    slide.classList.add('answered');

    if (correct) {
        btn.classList.add('opt-correct-feedback');
        // Tambah poin +10 untuk jawaban benar (animasi)
        addPointsAnimated(10);
    } else {
        btn.classList.add('opt-wrong-feedback');
        allBtns.forEach(b => {
            if (b.dataset.correct === '1') b.classList.add('opt-correct-feedback');
        });
        loseLife();
    }

    document.getElementById('progressInfo').textContent =
        answered + ' / ' + total + ' soal dijawab';

    if (gameOver) return;

    waitingNext = true;
    const isLast = current === total - 1;
    setTimeout(() => {
        if (isLast) {
            const sb = document.getElementById('submitBtn');
            if (sb) sb.disabled = false;
        } else {
            const nb = document.getElementById('btnNext');
            if (nb) nb.disabled = false;
        }
    }, 600);
}

function updatePoints() {
    const el = document.getElementById('scoreValue');
    if (el) {
        el.textContent = points;
        el.classList.remove('pop-bounce');
        void el.offsetWidth; // trigger reflow
        el.classList.add('pop-bounce');
    }
    const hud = document.querySelector('.score-hud');
    if (hud) {
        hud.classList.remove('pop-active');
        void hud.offsetWidth; // trigger reflow
        hud.classList.add('pop-active');
    }
}

// Animated points queue to increment numbers one-by-one
const pointsQueue = [];
let processingPoints = false;

function addPointsAnimated(amount){
    // visual burst
    const hud = document.querySelector('.score-hud');
    if(hud){
        const burst = document.createElement('span');
        burst.className = 'points-burst';
        burst.textContent = `+${amount}`;
        hud.appendChild(burst);
        setTimeout(()=>{ burst.remove(); }, 800);
    }

    pointsQueue.push(amount);
    if(!processingPoints) processPointsQueue();
}

function processPointsQueue(){
    if(processingPoints) return;
    const inc = pointsQueue.shift();
    if(typeof inc === 'undefined') return;
    processingPoints = true;
    const target = points + inc;
    const stepMs = 150; // 0.15s per increment
    
    const el = document.getElementById('scoreValue');
    if (el) el.classList.add('color-shift-active');

    function step(){
        if(points < target){
            points++;
            updatePoints();
            setTimeout(step, stepMs);
        } else {
            processingPoints = false;
            // Clear animation state at the end
            if (el) el.classList.remove('color-shift-active');
            const hud = document.querySelector('.score-hud');
            if (hud) hud.classList.remove('pop-active');
            
            if(pointsQueue.length > 0) processPointsQueue();
        }
    }
    step();
}

function loseLife() {
    if (lives <= 0) return;
    lives--;
    const hearts = document.querySelectorAll('.heart');
    if (hearts[lives]) hearts[lives].classList.add('heart-lost');
    document.getElementById('livesCount').textContent = lives;
    if (lives === 0) { gameOver = true; setTimeout(showGameOver, 800); }
}

function showGameOver() {
    document.getElementById('gameOverScore').textContent =
        answered + ' / ' + total + ' soal terjawab';
    document.getElementById('gameOverScreen').style.display = 'flex';
}

function submitQuiz() {
    if (Object.keys(answers).length < total) {
        if (!confirm(`Masih ada ${total - Object.keys(answers).length} soal belum dijawab. Yakin submit?`)) return;
    }
    doSubmit();
    // total_questions dikirim untuk penilaian /10
}

function forceSubmit() {
    document.getElementById('gameOverScreen').style.display = 'none';
    doSubmit();
    // total_questions dikirim untuk penilaian /10
}

function doSubmit() {
    const btn = document.getElementById('submitBtn');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Mengirim...'; }

    fetch('{{ route("quiz.submit") }}', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ module_id: moduleId, answers: answers, total_questions: total })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // If achievements were awarded, notify and open achievements page
            if (data.awarded && Array.isArray(data.awarded) && data.awarded.length > 0) {
                data.awarded.forEach(a => showToast('🏆 Achievement unlocked: ' + a.name, 'success', 2500));
                setTimeout(() => {
                    const url = '{{ route("achievements") }}' + '?open=' + encodeURIComponent(data.awarded[0].name);
                    window.location.href = url;
                }, 1200);
            } else {
                showToast('✅ Quiz selesai! Skor: ' + data.score + '/100', 'success');
                setTimeout(() => {
                    location.href = '{{ route("quiz") }}?module=' + moduleId;
                }, 1500);
            }
        } else {
            showToast(data.message || 'Gagal menyimpan.', 'error');
            if (btn) { btn.disabled = false; btn.textContent = '🚀 Kumpulkan Jawaban'; }
        }
    })
    .catch(() => {
        showToast('Koneksi error!', 'error');
        if (btn) { btn.disabled = false; btn.textContent = '🚀 Kumpulkan Jawaban'; }
    });
}

updatePoints();
if (slides.length > 0) showSlide(0);
</script>
@endpush