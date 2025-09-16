@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Queues</strong></h4>
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
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#queuesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
