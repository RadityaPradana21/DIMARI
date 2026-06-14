@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container">

	<h1>Manajemen User</h1>

	<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
		<a href="{{ route('admin.users.create') }}" class="btn-primary">✚ Tambah User</a>

		<form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:0.5rem; align-items:center;">
			<input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari username, email, atau nama lengkap..." class="form-input" />
			<select name="role" class="form-input" style="width:140px;">
				<option value="">Semua Role</option>
				<option value="user" {{ (request('role') === 'user') ? 'selected' : '' }}>User</option>
				<option value="mentor" {{ (request('role') === 'mentor') ? 'selected' : '' }}>Mentor</option>
				<option value="admin" {{ (request('role') === 'admin') ? 'selected' : '' }}>Admin</option>
			</select>
			<select name="per_page" class="form-input" style="width:90px;">
				<option value="10">10</option>
				<option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
				<option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
			</select>
			<button class="btn-primary">Cari</button>
		</form>
	</div>

	<div class="text-muted" style="margin-bottom:0.5rem;">{{ $users->total() }} pengguna</div>

	<div class="data-table-wrap">
		<table class="data-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Username</th>
					<th>Nama Lengkap</th>
					<th>Email</th>
					<th>Role</th>
					<th>Dibuat</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				@forelse($users as $u)
					<tr>
						<td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:0.8rem;">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
						<td>{{ $u->username }}</td>
						<td>{{ $u->full_name ?? '-' }}</td>
						<td>{{ $u->email }}</td>
						<td><span class="role-badge role-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
						<td class="text-muted">{{ optional($u->created_at)->format('d M Y') }}</td>
						<td>
							<div class="action-cell">
								<a href="{{ route('admin.users.show', $u->id) }}" class="btn-edit">👁 Lihat</a>
								<a href="{{ route('admin.users.edit', $u->id) }}" class="btn-edit">✏️ Edit</a>
								<form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn-delete">🗑 Hapus</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7"><div class="empty-state">Belum ada pengguna.</div></td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	<div style="margin-top:1rem;">{{ $users->links() }}</div>

</div>

@endsection
