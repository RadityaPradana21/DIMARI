@extends('layouts.app')
@section('title', 'Detail Soal Quiz')

@section('content')
<div class="admin-container">

    <div class="admin-header">
        <h1 class="page-title">👁 Detail Soal</h1>
        <p class="page-sub">Pratinjau pertanyaan dan semua pilihan jawaban.</p>
    </div>

    <div class="mentor-section">

        {{-- Meta Info --}}
        <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
            <div>
                <span class="form-label">Modul</span>
                <span class="role-badge role-mentor">{{ $question->module->title ?? '—' }}</span>
            </div>
            <div>
                <span class="form-label">Dibuat</span>
                <span class="text-muted">{{ $question->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="form-label">Jumlah Opsi</span>
                <span class="score-badge {{ $question->options->count() >= 4 ? 'good' : 'low' }}">
                    {{ $question->options->count() }}
                </span>
            </div>
        </div>

        {{-- Pertanyaan --}}
        <div class="q-card" style="margin-bottom:1.25rem;">
            <div class="q-card-header">
                <span class="form-label" style="margin:0;">Pertanyaan</span>
            </div>
            <p style="font-size:1.05rem; color:var(--text); line-height:1.7; margin:0;">
                {{ $question->question_text }}
            </p>
        </div>

        {{-- Opsi Jawaban --}}
        <div>
            <label class="form-label">Pilihan Jawaban</label>
            <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.5rem;">
                @foreach($question->options as $i => $option)
                    <div class="option-review-row {{ $option->is_correct ? 'option-correct' : '' }}">
                        <span class="option-letter {{ $option->is_correct ? 'option-letter--correct' : '' }}">
                            {{ chr(65 + $i) }}
                        </span>
                        <span class="option-text">{{ $option->option_text }}</span>
                        @if($option->is_correct)
                            <span class="option-correct-badge">✔ Benar</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="form-actions" style="margin-top:1.75rem;">
            <a href="{{ route('admin.quizzes.edit', $question->id) }}" class="btn-primary">
                ✏️ Edit Soal
            </a>
            <a href="{{ route('admin.quizzes.index') }}" class="btn-cancel-link">← Kembali</a>
            <form method="POST"
                  action="{{ route('admin.quizzes.destroy', $question->id) }}"
                  onsubmit="return confirm('Hapus soal ini secara permanen?')"
                  style="margin-left:auto;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">🗑 Hapus Soal</button>
            </form>
        </div>

    </div>

</div>
@endsection