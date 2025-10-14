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

    <!-- Tabs -->
    <div class="d-flex flex-column align-items-center mb-3">
        <ul class="nav nav-tabs sbadmin-status-tabs w-100 mb-3" id="statusTabs">
            <li class="nav-item flex-fill text-center shadow-sm"><a class="nav-link active" href="#" data-status="all">All</a></li>
            <li class="nav-item flex-fill text-center shadow-sm"><a class="nav-link" href="#" data-status="pending">Pending</a></li>
            <li class="nav-item flex-fill text-center shadow-sm"><a class="nav-link" href="#" data-status="approved">Approved</a></li>
            <li class="nav-item flex-fill text-center shadow-sm"><a class="nav-link" href="#" data-status="cancelled">Cancelled</a></li>
        </ul>
    </div>

    <!-- Date + Search -->
    <div class="row mb-4 align-items-end">
        <div class="col-md-3">
            <label for="fromDate" class="form-label">From:</label>
            <input type="date" id="fromDate" class="form-control" />
        </div>
        <div class="col-md-3">
            <label for="toDate" class="form-label">To:</label>
            <input type="date" id="toDate" class="form-control" />
        </div>
        <div class="col-md-6">
            <label for="globalSearch" class="form-label invisible">Search</label>
            <div class="search-wrapper w-100">
                <input type="text" id="globalSearch" class="form-control text-center" placeholder="Search bookings..." />
            </div>
        </div>
    </div>

    <!-- Filters + Entries -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <label for="entries" class="me-2 fw-semibold">Show</label>
                <select id="entries" class="form-control d-inline-block w-auto">
                    <option value="3">3</option>
                    <option value="5" selected>5</option>
                    <option value="7">7</option>
                </select>
                <span class="ms-1">entries</span>
            </div>

            <div>
                <select id="courtFilter" class="form-control w-auto">
                    <option value="">All Courts</option>
                    @foreach($courts as $court)
                    <option value="{{ strtolower($court->name) }}">{{ $court->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Booking Cards -->
    <div id="bookingList">
        @forelse ($bookingRequests as $bookingRequest)
        <div
            class="card border-left-primary shadow mb-4 booking-card"
            style="background: linear-gradient(135deg, #e6f0ff, #f8fbff);"
            data-status="{{ strtolower($bookingRequest->status) }}"
            data-court="{{ strtolower($bookingRequest->court->name) }}"
            data-date="{{ \Carbon\Carbon::parse($bookingRequest->booking_date)->format('Y-m-d') }}"
            data-search="{{ strtolower($bookingRequest->court->name) }} {{ strtolower($bookingRequest->transaction_no ?? '') }} {{ strtolower($bookingRequest->status) }}"
        >
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-primary"><strong>{{ $bookingRequest->court->name }}</strong></h5>
                        <p class="text-success mb-1">₱{{ number_format($bookingRequest->amount, 2) }}</p>
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

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1"><strong>Date</strong></p>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($bookingRequest->booking_date)->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1"><strong>Time</strong></p>
                            <p class="fw-semibold mb-0">
                                {{ \Carbon\Carbon::parse($bookingRequest->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($bookingRequest->end_time)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded">
                            <p class="small text-muted mb-1"><strong>Duration</strong></p>
                            <p class="fw-semibold mb-0">{{ $bookingRequest->expected_hours }}h {{ $bookingRequest->expected_minutes }}m</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($bookingRequest->status === 'pending')
            <div class="card-footer text-right">
                <a href="{{ route('customer.booking_requests.edit', $bookingRequest->id) }}" class="btn btn-warning">Edit</a>
            </div>
            @endif
        </div>
        @empty
        <div class="alert alert-info text-center">No booking requests yet.</div>
        @endforelse
    </div>

    <!-- Pagination Controls -->
    <nav>
        <ul class="pagination justify-content-center mt-4" id="paginationControls"></ul>
    </nav>
</div>
@endsection
@push('styles')
<style>
    /* === SB Admin 2 Tabs Style === */
    .sbadmin-status-tabs {
        border-bottom: 2px solid #e3e6f0;
        background-color: #fff;
        border-radius: 0.35rem 0.35rem 0 0;
     
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

    /* Responsive tweaks for smaller screens */
    @media (max-width: 768px) {
        .search-wrapper {
            width: 100% !important;
            margin-top: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("globalSearch");
    const statusTabs = document.querySelectorAll("#statusTabs .nav-link");
    const bookingCards = Array.from(document.querySelectorAll(".booking-card"));
    const courtFilter = document.getElementById("courtFilter");
    const fromDate = document.getElementById("fromDate");
    const toDate = document.getElementById("toDate");
    const entriesSelect = document.getElementById("entries");
    const paginationControls = document.getElementById("paginationControls");

    let filteredCards = [];
    let currentPage = 1;

    function filterCards() {
        const searchText = searchInput.value.toLowerCase();
        const activeStatus = document.querySelector("#statusTabs .nav-link.active").dataset.status;
        const courtVal = courtFilter.value;
        const fromVal = fromDate.value ? new Date(fromDate.value) : null;
        const toVal = toDate.value ? new Date(toDate.value) : null;

        filteredCards = bookingCards.filter(card => {
            const text = card.dataset.search;
            const status = card.dataset.status;
            const court = card.dataset.court;
            const date = new Date(card.dataset.date);

            const matchesSearch = text.includes(searchText);
            const matchesStatus = activeStatus === "all" || status === activeStatus;
            const matchesCourt = courtVal === "" || court === courtVal;
            const matchesFrom = fromVal === null || date >= fromVal;
            const matchesTo = toVal === null || date <= toVal;

            return matchesSearch && matchesStatus && matchesCourt && matchesFrom && matchesTo;
        });

        currentPage = 1;
        renderPage();
    }

    function renderPage() {
        const entriesPerPage = parseInt(entriesSelect.value, 10);
        const totalPages = Math.ceil(filteredCards.length / entriesPerPage);

        bookingCards.forEach(card => card.style.display = "none");

        const start = (currentPage - 1) * entriesPerPage;
        const end = start + entriesPerPage;
        filteredCards.slice(start, end).forEach(card => card.style.display = "");

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        paginationControls.innerHTML = "";

        if (totalPages <= 1) return;

        const prevLi = document.createElement("li");
        prevLi.className = "page-item" + (currentPage === 1 ? " disabled" : "");
        prevLi.innerHTML = `<a class="page-link" href="#">Previous</a>`;
        prevLi.addEventListener("click", e => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderPage();
            }
        });
        paginationControls.appendChild(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement("li");
            li.className = "page-item" + (i === currentPage ? " active" : "");
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener("click", e => {
                e.preventDefault();
                currentPage = i;
                renderPage();
            });
            paginationControls.appendChild(li);
        }

        const nextLi = document.createElement("li");
        nextLi.className = "page-item" + (currentPage === totalPages ? " disabled" : "");
        nextLi.innerHTML = `<a class="page-link" href="#">Next</a>`;
        nextLi.addEventListener("click", e => {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
            }
        });
        paginationControls.appendChild(nextLi);
    }

    [searchInput, courtFilter, fromDate, toDate, entriesSelect].forEach(el => {
        el.addEventListener("input", filterCards);
    });

    statusTabs.forEach(tab => {
        tab.addEventListener("click", function (e) {
            e.preventDefault();
            statusTabs.forEach(t => t.classList.remove("active"));
            this.classList.add("active");
            filterCards();
        });
    });

    filterCards();
});
</script>
@endpush
