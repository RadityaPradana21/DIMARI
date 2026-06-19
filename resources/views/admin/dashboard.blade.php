@extends('layouts.app')
@section('title', 'Admin Panel')

@section('content')
@php
$moduleScores = $quizResults->groupBy('module_id')->map(fn($g) => round($g->avg('score')));
$moduleLabels = $modules->pluck('title', 'id');
$barLabels = [];
$barData = [];
foreach ($moduleScores as $mid => $avg) {
$barLabels[] = 'M'.$mid.': '.(\Illuminate\Support\Str::limit($moduleLabels[$mid] ?? 'Modul '.$mid, 15));
$barData[] = $avg;
}

$dist = ['0-69' => 0, '70-89' => 0, '90-100' => 0];
foreach ($quizResults as $r) {
if ($r->score < 70) $dist['0-69']++;
    elseif ($r->score < 90) $dist['70-89']++;
        else $dist['90-100']++;
            }
            @endphp
            <div class="admin-container">

            <div class="admin-header">
                <h1 class="page-title">⚙️ Admin Panel</h1>
                <p class="page-sub">Kelola seluruh data platform DIMARI</p>
            </div>

            {{-- Stats --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-num">{{ $totalUsers }}</div>
                    <div class="stat-label">Total User</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num">{{ $totalModules }}</div>
                    <div class="stat-label">Total Modul</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num">{{ $totalQuestions }}</div>
                    <div class="stat-label">Total Soal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num">{{ $totalQuizThisWeek }}</div>
                    <div class="stat-label">Quiz Minggu Ini</div>
                </div>
            </div>

            <div class="tab-bar" id="adminTabBar">
                <button class="tab-btn active" onclick="switchTab('users', this)">👥 Users</button>
                <button class="tab-btn" onclick="switchTab('modules', this)">📚 Modul</button>
                <button class="tab-btn" onclick="switchTab('quizzes', this)">🧩 Soal Quiz</button>
                <button class="tab-btn" onclick="switchTab('results', this)">📊 Hasil Quiz</button>
            </div>

            {{-- Tab: Users (tanpa tombol Tambah User) --}}
            <div id="tab-users" class="tab-content">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:1rem;">
                    <span class="text-muted">{{ $users->count() }} user terdaftar</span>
                    <div style="display:flex; gap:0.5rem; align-items:center; margin-left:auto;">
                        <select id="usersRoleFilter" class="custom-select-native" onchange="adminFilter('usersTable', 'role', this.value)">
                            <option value="">Semua Role</option>
                            <option value="user">USER</option>
                            <option value="mentor">MENTOR</option>
                            <option value="admin">ADMIN</option>
                        </select>
                        <input id="usersSearch" class="form-input" placeholder="Cari username atau email..." style="width:260px;" oninput="adminSearch('usersTable', this.value)">
                    </div>
                </div>
                <div class="data-table-wrap">
                    <table class="data-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr data-role="{{ $user->role }}">
                                <td class="text-muted" style="font-family:'Space Mono',monospace; font-size:0.8rem;">{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ $user->role }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-cell">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit">✏️ Edit</a>
                                        <form method="POST"
                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                            style="display:inline"
                                            onsubmit="return dimariConfirm(event, 'Hapus user <strong>' + '{{ addslashes($user->username) }}' + '</strong>?<br><small>Tindakan ini tidak bisa dibatalkan.</small>')">
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
                                    <div class="empty-state">Belum ada user.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Modul --}}
            <div id="tab-modules" class="tab-content" style="display:none;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:0.5rem;">
                    <span class="text-muted">{{ $modules->count() }} modul tersedia</span>
                    <div style="display:flex; gap:0.5rem; align-items:center; margin-left:auto;">
                        <select id="modulesFilter" class="custom-select-native" onchange="adminSearch('modulesTable', '')">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod->id }}">M{{ $mod->id }}: {{ $mod->title }}</option>
                            @endforeach
                        </select>
                        <input id="modulesSearch" class="form-input" placeholder="Cari judul modul..." style="width:260px;" oninput="adminSearch('modulesTable', this.value)">
                        <a href="{{ route('admin.modules.create') }}" class="btn-primary">✚ Tambah Modul</a>
                    </div>
                </div>
                <div class="data-table-wrap">
                    <table class="data-table" id="modulesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul Modul</th>
                                <th>Jumlah Soal</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modules as $mod)
                            <tr data-module="{{ $mod->id }}">
                                <td class="text-muted" style="font-family:'Space Mono',monospace; font-size:0.8rem;">{{ $mod->id }}</td>
                                <td>{{ $mod->title }}</td>
                                <td>
                                    <span class="score-badge {{ $mod->questions_count > 0 ? 'good' : 'low' }}">
                                        {{ $mod->questions_count }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $mod->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-cell">
                                        <a href="{{ route('admin.modules.edit', $mod->id) }}" class="btn-edit">✏️ Edit</a>
                                        <form method="POST"
                                            action="{{ route('admin.modules.destroy', $mod->id) }}"
                                            style="display:inline"
                                            onsubmit="return dimariConfirm(event, 'Hapus modul <strong>' + '{{ addslashes($mod->title) }}' + '</strong>?<br><small>Semua soal terkait juga akan dihapus.</small>')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">🗑 Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">Belum ada modul.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Soal Quiz (view-only, hapus kelola) --}}
            <div id="tab-quizzes" class="tab-content" style="display:none;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:0.5rem;">
                    <span class="text-muted">{{ $questions->count() }} soal terdaftar</span>
                    <div style="display:flex; gap:0.5rem; align-items:center; margin-left:auto;">
                        <select id="quizModuleFilter" class="custom-select-native" onchange="filterQuizByModule(parseInt(this.value), this)">
                            <option value="0">Semua Modul</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod->id }}">M{{ $mod->id }}: {{ $mod->title }}</option>
                            @endforeach
                        </select>
                        <input id="quizSearch" class="form-input" placeholder="Cari pertanyaan..." style="width:320px;" oninput="adminSearch('quizTable', this.value)">
                        <span class="text-muted" style="font-size:0.8rem;">Pengelolaan soal dilakukan oleh Mentor</span>
                    </div>
                </div>

                <div class="data-table-wrap">
                    <table class="data-table" id="quizTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Modul</th>
                                <th>Pertanyaan</th>
                                <th>Opsi</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $q)
                            <tr data-module="{{ $q->module_id }}">
                                <td class="text-muted" style="font-family:'Space Mono',monospace; font-size:0.8rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="role-badge role-mentor" style="font-size:0.7rem;">
                                        {{ $q->module->title ?? '—' }}
                                    </span>
                                </td>
                                <td style="max-width:380px;">{{ Str::limit($q->question_text, 90) }}</td>
                                <td style="text-align:center;">
                                    <span class="score-badge {{ $q->options->count() >= 4 ? 'good' : 'low' }}">
                                        {{ $q->options->count() }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $q->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">Belum ada soal.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Hasil Quiz dengan grafik --}}
            <div id="tab-results" class="tab-content" style="display:none;">

                {{-- Grafik skor rata-rata per modul --}}
                <div class="mentor-section">
                    <h3 class="section-title">📊 Grafik Hasil Quiz</h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
                        <div class="stat-card">
                            <canvas id="mentorBarChart" height="300"></canvas>
                            <div class="stat-label" style="margin-top:0.5rem;">Rata-rata Skor per Modul</div>
                        </div>
                        <div class="stat-card">
                            <canvas id="mentorDistChart" height="200"></canvas>
                            <div class="stat-label" style="margin-top:0.5rem;">Distribusi Nilai</div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:0.5rem;">
                    <div></div>
                    <div style="display:flex; gap:0.5rem; align-items:center; margin-left:auto;">
                        <select id="resultsModuleFilter" class="custom-select-native" onchange="adminFilter('resultsTable','module', this.value)">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod->id }}">M{{ $mod->id }}: {{ $mod->title }}</option>
                            @endforeach
                        </select>
                        <input id="resultsSearch" class="form-input" placeholder="Cari username atau modul..." style="width:300px;" oninput="adminSearch('resultsTable', this.value)">
                    </div>
                </div>
                <div class="data-table-wrap">
                    <table class="data-table" id="resultsTable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Modul</th>
                                <th>Skor</th>
                                <th>Pekan</th>
                                <th>Dikerjakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizResults as $result)
                            @php
                            $scoreClass = $result->score == 100 ? 'perfect'
                            : ($result->score >= 70 ? 'good' : 'low');
                            @endphp
                            <tr>
                                <td>{{ $result->user->username ?? '-' }}</td>
                                <td>{{ $result->module->title ?? 'Modul ' . $result->module_id }}</td>
                                <td>
                                    <span class="score-badge {{ $scoreClass }}">
                                        {{ $result->score }}
                                    </span>
                                </td>
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
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">Belum ada hasil quiz.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Confirm Modal --}}
            <div id="dimariConfirmModal" class="modal-overlay hidden">
                <div class="modal-box" style="max-width:420px;">
                    <div class="modal-icon">⚠️</div>
                    <h3 class="modal-title" id="confirmTitle">Konfirmasi</h3>
                    <p class="modal-desc" id="confirmMsg"></p>
                    <div class="modal-actions">
                        <button onclick="cancelDimariConfirm()" class="btn-cancel">Batal</button>
                        <button onclick="executeDimariConfirm()" class="btn-confirm" style="background:var(--error);">Ya, Hapus</button>
                    </div>
                </div>
            </div>

            </div>
            @endsection

            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
                // Tab switching
                function switchTab(name, btn) {
                    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
                    document.querySelectorAll('#adminTabBar .tab-btn').forEach(b => b.classList.remove('active'));
                    document.getElementById('tab-' + name).style.display = 'block';
                    btn.classList.add('active');
                    if (name === 'results') initCharts();
                }

                // Quiz filter per modul (select-based)
                function filterQuizByModule(moduleId, el) {
                    // If called from a select element, no tab classes to toggle
                    document.querySelectorAll('#quizTable tbody tr').forEach(row => {
                        row.style.display = (moduleId === 0 || parseInt(row.dataset.module) === moduleId) ? '' : 'none';
                    });
                }

                // Generic table search: tableId is the table's id, query is the search string
                function adminSearch(tableId, query) {
                    const q = (query || '').toLowerCase().trim();
                    const table = document.getElementById(tableId);
                    if (!table) return;
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (!q) { row.style.display = ''; return; }
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                    });
                }

                // Generic filter by data attribute (e.g., role or module)
                function adminFilter(tableId, attr, value) {
                    const table = document.getElementById(tableId);
                    if (!table) return;
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (!value) { row.style.display = ''; return; }
                        const val = row.dataset[attr];
                        row.style.display = (val == value) ? '' : 'none';
                    });
                }

                // Confirm modal
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
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') cancelDimariConfirm();
                });

                // Grafik
                let chartsInited = false;

                function initCharts() {
                    if (chartsInited) return;
                    chartsInited = true;

                    // Menggunakan Js::from agar VS Code membaca ini sebagai sintaks Blade yang valid
                    const barLabelsData = @json($barLabels);
                    const barValuesData = @json(array_values($barData));
                    const distValuesData = @json(array_values($dist));

                    // Chart 1: Bar Chart
                    new Chart(document.getElementById('mentorBarChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: barLabelsData,
                            datasets: [{
                                label: 'Rata-rata Skor',
                                data: barValuesData,
                                backgroundColor: 'rgba(139,92,246,0.7)',
                                borderColor: 'rgba(139,92,246,1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#ccc'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        color: '#aaa'
                                    },
                                    grid: {
                                        color: 'rgba(255,255,255,0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#aaa',
                                        maxRotation: 30
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    // Chart 2: Doughnut Chart
                    new Chart(document.getElementById('mentorDistChart').getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['0–69', '70–89', '90–100'],
                            datasets: [{
                                data: distValuesData,
                                backgroundColor: ['#ef4444', '#f59e0b', '#22c55e'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#ccc',
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
            @endpush