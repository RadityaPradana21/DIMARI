@extends('layouts.app')
@section('title', 'Profile Saya')

@section('content')
<div class="profile-container">

    <div class="profile-header">
        <h2 class="page-title">👤 Profile Saya</h2>
        <p class="page-sub">Kelola informasi profil dan preferensi kamu</p>
    </div>

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success">✅ Profile berhasil diperbarui!</div>
    @elseif(session('status') === 'password-updated')
        <div class="alert alert-success">✅ Password berhasil diubah!</div>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $err)
            <div class="alert alert-error">{{ $err }}</div>
        @endforeach
    @endif

    <div class="profile-content">

        {{-- Profile Overview --}}
        <div class="profile-overview">
            <div class="profile-avatar-section">
                <div class="current-avatar">
                    <img src="{{ $user->avatar }}" alt="Profile Avatar"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->username) }}&background=8b5cf6&color=fff&bold=true&size=128'">
                </div>
                <div class="avatar-info">
                    <h3 class="username">{{ $user->username }}</h3>
                    <span class="role-badge role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                    <p style="font-size:.85rem; color:var(--text-muted); margin-top:.3rem;">{{ $user->email }}</p>
                </div>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $unlockedCount }}</div>
                    <div class="stat-label">Achievements</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $totalCompleted }}</div>
                    <div class="stat-label">Modules</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $avgScore }}</div>
                    <div class="stat-label">Avg Score</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $weekScore }}</div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="edit-profile">📝 Edit Profile</button>
            <button class="tab-btn" data-tab="avatar-settings">🖼️ Avatar</button>
            <button class="tab-btn" data-tab="account-settings">⚙️ Account</button>
        </div>

        {{-- Tab: Edit Profile --}}
        <div class="tab-content active" id="edit-profile">
            <div class="profile-section">
                <h3 class="section-title">📝 Informasi Profil</h3>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-input" value="{{ $user->username }}" disabled>
                            <small class="form-hint">Username tidak dapat diubah</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                            <small class="form-hint">Email tidak dapat diubah</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-input"
                               value="{{ old('full_name', $user->full_name) }}"
                               placeholder="Nama lengkapmu" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="tel" name="phone_number" class="form-input"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               placeholder="+62 812-3456-7890">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tab: Avatar --}}
        <div class="tab-content" id="avatar-settings">
            <div class="profile-section">
                <h3 class="section-title">🖼️ Pengaturan Avatar</h3>

                {{-- Pilih Avatar Default --}}
                <div class="avatar-selection">
                    <h4 class="subsection-title">Pilih Avatar Default</h4>
                    <form method="POST" action="{{ route('profile.avatar') }}">
                        @csrf
                        <div class="avatar-grid">
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $isSelected = !$user->is_using_custom_avatar
                                        && str_contains($user->avatar_url ?? '', "avatar_{$i}.svg");
                                @endphp
                                <div class="avatar-option">
                                    <input type="radio" name="avatar_choice" value="{{ $i }}"
                                           id="avatar_{{ $i }}" {{ $isSelected ? 'checked' : '' }}>
                                    <label for="avatar_{{ $i }}" class="avatar-label">
                                        <img src="{{ asset('avatars/default/avatar_' . $i . '.svg') }}"
                                             alt="Avatar {{ $i }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                                        <span style="display:none; font-size:2rem;">{{ ['😊','🦊','🐱','🐶','🦋','🐸','🦁','🐼','🦅','🐉'][$i-1] }}</span>
                                        <span class="avatar-number">{{ $i }}</span>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">🖼️ Gunakan Avatar</button>
                        </div>
                    </form>
                </div>

                {{-- Upload Custom --}}
                <div class="avatar-upload" style="margin-top:2rem; padding-top:2rem; border-top:1px solid var(--border);">
                    <h4 class="subsection-title">Upload Avatar Custom</h4>
                    <form method="POST" action="{{ route('profile.avatar.upload') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="custom_avatar" id="customAvatar"
                                   accept="image/*" class="file-input">
                            <label for="customAvatar" class="upload-label">
                                <div class="upload-icon">📤</div>
                                <div class="upload-title" id="uploadTitle">Klik atau drag file ke sini</div>
                                <div class="upload-subtitle">JPG, PNG, GIF, WebP (max 2MB)</div>
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">📤 Upload Avatar</button>
                            @if($user->is_using_custom_avatar)
                                <form method="POST" action="{{ route('profile.avatar.reset') }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-cancel">🔄 Reset ke Default</button>
                                </form>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tab: Account --}}
        <div class="tab-content" id="account-settings">
            <div class="profile-section">
                <h3 class="section-title">⚙️ Pengaturan Akun</h3>

                <div class="settings-grid">
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4 class="setting-title">🔐 Ubah Password</h4>
                            <p class="setting-desc">Perbarui password untuk keamanan akun kamu</p>
                        </div>
                        <button class="btn-edit"
                                onclick="document.getElementById('passwordModal').classList.remove('hidden')">
                            Ubah
                        </button>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h4 class="setting-title">📋 Riwayat Quiz</h4>
                            <p class="setting-desc">{{ $quizHistory->count() }} quiz telah dikerjakan</p>
                        </div>
                        <button class="btn-edit" onclick="toggleQuizHistory()">Lihat</button>
                    </div>
                </div>

                {{-- Quiz History --}}
                <div id="quizHistoryPanel" style="display:none; margin-top:1.5rem;">
                    <h4 class="subsection-title">📋 Riwayat Quiz Terakhir</h4>
                    @if($quizHistory->isEmpty())
                        <div class="empty-state">Belum ada quiz yang dikerjakan.</div>
                    @else
                        <div class="quiz-history-list">
                            @foreach($quizHistory as $result)
                                <div class="qh-item">
                                    <div class="qh-module">{{ $result->module->title ?? 'Modul #'.$result->module_id }}</div>
                                    <div class="qh-score {{ $result->score >= 80 ? 'score-high' : ($result->score >= 60 ? 'score-mid' : 'score-low') }}">
                                        {{ $result->score }}<span>/100</span>
                                    </div>
                                    <div class="qh-date">{{ $result->created_at->format('d M Y') }}</div>
                                    <a href="{{ route('quiz', ['module' => $result->module_id, 'review' => 1]) }}"
                                       class="btn-edit" style="font-size:.78rem;">Review</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /profile-content --}}
</div>{{-- /profile-container --}}

{{-- Password Modal --}}
<div id="passwordModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h3 class="modal-title">🔐 Ubah Password</h3>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Password Lama</label>
                <input type="password" name="current_password" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-input" minlength="6" required>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-input" minlength="6" required>
            </div>
            <div class="modal-actions">
                <button type="button"
                        onclick="document.getElementById('passwordModal').classList.add('hidden')"
                        class="btn-cancel">Batal</button>
                <button type="submit" class="btn-primary">🔐 Ubah Password</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.profile-tabs .tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.profile-tabs .tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

// Quiz history toggle
function toggleQuizHistory() {
    const p = document.getElementById('quizHistoryPanel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
}

// Upload file name preview
document.getElementById('customAvatar')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('uploadTitle').textContent = file.name;
    }
});

// Close modal on backdrop / Escape
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) e.target.classList.add('hidden');
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.add('hidden'));
    }
});

// Buka tab sesuai flash status
@if(session('status'))
    document.querySelectorAll('.profile-tabs .tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelector('[data-tab="edit-profile"]').classList.add('active');
    document.getElementById('edit-profile').classList.add('active');
@endif
</script>
@endpush
