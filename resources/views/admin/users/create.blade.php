@extends('layouts.admin.app')

@section('content')
<div class="container">

    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif
        @include('admin.users.form')
    </form>
</div>
@endsection
