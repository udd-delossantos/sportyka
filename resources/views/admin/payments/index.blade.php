@extends('layouts.admin.app')
@section('title', 'Payments Dashboard')
@section('content')

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payments</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalCollected, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cash</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalCash, 2) }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">GCash</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalGCash, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unsettledCount }}</div>
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
            <h5 class="m-0 font-weight-bold text-primary">Payment Records</h5>
            
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="paymentsTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-gray-800">
                        <tr>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Session Type</th>
                            <th>Amount Paid</th>
                            <th>Payment Method</th>
                            <th>Reference No.</th>
                            <th>Recorded By</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td class="font-weight-bold text-gray-800">{{ $payment->session->customer_name ?? '—' }}</td>
                            <td>{{ $payment->session->court->name }}</td>
                            <td>{{ ucfirst($payment->session->session_type) }}</td>
                            <td class="text-success font-weight-bold">₱{{ number_format($payment->session->amount_paid, 2) }}</td>
                            <td class="text-center">
                                @if (strtolower($payment->payment_method) === 'gcash') 
                                    <span class="badge badge-info p-2 "><i class="fas fa-mobile-alt fa-sm mr-1"></i>GCash</span>
                                @else 
                                    <span class="badge badge-success p-2"><i class="fas fa-money-bill-wave fa-sm mr-1"></i>{{ ucfirst($payment->payment_method) }}</span>
                                @endif
                            </td>
                            <td><code>{{ $payment->transaction_no ?? 'N/A'}}</code></td>
                            <td>{{ $payment->staff->name ?? '—' }}</td>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
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
    var table = $('#paymentsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        dom: '<"top d-flex justify-content-between align-items-center mb-2"lf>rt<"bottom d-flex justify-content-between align-items-center"ip>', 
        buttons: [
            {
                extend: 'csv',
                title: 'Payment Records',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                title: 'Payment Records',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'Payment Records',
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
</script>
@endpush