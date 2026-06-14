@extends('layouts.app')

@section('title', 'Edit Modul')

@section('content')

<main class="relative z-10 pt-20 pb-16 max-w-2xl mx-auto px-6">

    <div class="mb-6 pt-4">

        <a
            href="{{ route('admin.index') }}"
            class="btn-secondary">

            ← Kembali ke Admin

        </a>

        <h1 class="font-orbitron font-black text-2xl grad-text mt-2">
            EDIT MODUL
        </h1>

    </div>
   
    <form
        method="POST"
        action="{{ route('admin.modules.update', $module) }}">

        @include('admin.modules.partials.form')

    </form>

</main>

@endsection