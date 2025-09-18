@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Payment Records</strong></h4>
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
<style>
    /* Small styling tweaks for DataTables + Buttons alignment */
    .dataTables_wrapper .dt-buttons {
        margin-right: .5rem;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 0;
        display: inline-block;
    }
    .dataTables_wrapper .dataTables_filter {
        text-align: right;
    }
    .dataTables_wrapper .dataTables_info {
        text-align: left;
        padding-top: 6px;
    }
    .dataTables_wrapper .dataTables_paginate {
        text-align: right;
        padding-top: 6px;
    }
</style>

@endpush
@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#paymentsTable').DataTable({
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
                title: 'Payment Records',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
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
