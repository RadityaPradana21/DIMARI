@extends('layouts.app')

@section('title', 'Daftar Modul')

@section('content')

<div class="container">

    <h1>Daftar Modul</h1>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <a href="{{ route('admin.modules.create') }}" class="btn-primary">✚ Tambah Modul</a>

        <form method="GET" action="{{ route('admin.modules.index') }}" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari judul, deskripsi atau konten..." class="form-input" />
            <select name="per_page" class="form-input" style="width:90px;">
                <option value="10">10</option>
                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <button class="btn-primary">Cari</button>
        </form>
    </div>

    <div class="text-muted" style="margin-bottom:0.5rem;">{{ $modules->total() }} modul ditemukan</div>

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
                @forelse($modules as $module)
                    <tr>
                        <td>{{ $module->id }}</td>
                        <td>{{ $module->title }}</td>
                        <td class="text-muted">{{ Str::limit($module->description ?? '-', 120) }}</td>
                        <td class="action-cell">
                            <a href="{{ route('admin.modules.show', $module) }}" class="btn-edit">👁 Lihat</a>
                            <a href="{{ route('admin.modules.edit', $module) }}" class="btn-edit">✏️ Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"><div class="empty-state">Belum ada modul.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">{{ $modules->links() }}</div>

</div>

@endsection