@extends('layouts.customer.app')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="bg-white border-bottom shadow-sm py-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 text-primary">My Bookings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Bookings</li>
                </ol>
            </nav>
        </div>
    </div>

    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Bar + Tabs -->
   <div class="d-flex flex-column align-items-center mb-3">
    <ul class="nav nav-tabs sbadmin-status-tabs w-100 mb-3" id="statusTabs">
        <li class="nav-item flex-fill text-center">
            <a class="nav-link active" href="#" data-status="all">All</a>
        </li>
        <li class="nav-item flex-fill text-center">
            <a class="nav-link" href="#" data-status="pending">Pending</a>
        </li>
        <li class="nav-item flex-fill text-center">
            <a class="nav-link" href="#" data-status="approved">Approved</a>
        </li>
        <li class="nav-item flex-fill text-center">
            <a class="nav-link" href="#" data-status="cancelled">Cancelled</a>
        </li>
    </ul>

    <div class="search-wrapper w-50 mx-auto">
        <input type="text" id="globalSearch" class="form-control text-center" placeholder="Search bookings...">
    </div>
</div>


    <!-- Booking Cards -->
    <div id="bookingList">
        @forelse ($bookingRequests as $bookingRequest)
        <div class="card border-left-primary shadow-lg mb-4 booking-card" style="background: linear-gradient(135deg, #e6f0ff, #f8fbff);"
             data-status="{{ strtolower($bookingRequest->status) }}"
             data-search="{{ strtolower($bookingRequest->court->name) }} {{ strtolower($bookingRequest->transaction_no ?? '') }} {{ strtolower($bookingRequest->status) }}">
            <div class="card-body">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="card-title mb-0 me-2 text-primary">
                                <strong>{{ $bookingRequest->court->name }}</strong>
                            </h5>
                        </div>
                        <p class="text-muted mb-1">₱{{ number_format($bookingRequest->amount, 2) }}</p>
                        <p class="text-muted mb-1">{{ $bookingRequest->transaction_no ?? '—' }}</p>
                    </div>
                    <div class="text-end">
                        @if ($bookingRequest->status === 'pending')
                            <span class="badge bg-warning text-light">Pending</span>
                        @elseif ($bookingRequest->status === 'cancelled')
                            <span class="badge bg-danger text-light">Cancelled</span>
                        @else
                            <span class="badge bg-success text-light">{{ ucfirst($bookingRequest->status) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1">Date</p>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($bookingRequest->booking_date)->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1">Time</p>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($bookingRequest->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($bookingRequest->end_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1">Duration</p>
                            <p class="fw-semibold mb-0">{{ $bookingRequest->expected_hours }}h {{ $bookingRequest->expected_minutes }}m</p>
                        </div>
                    </div>

                    
                </div>
            </div>
            <!-- Action Buttons -->
              @if($bookingRequest->status === 'pending')
            <div class="card-footer text-right">
                <a href="{{ route('customer.booking_requests.edit', $bookingRequest->id) }}" class="btn btn-warning">
                    Edit
                </a>
            </div>
            @endif
          

        </div>
        
        @empty
            <div class="alert alert-info text-center">No booking requests yet.</div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $bookingRequests->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    /* === SB Admin 2 Tabs Style === */
    .sbadmin-status-tabs {
        border-bottom: 2px solid #e3e6f0;
        background-color: #fff;
        border-radius: 0.35rem 0.35rem 0 0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    }

    .sbadmin-status-tabs .nav-link {
        color: #4e73df;
        font-weight: 500;
        border: none;
        border-radius: 0;
        padding: 0.75rem 0;
        transition: all 0.2s ease;
    }

    .sbadmin-status-tabs .nav-link:hover {
        background-color: #f8f9fc;
        color: #224abe;
    }

    .sbadmin-status-tabs .nav-link.active {
        background-color: #4e73df;
        color: #fff !important;
        font-weight: 600;
        border-bottom: 3px solid #224abe;
    }

    .sbadmin-status-tabs .nav-item {
        margin-bottom: -2px;
    }

    /* Responsive tweak: make search bar full width on mobile */
    @media (max-width: 768px) {
        .search-wrapper {
            width: 100% !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("globalSearch");
        const bookingCards = document.querySelectorAll(".booking-card");
        const statusTabs = document.querySelectorAll("#statusTabs .nav-link");

        function filterCards() {
            let searchText = searchInput.value.toLowerCase();
            let activeStatus = document.querySelector("#statusTabs .nav-link.active").dataset.status;

            bookingCards.forEach(card => {
                let text = card.innerText.toLowerCase();
                let status = card.dataset.status;

                let matchesSearch = text.includes(searchText);
                let matchesStatus = (activeStatus === "all" || status === activeStatus);

                card.style.display = (matchesSearch && matchesStatus) ? "" : "none";
            });
        }

        searchInput.addEventListener("keyup", filterCards);

        statusTabs.forEach(tab => {
            tab.addEventListener("click", function(e) {
                e.preventDefault();
                statusTabs.forEach(t => t.classList.remove("active"));
                this.classList.add("active");
                filterCards();
            });
        });
    });
</script>
@endpush

