@extends('layouts.staff.app')

@section('content')
<div class="container-fluid">
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

    <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <h4 class="mb-0 mr-2"><strong>Pending Requests</strong></h4>
            <!-- FILTER DROPDOWN -->
            <select id="courtFilter" class="form-control form-control-sm" style="width:200px;">
                <option value="">All Courts</option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}">{{ $court->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body" style="max-height: 700px; overflow-y: auto; padding: 1rem;">
        <div id="bookingList">
                @forelse($requests as $request)
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
                </div>
            @empty
            <div class="card shadow">
                <div class="card-body text-center">
                    <p class="mb-0">No Pending Requests.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>


<div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Records</strong></h4>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingRequestsTable">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Transaction No</th>
                            <!--<th>Approved By</th>-->
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedRequests as $processedRequest)
                            <tr>
                                <td>{{ $processedRequest->user->name }}</td>
                                <td>{{ $processedRequest->court->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($processedRequest->booking_date)->format('F d, Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($processedRequest->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($processedRequest->end_time)->format('h:i A') }}
                                </td>
                                <td>{{ $processedRequest->expected_hours }}h {{ $processedRequest->expected_minutes }}m</td>
                                <td>₱{{ number_format($processedRequest->amount, 2) }}</td>
                                <td>{{ $processedRequest->transaction_no ?? '—' }}</td>
                                <!--<td>{{ $processedRequest->staff->name ?? '—' }}</td>-->
                                <td class="text-light text-center">
                                    <span class="badge bg-{{ 
                                        $processedRequest->status === 'pending' ? 'warning' : 
                                        ($processedRequest->status === 'approved' ? 'success' : 
                                        ($processedRequest->status === 'completed' ? 'info' : 'danger')) 
                                    }}">
                                        {{ ucfirst($processedRequest->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#bookingRequestsTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
        });

        // Court Filter
        document.getElementById('courtFilter').addEventListener('change', function() {
            const courtId = this.value;

            document.querySelectorAll('#bookingList .booking-card').forEach(card => {
                if (!courtId || card.dataset.court === courtId) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>

@endpush
