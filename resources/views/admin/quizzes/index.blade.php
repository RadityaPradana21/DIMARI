@extends('layouts.app')
@section('title', 'Manajemen Quiz')

@section('content')
<div class="admin-container">

    <div class="admin-header">
        <h1 class="page-title">🧩 Manajemen Quiz</h1>
        <p class="page-sub">Kelola seluruh soal quiz pada setiap modul platform DIMARI.</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Action Bar with search and filter --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; gap:1rem;">
        <div style="display:flex; gap:0.5rem; align-items:center;">
            <form method="GET" action="{{ route('admin.quizzes.index') }}" style="display:flex; gap:0.5rem; align-items:center;">
                <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari soal, opsi, atau judul modul..." class="form-input">
                <select name="module" class="form-input">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}" {{ (int)request('module') === $m->id ? 'selected' : '' }}>{{ $m->title }}</option>
                    @endforeach
                </select>
                <select name="per_page" class="form-input" style="width:90px;">
                    <option value="10">10</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                </select>
                <button class="btn-primary">Cari</button>
            </form>
            <div class="text-muted" style="margin-left:0.5rem;">{{ $questions->total() ?? $questions->count() }} soal</div>
        </div>

        <div style="display:flex; gap:0.5rem; align-items:center;">
            <a href="{{ route('admin.quizzes.create') }}" class="btn-primary">✚ Tambah Soal</a>
            <span class="text-muted" style="font-size:0.8rem;">📌 Pengelolaan soal dilakukan oleh Mentor</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Modul</th>
                    <th>Pertanyaan</th>
                    <th>Jml. Opsi</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                    <tr>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:0.8rem;">
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            <span class="role-badge role-mentor" style="font-size:0.7rem;">
                                {{ $q->module->title ?? '—' }}
                            </span>
                        </td>
                        <td style="max-width:380px;">
                            {{ Str::limit($q->question_text, 90) }}
                        </td>
                        <td style="text-align:center;">
                            <span class="score-badge {{ $q->options->count() >= 4 ? 'good' : 'low' }}">
                                {{ $q->options->count() }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:0.85rem;">
                            {{ $q->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-cell">
                                <a href="{{ route('admin.quizzes.show', $q->id) }}" class="btn-edit">👁 Lihat</a>
                                <a href="{{ route('admin.quizzes.edit', $q->id) }}" class="btn-edit">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.quizzes.destroy', $q->id) }}" onsubmit="return confirm('Hapus soal ini?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑 Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                Belum ada soal quiz. <a href="{{ route('admin.quizzes.create') }}">Tambah sekarang</a>.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">{{ $questions->links() }}</div>

</div>
@endsection