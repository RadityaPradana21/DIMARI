@csrf

@if(isset($question))
    @method('PUT')
@endif

{{-- Validasi Error --}}
@if($errors->any())
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:1.25rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Pilih Modul --}}
<div class="form-group">
    <label class="form-label">Modul</label>
    <select name="module_id" class="form-input" required>
        <option value="" disabled {{ old('module_id', $question->module_id ?? '') === '' ? 'selected' : '' }}>
            — Pilih Modul —
        </option>
        @foreach($modules as $module)
            <option value="{{ $module->id }}"
                {{ old('module_id', $question->module_id ?? '') == $module->id ? 'selected' : '' }}>
                {{ $module->title }}
            </option>
        @endforeach
    </select>
</div>

{{-- Teks Pertanyaan --}}
<div class="form-group">
    <label class="form-label">Pertanyaan</label>
    <textarea
        name="question_text"
        class="form-input"
        rows="3"
        placeholder="Tulis pertanyaan quiz di sini..."
        required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
</div>

{{-- Opsi Jawaban --}}
<div class="form-group">
    <label class="form-label">
        Pilihan Jawaban
        <span class="hint">— centang radio untuk jawaban benar</span>
    </label>

    <div id="options-wrapper" style="display:flex; flex-direction:column; gap:0.5rem;">
        @php
            $existingOptions = old('options', isset($question) ? $question->options->pluck('option_text')->toArray() : []);
            $correctOption   = old('correct_option', isset($question) ? $question->options->search(fn($o) => $o->is_correct) : 0);
            // Pastikan minimal 4 baris
            while (count($existingOptions) < 4) {
                $existingOptions[] = '';
            }
        @endphp

        @foreach($existingOptions as $i => $optionText)
            <div class="option-editor-row">
                <input
                    type="radio"
                    name="correct_option"
                    value="{{ $i }}"
                    {{ $correctOption == $i ? 'checked' : '' }}
                    title="Jawaban benar"
                    required>
                <input
                    type="text"
                    name="options[]"
                    class="form-input"
                    placeholder="Opsi {{ chr(65 + $i) }}"
                    value="{{ $optionText }}"
                    required>
                @if($i >= 4)
                    <button type="button"
                        onclick="removeOption(this)"
                        class="btn-delete-option"
                        title="Hapus opsi ini">✕</button>
                @endif
            </div>
        @endforeach
    </div>

    <button type="button" id="add-option-btn" onclick="addOption()" class="btn-add-option">
        + Tambah Opsi
    </button>
</div>

{{-- Aksi Form --}}
<div class="form-actions">
    <button type="submit" class="btn-primary">
        {{ isset($question) ? '💾 Simpan Perubahan' : '✚ Tambah Soal' }}
    </button>
    <a href="{{ route('admin.quizzes.index') }}" class="btn-cancel-link">Batal</a>
</div>

@push('scripts')
<script>
let optionCount = document.querySelectorAll('#options-wrapper .option-editor-row').length;

function addOption() {
    const wrapper = document.getElementById('options-wrapper');
    const index   = optionCount;
    const row     = document.createElement('div');
    row.className = 'option-editor-row';
    row.innerHTML = `
        <input type="radio" name="correct_option" value="${index}" title="Jawaban benar">
        <input type="text" name="options[]" class="form-input"
               placeholder="Opsi ${String.fromCharCode(65 + index)}" required>
        <button type="button" onclick="removeOption(this)"
                class="btn-delete-option" title="Hapus opsi ini">✕</button>
    `;
    wrapper.appendChild(row);
    optionCount++;

    // Batasi maksimum 8 opsi
    if (optionCount >= 8) {
        document.getElementById('add-option-btn').disabled = true;
    }
}

function removeOption(btn) {
    const row = btn.closest('.option-editor-row');
    row.remove();
    optionCount--;

    // Re-index radio values & placeholders
    document.querySelectorAll('#options-wrapper .option-editor-row').forEach((r, i) => {
        const radio = r.querySelector('input[type="radio"]');
        const text  = r.querySelector('input[type="text"]');
        radio.value = i;
        text.placeholder = `Opsi ${String.fromCharCode(65 + i)}`;
    });

    document.getElementById('add-option-btn').disabled = false;
}
</script>
@endpush