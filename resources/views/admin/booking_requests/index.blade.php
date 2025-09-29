
@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Booking Requests</strong></h2>  
            <div class="d-flex gap-3">
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-primary" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">All</h6>
                    <h4 class="mb-0 text-primary">{{ $requestCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-warning" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h4 class="mb-0 text-warning">{{ $pendingCount}}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Approved</h6>
                    <h4 class="mb-0 text-success">{{ $approvedCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Cancelled</h6>
                    <h4 class="mb-0 text-danger">{{ $cancelledCount }}</h4>
                </div>
            </div>
        </div>
    </div>


<div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Records</strong></h4>
            <div>
                <button id="exportCsv" class="btn btn-info btn-sm">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button id="printTable" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
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
                        @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->court->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($request->booking_date)->format('F d, Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}
                                </td>
                                <td>{{ $request->expected_hours }}h {{ $request->expected_minutes }}m</td>
                                <td>₱{{ number_format($request->amount, 2) }}</td>
                                <td>{{ $request->transaction_no ?? '—' }}</td>
                                <!--<td>{{ $request->staff->name ?? '—' }}</td>-->
                                <td class="text-light text-center">
                                    <span class="badge bg-{{ 
                                        $request->status === 'pending' ? 'warning' : 
                                        ($request->status === 'approved' ? 'success' : 
                                        ($request->status === 'completed' ? 'info' : 'danger')) 
                                    }}">
                                        {{ ucfirst($request->status) }}
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
    var table = $('#bookingRequestsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: 
            // top (search removed since you already have buttons outside)
            '<"top d-flex justify-content-between align-items-center mb-2"lf>rt' +
            // bottom with pagination aligned right
            '<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'Booking Request Records',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'Booking Request Records',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'Booking Request Records',
                exportOptions: { columns: ':visible' }
            }
        ]
    });

    // External buttons
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
</script>
@endpush
