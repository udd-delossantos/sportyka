@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Queues</strong></h4>
            <!-- Export buttons -->
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="queuesTable">
                    <thead>
                        <tr>
                            <th>Queue No</th>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Duration</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Amount Paid</th>
                            <th>Transaction No.</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($queues as $queue)
                        <tr>
                             <td class="text-center">
                                        <h6 class="text-danger"><strong>#{{ $queue->queue_number }}</strong></h6>
                                    </td>
                            <td>{{ $queue->customer_name ?? 'N/A' }}</td>                       
                            <td>{{ $queue->court->name ?? 'N/A' }}</td>                    
                            <td>{{ $queue->expected_hours }}h {{$queue->expected_minutes}}m</td>
                            <td>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</td>                          
                            <td>{{ $queue->amount }}</td>
                            <td>{{ $queue->transaction_no ?? 'N/A' }}</td>
                            <td>{{ $queue->staff->name }}</td>
                             <td class="text-light text-center">
                                <span class="badge bg-{{ 
                                    $queue->status === 'waiting' ? 'warning' : 
                                    ($queue->status === 'called' ? 'success' : 
                                    ($queue->status === 'completed' ? 'info' : 'danger')) 
                                    }}">
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

@push('styles')

@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#queuesTable').DataTable({
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
                title: 'All Queues',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'All Queues',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'All Queues',
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
