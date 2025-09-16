@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Payment Records</strong></h4>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Session Type</th>
                            <th>Duration</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Amount</th>
                            <th>Recorded By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->session->customer_name ?? 'N/A' }}</td>                     
                            <td>{{ $payment->session->court->name ?? 'N/A' }}</td>  
                            <td>{{ ucfirst($payment->session->session_type) }}</td>
                            <td>{{ $payment->session->expected_hours }}h {{ $payment->session->expected_minutes }}m</td>
                            <td>{{ \Carbon\Carbon::parse($payment->session->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->session->end_time)->format('h:i A') }}</td>               
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->staff->name ?? 'N/A' }}</td>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') ?? ''}}</td>
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
    $('#paymentsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
