@extends('layouts.app')
@section('title', 'Edit Soal Quiz')

@section('content')
<div class="admin-container">

    <div class="admin-header">
        <h1 class="page-title">✏️ Edit Soal Quiz</h1>
        <p class="page-sub">Perbarui pertanyaan atau pilihan jawaban yang sudah ada.</p>
    </div>

    <div class="mentor-section">
        <form method="POST" action="{{ route('admin.quizzes.update', $question->id) }}">
            @include('admin.quizzes.partials.form')
        </form>
    </div>

</div>
@endsection