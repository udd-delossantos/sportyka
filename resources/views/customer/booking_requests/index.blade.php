@extends('layouts.customer.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="text-primary"><strong>My Bookings</strong></h2>
            <p class="mb-0">View and manage all your court bookings below.</p>
    </div>

    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Bar + Tabs -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-tabs" id="statusTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-status="all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="approved">Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="cancelled">Cancelled</a>
            </li>
        </ul>

         <input type="text" id="globalSearch" class="form-control w-25" placeholder="Search bookings...">
    </div>

    <!-- Booking Cards -->
    <div id="bookingList">
        @forelse ($bookingRequests as $bookingRequest)
        <div class="card shadow-lg mb-4 booking-card" 
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
                        <div class="p-3 bg-light rounded">
                            <p class="small text-muted mb-1">Date</p>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($bookingRequest->booking_date)->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <p class="small text-muted mb-1">Time</p>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($bookingRequest->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($bookingRequest->end_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
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

<!-- Filtering Script -->
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
@endsection
