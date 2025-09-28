@extends('layouts.staff.app')
@section('content')
<div class="container-fluid">
    <div class="px-0">
            <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Queues</strong></h2>
            <div class="d-flex gap-3">
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-warning" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Waiting</h6>
                    <h4 class="mb-0 text-warning">{{ $waitingCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Called</h6>
                    <h4 class="mb-0 text-success">{{ $calledCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h4 class="mb-0 text-info">{{ $completedCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Skipped</h6>
                    <h4 class="mb-0 text-danger">{{ $skippedCount }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DISPLAY ONLY THE WAITING CUSTOMERS HERE -->


<!-- DISPLAY ONLY THE WAITING CUSTOMERS HERE -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h4 class="mb-0 mr-2"><strong>Waiting Customers</strong></h4>
                <!-- FILTER DROPDOWN -->
                <select id="courtFilter" class="form-control form-control-sm" style="width:200px;">
                    <option value="">All Courts</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">{{ $court->name }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('staff.queues.create') }}" class="btn btn-primary">Add Queue</a>
        </div>
        <div class="card-body" style="max-height: 700px; overflow-y: auto; padding: 1rem;">
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}@endif
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div> @endif
            <div id="bookingList">
                @forelse ($waitingQueues as $queue)
                    <div class="card shadow-sm border-left-primary mb-4 booking-card" data-court="{{ $queue->court_id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="card-title mb-0 me-2 mr-1">
                                            <strong class="text-danger"> #{{ $queue->queue_number }} </strong>
                                            <strong>{{ $queue->customer_name }}</strong>
                                        </h5>
                                        <span class="badge bg-warning text-light">{{ ucfirst($queue->status) }}</span>
                                    </div>
                                    <p class="mb-1 text-primary">{{ $queue->court->name }}</p>
                                </div>
                                <div class="text-end">
                                    <h4 class="fs-4 fw-bold text-success mb-0">
                                        ₱{{ number_format($queue->amount, 2) }}
                                    </h4>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded">
                                        <p class="small text-muted mb-1">Time</p>
                                        <p class="fw-semibold mb-0">
                                            {{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded">
                                        <p class="small text-muted mb-1">Duration</p>
                                        <p class="fw-semibold mb-0">
                                            {{ $queue->expected_hours}}h {{ $queue->expected_minutes }}m
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded">
                                        <p class="small text-muted mb-1">Created By</p>
                                        <p class="fw-semibold mb-0">
                                            {{ $queue->staff->name ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-light rounded">
                                        <p class="small text-muted mb-1">Transaction No.</p>
                                        <p class="fw-semibold mb-0">
                                            {{ $queue->transaction_no ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            @if($queue->queue_number == 1)
                            <form action="{{ route('staff.queues.call', $queue->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm mx-1">Call</button>
                            </form>
                            @endif
                            <form action="{{ route('staff.queues.skip', $queue->id) }}" method="POST" onsubmit="return confirm('Skip this queue?');" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Skip</button>
                            </form>
                        </div>
                    </div>
           
                @empty
                <div class="card shadow">
                    <div class="card-body text-center">
                        <p class="mb-0">No Customers in Queue.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Queue History</strong></h4>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="queuesTable">
                    <thead>
                        <tr>
                     
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Transaction No</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
         
                        </tr>
                    </thead>
                    <!-- TABLE BELOW NOW SHOWS CALLED / SKIPPED ONLY -->
                    <tbody>
                    @foreach($processedQueues as $queue)
                            <tr data-court="{{ $queue->court_id }}">
                                <td>{{ $queue->customer_name }}</td>
                                <td>{{ $queue->court->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</td>
                                <td>{{ $queue->expected_hours}}h {{ $queue->expected_minutes }}m</td>
                                <td>₱{{ number_format($queue->amount, 2) }}</td>
                                <td>{{ $queue->transaction_no ?? 'N/A' }}</td>
                                <td>{{ $queue->staff->name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $queue->status === 'called' ? 'success' : 'danger' }} text-light">
                                        {{ ucfirst($queue->status) }}
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
     $('#queuesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mb-2"lBf>rtip', 
        buttons: [
            {
                extend: 'print',
                title: 'Queues',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            }
        ]
    });
    
    // Court Filter
    document.getElementById('courtFilter').addEventListener('change', function() {
        const courtId = this.value;

        // Filter waiting customers (cards)
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
