@extends('layouts.app')

@section('title','Edit User')

@section('content')

<h2>Edit User</h2>

<form method="POST"
action="{{ route('admin.users.update',$user) }}">

    @include('admin.users.partials.form')

</form>

@endsection