@extends('layouts.staff.app')
@section('content')
<div class="container-fluid">

    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Payments</strong></h2>
        <div class="d-flex gap-3">
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Cash</h6>
                <h4 class="mb-0 text-success">₱{{ number_format($totalCash, 2) }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-primary" style="min-width: 200px;">
                <h6 class="text-muted mb-1">GCash</h6>
                <h4 class="mb-0 text-primary">₱{{ number_format($totalGCash, 2) }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Total</h6>
                <h4 class="mb-0 text-info">₱{{ number_format($totalCollected, 2) }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 border-bottom-warning" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Pending Payments</h6>
                <h4 class="mb-0 text-warning">{{ $unsettledCount }}</h4>
            </div>
        </div>

    </div>
    

</div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Payment Records</strong></h4>
            <a href="{{ route('staff.payments.create') }}" class="btn btn-primary">Record Payment</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Session Type</th>
                            <th>Amount Paid</th>
                            <th>Payment Method</th>
                            <th>Transaction No.</th>
                            <th>Recorded By</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->session->customer_name ?? '—' }}</td>
                            <td>{{ $payment->session->court->name }}</td>
                            <td>{{ ucfirst($payment->session->session_type) }}</td>
                            <td>₱{{ number_format($payment->session->amount_paid, 2) }}</td>
                            <td>
                                @if (strtolower($payment->payment_method) === 'gcash') GCash @else {{ ucfirst($payment->payment_method) }} @endif
                            </td>

                            <td>{{ $payment->transaction_no ?? 'N/A'}}</td>
                            <td>{{ $payment->staff->name ?? '—' }}</td>
                            <td>{{ $payment->created_at->format('F d, Y - h:i A') }}</td>
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
    $('#paymentsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
