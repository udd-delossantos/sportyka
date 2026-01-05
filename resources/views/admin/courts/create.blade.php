@extends('layouts.admin.app')

@section('content')
<div class="container">
  <h1 class="h3 mb-3 text-gray-800">Add Court</h1>
  <form method="POST" action="{{ isset($court) ? route('admin.courts.update', $court) : route('admin.courts.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($court))
      @method('PUT')
    @endif

    @include('admin.courts._form', ['court' => $court ?? null])
  </form>
</div>
@endsection
