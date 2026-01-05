@extends('layouts.staff.app')
@section('title', 'Queues Management')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Queues</h1>
        <a href="{{ route('staff.queues.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Add Queue
        </a>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total DP Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($queueTotalCollected, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cash Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($queueCashCollected, 2) }}</div>
                        </div>
                        <div class="col-auto">
                           <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">GCash Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($queueGCashCollected, 2) }}</div>
                        </div>
                        <div class="col-auto">
                             <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Skipped Queues</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $skippedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">Waiting Customers</h5>
            <select id="courtFilter" class="form-control form-control-sm border-left-primary ml-auto" style="width:200px;">
                <option value="">
                    All Courts 
                    @php 
                        $totalWaiting = $courts->sum('waiting_count'); 
                    @endphp 
                    @if($totalWaiting > 0) ({{ $totalWaiting }}) @endif
                </option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}">
                        {{ $court->name }} 
                        @if($court->waiting_count > 0) ({{ $court->waiting_count }}) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="card-body bg-light" style="max-height: 700px; overflow-y: auto;">
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <div id="bookingList">
                @forelse ($waitingQueues as $queue)
                    <div class="card shadow-sm mb-3 booking-card border-left-primary" data-court="{{ $queue->court_id }}">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 text-center px-2 py-1 bg-primary text-white rounded">
                                            <small class="d-block text-uppercase" style="font-size: 0.6rem;">Queue</small>
                                            <h5 class="mb-0">#{{ $queue->queue_number }}</h5>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 text-gray-800 font-weight-bold">{{ $queue->customer_name }}</h5>
                                            <span class="badge badge-warning">{{ ucfirst($queue->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 border-left">
                                    <div class="row no-gutters">
                                        <div class="col-6">
                                            <small class="text-muted text-uppercase font-weight-bold">Time Range</small>
                                            <div class="text-gray-800 font-weight-bold small">
                                                {{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted text-uppercase font-weight-bold">Court</small>
                                            <div class="text-primary font-weight-bold small">{{ $queue->court->name }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <div class="h5 mb-2 font-weight-bold text-success">₱{{ number_format($queue->amount, 2) }}</div>
                                    @if($queue->queue_number == 1)
                                        <form action="{{ route('staff.queues.call', $queue->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"></i>Start</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('staff.queues.skip', $queue->id) }}" method="POST" onsubmit="return confirm('Skip this queue?');" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Skip</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-user-clock fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No Customers in Queue.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Queue History</h5>
              <div class="btn-group shadow-sm" role="group">
                <button id="exportCsv" class="btn btn-sm btn-info">
                    <i class="fas fa-file-csv fa-sm text-white-50 mr-1"></i> CSV
                </button>
                <button id="exportExcel" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Excel
                </button>
                <button id="printTable" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="queuesTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Time Slot</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Ref No.</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedQueues as $queue)
                        <tr data-court="{{ $queue->court_id }}">
                            <td class="font-weight-bold text-gray-800">{{ $queue->customer_name }}</td>
                            <td>{{ $queue->court->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</td>
                            <td>{{ $queue->expected_hours}}h {{ $queue->expected_minutes }}m</td>
                            <td class="text-success font-weight-bold">₱{{ number_format($queue->amount, 2) }}</td>
                            <td><code>{{ $queue->transaction_no ?? 'Cash' }}</code></td>
                            <td>{{ $queue->staff->name ?? '—' }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = [
                                        'called' => 'badge-success',
                                        'completed' => 'badge-info',
                                        'skipped' => 'badge-danger'
                                    ][$queue->status] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }} p-2 w-75">{{ ucfirst($queue->status) }}</span>
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
    // Initializing DataTable with your specific requirements
    var table = $('#queuesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        dom: '<"top d-flex justify-content-between align-items-center mb-2"lf>rt<"bottom d-flex justify-content-between align-items-center"ip>', 
        buttons: [
            {
                extend: 'csv',
                title: 'Queues',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                title: 'Queues',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'Queues',
                exportOptions: { columns: ':visible' }
            }
        ]
    });

    // Binding your specific IDs to the DataTable buttons
    $('#exportCsv').on('click', function() {
        table.button(0).trigger();
    });
    $('#exportExcel').on('click', function() {
        table.button(1).trigger();
    });
    $('#printTable').on('click', function() {
        table.button(2).trigger();
    });
});

document.getElementById('courtFilter').addEventListener('change', function () {
    let selectedCourt = this.value.trim();
    let rows = document.querySelectorAll('.booking-card'); // correct selector

    rows.forEach(row => {
        let rowCourt = row.getAttribute('data-court').trim();

        // Show all if empty
        if (selectedCourt === "") {
            row.style.display = "";
            return;
        }

        // Filter by court ID
        row.style.display = (rowCourt === selectedCourt) ? "" : "none";
    });
});
</script>
@endpush