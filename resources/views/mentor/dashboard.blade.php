@extends('layouts.app')
@section('title', 'Mentor Panel')

@section('content')
<div class="mentor-container">

    <div class="mentor-header">
        <h2 class="page-title">🎓 Mentor Panel</h2>
        <p class="page-sub">Full CRUD — Modul, Soal, Pengaturan Quiz</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Tab Bar --}}
    <div class="tab-bar">
        <a href="{{ route('mentor.index', ['tab' => 'questions', 'module' => $selectedMod]) }}"
           class="tab-btn {{ $tab === 'questions' ? 'active' : '' }}">❓ Kelola Soal</a>
        <a href="{{ route('mentor.index', ['tab' => 'modules']) }}"
           class="tab-btn {{ $tab === 'modules' ? 'active' : '' }}">📚 Kelola Modul</a>
        <a href="{{ route('mentor.index', ['tab' => 'results']) }}"
           class="tab-btn {{ $tab === 'results' ? 'active' : '' }}">📊 Hasil Quiz</a>
    </div>

    @if($tab === 'results')
    {{-- ===== TAB: HASIL QUIZ ===== --}}
        @php
            if (!function_exists('highlightSearch')) {
                function highlightSearch($text, $query) {
                    if (empty($query)) return e($text);
                    $escaped = preg_quote($query, '/');
                    $escapedText = e($text);
                    return preg_replace('/(' . $escaped . ')/i', '<mark class="highlight-search">$1</mark>', $escapedText);
                }
            }
        @endphp
        
        {{-- Tabel --}}
        <div class="mentor-section">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom: 1rem;">
                <h3 class="section-title" style="margin-bottom:0;">Daftar Hasil Quiz</h3>
                
                <form method="GET" action="{{ route('mentor.index') }}" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                    <input type="hidden" name="tab" value="results">
                    <input type="hidden" name="res_sort" value="{{ request('res_sort', 'newest') }}">
                    
                    {{-- Search Input --}}
                    <input type="search" name="res_q" value="{{ request('res_q') }}" placeholder="Cari user/email..." class="form-input" style="width:180px;">
                    
                    {{-- Module Filter --}}
                    <select name="res_module" class="form-input" style="width:180px;">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ request('res_module') == $mod->id ? 'selected' : '' }}>
                                {{ $mod->title }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn-primary">Cari</button>
                    @if(request('res_q') || request('res_module') || request('res_sort'))
                        <a href="{{ route('mentor.index', ['tab' => 'results']) }}" class="btn-cancel" style="padding: 0.4rem 0.8rem; font-size:0.85rem;">Reset</a>
                    @endif
                </form>
            </div>
            
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem;">
                                    <span>User</span>
                                    @php
                                        $currUserSort = request('res_sort');
                                        $nextUserSort = $currUserSort === 'user_asc' ? 'user_desc' : 'user_asc';
                                        $userLabel = $currUserSort === 'user_desc' ? 'Z -> A' : 'A -> Z';
                                        $userActive = in_array($currUserSort, ['user_asc', 'user_desc']);
                                    @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['res_sort' => $nextUserSort]) }}" class="sort-btn {{ $userActive ? 'active' : '' }}">
                                        {{ $userLabel }}
                                    </a>
                                </div>
                            </th>
                            <th>Modul</th>
                            <th>
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem;">
                                    <span>Skor</span>
                                    @php
                                        $currScoreSort = request('res_sort');
                                        $nextScoreSort = $currScoreSort === 'score_asc' ? 'score_desc' : 'score_asc';
                                        $scoreLabel = $currScoreSort === 'score_asc' ? '0 -> 100' : '100 -> 0';
                                        $scoreActive = in_array($currScoreSort, ['score_asc', 'score_desc']);
                                    @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['res_sort' => $nextScoreSort]) }}" class="sort-btn {{ $scoreActive ? 'active' : '' }}">
                                        {{ $scoreLabel }}
                                    </a>
                                </div>
                            </th>
                            <th>Pekan</th>
                            <th>
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem;">
                                    <span>Dikerjakan</span>
                                    @php
                                        $currDateSort = request('res_sort');
                                        $nextDateSort = $currDateSort === 'oldest' ? 'newest' : 'oldest';
                                        $dateLabel = $currDateSort === 'oldest' ? 'terlama -> terbaru' : 'terbaru -> terlama';
                                        $dateActive = in_array($currDateSort, ['newest', 'oldest']) || empty($currDateSort);
                                    @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['res_sort' => $nextDateSort]) }}" class="sort-btn {{ $dateActive ? 'active' : '' }}">
                                        {{ $dateLabel }}
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizResults as $result)
                            @php
                                $sc = $result->score >= 90 ? 'good'
                                    : ($result->score >= 70 ? 'perfect' : 'low');
                            @endphp
                            <tr>
                                <td>
                                    <strong>{!! highlightSearch($result->user->name ?? $result->user->username ?? '-', request('res_q')) !!}</strong><br>
                                    <small class="text-muted">{!! highlightSearch($result->user->email ?? '-', request('res_q')) !!}</small>
                                </td>
                                <td>{{ $result->module->title ?? 'Modul '.$result->module_id }}</td>
                                <td><span class="score-badge {{ $sc }}">{{ $result->score }}</span></td>
                                <td class="text-muted" style="line-height: 1.3;">
                                    @php
                                        $wStart = \Carbon\Carbon::parse($result->week_start);
                                        $wEnd = $wStart->copy()->addDays(6);
                                    @endphp
                                    {!! $wStart->format('j M Y') . ' - <br>' . $wEnd->format('j M Y') !!}
                                </td>
                                <td class="text-muted">{{ $result->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty-state">Belum ada hasil quiz atau data tidak ditemukan.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:1rem;">
                {{ $quizResults->links() }}
            </div>
        </div>

    @elseif($tab === 'modules')
    {{-- ===== TAB: KELOLA MODUL ===== --}}

        {{-- Form Tambah / Edit Modul (inline, seperti edit quiz) --}}
        <div class="mentor-section">
            <h3 class="section-title" id="modFormTitle">
                {{ $editMod ? '✏️ Edit Modul #' . $editMod->id : '+ Tambah Modul Baru' }}
            </h3>

            <form method="POST"
                  action="{{ $editMod ? route('mentor.modules.update', $editMod->id) : route('mentor.modules.store') }}">
                @csrf
                @if($editMod) @method('PUT') @endif

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Judul Modul</label>
                        <input type="text" name="title" class="form-input"
                               placeholder="Contoh: Pengantar Digital Marketing"
                               value="{{ old('title', $editMod->title ?? '') }}" required>
                        @error('title')
                            <div class="alert alert-error mt-xs">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi <span class="hint">(opsional)</span></label>
                        <input type="text" name="description" class="form-input"
                               placeholder="Deskripsi singkat modul"
                               value="{{ old('description', $editMod->description ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Judul Video (opsional)</label>
                        <input type="text" name="video_title" class="form-input"
                               placeholder="Judul singkat video"
                               value="{{ old('video_title', $editMod->video_title ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Link Video YouTube (opsional)</label>
                        <input type="url" name="video_url" class="form-input"
                               placeholder="https://www.youtube.com/watch?v=..."
                               value="{{ old('video_url', $editMod->video_url ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Video (opsional)</label>
                        <input type="text" name="video_description" class="form-input"
                               placeholder="Ringkasan singkat tentang isi video"
                               value="{{ old('video_description', $editMod->video_description ?? '') }}">
                    </div>
                </div>

                {{-- WYSIWYG Editor Konten Materi --}}
                <div class="form-group">
                    <label class="form-label">
                        Konten Materi
                        <span class="hint">Ketik langsung — format otomatis tampil, disimpan sebagai teks</span>
                    </label>
                    <div class="wysiwyg-toolbar">
                        <button type="button" class="tb-btn" onclick="wfmt('bold')"><strong>B</strong></button>
                        <button type="button" class="tb-btn" onclick="wfmt('italic')"><em>I</em></button>
                        <button type="button" class="tb-btn" onclick="wfmt('underline')"><u>U</u></button>
                        <div class="tb-sep"></div>
                        <button type="button" class="tb-btn" onclick="wblock('h2')">H2</button>
                        <button type="button" class="tb-btn" onclick="wblock('h3')">H3</button>
                        <button type="button" class="tb-btn" onclick="wblock('h4')">H4</button>
                        <button type="button" class="tb-btn" onclick="wblock('p')">P</button>
                        <div class="tb-sep"></div>
                        <button type="button" class="tb-btn" onclick="wfmt('insertUnorderedList')">• List</button>
                        <button type="button" class="tb-btn" onclick="wfmt('insertOrderedList')">1. List</button>
                        <div class="tb-sep"></div>
                        <button type="button" class="tb-btn" onclick="wclearFormat()">✕ fmt</button>
                    </div>
                    @php
                        $existingContent = old('content', $editMod->content ?? '');
                        // Konversi plain text (penanda) → HTML agar tampil bagus di editor
                        $editorHtml = \App\Helpers\ContentRenderer::toHtml($existingContent);
                    @endphp
                    <div id="modEditor"
                         class="wysiwyg-editor module-body"
                         contenteditable="true"
                         data-placeholder="Ketik konten materi di sini..."
                         oninput="syncEditor('modEditor','modContent')">
                        {!! $editorHtml !!}
                    </div>
                    <textarea id="modContent" name="content" style="display:none;">{{ $existingContent }}</textarea>
                </div>

                <div class="form-actions">
                    @if($editMod)
                        <a href="{{ route('mentor.index', ['tab' => 'modules']) }}" class="btn-cancel">Batal</a>
                    @endif
                    <button type="submit" class="btn-primary">
                        {{ $editMod ? '💾 Simpan Perubahan' : '+ Tambah Modul' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar Modul --}}
        <div class="mentor-section">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="section-title">Daftar Modul ({{ $modules->count() }})</h3>
                <form method="GET" action="{{ route('mentor.index') }}" style="display:flex; gap:0.5rem; align-items:center;">
                    <input type="hidden" name="tab" value="modules">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari modul..." class="form-input">
                    <select name="per_page" class="form-input" style="width:90px;">
                        <option value="10">10</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                    </select>
                    <button class="btn-primary">Cari</button>
                </form>
            </div>
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $mod)
                            <tr {{ ($editMod && $editMod->id === $mod->id) ? 'style=background:rgba(139,92,246,.08)' : '' }}>
                                <td>{{ $mod->id }}</td>
                                <td>{{ $mod->title }}</td>
                                <td class="text-muted">{{ $mod->description ?? '—' }}</td>
                                <td class="action-cell">
                                    <a href="{{ route('mentor.index', ['tab' => 'modules', 'edit_mod' => $mod->id]) }}"
                                       class="btn-edit {{ ($editMod && $editMod->id === $mod->id) ? 'active' : '' }}">
                                        ✏️ Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('mentor.modules.destroy', $mod->id) }}"
                                          style="display:inline"
                                          onsubmit="return dimariConfirm(event, 'Hapus modul <strong>{{ addslashes($mod->title) }}</strong>?<br><small>Semua soal terkait juga akan dihapus.</small>')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">Belum ada modul.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
    {{-- ===== TAB: KELOLA SOAL ===== --}}

        {{-- Module Picker --}}
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
            <div class="module-picker">
            @foreach($modules as $mod)
                <a href="{{ route('mentor.index', ['tab' => 'questions', 'module' => $mod->id]) }}"
                   class="mod-tab {{ $mod->id === $selectedMod ? 'active' : '' }}">
                    M{{ $mod->id }}: {{ $mod->title }}
                </a>
            @endforeach
            </div>

            <form method="GET" action="{{ route('mentor.index') }}" style="display:flex; gap:0.5rem; align-items:center;">
                <input type="hidden" name="tab" value="questions">
                <input type="hidden" name="module" value="{{ $selectedMod }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari soal atau opsi..." class="form-input">
                <select name="per_page" class="form-input" style="width:90px;">
                    <option value="10">10</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                </select>
                <button class="btn-primary">Cari</button>
            </form>
        </div>

        {{-- Form Tambah / Edit Soal --}}
        <div class="mentor-section">
            <h3 class="section-title">
                {{ $editQ ? '✏️ Edit Soal #' . $editQ->id : '+ Tambah Soal Baru' }}
            </h3>

            <form method="POST"
                  action="{{ $editQ
                      ? route('mentor.quizzes.update', $editQ->id)
                      : route('mentor.quizzes.store') }}">
                @csrf
                @if($editQ)
                    @method('PUT')
                @else
                    <input type="hidden" name="module_id" value="{{ $selectedMod }}">
                @endif

                <div class="form-group">
                    <label class="form-label">Pertanyaan</label>
                    <textarea name="question_text" class="form-input" rows="3" required
                              placeholder="Tulis pertanyaan di sini...">{{ old('question_text', $editQ->question_text ?? '') }}</textarea>
                    @error('question_text')
                        <div class="alert alert-error mt-xs">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Pilihan Jawaban
                        <span class="hint">(centang radio = jawaban benar, radio hijau = benar)</span>
                    </label>

                    @php
                        $existingOpts = $editQ
                            ? $editQ->options->toArray()
                            : [['option_text'=>'','is_correct'=>0],['option_text'=>'','is_correct'=>0],['option_text'=>'','is_correct'=>0],['option_text'=>'','is_correct'=>0]];
                        $correctIdx = 0;
                        foreach ($existingOpts as $i => $eo) {
                            if ($eo['is_correct']) { $correctIdx = $i; break; }
                        }
                    @endphp

                    @foreach($existingOpts as $i => $eo)
                        <div class="option-editor-row">
                            <input type="radio" name="correct_option" value="{{ $i }}"
                                   class="radio-correct"
                                   {{ $i === $correctIdx ? 'checked' : '' }}
                                   title="Jawaban benar">
                            <input type="text" name="options[]" class="form-input"
                                   value="{{ old('options.' . $i, $eo['option_text']) }}"
                                   placeholder="Opsi {{ chr(65 + $i) }}"
                                   required>
                        </div>
                    @endforeach

                    @error('options')
                        <div class="alert alert-error mt-xs">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    @if($editQ)
                        <a href="{{ route('mentor.index', ['tab' => 'questions', 'module' => $editQ->module_id]) }}"
                           class="btn-cancel">Batal</a>
                    @endif
                    <button type="submit" class="btn-primary">
                        {{ $editQ ? '💾 Simpan Perubahan' : '➕ Tambah Soal' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar Soal --}}
        <div class="mentor-section">
            <h3 class="section-title">
                Soal Modul {{ $selectedMod }} ({{ $questions->total() }} soal)
            </h3>

            @if($questions->isEmpty())
                <div class="empty-state">Belum ada soal untuk modul ini.</div>
            @else
                <div class="questions-list">
                    @foreach($questions as $q)
                        <div class="q-card">
                            <div class="q-card-header">
                                <span class="q-num">Soal {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}</span>
                                <div class="q-actions">
                                    <a href="{{ route('mentor.index', ['tab' => 'questions', 'module' => $selectedMod, 'edit_q' => $q->id]) }}"
                                       class="btn-edit">✏️ Edit</a>
                                    <form method="POST"
                                          action="{{ route('mentor.quizzes.destroy', $q->id) }}"
                                          style="display:inline"
                                          onsubmit="return dimariConfirm(event, 'Hapus soal ini?<br><small>Tindakan ini tidak bisa dibatalkan.</small>')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑</button>
                                    </form>
                                </div>
                            </div>
                            <div class="q-text">{{ $q->question_text }}</div>
                            <div class="options-grid-2 preview">
                                @foreach($q->options as $opt)
                                    <div class="preview-opt {{ $opt->is_correct ? 'correct' : '' }}">
                                        {{ $opt->is_correct ? '✅ ' : '' }}{{ $opt->option_text }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
                <div style="margin-top:1rem;">{{ $questions->links() }}</div>
        </div>

    @endif

    {{-- Confirm Modal (app-native) --}}
    <div id="dimariConfirmModal" class="modal-overlay hidden">
        <div class="modal-box" style="max-width:420px;">
            <div class="modal-icon">⚠️</div>
            <h3 class="modal-title">Konfirmasi Hapus</h3>
            <p class="modal-desc" id="confirmMsg"></p>
            <div class="modal-actions">
                <button onclick="cancelDimariConfirm()" class="btn-cancel">Batal</button>
                <button onclick="executeDimariConfirm()" class="btn-confirm" style="background:var(--error);">Ya, Hapus</button>
            </div>
        </div>
    </div>

</div>

<style>
/* Radio button hijau untuk jawaban benar */
.radio-correct { accent-color: #22c55e; width: 18px; height: 18px; cursor: pointer; }
.option-editor-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
.option-editor-row .form-input { flex: 1; }

/* WYSIWYG Toolbar */
.wysiwyg-toolbar {
    display: flex; flex-wrap: wrap; gap: 0.2rem; align-items: center;
    padding: 0.4rem 0.6rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-bottom: none;
    border-radius: 6px 6px 0 0;
}
.tb-sep { width: 1px; height: 18px; background: rgba(255,255,255,0.15); margin: 0 .2rem; }
.tb-btn {
    padding: 0.25rem 0.5rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 4px;
    color: var(--text-light, #e5e7eb);
    cursor: pointer; font-size: 0.78rem; transition: all .15s; font-family: inherit;
    line-height: 1.4;
}
.tb-btn:hover, .tb-btn:active { background: rgba(139,92,246,0.3); border-color: var(--primary, #8b5cf6); }

/* WYSIWYG Editor (contenteditable) */
.wysiwyg-editor {
    min-height: 200px;
    padding: 1rem 1.25rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0 0 8px 8px;
    color: var(--text-light, #e5e7eb);
    font-family: inherit;
    font-size: 0.9rem;
    line-height: 1.7;
    outline: none;
    cursor: text;
}
.wysiwyg-editor:focus {
    border-color: rgba(139,92,246,0.5);
    background: rgba(255,255,255,0.06);
}
.wysiwyg-editor.empty::before {
    content: attr(data-placeholder);
    color: var(--text-muted, #6b7280);
    pointer-events: none;
    font-style: italic;
}
/* Inline WYSIWYG content styles */
.wysiwyg-editor h2 { color: var(--primary, #8b5cf6); font-size: 1.2rem; margin: 1rem 0 .5rem; border-bottom: 1px solid rgba(255,255,255,.1); padding-bottom: .3rem; }
.wysiwyg-editor h3 { color: var(--secondary, #a78bfa); font-size: 1.05rem; margin: .9rem 0 .4rem; }
.wysiwyg-editor h4 { color: var(--secondary, #a78bfa); font-size: .95rem; margin: .7rem 0 .35rem; }
.wysiwyg-editor p  { margin-bottom: .7rem; }
.wysiwyg-editor ul, .wysiwyg-editor ol { margin: .4rem 0 .8rem 1.4rem; }
.wysiwyg-editor li { margin-bottom: .3rem; }
.wysiwyg-editor blockquote { border-left: 3px solid var(--primary); padding: .5rem 1rem; margin: .8rem 0; background: rgba(255,255,255,.03); border-radius: 0 6px 6px 0; }
.wysiwyg-editor a { color: var(--primary); text-decoration: underline; }
.wysiwyg-editor hr { border: none; border-top: 1px solid rgba(255,255,255,.15); margin: 1rem 0; }

/* Custom styling for Dropdown / Select List */
select.form-input {
    background-color: #121225;
    color: #e5e7eb;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-sm);
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    font-family: inherit;
    cursor: pointer;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

select.form-input option {
    background-color: #121225;
    color: #e5e7eb;
    padding: 0.5rem;
}

select.form-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
}

/* Highlight Search Result */
.highlight-search {
    background-color: rgba(0, 204, 255, 0.25);
    color: #00f0ff;
    padding: 0 3px;
    border-radius: 3px;
    font-weight: 700;
}

/* Sorting Buttons in Table Headers */
.sort-btn {
    display: inline-flex;
    align-items: center;
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 4px;
    padding: 0.25rem 0.5rem;
    color: var(--text-muted);
    font-size: 0.72rem;
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 600;
    font-family: inherit;
}

.sort-btn:hover {
    background: rgba(139, 92, 246, 0.25);
    border-color: var(--primary);
    color: #ffffff;
}

.sort-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #ffffff;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── WYSIWYG Editor ────────────────────────────────────
// activeEditor: tracks which contenteditable is focused
let activeWysiwygEl = null;

document.querySelectorAll('.wysiwyg-editor').forEach(el => {
    el.addEventListener('focus', () => { activeWysiwygEl = el; });
    el.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            e.preventDefault();
            document.execCommand('insertHTML', false, '&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });
    // Placeholder
    const placeholder = el.dataset.placeholder;
    if (placeholder) {
        if (!el.innerHTML.trim()) el.classList.add('empty');
        el.addEventListener('input', () => {
            el.classList.toggle('empty', !el.innerHTML.trim() || el.innerHTML === '<br>');
        });
        el.addEventListener('focus', () => el.classList.remove('empty'));
        el.addEventListener('blur',  () => {
            el.classList.toggle('empty', !el.innerHTML.trim() || el.innerHTML === '<br>');
        });
    }
});

function wfmt(cmd) {
    if (activeWysiwygEl) activeWysiwygEl.focus();
    document.execCommand(cmd, false, null);
}

function wblock(tag) {
    if (activeWysiwygEl) activeWysiwygEl.focus();
    document.execCommand('formatBlock', false, tag);
}

function winsertHR() {
    if (activeWysiwygEl) activeWysiwygEl.focus();
    document.execCommand('insertHorizontalRule', false, null);
}

function wclearFormat() {
    if (activeWysiwygEl) activeWysiwygEl.focus();
    document.execCommand('removeFormat', false, null);
    document.execCommand('formatBlock', false, 'p');
}

// Sync contenteditable → hidden textarea before submit
function syncEditor(editorId, textareaId) {
    const editor   = document.getElementById(editorId);
    const textarea = document.getElementById(textareaId);
    if (editor && textarea) textarea.value = editor.innerHTML;
}

// Sync on form submit
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        document.querySelectorAll('.wysiwyg-editor').forEach(editor => {
            const ta = document.getElementById(editor.id.replace('Editor','Content'));
            if (ta) ta.value = editor.innerHTML;
        });
    });
});

// ── Escape untuk tutup confirm modal ──────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cancelDimariConfirm();
});

// Scroll ke form saat halaman load dengan edit_mod
@if($editMod)
    window.addEventListener('load', () => {
        const form = document.getElementById('modFormTitle');
        if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
@endif

// ── Confirm Modal (native app) ────────────────────────
let pendingForm = null;
function dimariConfirm(e, msg) {
    e.preventDefault();
    pendingForm = e.target.closest('form');
    document.getElementById('confirmMsg').innerHTML = msg;
    document.getElementById('dimariConfirmModal').classList.remove('hidden');
    return false;
}
function cancelDimariConfirm() {
    document.getElementById('dimariConfirmModal').classList.add('hidden');
    pendingForm = null;
}
function executeDimariConfirm() {
    document.getElementById('dimariConfirmModal').classList.add('hidden');
    if (pendingForm) pendingForm.submit();
}


</script>
@endpush
