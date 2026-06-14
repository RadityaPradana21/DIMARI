@extends('layouts.app')

@section('title','Tambah User')

@section('content')

<h2>Tambah User</h2>

<form method="POST"
action="{{ route('admin.users.store') }}">

    @include('admin.users.partials.form')

</form>

@endsection