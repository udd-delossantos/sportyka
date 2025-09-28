@extends('layouts.customer.app') @section('content')
<div class="container">
    <!-- Page Header -->
    <div class="bg-white border-bottom shadow-sm py-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 text-primary">Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
            </nav>
        </div>
    </div>
  
  <div class="px-4 py-5 mb-5 text-center shadow rounded-4 bg-primary" style="background: linear-gradient(135deg, #e6f0ff, #f8fbff);">
        <img class="d-block mx-auto mb-4 rounded-circle border border-3 border-primary shadow-sm" 
        src="{{ asset('img/sk-logo.png') }}" 
        alt="Sporty Ka Logo" 
        width="100" 
        height="100">

        <h1 class="text-primary">Welcome to Sporty Ka?</h1>
        <div class="col-lg-8 mx-auto">
            <p class="lead mb-4">
                Your all-in-one sports hub! Reserve courts, manage your sessions, and track your payments, all in one place. 
                Whether it’s basketball, tennis, badminton, or pickleball, we’ve got you covered.
            </p>
            <div class="d-grid gap-3 d-sm-grid justify-content-center">
            <a href="{{ route('customer.booking_requests.create') }}" class="btn btn-primary btn-lg px-4 mb-2">
                Book a Court
            </a>
            <a href="{{ route('customer.booking_requests.index') }}" class="btn btn-secondary btn-lg px-4 mb-2">
                View Bookings
            </a>
        </div>

        </div>
    </div>

    <div class="text-center">
        <h2 class="fw-bold text-primary mb-3">
             Courts Available
        </h2>
        <p class="text-muted">Browse through the well-maintained courts of Proving Grounds Sports Center and book your next game with ease.</p>
        <hr class="mx-auto" style="width: 120px; height: 3px; background-color: #0d6efd; border: none; border-radius: 2px;">
    </div>

  <!-- COURTS DISPLAY -->
    <div class="row">
        @foreach($courts as $court)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                @if(!empty($court->images) && isset($court->images[0]))
                <img src="{{ asset('storage/' . $court->images[0]) }}" class="card-img-top" alt="{{ $court->name }}" style="height: 180px; object-fit: cover;" />
                @else
                <svg class="bd-placeholder-img card-img-top" width="100%" height="180" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: No image" preserveAspectRatio="xMidYMid slice" focusable="false">
                    <title>No image available</title>
                    <rect width="100%" height="100%" fill="#868e96"></rect>
                    <text x="50%" y="50%" fill="#dee2e6" dy=".3em" text-anchor="middle">No image</text>
                </svg>
                @endif

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary">{{ $court->name }}</h5>
                    <p class="card-text flex-grow-1">
                        {{ Str::limit($court->description, 100) }}
                    </p>
                    <a href="{{ route('customer.courts.show', $court->id) }}" class="btn btn-primary mt-auto">View Court</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
