@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3 text-gray-800">Edit User</h1>

    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif
        @include('admin.users.form')
    </form>
</div>
@endsection
