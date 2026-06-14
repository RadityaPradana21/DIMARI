@csrf

@if(isset($module))
@method('PUT')
@endif

<div class="glass-card p-6">

    {{-- Judul Modul --}}
    <div class="form-group">
        <label class="form-label" for="title">
            Judul Modul <span style="color:var(--error)">*</span>
        </label>
        <input type="text" id="title" name="title"
            class="form-input {{ $errors->has('title') ? 'input-error' : '' }}"
            value="{{ old('title', $module->title ?? '') }}"
            placeholder="Contoh: Social Media Marketing"
            required maxlength="200">
        @error('title')
        <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- Deskripsi --}}
    <div class="form-group">
        <label class="form-label" for="description">Deskripsi Singkat</label>
        <textarea id="description" name="description"
            class="form-input {{ $errors->has('description') ? 'input-error' : '' }}"
            rows="2"
            placeholder="Deskripsi singkat tentang isi modul ini...">{{ old('description', $module->description ?? '') }}</textarea>
        @error('description')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- Video fields --}}
    <div class="form-group">
        <label class="form-label" for="video_title">Judul Video (opsional)</label>
        <input id="video_title" name="video_title" type="text"
               class="form-input {{ $errors->has('video_title') ? 'input-error' : '' }}"
               value="{{ old('video_title', $module->video_title ?? '') }}"
               placeholder="Contoh: Pengantar Modul — Video" maxlength="200">
        @error('video_title') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="video_url">Link Video YouTube (opsional)</label>
        <input id="video_url" name="video_url" type="url"
               class="form-input {{ $errors->has('video_url') ? 'input-error' : '' }}"
               value="{{ old('video_url', $module->video_url ?? '') }}"
               placeholder="https://www.youtube.com/watch?v=...">
        @error('video_url') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="video_description">Deskripsi Video (opsional)</label>
        <textarea id="video_description" name="video_description" rows="2"
            class="form-input {{ $errors->has('video_description') ? 'input-error' : '' }}"
            placeholder="Ringkasan singkat tentang isi video...">{{ old('video_description', $module->video_description ?? '') }}</textarea>
        @error('video_description') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    {{-- Konten Materi dengan Rich Editor --}}
    <div class="form-group">
        <label class="form-label">
            Konten Materi
            <span class="hint">Gunakan toolbar untuk memformat konten</span>
        </label>

        {{-- Rich Editor Toolbar --}}
        <div class="rich-toolbar" id="richToolbar">
            <div class="toolbar-group">
                <button type="button" class="tb-btn" onclick="insertBlock('h2')" title="Judul Utama">H2</button>
                <button type="button" class="tb-btn" onclick="insertBlock('h3')" title="Sub Judul">H3</button>
                <button type="button" class="tb-btn" onclick="insertBlock('h4')" title="Sub Sub Judul">H4</button>
            </div>
            <div class="toolbar-group">
                <button type="button" class="tb-btn" onclick="wrapSelection('strong')" title="Bold"><strong>B</strong></button>
                <button type="button" class="tb-btn" onclick="wrapSelection('em')" title="Italic"><em>I</em></button>
                <button type="button" class="tb-btn" onclick="wrapSelection('mark')" title="Highlight">🖊</button>
            </div>
            <div class="toolbar-group">
                <button type="button" class="tb-btn" onclick="insertBlock('p')" title="Paragraf">¶</button>
                <button type="button" class="tb-btn" onclick="insertList('ul')" title="List Bullet">• List</button>
                <button type="button" class="tb-btn" onclick="insertList('ol')" title="List Nomor">1. List</button>
                <button type="button" class="tb-btn" onclick="insertBlock('blockquote')" title="Kutipan">❝</button>
            </div>
            <div class="toolbar-group">
                <button type="button" class="tb-btn tb-preview" onclick="togglePreview()" title="Toggle Preview">👁 Preview</button>
            </div>
        </div>

        {{-- Editor Area --}}
        <div id="editorWrap" style="position:relative;">
            <textarea id="content" name="content"
                class="form-input rich-editor {{ $errors->has('content') ? 'input-error' : '' }}"
                rows="14"
                placeholder="Tulis konten materi di sini...&#10;&#10;Tips: gunakan toolbar di atas untuk memformat teks.&#10;Contoh HTML yang didukung:&#10;  <h3>Sub Judul</h3>&#10;  <p>Isi paragraf...</p>&#10;  <ul><li>Poin penting</li></ul>">{{ old('content', $module->content ?? '') }}</textarea>

            {{-- Preview Panel --}}
            <div id="previewPanel" class="rich-preview hidden">
                <div class="preview-header">
                    <span>👁 Preview Konten</span>
                    <button type="button" onclick="togglePreview()" class="preview-close">✕</button>
                </div>
                <div id="previewBody" class="module-body preview-content"></div>
            </div>
        </div>

        @error('content')
            <div class="field-error">{{ $message }}</div>
        @enderror
        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.35rem;">
            💡 Mendukung HTML: &lt;h2&gt; &lt;h3&gt; &lt;h4&gt; &lt;p&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;strong&gt; &lt;em&gt; &lt;blockquote&gt; &lt;mark&gt;
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-4">
        <div class="rounded p-3 bg-red-500/10 border border-red-500/20">
            @foreach($errors->all() as $error)
            <div class="text-sm text-red-400">{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tombol --}}
    <div class="flex gap-3">
        <button type="submit" class="btn-primary flex-1">
            {{ isset($module) ? 'UPDATE MODUL →' : 'SIMPAN MODUL →' }}
        </button>
        <a href="{{ route('admin.index') }}" class="btn-secondary">Batal</a>
    </div>

