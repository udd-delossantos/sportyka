@extends('layouts.customer.app')

@section('content')
<div class="container" data-aos="fade-up" data-aos-delay="100">
    <!-- Page Header -->
    <div class="bg-white border-bottom shadow-sm py-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 text-primary">View Court</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Court</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Court Header with Image and Basic Info -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <div class="room-header-image position-relative">
                @if(!empty($court->images) && isset($court->images[0]))
                    <img src="{{ asset('storage/' . $court->images[0]) }}" 
                        alt="{{ $court->name }}" 
                        class="img-fluid rounded">
                @else
                    <img src="https://via.placeholder.com/800x500?text=No+Image" 
                        alt="No Image" class="img-fluid rounded">
                @endif
                <div class="room-badge">
                    <span class="text-white">{{ ucfirst($court->status) }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h3 class="text-primary mb-3 pt-0 mt-0">{{ $court->name }}</h3>
            <h5 class="mb-3"><strong>Sport: </strong>{{ $court->sport }}</h5>
            <p class="mb-3">{{ $court->description }}</p> 
            <h2 class="mb-4"><strong class="text-success">₱{{ number_format($court->hourly_rate, 2) }}</strong>
                <span style="font-size: 0.6em;"> per hour</span>
            </h2>
            <a href="{{ route('customer.booking_requests.create') }}" class="btn btn-book-now btn-primary">Book Now</a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    

    <!-- Court Gallery -->
    <div class="mb-4">
        <div class="text-center">
            <h3 class="fw-bold text-primary mb-3">
                Court Gallery
            </h3>
            <p class="text-muted">See more photos of this court.</p>
            <hr class="mx-auto" style="width: 120px; height: 3px; background-color: #0d6efd; border: none; border-radius: 2px;">
        </div>
        <div>
            @if(!empty($court->images) && count($court->images) > 0)
                <!-- Main Image -->
                <div class="gallery-main mb-3">
                    <a href="{{ asset('storage/' . $court->images[0]) }}" class="glightbox">
                        <img src="{{ asset('storage/' . $court->images[0]) }}" 
                            alt="{{ $court->name }} Main Image" 
                            class="img-fluid rounded shadow-sm">
                    </a>
                </div>
                <hr>

                <!-- Thumbnails -->
                <div class="gallery-thumbnails d-flex flex-wrap gap-3">
                    @foreach($court->images as $index => $image)
                        @if($index > 0)
                            <a href="{{ asset('storage/' . $image) }}" class="glightbox">
                                <img src="{{ asset('storage/' . $image) }}" 
                                    alt="Court Image {{ $index+1 }}" 
                                    class="img-thumbnail rounded shadow-sm"
                                    style="width:150px; height:100px; object-fit:cover;">
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted">No images available for this court.</p>
            @endif
        </div>
    </div>

</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            closeButton: true,
            zoomable: true,
            draggable: true,
        });
    });
</script>
@endpush