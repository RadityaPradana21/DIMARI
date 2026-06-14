@extends('layouts.app')
@section('title', 'Tambah Soal Quiz')

@section('content')
<div class="admin-container">

    <div class="admin-header">
        <h1 class="page-title">✚ Tambah Soal Quiz</h1>
        <p class="page-sub">Buat pertanyaan baru beserta pilihan jawaban untuk modul tertentu.</p>
    </div>

    <div class="mentor-section">
        <form method="POST" action="{{ route('admin.quizzes.store') }}">
            @include('admin.quizzes.partials.form')
        </form>
    </div>

</div>
@endsection