</div>

<style>
.rich-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    padding: 0.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-bottom: none;
    border-radius: 8px 8px 0 0;
}
.toolbar-group {
    display: flex;
    gap: 0.2rem;
    padding-right: 0.5rem;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.toolbar-group:last-child { border-right: none; }
.tb-btn {
    padding: 0.3rem 0.6rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 4px;
    color: var(--text-light, #e5e7eb);
    cursor: pointer;
    font-size: 0.8rem;
    transition: all .15s;
    font-family: inherit;
}
.tb-btn:hover { background: rgba(139,92,246,0.3); border-color: var(--primary, #8b5cf6); }
.tb-preview { margin-left: auto; }
.rich-editor {
    border-radius: 0 0 8px 8px !important;
    font-family: 'Space Mono', monospace;
    font-size: 0.85rem;
    line-height: 1.6;
}
.rich-preview {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: var(--bg-card, #1a1a2e);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 0 0 8px 8px;
    overflow: auto;
    z-index: 10;
}
.rich-preview.hidden { display: none; }
.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    font-size: 0.8rem;
    color: var(--text-muted);
    position: sticky;
    top: 0;
    background: var(--bg-card, #1a1a2e);
}
.preview-close {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 1rem;
}
.preview-content {
    padding: 1rem 1.25rem;
}
</style>

@push('scripts')
<script>
const contentArea = document.getElementById('content');

function insertBlock(tag) {
    const templates = {
        h2: '<h2>Judul Utama</h2>\n',
        h3: '<h3>Sub Judul</h3>\n',
        h4: '<h4>Sub Sub Judul</h4>\n',
        p:  '<p>Isi paragraf di sini.</p>\n',
        blockquote: '<blockquote>Kutipan penting...</blockquote>\n',
    };
    const tpl = templates[tag] || '';
    insertAtCursor(contentArea, tpl);
}

function insertList(type) {
    const tpl = type === 'ul'
        ? '<ul>\n  <li>Item pertama</li>\n  <li>Item kedua</li>\n</ul>\n'
        : '<ol>\n  <li>Langkah pertama</li>\n  <li>Langkah kedua</li>\n</ol>\n';
    insertAtCursor(contentArea, tpl);
}

function wrapSelection(tag) {
    const start = contentArea.selectionStart;
    const end   = contentArea.selectionEnd;
    const sel   = contentArea.value.substring(start, end) || 'teks';
    const wrapped = `<${tag}>${sel}</${tag}>`;
    contentArea.setRangeText(wrapped, start, end, 'end');
    contentArea.focus();
}

function insertAtCursor(el, text) {
    const start = el.selectionStart;
    const end   = el.selectionEnd;
    el.setRangeText(text, start, end, 'end');
    el.focus();
}

function togglePreview() {
    const panel = document.getElementById('previewPanel');
    const body  = document.getElementById('previewBody');
    if (panel.classList.toggle('hidden')) return;
    body.innerHTML = contentArea.value || '<p class="text-muted">Konten kosong.</p>';
}
</script>
@endpush
