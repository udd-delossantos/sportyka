@extends('layouts.staff.app')

@section('content')
<div class="container">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Booking Requests</strong></h2>  
            <div class="d-flex gap-3">
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-primary" style="min-width: 120px;">
                <h6 class="text-muted mb-1">All</h6>
                <h4 class="mb-0 text-primary">{{ $requestCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-warning" style="min-width: 120px;">
                <h6 class="text-muted mb-1">Pending</h6>
                <h4 class="mb-0 text-warning">{{ $pendingCount}}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 120px;">
                <h6 class="text-muted mb-1">Approved</h6>
                <h4 class="mb-0 text-success">{{ $approvedCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 120px;">
                <h6 class="text-muted mb-1">Cancelled</h6>
                <h4 class="mb-0 text-danger">{{ $cancelledCount }}</h4>
            </div>
        </div>
    </div>
</div>

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

  <div id="bookingList">
      @foreach ($requests as $request)
          <div class="card shadow border-left-primary mb-4 booking-card" data-status="{{ $request->status }}">
              <div class="card-body">
                  <!-- Header -->
                  <div class="d-flex justify-content-between align-items-start mb-3">
                      <div class="flex-grow-1">
                          <div class="d-flex align-items-center mb-2">
                              <h5 class="card-title mb-0 me-2 mr-1">
                                  <strong>{{ $request->user->name }}</strong>
                              </h5>
                              <span class="text-light badge bg-{{ 
                                  $request->status === 'pending' ? 'warning' : 
                                  ($request->status === 'approved' ? 'success' : 
                                  ($request->status === 'completed' ? 'info' : 'danger')) 
                              }}">
                                  {{ ucfirst($request->status) }}
                              </span>
                          </div>
                          <p class="mb-1 text-primary">{{ $request->court->name }}</p>
                      </div>
                      <div class="text-end">
                          <h4 class="fs-4 fw-bold text-success mb-0">
                              ₱{{ number_format($request->amount, 2) }}
                          </h4>
                      </div>
                  </div>

                  <!-- Details Grid -->
                  <div class="row g-3 mb-3">
                      <div class="col-md-4">
                          <div class="p-3 bg-light rounded">
                              <p class="small text-muted mb-1">Date</p>
                              <p class="fw-semibold mb-0">
                                  {{ \Carbon\Carbon::parse($request->booking_date)->format('F d, Y') }}
                              </p>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="p-3 bg-light rounded">
                              <p class="small text-muted mb-1">Time</p>
                              <p class="fw-semibold mb-0">
                                  {{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - 
                                  {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}
                              </p>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="p-3 bg-light rounded">
                              <p class="small text-muted mb-1">Duration</p>
                              <p class="fw-semibold mb-0">
                                  {{ $request->expected_hours }}h {{ $request->expected_minutes }}m
                              </p>
                          </div>
                      </div>
                  </div>

                  <!-- Notes -->
                  <div class="p-3 bg-light mb-3">
                      <p class="small text-muted mb-1">Transaction No.</p>
                      <p class="text-dark mb-0">{{ $request->transaction_no ?? '—' }}</p>
                  </div>
              </div>

              <!-- Action Buttons -->
              @if($request->status === 'pending')
              <div class="card-footer text-right">
                  <form action="{{ route('staff.booking_requests.approve', $request->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-success">Approve</button>
                  </form>
                  <form action="{{ route('staff.booking_requests.cancel', $request->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-danger">Cancel</button>
                  </form>
              </div>
              @endif
          </div>
      @endforeach
  </div>

  <!-- Filtering Script -->
<!-- Filtering Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("globalSearch");
        const bookingCards = document.querySelectorAll(".booking-card");
        const statusTabs = document.querySelectorAll("#statusTabs .nav-link");

        // Restore saved state from localStorage
        if (localStorage.getItem("bookingSearch")) {
            searchInput.value = localStorage.getItem("bookingSearch");
        }
        if (localStorage.getItem("activeStatus")) {
            let savedStatus = localStorage.getItem("activeStatus");
            statusTabs.forEach(tab => {
                if (tab.dataset.status === savedStatus) {
                    statusTabs.forEach(t => t.classList.remove("active"));
                    tab.classList.add("active");
                }
            });
        }

        function filterCards() {
            let searchText = searchInput.value.toLowerCase();
            let activeStatus = document.querySelector("#statusTabs .nav-link.active").dataset.status;

            // Save to localStorage
            localStorage.setItem("bookingSearch", searchText);
            localStorage.setItem("activeStatus", activeStatus);

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

        // Run filter once on page load with saved state
        filterCards();
    });
</script>

</div>
@endsection
