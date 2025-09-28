@extends('layouts.customer.app') @section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="text-primary"><strong>Courts</strong></h2>
            <p class="mb-0">View and select your desired court anytime!</p>
        </div>
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
                    <h5 class="card-title">{{ $court->name }}</h5>
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
